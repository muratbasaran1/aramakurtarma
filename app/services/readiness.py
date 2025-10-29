from __future__ import annotations

from sqlalchemy import func, select
from sqlalchemy.ext.asyncio import AsyncSession

from app.models import (
    DeploymentReport,
    DeploymentReportStatus,
    Incident,
    IncidentStatus,
    IncidentDeployment,
    IncidentDeploymentStatus,
    RecoveryPlan,
    RecoveryPlanStatus,
    ReliefDelivery,
    ReliefDeliveryStatus,
    ResourceRequest,
    ResourceRequestStatus,
    Tenant,
)
from app.schemas.incident import (
    IncidentProgressStep,
    IncidentReadinessEntry,
    IncidentReadinessOverview,
    IncidentReadinessRead,
)


async def compute_incident_progress_steps(
    session: AsyncSession, *, incident: Incident, tenant: Tenant
) -> list[IncidentProgressStep]:
    """Assemble the ordered list of lifecycle milestones for an incident."""

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

    return steps


def build_incident_readiness(
    *, incident: Incident, steps: list[IncidentProgressStep]
) -> IncidentReadinessRead:
    """Derive readiness information from lifecycle steps for a single incident."""

    completed_steps = sum(1 for step in steps if step.completed)
    total_steps = len(steps)
    remaining_steps = total_steps - completed_steps
    completion_ratio = completed_steps / total_steps if total_steps else 0.0
    ready = remaining_steps == 0
    pending_steps = [step for step in steps if not step.completed]

    if ready:
        current_stage_key = "completed"
        current_stage_title = "Tüm aşamalar tamamlandı"
        current_stage_detail = "Olay yönetimi süreçleri ayağa kaldırılmaya hazır."
        summary = "Tüm operasyon adımları tamamlandı; sistem sahaya alınabilir. Kalan adım: 0."
    else:
        next_step = pending_steps[0]
        current_stage_key = next_step.key
        current_stage_title = next_step.title
        current_stage_detail = next_step.detail
        pending_titles = ", ".join(step.title for step in pending_steps)
        summary = (
            f"Sıradaki aşama: {next_step.title}. Süreci ayağa kaldırmak için {remaining_steps} adım kaldı. "
            f"Kalan adımlar: {pending_titles}."
        )

    return IncidentReadinessRead(
        incident_id=incident.id,
        current_stage_key=current_stage_key,
        current_stage_title=current_stage_title,
        current_stage_detail=current_stage_detail,
        total_steps=total_steps,
        completed_steps=completed_steps,
        remaining_steps=remaining_steps,
        completion_ratio=completion_ratio,
        ready=ready,
        summary=summary,
        steps=steps,
        pending_steps=pending_steps,
    )


async def compute_tenant_readiness_overview(
    session: AsyncSession, *, tenant: Tenant
) -> IncidentReadinessOverview:
    """Aggregate readiness metrics across all incidents in the current tenant."""

    result = await session.execute(
        select(Incident).where(Incident.tenant_id == tenant.id).order_by(Incident.reported_at.asc())
    )
    incidents = result.scalars().all()

    readiness_entries: list[IncidentReadinessEntry] = []
    total_ratio = 0.0
    ready_count = 0

    for incident in incidents:
        steps = await compute_incident_progress_steps(session, incident=incident, tenant=tenant)
        readiness = build_incident_readiness(incident=incident, steps=steps)
        readiness_entries.append(
            IncidentReadinessEntry(
                **readiness.model_dump(),
                incident_title=incident.title,
                incident_status=incident.status,
            )
        )
        total_ratio += readiness.completion_ratio
        if readiness.ready:
            ready_count += 1

    total_incidents = len(readiness_entries)
    average_ratio = total_ratio / total_incidents if total_incidents else 0.0

    return IncidentReadinessOverview(
        tenant_id=tenant.id,
        total_incidents=total_incidents,
        ready_incidents=ready_count,
        not_ready_incidents=total_incidents - ready_count,
        average_completion_ratio=average_ratio,
        incidents=readiness_entries,
    )
