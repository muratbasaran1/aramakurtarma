from fastapi import APIRouter, Depends, HTTPException, status
from sqlalchemy import select
from sqlalchemy.exc import IntegrityError
from sqlalchemy.ext.asyncio import AsyncSession

from app.api.deps import get_current_active_superuser, get_db_session
from app.models import Tenant
from app.schemas.tenant import (
    TenantCreate,
    TenantRead,
    TenantReadinessOverview,
    TenantReadinessSnapshot,
)
from app.services.readiness import compute_tenant_readiness_overview

router = APIRouter(prefix="/tenants", tags=["tenants"])


@router.post("/", response_model=TenantRead, status_code=status.HTTP_201_CREATED, summary="Create tenant")
async def create_tenant(
    tenant_in: TenantCreate,
    _: None = Depends(get_current_active_superuser),
    session: AsyncSession = Depends(get_db_session),
) -> TenantRead:
    """Register a new tenant environment."""

    tenant = Tenant(name=tenant_in.name, slug=tenant_in.slug)
    session.add(tenant)
    try:
        await session.commit()
    except IntegrityError as exc:
        await session.rollback()
        raise HTTPException(status_code=status.HTTP_400_BAD_REQUEST, detail="Tenant already exists") from exc
    await session.refresh(tenant)
    return TenantRead.model_validate(tenant)


@router.get("/", response_model=list[TenantRead], summary="List tenants")
async def list_tenants(
    _: None = Depends(get_current_active_superuser),
    session: AsyncSession = Depends(get_db_session),
) -> list[TenantRead]:
    """Return all registered tenants."""

    result = await session.execute(select(Tenant))
    tenants = result.scalars().all()
    return [TenantRead.model_validate(item) for item in tenants]


@router.get(
    "/readiness/overview",
    response_model=TenantReadinessOverview,
    summary="Get readiness overview across tenants",
)
async def read_tenant_readiness_overview(
    _: None = Depends(get_current_active_superuser),
    session: AsyncSession = Depends(get_db_session),
) -> TenantReadinessOverview:
    """Provide a global readiness picture across all tenants and incidents."""

    result = await session.execute(select(Tenant).order_by(Tenant.name.asc()))
    tenants = result.scalars().all()

    snapshots: list[TenantReadinessSnapshot] = []
    total_incidents = 0
    ready_incidents = 0
    completion_sum = 0.0
    ready_tenants = 0

    for tenant in tenants:
        tenant_overview = await compute_tenant_readiness_overview(session, tenant=tenant)
        snapshots.append(
            TenantReadinessSnapshot(
                tenant_id=tenant.id,
                tenant_name=tenant.name,
                tenant_slug=tenant.slug,
                total_incidents=tenant_overview.total_incidents,
                ready_incidents=tenant_overview.ready_incidents,
                not_ready_incidents=tenant_overview.not_ready_incidents,
                average_completion_ratio=tenant_overview.average_completion_ratio,
            )
        )
        total_incidents += tenant_overview.total_incidents
        ready_incidents += tenant_overview.ready_incidents
        completion_sum += sum(entry.completion_ratio for entry in tenant_overview.incidents)
        if tenant_overview.not_ready_incidents == 0:
            ready_tenants += 1

    total_tenants = len(snapshots)
    not_ready_tenants = total_tenants - ready_tenants
    not_ready_incidents = total_incidents - ready_incidents
    average_completion_ratio = (
        completion_sum / total_incidents if total_incidents else 0.0
    )

    return TenantReadinessOverview(
        total_tenants=total_tenants,
        ready_tenants=ready_tenants,
        not_ready_tenants=not_ready_tenants,
        total_incidents=total_incidents,
        ready_incidents=ready_incidents,
        not_ready_incidents=not_ready_incidents,
        average_completion_ratio=average_completion_ratio,
        tenants=snapshots,
    )
