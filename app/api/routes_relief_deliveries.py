from __future__ import annotations

import uuid

from fastapi import APIRouter, Depends, HTTPException, Query, status
from sqlalchemy import select
from sqlalchemy.ext.asyncio import AsyncSession

from app.api import deps
from app.models import (
    Incident,
    IncidentDeployment,
    ReliefDelivery,
    ReliefDeliveryStatus,
    ResourceRequest,
    Tenant,
    User,
)
from app.schemas.relief_delivery import (
    ReliefDeliveryCreate,
    ReliefDeliveryRead,
    ReliefDeliveryUpdate,
)


router = APIRouter(prefix="/relief-deliveries", tags=["relief-deliveries"])


async def _validate_related_entities(
    *,
    session: AsyncSession,
    tenant: Tenant,
    incident_id: uuid.UUID,
    resource_request_id: uuid.UUID | None,
    deployment_id: uuid.UUID | None,
    handled_by_id: uuid.UUID | None,
) -> None:
    """Ensure related entities belong to the same tenant and incident."""

    incident = await session.get(Incident, incident_id)
    if incident is None or incident.tenant_id != tenant.id:
        raise HTTPException(status_code=status.HTTP_404_NOT_FOUND, detail="Incident not found")

    if resource_request_id is not None:
        resource_request = await session.get(ResourceRequest, resource_request_id)
        if (
            resource_request is None
            or resource_request.tenant_id != tenant.id
            or resource_request.incident_id != incident.id
        ):
            raise HTTPException(status_code=status.HTTP_404_NOT_FOUND, detail="Resource request not found")

    if deployment_id is not None:
        deployment = await session.get(IncidentDeployment, deployment_id)
        if (
            deployment is None
            or deployment.tenant_id != tenant.id
            or deployment.incident_id != incident.id
        ):
            raise HTTPException(status_code=status.HTTP_404_NOT_FOUND, detail="Deployment not found")

    if handled_by_id is not None:
        handler = await session.get(User, handled_by_id)
        if handler is None or handler.tenant_id != tenant.id:
            raise HTTPException(status_code=status.HTTP_404_NOT_FOUND, detail="Handler not found")


@router.post(
    "/",
    response_model=ReliefDeliveryRead,
    status_code=status.HTTP_201_CREATED,
    summary="Create relief delivery",
)
async def create_relief_delivery(
    delivery_in: ReliefDeliveryCreate,
    tenant: Tenant = Depends(deps.ensure_tenant_access),
    _: User = Depends(deps.get_current_active_user),
    session: AsyncSession = Depends(deps.get_db_session),
) -> ReliefDeliveryRead:
    """Register a new relief delivery for an incident."""

    await _validate_related_entities(
        session=session,
        tenant=tenant,
        incident_id=delivery_in.incident_id,
        resource_request_id=delivery_in.resource_request_id,
        deployment_id=delivery_in.deployment_id,
        handled_by_id=delivery_in.handled_by_id,
    )

    delivery = ReliefDelivery(
        title=delivery_in.title,
        status=delivery_in.status,
        destination=delivery_in.destination,
        items_description=delivery_in.items_description,
        quantity_delivered=delivery_in.quantity_delivered,
        dispatched_at=delivery_in.dispatched_at,
        delivered_at=delivery_in.delivered_at,
        verified_at=delivery_in.verified_at,
        incident_id=delivery_in.incident_id,
        tenant_id=tenant.id,
        resource_request_id=delivery_in.resource_request_id,
        deployment_id=delivery_in.deployment_id,
        handled_by_id=delivery_in.handled_by_id,
    )
    session.add(delivery)
    await session.commit()
    await session.refresh(delivery)
    return ReliefDeliveryRead.model_validate(delivery)


