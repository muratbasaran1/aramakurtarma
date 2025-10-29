import uuid

from pydantic import BaseModel, Field, ConfigDict


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
