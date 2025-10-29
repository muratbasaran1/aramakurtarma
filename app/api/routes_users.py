from fastapi import APIRouter, Depends, HTTPException, status
from sqlalchemy import select
from sqlalchemy.exc import IntegrityError
from sqlalchemy.ext.asyncio import AsyncSession

from app.api import deps
from app.core.security import get_password_hash
from app.models import Tenant, User
from app.schemas.user import UserCreate, UserRead

router = APIRouter(prefix="/users", tags=["users"])


@router.post("/", response_model=UserRead, status_code=status.HTTP_201_CREATED, summary="Create user")
async def create_user(
    user_in: UserCreate,
    _: User = Depends(deps.get_current_active_superuser),
    session: AsyncSession = Depends(deps.get_db_session),
) -> UserRead:
    """Create a new system user."""

    tenant = await session.get(Tenant, user_in.tenant_id)
    if tenant is None:
        raise HTTPException(status_code=status.HTTP_404_NOT_FOUND, detail="Tenant not found")

    user = User(
        email=user_in.email,
        full_name=user_in.full_name,
        role=user_in.role,
        hashed_password=get_password_hash(user_in.password),
        tenant_id=user_in.tenant_id,
        is_active=user_in.is_active,
        is_superuser=user_in.is_superuser,
    )
    session.add(user)
    try:
        await session.commit()
    except IntegrityError as exc:
        await session.rollback()
        raise HTTPException(status_code=status.HTTP_400_BAD_REQUEST, detail="User already exists") from exc
    await session.refresh(user)
    return UserRead.model_validate(user)


@router.get("/me", response_model=UserRead, summary="Current user profile")
async def read_current_user(current_user: User = Depends(deps.get_current_active_user)) -> UserRead:
    """Return details about the authenticated user."""

    return UserRead.model_validate(current_user)


@router.get("/", response_model=list[UserRead], summary="List users for tenant")
async def list_users(
    tenant: Tenant = Depends(deps.ensure_tenant_access),
    _: User = Depends(deps.get_current_active_user),
    session: AsyncSession = Depends(deps.get_db_session),
) -> list[UserRead]:
    """List users scoped to the tenant provided via header."""

    result = await session.execute(select(User).where(User.tenant_id == tenant.id))
    users = result.scalars().all()
    return [UserRead.model_validate(user) for user in users]
