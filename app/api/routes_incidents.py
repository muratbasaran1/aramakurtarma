import uuid

from fastapi import APIRouter, Depends, HTTPException, status
from sqlalchemy import func, select
from sqlalchemy.ext.asyncio import AsyncSession

from app.api import deps
from app.models import (
    DeploymentReport,
    DeploymentReportStatus,
    Incident,
    IncidentStatus,
    IncidentDeployment,
    IncidentDeploymentStatus,
    IncidentUpdate,
    RecoveryPlan,
    RecoveryPlanStatus,
    ReliefDelivery,
    ReliefDeliveryStatus,
    ResourceRequest,
    ResourceRequestStatus,
    Tenant,
    User,
)
from app.schemas.incident import (
    IncidentCreate,
    IncidentProgressRead,
    IncidentProgressStep,
    IncidentRead,
)
from app.schemas.incident_update import IncidentUpdateCreate, IncidentUpdateRead

router = APIRouter(prefix="/incidents", tags=["incidents"])


@router.post("/", response_model=IncidentRead, status_code=status.HTTP_201_CREATED, summary="Create incident")
async def create_incident(
    incident_in: IncidentCreate,
    tenant: Tenant = Depends(deps.ensure_tenant_access),
    reporter: User = Depends(deps.get_current_active_user),
    session: AsyncSession = Depends(deps.get_db_session),
) -> IncidentRead:
    """Create a new incident scoped to the tenant header."""

    incident = Incident(
        title=incident_in.title,
        description=incident_in.description,
        status=incident_in.status,
        severity=incident_in.severity,
        location=incident_in.location,
        tenant_id=tenant.id,
        reporter_id=reporter.id,
    )
    session.add(incident)
    await session.commit()
    await session.refresh(incident)
    return IncidentRead.model_validate(incident)


@router.get("/", response_model=list[IncidentRead], summary="List incidents")
async def list_incidents(
    tenant: Tenant = Depends(deps.ensure_tenant_access),
    _: User = Depends(deps.get_current_active_user),
    session: AsyncSession = Depends(deps.get_db_session),
) -> list[IncidentRead]:
    """List incidents associated with the tenant."""

    result = await session.execute(select(Incident).where(Incident.tenant_id == tenant.id))
    incidents = result.scalars().all()
    return [IncidentRead.model_validate(item) for item in incidents]


@router.get("/{incident_id}", response_model=IncidentRead, summary="Get incident")
async def read_incident(
    incident_id: uuid.UUID,
    tenant: Tenant = Depends(deps.ensure_tenant_access),
    _: User = Depends(deps.get_current_active_user),
    session: AsyncSession = Depends(deps.get_db_session),
) -> IncidentRead:
    """Retrieve a single incident by identifier."""

    incident = await session.get(Incident, incident_id)
    if incident is None or incident.tenant_id != tenant.id:
        raise HTTPException(status_code=status.HTTP_404_NOT_FOUND, detail="Incident not found")
    return IncidentRead.model_validate(incident)


@router.post(
    "/{incident_id}/updates",
    response_model=IncidentUpdateRead,
    status_code=status.HTTP_201_CREATED,
    summary="Create incident update",
)
async def create_incident_update(
    incident_id: uuid.UUID,
    update_in: IncidentUpdateCreate,
    tenant: Tenant = Depends(deps.ensure_tenant_access),
    author: User = Depends(deps.get_current_active_user),
    session: AsyncSession = Depends(deps.get_db_session),
) -> IncidentUpdateRead:
    """Append a chronological update to an incident and optionally adjust its status."""

    incident = await session.get(Incident, incident_id)
    if incident is None or incident.tenant_id != tenant.id:
        raise HTTPException(status_code=status.HTTP_404_NOT_FOUND, detail="Incident not found")

    update = IncidentUpdate(
        incident_id=incident.id,
        author_id=author.id,
        message=update_in.message,
        status=update_in.status,
    )
    session.add(update)

    if update_in.status is not None and update_in.status != incident.status:
        incident.status = update_in.status

    await session.commit()
    await session.refresh(update)
    return IncidentUpdateRead.model_validate(update)


@router.get(
    "/{incident_id}/updates",
    response_model=list[IncidentUpdateRead],
    summary="List incident updates",
)
async def list_incident_updates(
    incident_id: uuid.UUID,
    tenant: Tenant = Depends(deps.ensure_tenant_access),
    _: User = Depends(deps.get_current_active_user),
    session: AsyncSession = Depends(deps.get_db_session),
) -> list[IncidentUpdateRead]:
    """Return the chronological updates tied to an incident in the same tenant."""

    result = await session.execute(
        select(IncidentUpdate)
        .join(Incident)
        .where(IncidentUpdate.incident_id == incident_id, Incident.tenant_id == tenant.id)
        .order_by(IncidentUpdate.created_at.asc())
    )
    updates = result.scalars().all()
    return [IncidentUpdateRead.model_validate(update) for update in updates]


