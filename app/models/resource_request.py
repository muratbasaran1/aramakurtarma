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
    from app.models.relief_delivery import ReliefDelivery
    from app.models.tenant import Tenant
    from app.models.user import User


class ResourceRequestStatus(str, PyEnum):  # type: ignore[misc]
    """Lifecycle states for requested emergency resources."""

    PENDING = "pending"
    APPROVED = "approved"
    DISPATCHED = "dispatched"
    FULFILLED = "fulfilled"
    CANCELLED = "cancelled"


class ResourceRequest(Base):
    """Represents a supply or support request raised within an incident."""

    __tablename__ = "resource_request"

    id: Mapped[uuid.UUID] = mapped_column(primary_key=True, default=uuid.uuid4)
    summary: Mapped[str] = mapped_column(String(200), nullable=False)
    details: Mapped[str | None] = mapped_column(Text, nullable=True)
    quantity: Mapped[int] = mapped_column(Integer, nullable=False, default=1)
    status: Mapped[ResourceRequestStatus] = mapped_column(
        SQLEnum(ResourceRequestStatus), default=ResourceRequestStatus.PENDING, nullable=False
    )
    created_at: Mapped[datetime] = mapped_column(
        UTCDateTime(), default=utc_now, nullable=False
    )
    updated_at: Mapped[datetime] = mapped_column(
        UTCDateTime(), default=utc_now, onupdate=utc_now, nullable=False
    )

    incident_id: Mapped[uuid.UUID] = mapped_column(
        ForeignKey("incident.id", ondelete="CASCADE"), index=True, nullable=False
    )
    incident: Mapped["Incident"] = relationship(back_populates="resource_requests")

    tenant_id: Mapped[uuid.UUID] = mapped_column(
        ForeignKey("tenant.id", ondelete="CASCADE"), index=True, nullable=False
    )
    tenant: Mapped["Tenant"] = relationship(back_populates="resource_requests")

    requested_by_id: Mapped[uuid.UUID | None] = mapped_column(
        ForeignKey("user.id", ondelete="SET NULL"), index=True, nullable=True
    )
    requested_by: Mapped["User | None"] = relationship(back_populates="resource_requests")
    deployments: Mapped[list["IncidentDeployment"]] = relationship(
        back_populates="resource_request"
    )
    relief_deliveries: Mapped[list["ReliefDelivery"]] = relationship(
        back_populates="resource_request",
        cascade="all, delete-orphan",
    )
