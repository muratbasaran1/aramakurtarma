from functools import lru_cache
from pydantic_settings import BaseSettings, SettingsConfigDict


class Settings(BaseSettings):
    """Application configuration values."""

    api_v1_prefix: str = "/api/v1"
    secret_key: str = "change-me"
    access_token_expire_minutes: int = 60
    algorithm: str = "HS256"
    database_url: str = "sqlite+aiosqlite:///./app.db"
    first_superuser_email: str | None = None
    first_superuser_password: str | None = None
    bootstrap_tenant_name: str = "Merkez Komuta"
    bootstrap_tenant_slug: str = "merkez-komuta"

    model_config = SettingsConfigDict(env_file=".env", env_file_encoding="utf-8")


@lru_cache
def get_settings() -> Settings:
    """Return cached application settings."""

    return Settings()
