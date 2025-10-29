from __future__ import annotations

import uuid

from sqlalchemy import Boolean, ForeignKey, String
from sqlalchemy.orm import Mapped, mapped_column, relationship

from app.models.base import Base


class User(Base):
    """System user with tenant scoped access."""

    __tablename__ = "user"

    id: Mapped[uuid.UUID] = mapped_column(primary_key=True, default=uuid.uuid4)
    email: Mapped[str] = mapped_column(String(320), unique=True, index=True, nullable=False)
    full_name: Mapped[str | None] = mapped_column(String(200), nullable=True)
    hashed_password: Mapped[str] = mapped_column(String(1024), nullable=False)
    is_active: Mapped[bool] = mapped_column(Boolean, default=True)
    is_superuser: Mapped[bool] = mapped_column(Boolean, default=False)
    role: Mapped[str] = mapped_column(String(100), default="responder")
    mfa_secret: Mapped[str | None] = mapped_column(String(64), nullable=True)

    tenant_id: Mapped[uuid.UUID] = mapped_column(ForeignKey("tenant.id", ondelete="CASCADE"))
    tenant: Mapped["Tenant"] = relationship(back_populates="users")

    incidents: Mapped[list["Incident"]] = relationship(back_populates="reporter")
    tasks: Mapped[list["Task"]] = relationship(back_populates="assignee")
    incident_updates: Mapped[list["IncidentUpdate"]] = relationship(
        back_populates="author"
    )
    resource_requests: Mapped[list["ResourceRequest"]] = relationship(
        back_populates="requested_by"
    )
    deployments: Mapped[list["IncidentDeployment"]] = relationship(
        back_populates="leader"
    )
    deployment_reports: Mapped[list["DeploymentReport"]] = relationship(
        back_populates="author"
    )
    relief_deliveries: Mapped[list["ReliefDelivery"]] = relationship(
        back_populates="handled_by"
    )
    recovery_plans: Mapped[list["RecoveryPlan"]] = relationship(
        back_populates="owner"
    )
