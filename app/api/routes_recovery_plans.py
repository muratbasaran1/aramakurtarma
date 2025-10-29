"""Recovery plan API endpoints."""

from __future__ import annotations

import uuid
from datetime import UTC, datetime

from fastapi import APIRouter, Depends, HTTPException, Query, status
from sqlalchemy import select
from sqlalchemy.ext.asyncio import AsyncSession

from app.api import deps
from app.models import Incident, RecoveryPlan, RecoveryPlanStatus, Tenant, User
from app.schemas.recovery_plan import (
    RecoveryPlanCreate,
    RecoveryPlanRead,
    RecoveryPlanUpdate,
)


router = APIRouter(prefix="/recovery-plans", tags=["recovery-plans"])


@router.post(
    "/",
    response_model=RecoveryPlanRead,
    status_code=status.HTTP_201_CREATED,
    summary="Create recovery plan",
)
async def create_recovery_plan(
    plan_in: RecoveryPlanCreate,
    tenant: Tenant = Depends(deps.ensure_tenant_access),
    current_user: User = Depends(deps.get_current_active_user),
    session: AsyncSession = Depends(deps.get_db_session),
) -> RecoveryPlanRead:
    """Create a recovery plan scoped to a specific incident."""

    incident = await session.get(Incident, plan_in.incident_id)
    if incident is None or incident.tenant_id != tenant.id:
        raise HTTPException(status_code=status.HTTP_404_NOT_FOUND, detail="Incident not found")

    plan = RecoveryPlan(
        title=plan_in.title,
        description=plan_in.description,
        status=plan_in.status,
        priority=plan_in.priority,
        started_at=plan_in.started_at,
        target_completion_at=plan_in.target_completion_at,
        incident_id=incident.id,
        tenant_id=tenant.id,
        owner_id=current_user.id,
    )
    session.add(plan)
    await session.commit()
    await session.refresh(plan)
    return RecoveryPlanRead.model_validate(plan)


@router.get("/", response_model=list[RecoveryPlanRead], summary="List recovery plans")
async def list_recovery_plans(
    tenant: Tenant = Depends(deps.ensure_tenant_access),
    _: User = Depends(deps.get_current_active_user),
    session: AsyncSession = Depends(deps.get_db_session),
    incident_id: uuid.UUID | None = Query(default=None, description="Filter by incident ID"),
    status_filter: RecoveryPlanStatus | None = Query(
        default=None, alias="status", description="Filter by plan status"
    ),
) -> list[RecoveryPlanRead]:
    """List recovery plans within the tenant."""

    stmt = select(RecoveryPlan).where(RecoveryPlan.tenant_id == tenant.id)
    if incident_id is not None:
        stmt = stmt.where(RecoveryPlan.incident_id == incident_id)
    if status_filter is not None:
        stmt = stmt.where(RecoveryPlan.status == status_filter)

    result = await session.execute(stmt.order_by(RecoveryPlan.created_at.desc()))
    plans = result.scalars().all()
    return [RecoveryPlanRead.model_validate(plan) for plan in plans]


@router.get(
    "/{plan_id}",
    response_model=RecoveryPlanRead,
    summary="Get recovery plan",
)
async def read_recovery_plan(
    plan_id: uuid.UUID,
    tenant: Tenant = Depends(deps.ensure_tenant_access),
    _: User = Depends(deps.get_current_active_user),
    session: AsyncSession = Depends(deps.get_db_session),
) -> RecoveryPlanRead:
    """Retrieve a recovery plan if it belongs to the tenant."""

    result = await session.execute(
        select(RecoveryPlan).where(
            RecoveryPlan.id == plan_id,
            RecoveryPlan.tenant_id == tenant.id,
        )
    )
    plan = result.scalar_one_or_none()
    if plan is None:
        raise HTTPException(status_code=status.HTTP_404_NOT_FOUND, detail="Recovery plan not found")
    return RecoveryPlanRead.model_validate(plan)


@router.patch(
    "/{plan_id}",
    response_model=RecoveryPlanRead,
    summary="Update recovery plan",
)
async def update_recovery_plan(
    plan_id: uuid.UUID,
    plan_in: RecoveryPlanUpdate,
    tenant: Tenant = Depends(deps.ensure_tenant_access),
    _: User = Depends(deps.get_current_active_user),
    session: AsyncSession = Depends(deps.get_db_session),
) -> RecoveryPlanRead:
    """Update mutable fields of a recovery plan."""

    result = await session.execute(
        select(RecoveryPlan).where(
            RecoveryPlan.id == plan_id,
            RecoveryPlan.tenant_id == tenant.id,
        )
    )
    plan = result.scalar_one_or_none()
    if plan is None:
        raise HTTPException(status_code=status.HTTP_404_NOT_FOUND, detail="Recovery plan not found")

    update_data = plan_in.model_dump(exclude_unset=True)

    if "title" in update_data:
        new_title = update_data["title"]
        if new_title is None:
            raise HTTPException(status_code=status.HTTP_400_BAD_REQUEST, detail="Title cannot be empty")
        plan.title = new_title

    if "description" in update_data:
        plan.description = update_data["description"]

    if "status" in update_data:
        plan.status = update_data["status"]
        if (
            update_data["status"] == RecoveryPlanStatus.COMPLETED
            and plan.completed_at is None
            and "completed_at" not in update_data
        ):
            plan.completed_at = datetime.now(UTC)

    if "priority" in update_data:
        plan.priority = update_data["priority"]

    if "started_at" in update_data:
        plan.started_at = update_data["started_at"]

    if "target_completion_at" in update_data:
        plan.target_completion_at = update_data["target_completion_at"]

    if "completed_at" in update_data:
        plan.completed_at = update_data["completed_at"]

    await session.commit()
    await session.refresh(plan)
    return RecoveryPlanRead.model_validate(plan)
