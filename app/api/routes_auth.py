from datetime import timedelta

from fastapi import APIRouter, Depends, HTTPException, status
from fastapi.security import OAuth2PasswordRequestForm
from sqlalchemy import select
from sqlalchemy.ext.asyncio import AsyncSession

from app.api.deps import get_db_session
from app.core.config import get_settings
from app.core.security import create_access_token, verify_password
from app.models import User
from app.schemas.auth import Token

router = APIRouter(prefix="/auth", tags=["auth"])
settings = get_settings()


@router.post("/token", response_model=Token, summary="Obtain an access token")
async def login_access_token(
    form_data: OAuth2PasswordRequestForm = Depends(),
    session: AsyncSession = Depends(get_db_session),
) -> Token:
    """Authenticate user and return a JWT access token."""

    result = await session.execute(select(User).where(User.email == form_data.username))
    user = result.scalar_one_or_none()
    if not user or not verify_password(form_data.password, user.hashed_password):
        raise HTTPException(status_code=status.HTTP_400_BAD_REQUEST, detail="Incorrect email or password")
    if not user.is_active:
        raise HTTPException(status_code=status.HTTP_400_BAD_REQUEST, detail="Inactive user")

    access_token_expires = timedelta(minutes=settings.access_token_expire_minutes)
    return Token(access_token=create_access_token(str(user.id), expires_delta=access_token_expires))
