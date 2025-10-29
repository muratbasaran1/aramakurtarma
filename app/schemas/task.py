import uuid
from datetime import datetime

from pydantic import BaseModel, Field, ConfigDict

from app.models.task import TaskStatus


class TaskBase(BaseModel):
    """Common task fields."""

    title: str = Field(max_length=200)
    description: str | None = None
    status: TaskStatus = TaskStatus.PENDING
    due_at: datetime | None = None


class TaskCreate(TaskBase):
    """Payload for creating tasks."""

    incident_id: uuid.UUID
    assignee_id: uuid.UUID | None = None


class TaskUpdate(BaseModel):
    """Payload for updating tasks."""

    title: str | None = Field(default=None, max_length=200)
    description: str | None = None
    status: TaskStatus | None = None
    due_at: datetime | None = None
    assignee_id: uuid.UUID | None = Field(default=None)


class TaskRead(TaskBase):
    """Task representation returned to clients."""

    id: uuid.UUID
    incident_id: uuid.UUID
    assignee_id: uuid.UUID | None = None

    model_config = ConfigDict(from_attributes=True)
