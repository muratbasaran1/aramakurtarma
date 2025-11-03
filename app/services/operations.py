from __future__ import annotations

from collections.abc import Iterable
from dataclasses import dataclass
from typing import Callable

from sqlalchemy import func, select
from sqlalchemy.ext.asyncio import AsyncSession

from app.models import (
    DeploymentReport,
    DeploymentReportStatus,
    Incident,
    IncidentDeployment,
    IncidentDeploymentStatus,
    IncidentStatus,
    RecoveryPlan,
    RecoveryPlanStatus,
    ReliefDelivery,
    ReliefDeliveryStatus,
    ResourceRequest,
    ResourceRequestStatus,
    Task,
    TaskStatus,
    Tenant,
)
from app.schemas.operations import (
    GlobalOperationsBacklog,
    GlobalOperationsBacklogCategory,
    GlobalOperationsSummary,
    OperationsBacklogItem,
    OperationsCategorySummary,
    TenantOperationsBacklog,
    TenantOperationsSummary,
)


@dataclass(frozen=True)
class BacklogCategoryConfig:
    """Reusable configuration for backlog category queries."""

    category: str
    open_statuses: tuple[object, ...]
    count_stmt_factory: Callable[[Tenant], object]
    oldest_stmt_factory: Callable[[Tenant], object]


incident_open_statuses = (
    IncidentStatus.NEW,
    IncidentStatus.TRIAGED,
    IncidentStatus.IN_PROGRESS,
)


resource_open_statuses = (
    ResourceRequestStatus.PENDING,
    ResourceRequestStatus.APPROVED,
    ResourceRequestStatus.DISPATCHED,
)


deployment_open_statuses = (
    IncidentDeploymentStatus.PREPARING,
    IncidentDeploymentStatus.EN_ROUTE,
    IncidentDeploymentStatus.ON_SCENE,
)


report_open_statuses = (
    DeploymentReportStatus.DRAFT,
    DeploymentReportStatus.SUBMITTED,
    DeploymentReportStatus.REQUIRES_FOLLOW_UP,
)


delivery_open_statuses = (
    ReliefDeliveryStatus.PLANNED,
    ReliefDeliveryStatus.IN_TRANSIT,
    ReliefDeliveryStatus.DELIVERED,
)


recovery_open_statuses = (
    RecoveryPlanStatus.DRAFT,
    RecoveryPlanStatus.IN_PROGRESS,
)


task_open_statuses = (
    TaskStatus.PENDING,
    TaskStatus.ASSIGNED,
    TaskStatus.IN_PROGRESS,
)


