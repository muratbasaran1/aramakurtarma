import uuid
from datetime import datetime

from pydantic import BaseModel, ConfigDict, Field

from app.models.deployment import IncidentDeploymentStatus


class IncidentDeploymentCreate(BaseModel):
    """Payload for registering a field deployment."""

    name: str = Field(max_length=200)
    status: IncidentDeploymentStatus = IncidentDeploymentStatus.PREPARING
    contact_info: str | None = Field(default=None, max_length=200)
    notes: str | None = None
    eta: datetime | None = None
    incident_id: uuid.UUID
    resource_request_id: uuid.UUID | None = None
    leader_id: uuid.UUID | None = None


class IncidentDeploymentUpdate(BaseModel):
    """Payload for updating deployment progress."""

    name: str | None = Field(default=None, max_length=200)
    status: IncidentDeploymentStatus | None = None
    contact_info: str | None = Field(default=None, max_length=200)
    notes: str | None = None
    eta: datetime | None = None
    arrived_at: datetime | None = None
    demobilized_at: datetime | None = None
    resource_request_id: uuid.UUID | None = None
    leader_id: uuid.UUID | None = None


class IncidentDeploymentRead(BaseModel):
    """API representation of an incident deployment."""

    id: uuid.UUID
    name: str
    status: IncidentDeploymentStatus
    contact_info: str | None
    notes: str | None
    created_at: datetime
    eta: datetime | None
    arrived_at: datetime | None
    demobilized_at: datetime | None
    incident_id: uuid.UUID
    tenant_id: uuid.UUID
    leader_id: uuid.UUID | None
    resource_request_id: uuid.UUID | None

    model_config = ConfigDict(from_attributes=True)
