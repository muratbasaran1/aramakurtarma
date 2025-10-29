from __future__ import annotations

import uuid
from datetime import datetime

from sqlalchemy import DateTime, Enum as SQLEnum, ForeignKey, Text
from sqlalchemy.orm import Mapped, mapped_column, relationship

from app.models.base import Base
from app.models.incident import Incident, IncidentStatus
from app.models.user import User


class IncidentUpdate(Base):
    """Chronological update shared for an incident."""

    __tablename__ = "incident_update"

    id: Mapped[uuid.UUID] = mapped_column(primary_key=True, default=uuid.uuid4)
    incident_id: Mapped[uuid.UUID] = mapped_column(
        ForeignKey("incident.id", ondelete="CASCADE"), index=True
    )
    incident: Mapped[Incident] = relationship(back_populates="updates")

    author_id: Mapped[uuid.UUID | None] = mapped_column(
        ForeignKey("user.id", ondelete="SET NULL"), index=True
    )
    author: Mapped[User | None] = relationship(back_populates="incident_updates")

    message: Mapped[str] = mapped_column(Text, nullable=False)
    status: Mapped[IncidentStatus | None] = mapped_column(
        SQLEnum(IncidentStatus), nullable=True
    )
    created_at: Mapped[datetime] = mapped_column(
        DateTime, default=datetime.utcnow, nullable=False
    )
