import uuid

from pydantic import BaseModel, ConfigDict, Field


class TenantBase(BaseModel):
    """Common fields shared by tenant operations."""

    name: str = Field(max_length=200)
    slug: str = Field(max_length=100, pattern="^[a-z0-9-]+$")


class TenantCreate(TenantBase):
    """Payload used to register a new tenant."""

    pass


class TenantRead(TenantBase):
    """Tenant representation returned to clients."""

    id: uuid.UUID

    model_config = ConfigDict(from_attributes=True)


class TenantReadinessSnapshot(BaseModel):
    """Readiness metrics for a specific tenant."""

    tenant_id: uuid.UUID
    tenant_name: str
    tenant_slug: str
    total_incidents: int
    ready_incidents: int
    not_ready_incidents: int
    average_completion_ratio: float


class TenantReadinessOverview(BaseModel):
    """Aggregated readiness overview across all tenants."""

    total_tenants: int
    ready_tenants: int
    not_ready_tenants: int
    total_incidents: int
    ready_incidents: int
    not_ready_incidents: int
    average_completion_ratio: float
    tenants: list[TenantReadinessSnapshot]
