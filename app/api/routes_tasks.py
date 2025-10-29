from __future__ import annotations

import uuid

from fastapi import APIRouter, Depends, HTTPException, Query, status
from sqlalchemy import select
from sqlalchemy.ext.asyncio import AsyncSession

from app.api import deps
from app.models import Incident, Task, Tenant, User
from app.schemas.task import TaskCreate, TaskRead, TaskUpdate


router = APIRouter(prefix="/tasks", tags=["tasks"])


@router.post("/", response_model=TaskRead, status_code=status.HTTP_201_CREATED, summary="Create task")
async def create_task(
    task_in: TaskCreate,
    tenant: Tenant = Depends(deps.ensure_tenant_access),
    _: User = Depends(deps.get_current_active_user),
    session: AsyncSession = Depends(deps.get_db_session),
) -> TaskRead:
    """Create a new task linked to an incident within the tenant."""

    incident = await session.get(Incident, task_in.incident_id)
    if incident is None or incident.tenant_id != tenant.id:
        raise HTTPException(status_code=status.HTTP_404_NOT_FOUND, detail="Incident not found")

    if task_in.assignee_id is not None:
        assignee = await session.get(User, task_in.assignee_id)
        if assignee is None or assignee.tenant_id != tenant.id:
            raise HTTPException(status_code=status.HTTP_404_NOT_FOUND, detail="Assignee not found")

    task = Task(
        title=task_in.title,
        description=task_in.description,
        status=task_in.status,
        due_at=task_in.due_at,
        incident_id=incident.id,
        assignee_id=task_in.assignee_id,
    )
    session.add(task)
    await session.commit()
    await session.refresh(task)
    return TaskRead.model_validate(task)


@router.get("/", response_model=list[TaskRead], summary="List tasks")
async def list_tasks(
    tenant: Tenant = Depends(deps.ensure_tenant_access),
    _: User = Depends(deps.get_current_active_user),
    session: AsyncSession = Depends(deps.get_db_session),
    incident_id: uuid.UUID | None = Query(default=None, description="Filter by incident ID"),
) -> list[TaskRead]:
    """List tasks available within the tenant scope."""

    stmt = select(Task).join(Incident).where(Incident.tenant_id == tenant.id)
    if incident_id is not None:
        stmt = stmt.where(Task.incident_id == incident_id)

    result = await session.execute(stmt)
    tasks = result.scalars().all()
    return [TaskRead.model_validate(task) for task in tasks]


@router.get("/{task_id}", response_model=TaskRead, summary="Get task")
async def read_task(
    task_id: uuid.UUID,
    tenant: Tenant = Depends(deps.ensure_tenant_access),
    _: User = Depends(deps.get_current_active_user),
    session: AsyncSession = Depends(deps.get_db_session),
) -> TaskRead:
    """Retrieve a task if it belongs to the tenant."""

    result = await session.execute(
        select(Task).join(Incident).where(Task.id == task_id, Incident.tenant_id == tenant.id)
    )
    task = result.scalar_one_or_none()
    if task is None:
        raise HTTPException(status_code=status.HTTP_404_NOT_FOUND, detail="Task not found")
    return TaskRead.model_validate(task)


@router.patch("/{task_id}", response_model=TaskRead, summary="Update task")
async def update_task(
    task_id: uuid.UUID,
    task_in: TaskUpdate,
    tenant: Tenant = Depends(deps.ensure_tenant_access),
    _: User = Depends(deps.get_current_active_user),
    session: AsyncSession = Depends(deps.get_db_session),
) -> TaskRead:
    """Update task details such as status, assignee, or description."""

    result = await session.execute(
        select(Task).join(Incident).where(Task.id == task_id, Incident.tenant_id == tenant.id)
    )
    task = result.scalar_one_or_none()
    if task is None:
        raise HTTPException(status_code=status.HTTP_404_NOT_FOUND, detail="Task not found")

    update_data = task_in.model_dump(exclude_unset=True)

    if "title" in update_data:
        new_title = update_data["title"]
        if new_title is None:
            raise HTTPException(status_code=status.HTTP_400_BAD_REQUEST, detail="Title cannot be empty")
        task.title = new_title

    if "description" in update_data:
        task.description = update_data["description"]

    if "status" in update_data:
        task.status = update_data["status"]

    if "due_at" in update_data:
        task.due_at = update_data["due_at"]

    if "assignee_id" in update_data:
        assignee_id = update_data["assignee_id"]
        if assignee_id is not None:
            assignee = await session.get(User, assignee_id)
            if assignee is None or assignee.tenant_id != tenant.id:
                raise HTTPException(status_code=status.HTTP_404_NOT_FOUND, detail="Assignee not found")
        task.assignee_id = assignee_id

    await session.commit()
    await session.refresh(task)
    return TaskRead.model_validate(task)