BACKLOG_CATEGORY_CONFIGS: tuple[BacklogCategoryConfig, ...] = (
    BacklogCategoryConfig(
        category="incidents",
        open_statuses=incident_open_statuses,
        count_stmt_factory=lambda tenant: select(func.count(Incident.id)).where(
            Incident.tenant_id == tenant.id,
            Incident.status.in_(incident_open_statuses),
        ),
        oldest_stmt_factory=lambda tenant: (
            select(Incident.id, Incident.title, Incident.reported_at)
            .where(
                Incident.tenant_id == tenant.id,
                Incident.status.in_(incident_open_statuses),
            )
            .order_by(Incident.reported_at.asc())
            .limit(1)
        ),
    ),
    BacklogCategoryConfig(
        category="resource_requests",
        open_statuses=resource_open_statuses,
        count_stmt_factory=lambda tenant: select(
            func.count(ResourceRequest.id)
        ).where(
            ResourceRequest.tenant_id == tenant.id,
            ResourceRequest.status.in_(resource_open_statuses),
        ),
        oldest_stmt_factory=lambda tenant: (
            select(ResourceRequest.id, ResourceRequest.summary, ResourceRequest.created_at)
            .where(
                ResourceRequest.tenant_id == tenant.id,
                ResourceRequest.status.in_(resource_open_statuses),
            )
            .order_by(ResourceRequest.created_at.asc())
            .limit(1)
        ),
    ),
    BacklogCategoryConfig(
        category="deployments",
        open_statuses=deployment_open_statuses,
        count_stmt_factory=lambda tenant: select(
            func.count(IncidentDeployment.id)
        ).where(
            IncidentDeployment.tenant_id == tenant.id,
            IncidentDeployment.status.in_(deployment_open_statuses),
        ),
        oldest_stmt_factory=lambda tenant: (
            select(
                IncidentDeployment.id,
                IncidentDeployment.name,
                IncidentDeployment.created_at,
            )
            .where(
                IncidentDeployment.tenant_id == tenant.id,
                IncidentDeployment.status.in_(deployment_open_statuses),
            )
            .order_by(IncidentDeployment.created_at.asc())
            .limit(1)
        ),
    ),
    BacklogCategoryConfig(
        category="deployment_reports",
        open_statuses=report_open_statuses,
        count_stmt_factory=lambda tenant: select(
            func.count(DeploymentReport.id)
        ).where(
            DeploymentReport.tenant_id == tenant.id,
            DeploymentReport.status.in_(report_open_statuses),
        ),
        oldest_stmt_factory=lambda tenant: (
            select(DeploymentReport.id, DeploymentReport.title, DeploymentReport.created_at)
            .where(
                DeploymentReport.tenant_id == tenant.id,
                DeploymentReport.status.in_(report_open_statuses),
            )
            .order_by(DeploymentReport.created_at.asc())
            .limit(1)
        ),
    ),
    BacklogCategoryConfig(
        category="relief_deliveries",
        open_statuses=delivery_open_statuses,
        count_stmt_factory=lambda tenant: select(
            func.count(ReliefDelivery.id)
        ).where(
            ReliefDelivery.tenant_id == tenant.id,
            ReliefDelivery.status.in_(delivery_open_statuses),
        ),
        oldest_stmt_factory=lambda tenant: (
            select(ReliefDelivery.id, ReliefDelivery.title, ReliefDelivery.created_at)
            .where(
                ReliefDelivery.tenant_id == tenant.id,
                ReliefDelivery.status.in_(delivery_open_statuses),
            )
            .order_by(ReliefDelivery.created_at.asc())
            .limit(1)
        ),
    ),
    BacklogCategoryConfig(
        category="recovery_plans",
        open_statuses=recovery_open_statuses,
        count_stmt_factory=lambda tenant: select(
            func.count(RecoveryPlan.id)
        ).where(
            RecoveryPlan.tenant_id == tenant.id,
            RecoveryPlan.status.in_(recovery_open_statuses),
        ),
        oldest_stmt_factory=lambda tenant: (
            select(RecoveryPlan.id, RecoveryPlan.title, RecoveryPlan.created_at)
            .where(
                RecoveryPlan.tenant_id == tenant.id,
                RecoveryPlan.status.in_(recovery_open_statuses),
            )
            .order_by(RecoveryPlan.created_at.asc())
            .limit(1)
        ),
    ),
    BacklogCategoryConfig(
        category="tasks",
        open_statuses=task_open_statuses,
        count_stmt_factory=lambda tenant: select(func.count(Task.id))
        .join(Incident, Task.incident_id == Incident.id)
        .where(
            Incident.tenant_id == tenant.id,
            Task.status.in_(task_open_statuses),
        ),
        oldest_stmt_factory=lambda tenant: (
            select(Task.id, Task.title, Task.created_at)
            .join(Incident, Task.incident_id == Incident.id)
            .where(
                Incident.tenant_id == tenant.id,
                Task.status.in_(task_open_statuses),
            )
            .order_by(Task.created_at.asc())
            .limit(1)
        ),
    ),
)


def _status_key(status: object) -> str:
    """Return the string representation for enum values."""

    return status.value if hasattr(status, "value") else str(status)


def _initialise_counts(statuses: Iterable[object]) -> dict[str, int]:
    """Prepare a dictionary with zero values for each status."""

    return {_status_key(status): 0 for status in statuses}


async def _collect_counts(
    session: AsyncSession,
    stmt,
    statuses: Iterable[object],
) -> OperationsCategorySummary:
    """Execute an aggregate statement and normalise the results."""

    counts = _initialise_counts(statuses)
    result = await session.execute(stmt)
    for status, count in result.all():
        counts[_status_key(status)] = count
    total = sum(counts.values())
    return OperationsCategorySummary(total=total, by_status=counts)


