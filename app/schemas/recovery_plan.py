"""Pydantic schemas for recovery plan operations."""

import uuid
from datetime import datetime

from pydantic import BaseModel, ConfigDict, Field

from app.models.recovery_plan import RecoveryPlanStatus


class RecoveryPlanCreate(BaseModel):
    """Payload used when recording a recovery plan."""

    title: str = Field(max_length=200)
    description: str | None = None
    status: RecoveryPlanStatus = RecoveryPlanStatus.DRAFT
    priority: str | None = Field(default=None, max_length=50)
    started_at: datetime | None = None
    target_completion_at: datetime | None = None
    incident_id: uuid.UUID


class RecoveryPlanUpdate(BaseModel):
    """Mutable fields for an existing recovery plan."""

    title: str | None = Field(default=None, max_length=200)
    description: str | None = None
    status: RecoveryPlanStatus | None = None
    priority: str | None = Field(default=None, max_length=50)
    started_at: datetime | None = None
    target_completion_at: datetime | None = None
    completed_at: datetime | None = None


class RecoveryPlanRead(BaseModel):
    """API representation of a recovery plan."""

    id: uuid.UUID
    title: str
    description: str | None
    status: RecoveryPlanStatus
    priority: str | None
    started_at: datetime | None
    target_completion_at: datetime | None
    completed_at: datetime | None
    created_at: datetime
    incident_id: uuid.UUID
    tenant_id: uuid.UUID
    owner_id: uuid.UUID | None

    model_config = ConfigDict(from_attributes=True)
