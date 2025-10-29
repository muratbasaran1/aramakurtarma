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
    from app.models.resource_request import ResourceRequest
    from app.models.tenant import Tenant
    from app.models.user import User


class ReliefDeliveryStatus(str, PyEnum):  # type: ignore[misc]
    """Represents the lifecycle of relief deliveries."""

    PLANNED = "planned"
    IN_TRANSIT = "in_transit"
    DELIVERED = "delivered"
    VERIFIED = "verified"
    CANCELLED = "cancelled"


class ReliefDelivery(Base):
    """Records the transfer of relief supplies tied to an incident."""

    __tablename__ = "relief_delivery"

    id: Mapped[uuid.UUID] = mapped_column(primary_key=True, default=uuid.uuid4)
    title: Mapped[str] = mapped_column(String(200), nullable=False)
    status: Mapped[ReliefDeliveryStatus] = mapped_column(
        SQLEnum(ReliefDeliveryStatus), default=ReliefDeliveryStatus.PLANNED, nullable=False
    )
    destination: Mapped[str | None] = mapped_column(String(200), nullable=True)
    items_description: Mapped[str | None] = mapped_column(Text, nullable=True)
    quantity_delivered: Mapped[int] = mapped_column(Integer, nullable=False, default=0)
    created_at: Mapped[datetime] = mapped_column(
        UTCDateTime(), default=utc_now, nullable=False
    )
    updated_at: Mapped[datetime] = mapped_column(
        UTCDateTime(), default=utc_now, onupdate=utc_now, nullable=False
    )
    dispatched_at: Mapped[datetime | None] = mapped_column(UTCDateTime(), nullable=True)
    delivered_at: Mapped[datetime | None] = mapped_column(UTCDateTime(), nullable=True)
    verified_at: Mapped[datetime | None] = mapped_column(UTCDateTime(), nullable=True)

    incident_id: Mapped[uuid.UUID] = mapped_column(
        ForeignKey("incident.id", ondelete="CASCADE"), index=True, nullable=False
    )
    incident: Mapped["Incident"] = relationship(back_populates="relief_deliveries")

    tenant_id: Mapped[uuid.UUID] = mapped_column(
        ForeignKey("tenant.id", ondelete="CASCADE"), index=True, nullable=False
    )
    tenant: Mapped["Tenant"] = relationship(back_populates="relief_deliveries")

    resource_request_id: Mapped[uuid.UUID | None] = mapped_column(
        ForeignKey("resource_request.id", ondelete="SET NULL"), index=True, nullable=True
    )
    resource_request: Mapped["ResourceRequest | None"] = relationship(back_populates="relief_deliveries")

    deployment_id: Mapped[uuid.UUID | None] = mapped_column(
        ForeignKey("incident_deployment.id", ondelete="SET NULL"), index=True, nullable=True
    )
    deployment: Mapped["IncidentDeployment | None"] = relationship(back_populates="relief_deliveries")

    handled_by_id: Mapped[uuid.UUID | None] = mapped_column(
        ForeignKey("user.id", ondelete="SET NULL"), index=True, nullable=True
    )
    handled_by: Mapped["User | None"] = relationship(back_populates="relief_deliveries")
