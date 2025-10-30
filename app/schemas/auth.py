from pydantic import BaseModel, EmailStr


class Token(BaseModel):
    """Represents a JWT access token."""

    access_token: str
    token_type: str = "bearer"


class TokenPayload(BaseModel):
    """Payload embedded in JWT tokens."""

    sub: str | None = None


class LoginRequest(BaseModel):
    """Credentials required for obtaining an access token."""

    username: EmailStr
    password: str
