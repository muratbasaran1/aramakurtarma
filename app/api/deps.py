import uuid

from fastapi import Depends, Header, HTTPException, status
from fastapi.security import OAuth2PasswordBearer
from sqlalchemy import select
from sqlalchemy.ext.asyncio import AsyncSession

from app.core.config import get_settings
from app.core.database import get_session
from app.core.security import verify_access_token
from app.models import Tenant, User


settings = get_settings()
oauth2_scheme = OAuth2PasswordBearer(tokenUrl=f"{settings.api_v1_prefix}/auth/token")


async def get_db_session(session: AsyncSession = Depends(get_session)) -> AsyncSession:
    """Expose the database session as a dependency."""

    return session


async def get_current_user(
    token: str = Depends(oauth2_scheme),
    session: AsyncSession = Depends(get_db_session),
) -> User:
    """Validate the user from the provided access token."""

    try:
        user_id = uuid.UUID(verify_access_token(token))
    except (ValueError, TypeError) as exc:
        raise HTTPException(status_code=status.HTTP_401_UNAUTHORIZED, detail="Invalid credentials") from exc

    result = await session.execute(select(User).where(User.id == user_id))
    user = result.scalar_one_or_none()
    if not user or not user.is_active:
        raise HTTPException(status_code=status.HTTP_401_UNAUTHORIZED, detail="Inactive user")
    return user


async def get_current_active_user(current_user: User = Depends(get_current_user)) -> User:
    """Ensure the current user account is active."""

    if not current_user.is_active:
        raise HTTPException(status_code=status.HTTP_400_BAD_REQUEST, detail="Inactive user")
    return current_user


async def get_current_active_superuser(current_user: User = Depends(get_current_active_user)) -> User:
    """Ensure the user holds superuser privileges."""

    if not current_user.is_superuser:
        raise HTTPException(status_code=status.HTTP_403_FORBIDDEN, detail="Insufficient privileges")
    return current_user


async def get_tenant(
    tenant_slug: str | None = Header(default=None, alias="X-Tenant-ID"),
    session: AsyncSession = Depends(get_db_session),
) -> Tenant:
    """Resolve and return tenant based on request header."""

    if tenant_slug is None:
        raise HTTPException(status_code=status.HTTP_400_BAD_REQUEST, detail="Tenant header missing")

    tenant_result = await session.execute(select(Tenant).where(Tenant.slug == tenant_slug))
    tenant = tenant_result.scalar_one_or_none()
    if tenant is None:
        raise HTTPException(status_code=status.HTTP_404_NOT_FOUND, detail="Tenant not found")
    return tenant


async def ensure_tenant_access(
    tenant: Tenant = Depends(get_tenant),
    current_user: User = Depends(get_current_active_user),
) -> Tenant:
    """Verify that the authenticated user belongs to the tenant in scope."""

    if current_user.tenant_id != tenant.id and not current_user.is_superuser:
        raise HTTPException(status_code=status.HTTP_403_FORBIDDEN, detail="Tenant access denied")
    return tenant
