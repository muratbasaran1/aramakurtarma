from __future__ import annotations

import uuid

from sqlalchemy import String
from sqlalchemy.orm import Mapped, mapped_column, relationship

from app.models.base import Base


class Tenant(Base):
    """Represents an isolated tenant (province or agency)."""

    __tablename__ = "tenant"

    id: Mapped[uuid.UUID] = mapped_column(primary_key=True, default=uuid.uuid4)
    name: Mapped[str] = mapped_column(String(200), unique=True, nullable=False)
    slug: Mapped[str] = mapped_column(String(100), unique=True, nullable=False, index=True)

    users: Mapped[list["User"]] = relationship(back_populates="tenant", cascade="all, delete-orphan")
    incidents: Mapped[list["Incident"]] = relationship(back_populates="tenant", cascade="all, delete-orphan")
    resource_requests: Mapped[list["ResourceRequest"]] = relationship(
        back_populates="tenant", cascade="all, delete-orphan"
    )
    deployments: Mapped[list["IncidentDeployment"]] = relationship(
        back_populates="tenant", cascade="all, delete-orphan"
    )
    deployment_reports: Mapped[list["DeploymentReport"]] = relationship(
        back_populates="tenant", cascade="all, delete-orphan"
    )
    relief_deliveries: Mapped[list["ReliefDelivery"]] = relationship(
        back_populates="tenant", cascade="all, delete-orphan"
    )
    recovery_plans: Mapped[list["RecoveryPlan"]] = relationship(
        back_populates="tenant", cascade="all, delete-orphan"
    )
