"""Models for post-incident recovery and rehabilitation planning."""

from __future__ import annotations

import uuid
from datetime import datetime
from enum import Enum as PyEnum
from typing import TYPE_CHECKING

from sqlalchemy import DateTime, Enum as SQLEnum, ForeignKey, String, Text
from sqlalchemy.orm import Mapped, mapped_column, relationship

from app.models.base import Base

if TYPE_CHECKING:  # pragma: no cover
    from app.models.incident import Incident
    from app.models.tenant import Tenant
    from app.models.user import User


class RecoveryPlanStatus(str, PyEnum):  # type: ignore[misc]
    """Lifecycle states for recovery plans."""

    DRAFT = "draft"
    IN_PROGRESS = "in_progress"
    COMPLETED = "completed"
    CANCELLED = "cancelled"


class RecoveryPlan(Base):
    """Represents a structured post-incident recovery initiative."""

    __tablename__ = "recovery_plan"

    id: Mapped[uuid.UUID] = mapped_column(primary_key=True, default=uuid.uuid4)
    title: Mapped[str] = mapped_column(String(200), nullable=False)
    description: Mapped[str | None] = mapped_column(Text, nullable=True)
    status: Mapped[RecoveryPlanStatus] = mapped_column(
        SQLEnum(RecoveryPlanStatus), default=RecoveryPlanStatus.DRAFT, nullable=False
    )
    priority: Mapped[str | None] = mapped_column(String(50), nullable=True)
    started_at: Mapped[datetime | None] = mapped_column(DateTime, nullable=True)
    target_completion_at: Mapped[datetime | None] = mapped_column(DateTime, nullable=True)
    completed_at: Mapped[datetime | None] = mapped_column(DateTime, nullable=True)
    created_at: Mapped[datetime] = mapped_column(DateTime, default=datetime.utcnow, nullable=False)

    incident_id: Mapped[uuid.UUID] = mapped_column(
        ForeignKey("incident.id", ondelete="CASCADE"), index=True, nullable=False
    )
    incident: Mapped["Incident"] = relationship(back_populates="recovery_plans")

    tenant_id: Mapped[uuid.UUID] = mapped_column(
        ForeignKey("tenant.id", ondelete="CASCADE"), index=True, nullable=False
    )
    tenant: Mapped["Tenant"] = relationship(back_populates="recovery_plans")

    owner_id: Mapped[uuid.UUID | None] = mapped_column(
        ForeignKey("user.id", ondelete="SET NULL"), index=True, nullable=True
    )
    owner: Mapped["User | None"] = relationship(back_populates="recovery_plans")
