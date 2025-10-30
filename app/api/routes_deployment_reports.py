from __future__ import annotations

import uuid

from fastapi import APIRouter, Depends, HTTPException, Query, status
from sqlalchemy import select
from sqlalchemy.ext.asyncio import AsyncSession

from app.api import deps
from app.models import (
    DeploymentReport,
    DeploymentReportStatus,
    Incident,
    IncidentDeployment,
    Tenant,
    User,
)
from app.schemas.deployment_report import (
    DeploymentReportCreate,
    DeploymentReportRead,
    DeploymentReportUpdate,
)


router = APIRouter(prefix="/deployment-reports", tags=["deployment-reports"])


@router.post(
    "/",
    response_model=DeploymentReportRead,
    status_code=status.HTTP_201_CREATED,
    summary="Create deployment report",
)
async def create_deployment_report(
    report_in: DeploymentReportCreate,
    tenant: Tenant = Depends(deps.ensure_tenant_access),
    current_user: User = Depends(deps.get_current_active_user),
    session: AsyncSession = Depends(deps.get_db_session),
) -> DeploymentReportRead:
    """Record a structured report coming from a field deployment."""

    incident = await session.get(Incident, report_in.incident_id)
    if incident is None or incident.tenant_id != tenant.id:
        raise HTTPException(status_code=status.HTTP_404_NOT_FOUND, detail="Incident not found")

    deployment_id = report_in.deployment_id
    if deployment_id is not None:
        deployment = await session.get(IncidentDeployment, deployment_id)
        if (
            deployment is None
            or deployment.tenant_id != tenant.id
            or deployment.incident_id != incident.id
        ):
            raise HTTPException(status_code=status.HTTP_404_NOT_FOUND, detail="Deployment not found")

    report = DeploymentReport(
        title=report_in.title,
        summary=report_in.summary,
        status=report_in.status,
        incident_id=incident.id,
        deployment_id=deployment_id,
        tenant_id=tenant.id,
        author_id=current_user.id,
        submitted_at=report_in.submitted_at,
        follow_up_actions=report_in.follow_up_actions,
        personnel_count=report_in.personnel_count,
    )
    session.add(report)
    await session.commit()
    await session.refresh(report)
    return DeploymentReportRead.model_validate(report)


@router.get(
    "/",
    response_model=list[DeploymentReportRead],
    summary="List deployment reports",
)
async def list_deployment_reports(
    tenant: Tenant = Depends(deps.ensure_tenant_access),
    _: User = Depends(deps.get_current_active_user),
    session: AsyncSession = Depends(deps.get_db_session),
    incident_id: uuid.UUID | None = Query(default=None, description="Filter by incident ID"),
    deployment_id: uuid.UUID | None = Query(
        default=None, description="Filter by specific deployment"
    ),
    status_filter: DeploymentReportStatus | None = Query(
        default=None, alias="status", description="Filter by report status"
    ),
) -> list[DeploymentReportRead]:
    """List deployment reports within the tenant, optionally filtered by incident, deployment, or status."""

    stmt = select(DeploymentReport).where(DeploymentReport.tenant_id == tenant.id)
    if incident_id is not None:
        stmt = stmt.where(DeploymentReport.incident_id == incident_id)
    if deployment_id is not None:
        stmt = stmt.where(DeploymentReport.deployment_id == deployment_id)
    if status_filter is not None:
        stmt = stmt.where(DeploymentReport.status == status_filter)

    result = await session.execute(stmt.order_by(DeploymentReport.created_at.desc()))
    reports = result.scalars().all()
    return [DeploymentReportRead.model_validate(report) for report in reports]


@router.get(
    "/{report_id}",
    response_model=DeploymentReportRead,
    summary="Get deployment report",
)
async def read_deployment_report(
    report_id: uuid.UUID,
    tenant: Tenant = Depends(deps.ensure_tenant_access),
    _: User = Depends(deps.get_current_active_user),
    session: AsyncSession = Depends(deps.get_db_session),
) -> DeploymentReportRead:
    """Retrieve a deployment report if it belongs to the tenant."""

    result = await session.execute(
        select(DeploymentReport).where(
            DeploymentReport.id == report_id,
            DeploymentReport.tenant_id == tenant.id,
        )
    )
    report = result.scalar_one_or_none()
    if report is None:
        raise HTTPException(status_code=status.HTTP_404_NOT_FOUND, detail="Deployment report not found")
    return DeploymentReportRead.model_validate(report)


@router.patch(
    "/{report_id}",
    response_model=DeploymentReportRead,
    summary="Update deployment report",
)
async def update_deployment_report(
    report_id: uuid.UUID,
    report_in: DeploymentReportUpdate,
    tenant: Tenant = Depends(deps.ensure_tenant_access),
    _: User = Depends(deps.get_current_active_user),
    session: AsyncSession = Depends(deps.get_db_session),
) -> DeploymentReportRead:
    """Update mutable fields of a deployment report."""

    result = await session.execute(
        select(DeploymentReport).where(
            DeploymentReport.id == report_id,
            DeploymentReport.tenant_id == tenant.id,
        )
    )
    report = result.scalar_one_or_none()
    if report is None:
        raise HTTPException(status_code=status.HTTP_404_NOT_FOUND, detail="Deployment report not found")

    update_data = report_in.model_dump(exclude_unset=True)

    if "title" in update_data:
        new_title = update_data["title"]
        if new_title is None:
            raise HTTPException(status_code=status.HTTP_400_BAD_REQUEST, detail="Title cannot be empty")
        report.title = new_title

    if "summary" in update_data:
        new_summary = update_data["summary"]
        if new_summary is None:
            raise HTTPException(status_code=status.HTTP_400_BAD_REQUEST, detail="Summary cannot be empty")
        report.summary = new_summary

    if "status" in update_data:
        report.status = update_data["status"]

    if "submitted_at" in update_data:
        report.submitted_at = update_data["submitted_at"]

    if "follow_up_actions" in update_data:
        report.follow_up_actions = update_data["follow_up_actions"]

    if "personnel_count" in update_data:
        report.personnel_count = update_data["personnel_count"]

    if "deployment_id" in update_data:
        new_deployment_id = update_data["deployment_id"]
        if new_deployment_id is not None:
            deployment = await session.get(IncidentDeployment, new_deployment_id)
            if (
                deployment is None
                or deployment.tenant_id != tenant.id
                or deployment.incident_id != report.incident_id
            ):
                raise HTTPException(status_code=status.HTTP_404_NOT_FOUND, detail="Deployment not found")
        report.deployment_id = new_deployment_id

    await session.commit()
    await session.refresh(report)
    return DeploymentReportRead.model_validate(report)