@router.get(
    "/",
    response_model=list[ReliefDeliveryRead],
    summary="List relief deliveries",
)
async def list_relief_deliveries(
    tenant: Tenant = Depends(deps.ensure_tenant_access),
    _: User = Depends(deps.get_current_active_user),
    session: AsyncSession = Depends(deps.get_db_session),
    incident_id: uuid.UUID | None = Query(default=None, description="Filter by incident ID"),
    resource_request_id: uuid.UUID | None = Query(
        default=None, description="Filter by resource request"
    ),
    deployment_id: uuid.UUID | None = Query(default=None, description="Filter by deployment"),
    status_filter: ReliefDeliveryStatus | None = Query(
        default=None, alias="status", description="Filter by delivery status"
    ),
) -> list[ReliefDeliveryRead]:
    """List deliveries within the tenant scope."""

    stmt = select(ReliefDelivery).join(Incident).where(Incident.tenant_id == tenant.id)
    if incident_id is not None:
        stmt = stmt.where(ReliefDelivery.incident_id == incident_id)
    if resource_request_id is not None:
        stmt = stmt.where(ReliefDelivery.resource_request_id == resource_request_id)
    if deployment_id is not None:
        stmt = stmt.where(ReliefDelivery.deployment_id == deployment_id)
    if status_filter is not None:
        stmt = stmt.where(ReliefDelivery.status == status_filter)

    result = await session.execute(stmt.order_by(ReliefDelivery.created_at.desc()))
    deliveries = result.scalars().all()
    return [ReliefDeliveryRead.model_validate(delivery) for delivery in deliveries]


@router.get(
    "/{delivery_id}",
    response_model=ReliefDeliveryRead,
    summary="Get relief delivery",
)
async def read_relief_delivery(
    delivery_id: uuid.UUID,
    tenant: Tenant = Depends(deps.ensure_tenant_access),
    _: User = Depends(deps.get_current_active_user),
    session: AsyncSession = Depends(deps.get_db_session),
) -> ReliefDeliveryRead:
    """Retrieve a relief delivery if it belongs to the tenant."""

    result = await session.execute(
        select(ReliefDelivery)
        .join(Incident)
        .where(ReliefDelivery.id == delivery_id, Incident.tenant_id == tenant.id)
    )
    delivery = result.scalar_one_or_none()
    if delivery is None:
        raise HTTPException(status_code=status.HTTP_404_NOT_FOUND, detail="Relief delivery not found")
    return ReliefDeliveryRead.model_validate(delivery)


@router.patch(
    "/{delivery_id}",
    response_model=ReliefDeliveryRead,
    summary="Update relief delivery",
)
async def update_relief_delivery(
    delivery_id: uuid.UUID,
    delivery_in: ReliefDeliveryUpdate,
    tenant: Tenant = Depends(deps.ensure_tenant_access),
    _: User = Depends(deps.get_current_active_user),
    session: AsyncSession = Depends(deps.get_db_session),
) -> ReliefDeliveryRead:
    """Update mutable details of a relief delivery."""

    result = await session.execute(
        select(ReliefDelivery)
        .join(Incident)
        .where(ReliefDelivery.id == delivery_id, Incident.tenant_id == tenant.id)
    )
    delivery = result.scalar_one_or_none()
    if delivery is None:
        raise HTTPException(status_code=status.HTTP_404_NOT_FOUND, detail="Relief delivery not found")

    update_data = delivery_in.model_dump(exclude_unset=True)

    if any(
        key in update_data
        for key in ("resource_request_id", "deployment_id", "handled_by_id", "incident_id")
    ):
        await _validate_related_entities(
            session=session,
            tenant=tenant,
            incident_id=delivery.incident_id,
            resource_request_id=update_data.get("resource_request_id", delivery.resource_request_id),
            deployment_id=update_data.get("deployment_id", delivery.deployment_id),
            handled_by_id=update_data.get("handled_by_id", delivery.handled_by_id),
        )

    if "title" in update_data:
        new_title = update_data["title"]
        if new_title is None:
            raise HTTPException(status_code=status.HTTP_400_BAD_REQUEST, detail="Title cannot be empty")
        delivery.title = new_title

    if "status" in update_data:
        delivery.status = update_data["status"]

    if "destination" in update_data:
        delivery.destination = update_data["destination"]

    if "items_description" in update_data:
        delivery.items_description = update_data["items_description"]

    if "quantity_delivered" in update_data:
        delivery.quantity_delivered = update_data["quantity_delivered"]

    if "dispatched_at" in update_data:
        delivery.dispatched_at = update_data["dispatched_at"]

    if "delivered_at" in update_data:
        delivery.delivered_at = update_data["delivered_at"]

    if "verified_at" in update_data:
        delivery.verified_at = update_data["verified_at"]

    if "resource_request_id" in update_data:
        delivery.resource_request_id = update_data["resource_request_id"]

    if "deployment_id" in update_data:
        delivery.deployment_id = update_data["deployment_id"]

    if "handled_by_id" in update_data:
        delivery.handled_by_id = update_data["handled_by_id"]

    await session.commit()
    await session.refresh(delivery)
    return ReliefDeliveryRead.model_validate(delivery)
