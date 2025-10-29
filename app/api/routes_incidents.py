import uuid

from fastapi import APIRouter, Depends, HTTPException, status
from sqlalchemy import select
from sqlalchemy.ext.asyncio import AsyncSession

from app.api import deps
from app.models import Incident, Tenant, User
from app.schemas.incident import IncidentCreate, IncidentRead

router = APIRouter(prefix="/incidents", tags=["incidents"])


@router.post("/", response_model=IncidentRead, status_code=status.HTTP_201_CREATED, summary="Create incident")
async def create_incident(
    incident_in: IncidentCreate,
    tenant: Tenant = Depends(deps.ensure_tenant_access),
    reporter: User = Depends(deps.get_current_active_user),
    session: AsyncSession = Depends(deps.get_db_session),
) -> IncidentRead:
    """Create a new incident scoped to the tenant header."""

    incident = Incident(
        title=incident_in.title,
        description=incident_in.description,
        status=incident_in.status,
        severity=incident_in.severity,
        location=incident_in.location,
        tenant_id=tenant.id,
        reporter_id=reporter.id,
    )
    session.add(incident)
    await session.commit()
    await session.refresh(incident)
    return IncidentRead.model_validate(incident)


@router.get("/", response_model=list[IncidentRead], summary="List incidents")
async def list_incidents(
    tenant: Tenant = Depends(deps.ensure_tenant_access),
    _: User = Depends(deps.get_current_active_user),
    session: AsyncSession = Depends(deps.get_db_session),
) -> list[IncidentRead]:
    """List incidents associated with the tenant."""

    result = await session.execute(select(Incident).where(Incident.tenant_id == tenant.id))
    incidents = result.scalars().all()
    return [IncidentRead.model_validate(item) for item in incidents]


@router.get("/{incident_id}", response_model=IncidentRead, summary="Get incident")
async def read_incident(
    incident_id: uuid.UUID,
    tenant: Tenant = Depends(deps.ensure_tenant_access),
    _: User = Depends(deps.get_current_active_user),
    session: AsyncSession = Depends(deps.get_db_session),
) -> IncidentRead:
    """Retrieve a single incident by identifier."""

    incident = await session.get(Incident, incident_id)
    if incident is None or incident.tenant_id != tenant.id:
        raise HTTPException(status_code=status.HTTP_404_NOT_FOUND, detail="Incident not found")
    return IncidentRead.model_validate(incident)
