from contextlib import asynccontextmanager

from fastapi import FastAPI

from app.api import (
    routes_auth,
    routes_deployment_reports,
    routes_deployments,
    routes_health,
    routes_incidents,
    routes_operations,
    routes_recovery_plans,
    routes_relief_deliveries,
    routes_resource_requests,
    routes_tasks,
    routes_tenants,
    routes_users,
)
from app.core.config import get_settings
from app.core.database import AsyncSessionLocal, engine
from app.models import Base
from app.services.bootstrap import ensure_bootstrap_superuser


@asynccontextmanager
async def lifespan(_: FastAPI):
    """Initialize database schema and bootstrap accounts."""

    async with engine.begin() as conn:
        await conn.run_sync(Base.metadata.create_all)
    async with AsyncSessionLocal() as session:
        await ensure_bootstrap_superuser(session)
    yield


settings = get_settings()
app = FastAPI(title="TUDAK Afet Yönetim Sistemi", version="0.1.0", lifespan=lifespan)


app.include_router(routes_health.router)
app.include_router(routes_auth.router, prefix=settings.api_v1_prefix)
app.include_router(routes_tenants.router, prefix=settings.api_v1_prefix)
app.include_router(routes_users.router, prefix=settings.api_v1_prefix)
app.include_router(routes_incidents.router, prefix=settings.api_v1_prefix)
app.include_router(routes_resource_requests.router, prefix=settings.api_v1_prefix)
app.include_router(routes_tasks.router, prefix=settings.api_v1_prefix)
app.include_router(routes_deployments.router, prefix=settings.api_v1_prefix)
app.include_router(routes_deployment_reports.router, prefix=settings.api_v1_prefix)
app.include_router(routes_relief_deliveries.router, prefix=settings.api_v1_prefix)
app.include_router(routes_recovery_plans.router, prefix=settings.api_v1_prefix)
app.include_router(routes_operations.router, prefix=settings.api_v1_prefix)
