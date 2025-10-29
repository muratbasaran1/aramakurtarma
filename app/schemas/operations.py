from __future__ import annotations

import uuid
from datetime import datetime

from pydantic import BaseModel, Field


class OperationsCategorySummary(BaseModel):
    """Aggregated counts for a single operational category."""

    total: int = Field(ge=0)
    by_status: dict[str, int]


class TenantOperationsSummary(BaseModel):
    """Operational metrics for a specific tenant."""

    tenant_id: uuid.UUID
    tenant_name: str
    tenant_slug: str
    incidents: OperationsCategorySummary
    resource_requests: OperationsCategorySummary
    deployments: OperationsCategorySummary
    deployment_reports: OperationsCategorySummary
    relief_deliveries: OperationsCategorySummary
    recovery_plans: OperationsCategorySummary
    tasks: OperationsCategorySummary


class GlobalOperationsSummary(BaseModel):
    """Global operational snapshot across all tenants."""

    total_tenants: int = Field(ge=0)
    incidents: OperationsCategorySummary
    resource_requests: OperationsCategorySummary
    deployments: OperationsCategorySummary
    deployment_reports: OperationsCategorySummary
    relief_deliveries: OperationsCategorySummary
    recovery_plans: OperationsCategorySummary
    tasks: OperationsCategorySummary
    tenants: list[TenantOperationsSummary]


class OperationsBacklogItem(BaseModel):
    """Outstanding workload information for a specific category."""

    category: str
    open_statuses: list[str]
    pending: int = Field(ge=0)
    oldest_item_id: uuid.UUID | None = None
    oldest_item_title: str | None = None
    oldest_pending_since: datetime | None = None


class GlobalOperationsBacklogCategory(BaseModel):
    """Aggregated backlog perspective across tenants for a category."""

    category: str
    open_statuses: list[str]
    pending: int = Field(ge=0)
    tenant_count: int = Field(ge=0)
    worst_tenant_id: uuid.UUID | None = None
    worst_tenant_name: str | None = None
    worst_tenant_slug: str | None = None
    worst_tenant_pending: int | None = None
    oldest_item_id: uuid.UUID | None = None
    oldest_item_title: str | None = None
    oldest_pending_since: datetime | None = None


class TenantOperationsBacklog(BaseModel):
    """Backlog snapshot for an individual tenant."""

    tenant_id: uuid.UUID
    tenant_name: str
    tenant_slug: str
    total_pending: int = Field(ge=0)
    items: list[OperationsBacklogItem]


class GlobalOperationsBacklog(BaseModel):
    """Global backlog overview across all tenants."""

    total_pending: int = Field(ge=0)
    categories: list[GlobalOperationsBacklogCategory]
    tenants: list[TenantOperationsBacklog]
