import os
import sys
from collections.abc import AsyncGenerator, Generator
from pathlib import Path

import pytest
from asgi_lifespan import LifespanManager
from httpx import ASGITransport, AsyncClient

ROOT_DIR = Path(__file__).resolve().parents[1]
if str(ROOT_DIR) not in sys.path:
    sys.path.insert(0, str(ROOT_DIR))

from app.core.config import get_settings


@pytest.fixture(scope="session", autouse=True)
def test_settings(tmp_path_factory: pytest.TempPathFactory) -> Generator[None, None, None]:
    """Configure application settings for the test session."""

    data_dir = tmp_path_factory.mktemp("data")
    db_path = data_dir / "test.db"
    os.environ["DATABASE_URL"] = f"sqlite+aiosqlite:///{db_path}"
    os.environ["FIRST_SUPERUSER_EMAIL"] = "admin@test.local"
    os.environ["FIRST_SUPERUSER_PASSWORD"] = "SuperSecure123!"
    os.environ["SECRET_KEY"] = "test-secret"
    os.environ["BOOTSTRAP_TENANT_NAME"] = "Test Tenant"
    os.environ["BOOTSTRAP_TENANT_SLUG"] = "test-tenant"
    get_settings.cache_clear()
    yield
    get_settings.cache_clear()


@pytest.fixture()
async def client() -> AsyncGenerator[AsyncClient, None]:
    """Return an HTTPX async client with lifespan support."""

    from app.main import app  # imported lazily to ensure settings are applied

    async with LifespanManager(app):
        transport = ASGITransport(app=app)
        async with AsyncClient(transport=transport, base_url="http://testserver") as async_client:
            yield async_client
