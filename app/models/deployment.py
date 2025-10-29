from __future__ import annotations

import uuid
from datetime import datetime
from enum import Enum as PyEnum
from typing import TYPE_CHECKING

from sqlalchemy import Enum as SQLEnum, ForeignKey, String, Text
from sqlalchemy.orm import Mapped, mapped_column, relationship

from app.models.base import Base, UTCDateTime, utc_now

if TYPE_CHECKING:  # pragma: no cover
    from app.models.deployment_report import DeploymentReport
    from app.models.incident import Incident
    from app.models.relief_delivery import ReliefDelivery
    from app.models.resource_request import ResourceRequest
    from app.models.tenant import Tenant
    from app.models.user import User


class IncidentDeploymentStatus(str, PyEnum):  # type: ignore[misc]
    """Lifecycle stages for incident deployments."""

    PREPARING = "preparing"
    EN_ROUTE = "en_route"
    ON_SCENE = "on_scene"
    COMPLETED = "completed"
    CANCELLED = "cancelled"


class IncidentDeployment(Base):
    """Represents a field team or resource deployment responding to an incident."""

    __tablename__ = "incident_deployment"

    id: Mapped[uuid.UUID] = mapped_column(primary_key=True, default=uuid.uuid4)
    name: Mapped[str] = mapped_column(String(200), nullable=False)
    status: Mapped[IncidentDeploymentStatus] = mapped_column(
        SQLEnum(IncidentDeploymentStatus), default=IncidentDeploymentStatus.PREPARING, nullable=False
    )
    contact_info: Mapped[str | None] = mapped_column(String(200), nullable=True)
    notes: Mapped[str | None] = mapped_column(Text, nullable=True)
    created_at: Mapped[datetime] = mapped_column(
        UTCDateTime(), default=utc_now, nullable=False
    )
    eta: Mapped[datetime | None] = mapped_column(UTCDateTime(), nullable=True)
    arrived_at: Mapped[datetime | None] = mapped_column(UTCDateTime(), nullable=True)
    demobilized_at: Mapped[datetime | None] = mapped_column(
        UTCDateTime(), nullable=True
    )

    incident_id: Mapped[uuid.UUID] = mapped_column(
        ForeignKey("incident.id", ondelete="CASCADE"), index=True, nullable=False
    )
    incident: Mapped["Incident"] = relationship(back_populates="deployments")

    tenant_id: Mapped[uuid.UUID] = mapped_column(
        ForeignKey("tenant.id", ondelete="CASCADE"), index=True, nullable=False
    )
    tenant: Mapped["Tenant"] = relationship(back_populates="deployments")

    leader_id: Mapped[uuid.UUID | None] = mapped_column(
        ForeignKey("user.id", ondelete="SET NULL"), index=True, nullable=True
    )
    leader: Mapped["User | None"] = relationship(back_populates="deployments")

    resource_request_id: Mapped[uuid.UUID | None] = mapped_column(
        ForeignKey("resource_request.id", ondelete="SET NULL"), index=True, nullable=True
    )
    resource_request: Mapped["ResourceRequest | None"] = relationship(back_populates="deployments")
    reports: Mapped[list["DeploymentReport"]] = relationship(
        back_populates="deployment", cascade="all, delete-orphan"
    )
    relief_deliveries: Mapped[list["ReliefDelivery"]] = relationship(
        back_populates="deployment", cascade="all, delete-orphan"
    )
