import uuid
from datetime import datetime

from pydantic import BaseModel, ConfigDict, Field

from app.models.resource_request import ResourceRequestStatus


class ResourceRequestCreate(BaseModel):
    """Payload for raising a resource request."""

    summary: str = Field(max_length=200)
    details: str | None = None
    quantity: int = Field(default=1, ge=1)
    status: ResourceRequestStatus = ResourceRequestStatus.PENDING
    incident_id: uuid.UUID


class ResourceRequestUpdate(BaseModel):
    """Payload for updating an existing resource request."""

    summary: str | None = Field(default=None, max_length=200)
    details: str | None = None
    quantity: int | None = Field(default=None, ge=1)
    status: ResourceRequestStatus | None = None


class ResourceRequestRead(BaseModel):
    """Representation of a resource request exposed to API consumers."""

    id: uuid.UUID
    summary: str
    details: str | None
    quantity: int
    status: ResourceRequestStatus
    created_at: datetime
    incident_id: uuid.UUID
    tenant_id: uuid.UUID
    requested_by_id: uuid.UUID | None

    model_config = ConfigDict(from_attributes=True)
