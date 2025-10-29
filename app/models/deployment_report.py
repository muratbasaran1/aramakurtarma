from __future__ import annotations

import uuid
from datetime import datetime
from enum import Enum as PyEnum
from typing import TYPE_CHECKING

from sqlalchemy import Enum as SQLEnum, ForeignKey, Integer, String, Text
from sqlalchemy.orm import Mapped, mapped_column, relationship

from app.models.base import Base, UTCDateTime, utc_now

if TYPE_CHECKING:  # pragma: no cover
    from app.models.deployment import IncidentDeployment
    from app.models.incident import Incident
    from app.models.tenant import Tenant
    from app.models.user import User


class DeploymentReportStatus(str, PyEnum):  # type: ignore[misc]
    """Lifecycle states for reports submitted by field deployments."""

    DRAFT = "draft"
    SUBMITTED = "submitted"
    APPROVED = "approved"
    REQUIRES_FOLLOW_UP = "requires_follow_up"


class DeploymentReport(Base):
    """Structured field intelligence captured during or after a deployment."""

    __tablename__ = "deployment_report"

    id: Mapped[uuid.UUID] = mapped_column(primary_key=True, default=uuid.uuid4)
    title: Mapped[str] = mapped_column(String(200), nullable=False)
    summary: Mapped[str] = mapped_column(Text, nullable=False)
    status: Mapped[DeploymentReportStatus] = mapped_column(
        SQLEnum(DeploymentReportStatus), default=DeploymentReportStatus.DRAFT, nullable=False
    )
    created_at: Mapped[datetime] = mapped_column(
        UTCDateTime(), default=utc_now, nullable=False
    )
    submitted_at: Mapped[datetime | None] = mapped_column(UTCDateTime(), nullable=True)
    follow_up_actions: Mapped[str | None] = mapped_column(Text, nullable=True)
    personnel_count: Mapped[int | None] = mapped_column(Integer, nullable=True)

    incident_id: Mapped[uuid.UUID] = mapped_column(
        ForeignKey("incident.id", ondelete="CASCADE"), index=True, nullable=False
    )
    incident: Mapped["Incident"] = relationship(back_populates="deployment_reports")

    deployment_id: Mapped[uuid.UUID | None] = mapped_column(
        ForeignKey("incident_deployment.id", ondelete="SET NULL"), index=True, nullable=True
    )
    deployment: Mapped["IncidentDeployment | None"] = relationship(back_populates="reports")

    tenant_id: Mapped[uuid.UUID] = mapped_column(
        ForeignKey("tenant.id", ondelete="CASCADE"), index=True, nullable=False
    )
    tenant: Mapped["Tenant"] = relationship(back_populates="deployment_reports")

    author_id: Mapped[uuid.UUID | None] = mapped_column(
        ForeignKey("user.id", ondelete="SET NULL"), index=True, nullable=True
    )
    author: Mapped["User | None"] = relationship(back_populates="deployment_reports")