@router.get(
    "/{incident_id}/progress",
    response_model=IncidentProgressRead,
    summary="Get incident progress",
)
async def read_incident_progress(
    incident_id: uuid.UUID,
    tenant: Tenant = Depends(deps.ensure_tenant_access),
    _: User = Depends(deps.get_current_active_user),
    session: AsyncSession = Depends(deps.get_db_session),
) -> IncidentProgressRead:
    """Calculate high-level progress for the incident lifecycle."""

    incident = await session.get(Incident, incident_id)
    if incident is None or incident.tenant_id != tenant.id:
        raise HTTPException(status_code=status.HTTP_404_NOT_FOUND, detail="Incident not found")

    steps: list[IncidentProgressStep] = []

    def append_step(key: str, title: str, completed: bool, detail: str | None = None) -> None:
        steps.append(
            IncidentProgressStep(
                key=key,
                title=title,
                completed=completed,
                detail=detail,
            )
        )

    append_step(
        "incident_reported",
        "Olay kaydı oluşturuldu",
        True,
        detail=f"Durum: {incident.status.value}",
    )

    progressed = incident.status in {
        IncidentStatus.TRIAGED,
        IncidentStatus.IN_PROGRESS,
        IncidentStatus.RESOLVED,
        IncidentStatus.CLOSED,
    }
    append_step(
        "incident_progressed",
        "Durum değerlendirmesi tamamlandı",
        progressed,
        detail=f"Mevcut durum: {incident.status.value}",
    )

    resource_count = await session.execute(
        select(func.count(ResourceRequest.id)).where(
            ResourceRequest.incident_id == incident.id,
            ResourceRequest.tenant_id == tenant.id,
            ResourceRequest.status.in_(
                [ResourceRequestStatus.DISPATCHED, ResourceRequestStatus.FULFILLED]
            ),
        )
    )
    append_step(
        "resources_mobilized",
        "Kaynak talebi sevk edildi",
        resource_count.scalar_one() > 0,
    )

    deployment_count = await session.execute(
        select(func.count(IncidentDeployment.id)).where(
            IncidentDeployment.incident_id == incident.id,
            IncidentDeployment.tenant_id == tenant.id,
            IncidentDeployment.status.in_(
                [IncidentDeploymentStatus.ON_SCENE, IncidentDeploymentStatus.COMPLETED]
            ),
        )
    )
    append_step(
        "deployment_on_scene",
        "Saha konuşlandırması olay yerinde",
        deployment_count.scalar_one() > 0,
    )

    report_count = await session.execute(
        select(func.count(DeploymentReport.id)).where(
            DeploymentReport.incident_id == incident.id,
            DeploymentReport.tenant_id == tenant.id,
            DeploymentReport.status == DeploymentReportStatus.APPROVED,
        )
    )
    append_step(
        "field_report_approved",
        "Saha raporu onaylandı",
        report_count.scalar_one() > 0,
    )

    delivery_count = await session.execute(
        select(func.count(ReliefDelivery.id)).where(
            ReliefDelivery.incident_id == incident.id,
            ReliefDelivery.tenant_id == tenant.id,
            ReliefDelivery.status == ReliefDeliveryStatus.VERIFIED,
        )
    )
    append_step(
        "relief_delivery_verified",
        "Yardım teslimatı doğrulandı",
        delivery_count.scalar_one() > 0,
    )

    recovery_count = await session.execute(
        select(func.count(RecoveryPlan.id)).where(
            RecoveryPlan.incident_id == incident.id,
            RecoveryPlan.tenant_id == tenant.id,
            RecoveryPlan.status == RecoveryPlanStatus.COMPLETED,
        )
    )
    append_step(
        "recovery_plan_completed",
        "Rehabilitasyon planı tamamlandı",
        recovery_count.scalar_one() > 0,
    )

    completed_steps = sum(1 for step in steps if step.completed)
    total_steps = len(steps)
    completion_ratio = completed_steps / total_steps if total_steps else 0.0

    return IncidentProgressRead(
        incident_id=incident.id,
        total_steps=total_steps,
        completed_steps=completed_steps,
        completion_ratio=completion_ratio,
        steps=steps,
    )
