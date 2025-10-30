from sqlalchemy import select
from sqlalchemy.exc import IntegrityError
from sqlalchemy.ext.asyncio import AsyncSession

from app.core.config import get_settings
from app.core.security import get_password_hash
from app.models import Tenant, User


async def ensure_bootstrap_superuser(session: AsyncSession) -> None:
    """Create an initial tenant and superuser if configured via settings."""

    settings = get_settings()
    if not settings.first_superuser_email or not settings.first_superuser_password:
        return

    tenant_result = await session.execute(
        select(Tenant).where(Tenant.slug == settings.bootstrap_tenant_slug)
    )
    tenant = tenant_result.scalar_one_or_none()
    if tenant is None:
        tenant = Tenant(name=settings.bootstrap_tenant_name, slug=settings.bootstrap_tenant_slug)
        session.add(tenant)
        try:
            await session.commit()
        except IntegrityError:
            await session.rollback()
            tenant_result = await session.execute(
                select(Tenant).where(Tenant.slug == settings.bootstrap_tenant_slug)
            )
            tenant = tenant_result.scalar_one()

    user_result = await session.execute(select(User).where(User.email == settings.first_superuser_email))
    user = user_result.scalar_one_or_none()
    if user is None:
        user = User(
            email=settings.first_superuser_email,
            hashed_password=get_password_hash(settings.first_superuser_password),
            full_name="Sistem Yöneticisi",
            role="commander",
            is_superuser=True,
            tenant_id=tenant.id,
        )
        session.add(user)
        await session.commit()
    else:
        if not user.is_superuser:
            user.is_superuser = True
            user.tenant_id = tenant.id
            await session.commit()
