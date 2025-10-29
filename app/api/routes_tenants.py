from fastapi import APIRouter, Depends, HTTPException, status
from sqlalchemy import select
from sqlalchemy.exc import IntegrityError
from sqlalchemy.ext.asyncio import AsyncSession

from app.api.deps import get_current_active_superuser, get_db_session
from app.models import Tenant
from app.schemas.tenant import TenantCreate, TenantRead

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