async def compute_tenant_operations_summary(
    session: AsyncSession, *, tenant: Tenant
) -> TenantOperationsSummary:
    """Compile operational totals for the given tenant."""

    incident_stmt = (
        select(Incident.status, func.count(Incident.id))
        .where(Incident.tenant_id == tenant.id)
        .group_by(Incident.status)
    )
    incidents = await _collect_counts(session, incident_stmt, IncidentStatus)

    resource_stmt = (
        select(ResourceRequest.status, func.count(ResourceRequest.id))
        .where(ResourceRequest.tenant_id == tenant.id)
        .group_by(ResourceRequest.status)
    )
    resource_requests = await _collect_counts(session, resource_stmt, ResourceRequestStatus)

    deployment_stmt = (
        select(IncidentDeployment.status, func.count(IncidentDeployment.id))
        .where(IncidentDeployment.tenant_id == tenant.id)
        .group_by(IncidentDeployment.status)
    )
    deployments = await _collect_counts(session, deployment_stmt, IncidentDeploymentStatus)

    report_stmt = (
        select(DeploymentReport.status, func.count(DeploymentReport.id))
        .where(DeploymentReport.tenant_id == tenant.id)
        .group_by(DeploymentReport.status)
    )
    deployment_reports = await _collect_counts(session, report_stmt, DeploymentReportStatus)

    delivery_stmt = (
        select(ReliefDelivery.status, func.count(ReliefDelivery.id))
        .where(ReliefDelivery.tenant_id == tenant.id)
        .group_by(ReliefDelivery.status)
    )
    relief_deliveries = await _collect_counts(session, delivery_stmt, ReliefDeliveryStatus)

    recovery_stmt = (
        select(RecoveryPlan.status, func.count(RecoveryPlan.id))
        .where(RecoveryPlan.tenant_id == tenant.id)
        .group_by(RecoveryPlan.status)
    )
    recovery_plans = await _collect_counts(session, recovery_stmt, RecoveryPlanStatus)

    task_stmt = (
        select(Task.status, func.count(Task.id))
        .join(Incident, Task.incident_id == Incident.id)
        .where(Incident.tenant_id == tenant.id)
        .group_by(Task.status)
    )
    tasks = await _collect_counts(session, task_stmt, TaskStatus)

    return TenantOperationsSummary(
        tenant_id=tenant.id,
        tenant_name=tenant.name,
        tenant_slug=tenant.slug,
        incidents=incidents,
        resource_requests=resource_requests,
        deployments=deployments,
        deployment_reports=deployment_reports,
        relief_deliveries=relief_deliveries,
        recovery_plans=recovery_plans,
        tasks=tasks,
    )


def _merge_category_totals(
    accumulator: OperationsCategorySummary, update: OperationsCategorySummary
) -> OperationsCategorySummary:
    """Combine category counts in-place and return the accumulator."""

    for status, count in update.by_status.items():
        accumulator.by_status[status] = accumulator.by_status.get(status, 0) + count
    accumulator.total = sum(accumulator.by_status.values())
    return accumulator


async def compute_global_operations_summary(
    session: AsyncSession,
) -> GlobalOperationsSummary:
    """Aggregate operational metrics across every tenant."""

    result = await session.execute(select(Tenant).order_by(Tenant.name.asc()))
    tenants = result.scalars().all()

    aggregated_incidents = OperationsCategorySummary(
        total=0, by_status=_initialise_counts(IncidentStatus)
    )
    aggregated_resources = OperationsCategorySummary(
        total=0, by_status=_initialise_counts(ResourceRequestStatus)
    )
    aggregated_deployments = OperationsCategorySummary(
        total=0, by_status=_initialise_counts(IncidentDeploymentStatus)
    )
    aggregated_reports = OperationsCategorySummary(
        total=0, by_status=_initialise_counts(DeploymentReportStatus)
    )
    aggregated_deliveries = OperationsCategorySummary(
        total=0, by_status=_initialise_counts(ReliefDeliveryStatus)
    )
    aggregated_recovery = OperationsCategorySummary(
        total=0, by_status=_initialise_counts(RecoveryPlanStatus)
    )
    aggregated_tasks = OperationsCategorySummary(
        total=0, by_status=_initialise_counts(TaskStatus)
    )

    tenant_summaries: list[TenantOperationsSummary] = []

    for tenant in tenants:
        tenant_summary = await compute_tenant_operations_summary(session, tenant=tenant)
        tenant_summaries.append(tenant_summary)
        _merge_category_totals(aggregated_incidents, tenant_summary.incidents)
        _merge_category_totals(aggregated_resources, tenant_summary.resource_requests)
        _merge_category_totals(aggregated_deployments, tenant_summary.deployments)
        _merge_category_totals(aggregated_reports, tenant_summary.deployment_reports)
        _merge_category_totals(aggregated_deliveries, tenant_summary.relief_deliveries)
        _merge_category_totals(aggregated_recovery, tenant_summary.recovery_plans)
        _merge_category_totals(aggregated_tasks, tenant_summary.tasks)

    return GlobalOperationsSummary(
        total_tenants=len(tenant_summaries),
        incidents=aggregated_incidents,
        resource_requests=aggregated_resources,
        deployments=aggregated_deployments,
        deployment_reports=aggregated_reports,
        relief_deliveries=aggregated_deliveries,
        recovery_plans=aggregated_recovery,
        tasks=aggregated_tasks,
        tenants=tenant_summaries,
    )


