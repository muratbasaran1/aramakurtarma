from __future__ import annotations

import uuid

from fastapi import APIRouter, Depends, HTTPException, Query, status
from sqlalchemy import select
from sqlalchemy.ext.asyncio import AsyncSession

from app.api import deps
from app.models import (
    Incident,
    IncidentDeployment,
    IncidentDeploymentStatus,
    ResourceRequest,
    Tenant,
    User,
)
from app.schemas.deployment import (
    IncidentDeploymentCreate,
    IncidentDeploymentRead,
    IncidentDeploymentUpdate,
)


router = APIRouter(prefix="/deployments", tags=["deployments"])


@router.post(
    "/",
    response_model=IncidentDeploymentRead,
    status_code=status.HTTP_201_CREATED,
    summary="Create incident deployment",
)
async def create_deployment(
    deployment_in: IncidentDeploymentCreate,
    tenant: Tenant = Depends(deps.ensure_tenant_access),
    _: User = Depends(deps.get_current_active_user),
    session: AsyncSession = Depends(deps.get_db_session),
) -> IncidentDeploymentRead:
    """Register a new deployment responding to an incident."""

    incident = await session.get(Incident, deployment_in.incident_id)
    if incident is None or incident.tenant_id != tenant.id:
        raise HTTPException(status_code=status.HTTP_404_NOT_FOUND, detail="Incident not found")

    leader_id = deployment_in.leader_id
    if leader_id is not None:
        leader = await session.get(User, leader_id)
        if leader is None or leader.tenant_id != tenant.id:
            raise HTTPException(status_code=status.HTTP_404_NOT_FOUND, detail="Leader not found")

    resource_request_id = deployment_in.resource_request_id
    if resource_request_id is not None:
        resource_request = await session.get(ResourceRequest, resource_request_id)
        if (
            resource_request is None
            or resource_request.tenant_id != tenant.id
            or resource_request.incident_id != incident.id
        ):
            raise HTTPException(
                status_code=status.HTTP_404_NOT_FOUND,
                detail="Resource request not found",
            )

    deployment = IncidentDeployment(
        name=deployment_in.name,
        status=deployment_in.status,
        contact_info=deployment_in.contact_info,
        notes=deployment_in.notes,
        eta=deployment_in.eta,
        incident_id=incident.id,
        tenant_id=tenant.id,
        leader_id=leader_id,
        resource_request_id=resource_request_id,
    )
    session.add(deployment)
    await session.commit()
    await session.refresh(deployment)
    return IncidentDeploymentRead.model_validate(deployment)


@router.get("/", response_model=list[IncidentDeploymentRead], summary="List deployments")
async def list_deployments(
    tenant: Tenant = Depends(deps.ensure_tenant_access),
    _: User = Depends(deps.get_current_active_user),
    session: AsyncSession = Depends(deps.get_db_session),
    incident_id: uuid.UUID | None = Query(default=None, description="Filter by incident ID"),
    status_filter: IncidentDeploymentStatus | None = Query(
        default=None, alias="status", description="Filter by deployment status"
    ),
) -> list[IncidentDeploymentRead]:
    """List deployments within the tenant, optionally filtered by incident or status."""

    stmt = select(IncidentDeployment).where(IncidentDeployment.tenant_id == tenant.id)
    if incident_id is not None:
        stmt = stmt.where(IncidentDeployment.incident_id == incident_id)
    if status_filter is not None:
        stmt = stmt.where(IncidentDeployment.status == status_filter)

    result = await session.execute(stmt.order_by(IncidentDeployment.created_at.desc()))
    deployments = result.scalars().all()
    return [IncidentDeploymentRead.model_validate(deployment) for deployment in deployments]


@router.get("/{deployment_id}", response_model=IncidentDeploymentRead, summary="Get deployment")
async def read_deployment(
    deployment_id: uuid.UUID,
    tenant: Tenant = Depends(deps.ensure_tenant_access),
    _: User = Depends(deps.get_current_active_user),
    session: AsyncSession = Depends(deps.get_db_session),
) -> IncidentDeploymentRead:
    """Retrieve deployment details if they belong to the tenant."""

    result = await session.execute(
        select(IncidentDeployment).where(
            IncidentDeployment.id == deployment_id,
            IncidentDeployment.tenant_id == tenant.id,
        )
    )
    deployment = result.scalar_one_or_none()
    if deployment is None:
        raise HTTPException(status_code=status.HTTP_404_NOT_FOUND, detail="Deployment not found")
    return IncidentDeploymentRead.model_validate(deployment)


@router.patch("/{deployment_id}", response_model=IncidentDeploymentRead, summary="Update deployment")
async def update_deployment(
    deployment_id: uuid.UUID,
    deployment_in: IncidentDeploymentUpdate,
    tenant: Tenant = Depends(deps.ensure_tenant_access),
    _: User = Depends(deps.get_current_active_user),
    session: AsyncSession = Depends(deps.get_db_session),
) -> IncidentDeploymentRead:
    """Update mutable fields of a deployment such as status, contact, or timing."""

    result = await session.execute(
        select(IncidentDeployment).where(
            IncidentDeployment.id == deployment_id,
            IncidentDeployment.tenant_id == tenant.id,
        )
    )
    deployment = result.scalar_one_or_none()
    if deployment is None:
        raise HTTPException(status_code=status.HTTP_404_NOT_FOUND, detail="Deployment not found")

    update_data = deployment_in.model_dump(exclude_unset=True)

    if "name" in update_data:
        new_name = update_data["name"]
        if new_name is None:
            raise HTTPException(status_code=status.HTTP_400_BAD_REQUEST, detail="Name cannot be empty")
        deployment.name = new_name

    if "status" in update_data:
        deployment.status = update_data["status"]

    if "contact_info" in update_data:
        deployment.contact_info = update_data["contact_info"]

    if "notes" in update_data:
        deployment.notes = update_data["notes"]

    if "eta" in update_data:
        deployment.eta = update_data["eta"]

    if "arrived_at" in update_data:
        deployment.arrived_at = update_data["arrived_at"]

    if "demobilized_at" in update_data:
        deployment.demobilized_at = update_data["demobilized_at"]

    if "leader_id" in update_data:
        leader_id = update_data["leader_id"]
        if leader_id is not None:
            leader = await session.get(User, leader_id)
            if leader is None or leader.tenant_id != tenant.id:
                raise HTTPException(status_code=status.HTTP_404_NOT_FOUND, detail="Leader not found")
        deployment.leader_id = leader_id

    if "resource_request_id" in update_data:
        resource_request_id = update_data["resource_request_id"]
        if resource_request_id is not None:
            resource_request = await session.get(ResourceRequest, resource_request_id)
            if (
                resource_request is None
                or resource_request.tenant_id != tenant.id
                or resource_request.incident_id != deployment.incident_id
            ):
                raise HTTPException(
                    status_code=status.HTTP_404_NOT_FOUND, detail="Resource request not found"
                )
        deployment.resource_request_id = resource_request_id

    await session.commit()
    await session.refresh(deployment)
    return IncidentDeploymentRead.model_validate(deployment)
