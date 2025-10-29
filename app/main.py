from fastapi import FastAPI

from app.api import routes_auth, routes_health, routes_incidents, routes_tenants, routes_users
from app.core.config import get_settings
from app.core.database import AsyncSessionLocal, engine
from app.models import Base
from app.services.bootstrap import ensure_bootstrap_superuser


settings = get_settings()
app = FastAPI(title="TUDAK Afet Yönetim Sistemi", version="0.1.0")


@app.on_event("startup")
async def on_startup() -> None:
    """Create database tables on application start."""

    async with engine.begin() as conn:
        await conn.run_sync(Base.metadata.create_all)
    async with AsyncSessionLocal() as session:
        await ensure_bootstrap_superuser(session)


app.include_router(routes_health.router)
app.include_router(routes_auth.router, prefix=settings.api_v1_prefix)
app.include_router(routes_tenants.router, prefix=settings.api_v1_prefix)
app.include_router(routes_users.router, prefix=settings.api_v1_prefix)
app.include_router(routes_incidents.router, prefix=settings.api_v1_prefix)
