import uuid
from datetime import datetime

from pydantic import BaseModel, ConfigDict, Field

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


class IncidentProgressStep(BaseModel):
    """Represents a single milestone for incident progression."""

    key: str
    title: str
    completed: bool
    detail: str | None = None


class IncidentProgressRead(BaseModel):
    """Aggregated progress information for an incident."""

    incident_id: uuid.UUID
    total_steps: int
    completed_steps: int
    completion_ratio: float
    steps: list[IncidentProgressStep]


class IncidentReadinessRead(BaseModel):
    """High-level readiness summary indicating remaining work for activation."""

    incident_id: uuid.UUID
    current_stage_key: str
    current_stage_title: str
    current_stage_detail: str | None = None
    total_steps: int
    completed_steps: int
    remaining_steps: int
    completion_ratio: float
    ready: bool
    summary: str
    steps: list[IncidentProgressStep]
    pending_steps: list[IncidentProgressStep]


class IncidentReadinessEntry(IncidentReadinessRead):
    """Readiness details for a single incident with additional context."""

    incident_title: str
    incident_status: IncidentStatus


class IncidentReadinessOverview(BaseModel):
    """Aggregated readiness metrics across all incidents in a tenant."""

    tenant_id: uuid.UUID
    total_incidents: int
    ready_incidents: int
    not_ready_incidents: int
    average_completion_ratio: float
    incidents: list[IncidentReadinessEntry]
