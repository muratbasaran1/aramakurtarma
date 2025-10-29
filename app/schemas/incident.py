import uuid
from datetime import datetime

from pydantic import BaseModel, Field, ConfigDict

from app.models.incident import IncidentStatus, SeverityLevel


class IncidentBase(BaseModel):
    """Shared fields for incident operations."""

    title: str = Field(max_length=200)
    description: str | None = None
    status: IncidentStatus = IncidentStatus.NEW
    severity: SeverityLevel = SeverityLevel.MEDIUM
    location: str | None = Field(default=None, max_length=200)


class IncidentCreate(IncidentBase):
    """Payload used when creating a new incident."""

    pass


class IncidentRead(IncidentBase):
    """Incident representation returned to API consumers."""

    id: uuid.UUID
    tenant_id: uuid.UUID
    reporter_id: uuid.UUID | None = None
    reported_at: datetime

    model_config = ConfigDict(from_attributes=True)
