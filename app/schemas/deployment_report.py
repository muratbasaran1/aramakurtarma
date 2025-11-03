from __future__ import annotations

import uuid
from datetime import datetime

from pydantic import BaseModel, ConfigDict, Field

from app.models.deployment_report import DeploymentReportStatus


class DeploymentReportCreate(BaseModel):
    """Payload for logging a deployment situation report."""

    title: str = Field(max_length=200)
    summary: str
    status: DeploymentReportStatus = DeploymentReportStatus.DRAFT
    incident_id: uuid.UUID
    deployment_id: uuid.UUID | None = None
    submitted_at: datetime | None = None
    follow_up_actions: str | None = None
    personnel_count: int | None = Field(default=None, ge=0)


class DeploymentReportUpdate(BaseModel):
    """Payload for updating an existing deployment report."""

    title: str | None = Field(default=None, max_length=200)
    summary: str | None = None
    status: DeploymentReportStatus | None = None
    deployment_id: uuid.UUID | None = None
    submitted_at: datetime | None = None
    follow_up_actions: str | None = None
    personnel_count: int | None = Field(default=None, ge=0)


class DeploymentReportRead(BaseModel):
    """API representation of a deployment situation report."""

    id: uuid.UUID
    title: str
    summary: str
    status: DeploymentReportStatus
    incident_id: uuid.UUID
    deployment_id: uuid.UUID | None
    tenant_id: uuid.UUID
    author_id: uuid.UUID | None
    created_at: datetime
    updated_at: datetime
    submitted_at: datetime | None
    follow_up_actions: str | None
    personnel_count: int | None

    model_config = ConfigDict(from_attributes=True)
