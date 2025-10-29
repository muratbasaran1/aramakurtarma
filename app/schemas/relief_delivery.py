import uuid
from datetime import datetime

from pydantic import BaseModel, ConfigDict, Field

from app.models.relief_delivery import ReliefDeliveryStatus


class ReliefDeliveryCreate(BaseModel):
    """Payload for registering a relief delivery."""

    title: str = Field(max_length=200)
    status: ReliefDeliveryStatus = ReliefDeliveryStatus.PLANNED
    destination: str | None = Field(default=None, max_length=200)
    items_description: str | None = None
    quantity_delivered: int = Field(default=0, ge=0)
    dispatched_at: datetime | None = None
    delivered_at: datetime | None = None
    verified_at: datetime | None = None
    incident_id: uuid.UUID
    resource_request_id: uuid.UUID | None = None
    deployment_id: uuid.UUID | None = None
    handled_by_id: uuid.UUID | None = None


class ReliefDeliveryUpdate(BaseModel):
    """Payload for updating relief delivery progress."""

    title: str | None = Field(default=None, max_length=200)
    status: ReliefDeliveryStatus | None = None
    destination: str | None = Field(default=None, max_length=200)
    items_description: str | None = None
    quantity_delivered: int | None = Field(default=None, ge=0)
    dispatched_at: datetime | None = None
    delivered_at: datetime | None = None
    verified_at: datetime | None = None
    resource_request_id: uuid.UUID | None = None
    deployment_id: uuid.UUID | None = None
    handled_by_id: uuid.UUID | None = None


class ReliefDeliveryRead(BaseModel):
    """API representation of a relief delivery."""

    id: uuid.UUID
    title: str
    status: ReliefDeliveryStatus
    destination: str | None
    items_description: str | None
    quantity_delivered: int
    created_at: datetime
    updated_at: datetime
    dispatched_at: datetime | None
    delivered_at: datetime | None
    verified_at: datetime | None
    incident_id: uuid.UUID
    tenant_id: uuid.UUID
    resource_request_id: uuid.UUID | None
    deployment_id: uuid.UUID | None
    handled_by_id: uuid.UUID | None

    model_config = ConfigDict(from_attributes=True)
