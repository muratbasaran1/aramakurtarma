import uuid
from datetime import datetime

from pydantic import BaseModel, Field, ConfigDict

from app.models.incident import IncidentStatus


class IncidentUpdateBase(BaseModel):
    """Shared attributes across incident update operations."""

    message: str = Field(min_length=1, max_length=2000)
    status: IncidentStatus | None = None


class IncidentUpdateCreate(IncidentUpdateBase):
    """Payload to append a new update to an incident."""

    pass


class IncidentUpdateRead(IncidentUpdateBase):
    """Serialized incident update returned to clients."""

    id: uuid.UUID
    incident_id: uuid.UUID
    author_id: uuid.UUID | None = None
    created_at: datetime

    model_config = ConfigDict(from_attributes=True)
