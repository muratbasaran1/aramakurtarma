import uuid

from pydantic import BaseModel, EmailStr, Field, ConfigDict


class UserBase(BaseModel):
    """Common user fields."""

    email: EmailStr
    full_name: str | None = None
    role: str = Field(default="responder", examples=["responder", "coordinator", "commander"])
    is_active: bool = True


class UserCreate(UserBase):
    """Schema for creating a user."""

    password: str = Field(min_length=8, max_length=128)
    tenant_id: uuid.UUID
    is_superuser: bool = False


class UserRead(UserBase):
    """Schema returned to clients."""

    id: uuid.UUID
    is_superuser: bool
    tenant_id: uuid.UUID

    model_config = ConfigDict(from_attributes=True)
