from __future__ import annotations

import uuid
from datetime import datetime
from enum import Enum as PyEnum

from sqlalchemy import DateTime, Enum as SQLEnum, ForeignKey, String, Text
from sqlalchemy.orm import Mapped, mapped_column, relationship

from app.models.base import Base


class IncidentStatus(str, PyEnum):  # type: ignore[misc]
    """Possible states for incident lifecycle."""

    NEW = "new"
    TRIAGED = "triaged"
    IN_PROGRESS = "in_progress"
    RESOLVED = "resolved"
    CLOSED = "closed"


class SeverityLevel(str, PyEnum):  # type: ignore[misc]
    """Severity classification for incidents."""

    LOW = "low"
    MEDIUM = "medium"
    HIGH = "high"
    CRITICAL = "critical"


class Incident(Base):
    """Incident reported within a tenant."""

    __tablename__ = "incident"

    id: Mapped[uuid.UUID] = mapped_column(primary_key=True, default=uuid.uuid4)
    title: Mapped[str] = mapped_column(String(200), nullable=False)
    description: Mapped[str | None] = mapped_column(Text, nullable=True)
    status: Mapped[IncidentStatus] = mapped_column(
        SQLEnum(IncidentStatus), default=IncidentStatus.NEW, nullable=False
    )
    severity: Mapped[SeverityLevel] = mapped_column(
        SQLEnum(SeverityLevel), default=SeverityLevel.MEDIUM, nullable=False
    )
    location: Mapped[str | None] = mapped_column(String(200), nullable=True)
    reported_at: Mapped[datetime] = mapped_column(DateTime, default=datetime.utcnow, nullable=False)

    tenant_id: Mapped[uuid.UUID] = mapped_column(ForeignKey("tenant.id", ondelete="CASCADE"))
    tenant: Mapped["Tenant"] = relationship(back_populates="incidents")

    reporter_id: Mapped[uuid.UUID | None] = mapped_column(ForeignKey("user.id", ondelete="SET NULL"))
    reporter: Mapped[User | None] = relationship(back_populates="incidents")

    tasks: Mapped[list["Task"]] = relationship(
        back_populates="incident", cascade="all, delete-orphan"
    )
    updates: Mapped[list["IncidentUpdate"]] = relationship(
        back_populates="incident", cascade="all, delete-orphan"
    )
    resource_requests: Mapped[list["ResourceRequest"]] = relationship(
        back_populates="incident", cascade="all, delete-orphan"
    )
    deployments: Mapped[list["IncidentDeployment"]] = relationship(
        back_populates="incident", cascade="all, delete-orphan"
    )
    deployment_reports: Mapped[list["DeploymentReport"]] = relationship(
        back_populates="incident", cascade="all, delete-orphan"
    )
    relief_deliveries: Mapped[list["ReliefDelivery"]] = relationship(
        back_populates="incident", cascade="all, delete-orphan"
    )
    recovery_plans: Mapped[list["RecoveryPlan"]] = relationship(
        back_populates="incident", cascade="all, delete-orphan"
    )
