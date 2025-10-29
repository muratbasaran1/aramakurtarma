from __future__ import annotations

from __future__ import annotations

import uuid

from fastapi import APIRouter, Depends, HTTPException, Query, status
from sqlalchemy import select
from sqlalchemy.ext.asyncio import AsyncSession

from app.api import deps
from app.models import (
    Incident,
    ResourceRequest,
    ResourceRequestStatus,
    Tenant,
    User,
)
from app.schemas.resource_request import (
    ResourceRequestCreate,
    ResourceRequestRead,
    ResourceRequestUpdate,
)


router = APIRouter(prefix="/resource-requests", tags=["resource-requests"])


@router.post(
    "/",
    response_model=ResourceRequestRead,
    status_code=status.HTTP_201_CREATED,
    summary="Create resource request",
)
async def create_resource_request(
    request_in: ResourceRequestCreate,
    tenant: Tenant = Depends(deps.ensure_tenant_access),
    current_user: User = Depends(deps.get_current_active_user),
    session: AsyncSession = Depends(deps.get_db_session),
) -> ResourceRequestRead:
    """Create a new resource request scoped to an incident within the tenant."""

    incident = await session.get(Incident, request_in.incident_id)
    if incident is None or incident.tenant_id != tenant.id:
        raise HTTPException(status_code=status.HTTP_404_NOT_FOUND, detail="Incident not found")

    request = ResourceRequest(
        summary=request_in.summary,
        details=request_in.details,
        quantity=request_in.quantity,
        status=request_in.status,
        incident_id=incident.id,
        tenant_id=tenant.id,
        requested_by_id=current_user.id,
    )
    session.add(request)
    await session.commit()
    await session.refresh(request)
    return ResourceRequestRead.model_validate(request)


@router.get("/", response_model=list[ResourceRequestRead], summary="List resource requests")
async def list_resource_requests(
    tenant: Tenant = Depends(deps.ensure_tenant_access),
    _: User = Depends(deps.get_current_active_user),
    session: AsyncSession = Depends(deps.get_db_session),
    incident_id: uuid.UUID | None = Query(default=None, description="Filter by incident ID"),
    status_filter: ResourceRequestStatus | None = Query(
        default=None, alias="status", description="Filter by request status"
    ),
) -> list[ResourceRequestRead]:
    """List resource requests within the tenant, optionally filtered by incident or status."""

    stmt = select(ResourceRequest).where(ResourceRequest.tenant_id == tenant.id)
    if incident_id is not None:
        stmt = stmt.where(ResourceRequest.incident_id == incident_id)
    if status_filter is not None:
        stmt = stmt.where(ResourceRequest.status == status_filter)

    result = await session.execute(stmt.order_by(ResourceRequest.created_at.desc()))
    requests = result.scalars().all()
    return [ResourceRequestRead.model_validate(req) for req in requests]


@router.get(
    "/{request_id}",
    response_model=ResourceRequestRead,
    summary="Get resource request",
)
async def read_resource_request(
    request_id: uuid.UUID,
    tenant: Tenant = Depends(deps.ensure_tenant_access),
    _: User = Depends(deps.get_current_active_user),
    session: AsyncSession = Depends(deps.get_db_session),
) -> ResourceRequestRead:
    """Retrieve a resource request if it belongs to the tenant."""

    result = await session.execute(
        select(ResourceRequest).where(
            ResourceRequest.id == request_id, ResourceRequest.tenant_id == tenant.id
        )
    )
    request = result.scalar_one_or_none()
    if request is None:
        raise HTTPException(status_code=status.HTTP_404_NOT_FOUND, detail="Resource request not found")
    return ResourceRequestRead.model_validate(request)


@router.patch(
    "/{request_id}",
    response_model=ResourceRequestRead,
    summary="Update resource request",
)
async def update_resource_request(
    request_id: uuid.UUID,
    request_in: ResourceRequestUpdate,
    tenant: Tenant = Depends(deps.ensure_tenant_access),
    _: User = Depends(deps.get_current_active_user),
    session: AsyncSession = Depends(deps.get_db_session),
) -> ResourceRequestRead:
    """Update mutable fields of a resource request such as quantity or status."""

    result = await session.execute(
        select(ResourceRequest).where(
            ResourceRequest.id == request_id, ResourceRequest.tenant_id == tenant.id
        )
    )
    request = result.scalar_one_or_none()
    if request is None:
        raise HTTPException(status_code=status.HTTP_404_NOT_FOUND, detail="Resource request not found")

    update_data = request_in.model_dump(exclude_unset=True)

    if "summary" in update_data:
        new_summary = update_data["summary"]
        if new_summary is None:
            raise HTTPException(status_code=status.HTTP_400_BAD_REQUEST, detail="Summary cannot be empty")
        request.summary = new_summary

    if "details" in update_data:
        request.details = update_data["details"]

    if "quantity" in update_data:
        request.quantity = update_data["quantity"]

    if "status" in update_data:
        request.status = update_data["status"]

    await session.commit()
    await session.refresh(request)
    return ResourceRequestRead.model_validate(request)
