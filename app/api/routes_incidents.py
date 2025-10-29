import uuid

from fastapi import APIRouter, Depends, HTTPException, Query, status
from sqlalchemy import select
from sqlalchemy.ext.asyncio import AsyncSession

from app.api import deps
from app.models import (
    Incident,
    IncidentStatus,
    IncidentUpdate,
    SeverityLevel,
    Tenant,
    User,
)
from app.schemas.incident import (
    IncidentCreate,
    IncidentProgressRead,
    IncidentRead,
    IncidentReadinessOverview,
    IncidentReadinessRead,
)
from app.schemas.incident_update import IncidentUpdateCreate, IncidentUpdateRead
from app.services.readiness import (
    build_incident_readiness,
    compute_incident_progress_steps,
    compute_tenant_readiness_overview,
)

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
    status_filter: list[IncidentStatus] | None = Query(
        default=None,
        alias="status",
        description="Filter by one or more incident statuses",
    ),
    severity_filter: list[SeverityLevel] | None = Query(
        default=None,
        alias="severity",
        description="Filter by one or more incident severities",
    ),
) -> list[IncidentRead]:
    """List incidents associated with the tenant."""

    stmt = select(Incident).where(Incident.tenant_id == tenant.id)
    status_values = list(status_filter or [])
    if status_values:
        stmt = stmt.where(Incident.status.in_(status_values))

    severity_values = list(severity_filter or [])
    if severity_values:
        stmt = stmt.where(Incident.severity.in_(severity_values))

    result = await session.execute(stmt.order_by(Incident.reported_at.asc()))
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

    steps = await compute_incident_progress_steps(session, incident=incident, tenant=tenant)

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


@router.get(
    "/{incident_id}/readiness",
    response_model=IncidentReadinessRead,
    summary="Get incident readiness status",
)
async def read_incident_readiness(
    incident_id: uuid.UUID,
    tenant: Tenant = Depends(deps.ensure_tenant_access),
    _: User = Depends(deps.get_current_active_user),
    session: AsyncSession = Depends(deps.get_db_session),
) -> IncidentReadinessRead:
    """Provide the current lifecycle stage and remaining work to become fully operational."""

    incident = await session.get(Incident, incident_id)
    if incident is None or incident.tenant_id != tenant.id:
        raise HTTPException(status_code=status.HTTP_404_NOT_FOUND, detail="Incident not found")

    steps = await compute_incident_progress_steps(session, incident=incident, tenant=tenant)

    return build_incident_readiness(incident=incident, steps=steps)


@router.get(
    "/readiness/overview",
    response_model=IncidentReadinessOverview,
    summary="Get readiness overview for tenant incidents",
)
async def read_incident_readiness_overview(
    tenant: Tenant = Depends(deps.ensure_tenant_access),
    _: User = Depends(deps.get_current_active_user),
    session: AsyncSession = Depends(deps.get_db_session),
) -> IncidentReadinessOverview:
    """Aggregate readiness metrics across all incidents in the current tenant."""

    return await compute_tenant_readiness_overview(session, tenant=tenant)
