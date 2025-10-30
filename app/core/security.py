from datetime import UTC, datetime, timedelta
from typing import Any

from jose import JWTError, jwt
from passlib.context import CryptContext

from app.core.config import get_settings


pwd_context = CryptContext(schemes=["bcrypt"], deprecated="auto")
settings = get_settings()


def create_access_token(subject: str, expires_delta: timedelta | None = None) -> str:
    """Generate a JWT access token."""

    expire = datetime.now(UTC) + (
        expires_delta or timedelta(minutes=settings.access_token_expire_minutes)
    )
    to_encode: dict[str, Any] = {"exp": expire, "sub": subject}
    return jwt.encode(to_encode, settings.secret_key, algorithm=settings.algorithm)


def verify_access_token(token: str) -> str:
    """Decode the JWT token and return the subject."""

    try:
        payload = jwt.decode(token, settings.secret_key, algorithms=[settings.algorithm])
    except JWTError as exc:  # pragma: no cover - jose already well tested
        raise ValueError("Invalid token") from exc

    subject: str | None = payload.get("sub")
    if subject is None:
        raise ValueError("Invalid token payload")
    return subject


def get_password_hash(password: str) -> str:
    """Hash a raw password."""

    return pwd_context.hash(password)


def verify_password(plain_password: str, hashed_password: str) -> bool:
    """Validate a password against a stored hash."""

    return pwd_context.verify(plain_password, hashed_password)
