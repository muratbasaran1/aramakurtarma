from fastapi import APIRouter, Depends
from sqlalchemy.ext.asyncio import AsyncSession

from app.api import deps
from app.models import Tenant, User
from app.schemas.operations import (
    GlobalOperationsBacklog,
    GlobalOperationsSummary,
    TenantOperationsBacklog,
    TenantOperationsSummary,
)
from app.services.operations import (
    compute_global_operations_backlog,
    compute_global_operations_summary,
    compute_tenant_operations_backlog,
    compute_tenant_operations_summary,
)

router = APIRouter(tags=["operations"])


@router.get(
    "/operations/summary",
    response_model=TenantOperationsSummary,
    summary="Get tenant operations summary",
)
async def read_tenant_operations_summary(
    tenant: Tenant = Depends(deps.ensure_tenant_access),
    _: User = Depends(deps.get_current_active_user),
    session: AsyncSession = Depends(deps.get_db_session),
) -> TenantOperationsSummary:
    """Summarise operational workload for the tenant in scope."""

    return await compute_tenant_operations_summary(session, tenant=tenant)


@router.get(
    "/operations/backlog",
    response_model=TenantOperationsBacklog,
    summary="Get tenant backlog overview",
)
async def read_tenant_operations_backlog(
    tenant: Tenant = Depends(deps.ensure_tenant_access),
    _: User = Depends(deps.get_current_active_user),
    session: AsyncSession = Depends(deps.get_db_session),
) -> TenantOperationsBacklog:
    """Expose outstanding operational workload for the tenant."""

    return await compute_tenant_operations_backlog(session, tenant=tenant)


@router.get(
    "/operations/overview",
    response_model=GlobalOperationsSummary,
    summary="Get global operations overview",
)
async def read_global_operations_overview(
    _: None = Depends(deps.get_current_active_superuser),
    session: AsyncSession = Depends(deps.get_db_session),
) -> GlobalOperationsSummary:
    """Provide an aggregated overview of operations across all tenants."""

    return await compute_global_operations_summary(session)


@router.get(
    "/operations/backlog/overview",
    response_model=GlobalOperationsBacklog,
    summary="Get global backlog overview",
)
async def read_global_operations_backlog(
    _: None = Depends(deps.get_current_active_superuser),
    session: AsyncSession = Depends(deps.get_db_session),
) -> GlobalOperationsBacklog:
    """Provide backlog visibility across all tenants."""

    return await compute_global_operations_backlog(session)