async def compute_tenant_operations_backlog(
    session: AsyncSession, *, tenant: Tenant
) -> TenantOperationsBacklog:
    """Highlight outstanding workload for the given tenant."""

    backlog_items: list[OperationsBacklogItem] = []
    total_pending = 0

    for config in BACKLOG_CATEGORY_CONFIGS:
        count_stmt = config.count_stmt_factory(tenant)
        count = (await session.execute(count_stmt)).scalar_one()
        total_pending += count

        open_statuses = [_status_key(status) for status in config.open_statuses]

        if count:
            result = await session.execute(config.oldest_stmt_factory(tenant))
            oldest_id, oldest_title, oldest_since = result.one()
            backlog_items.append(
                OperationsBacklogItem(
                    category=config.category,
                    open_statuses=open_statuses,
                    pending=count,
                    oldest_item_id=oldest_id,
                    oldest_item_title=oldest_title,
                    oldest_pending_since=oldest_since,
                )
            )
        else:
            backlog_items.append(
                OperationsBacklogItem(
                    category=config.category,
                    open_statuses=open_statuses,
                    pending=0,
                )
            )

    return TenantOperationsBacklog(
        tenant_id=tenant.id,
        tenant_name=tenant.name,
        tenant_slug=tenant.slug,
        total_pending=total_pending,
        items=backlog_items,
    )


async def compute_global_operations_backlog(
    session: AsyncSession,
) -> GlobalOperationsBacklog:
    """Aggregate backlog metrics across all tenants."""

    result = await session.execute(select(Tenant).order_by(Tenant.name.asc()))
    tenants = result.scalars().all()

    total_pending = 0
    tenant_backlogs: list[TenantOperationsBacklog] = []

    category_accumulator: dict[str, dict[str, object]] = {
        config.category: {
            "open_statuses": [_status_key(status) for status in config.open_statuses],
            "pending": 0,
            "tenant_count": 0,
            "worst": None,
            "oldest": None,
        }
        for config in BACKLOG_CATEGORY_CONFIGS
    }

    for tenant in tenants:
        backlog = await compute_tenant_operations_backlog(session, tenant=tenant)
        tenant_backlogs.append(backlog)
        total_pending += backlog.total_pending

        for item in backlog.items:
            accumulator = category_accumulator[item.category]
            accumulator["pending"] = int(accumulator["pending"]) + item.pending
            if item.pending > 0:
                accumulator["tenant_count"] = int(accumulator["tenant_count"]) + 1

                worst = accumulator["worst"]
                worst_pending = worst["pending"] if worst else -1
                candidate_since = item.oldest_pending_since
                if item.pending > worst_pending:
                    accumulator["worst"] = {
                        "tenant": backlog,
                        "pending": item.pending,
                        "item": item,
                    }
                elif item.pending == worst_pending and worst is not None:
                    current_item = worst["item"]
                    current_since = current_item.oldest_pending_since
                    if candidate_since and (
                        current_since is None or candidate_since < current_since
                    ):
                        accumulator["worst"] = {
                            "tenant": backlog,
                            "pending": item.pending,
                            "item": item,
                        }

                oldest = accumulator["oldest"]
                if candidate_since:
                    if (
                        oldest is None
                        or oldest["item"].oldest_pending_since is None
                        or candidate_since < oldest["item"].oldest_pending_since
                    ):
                        accumulator["oldest"] = {
                            "tenant": backlog,
                            "item": item,
                        }

    categories: list[GlobalOperationsBacklogCategory] = []

    for config in BACKLOG_CATEGORY_CONFIGS:
        accumulator = category_accumulator[config.category]
        worst = accumulator["worst"]
        oldest = accumulator["oldest"]

        categories.append(
            GlobalOperationsBacklogCategory(
                category=config.category,
                open_statuses=accumulator["open_statuses"],
                pending=int(accumulator["pending"]),
                tenant_count=int(accumulator["tenant_count"]),
                worst_tenant_id=(
                    worst["tenant"].tenant_id if worst else None
                ),
                worst_tenant_name=(
                    worst["tenant"].tenant_name if worst else None
                ),
                worst_tenant_slug=(
                    worst["tenant"].tenant_slug if worst else None
                ),
                worst_tenant_pending=(worst["pending"] if worst else None),
                oldest_item_id=(
                    oldest["item"].oldest_item_id if oldest else None
                ),
                oldest_item_title=(
                    oldest["item"].oldest_item_title if oldest else None
                ),
                oldest_pending_since=(
                    oldest["item"].oldest_pending_since if oldest else None
                ),
            )
        )

    return GlobalOperationsBacklog(
        total_pending=total_pending,
        categories=categories,
        tenants=tenant_backlogs,
    )
