from fastapi import APIRouter

router = APIRouter(tags=["health"])


@router.get("/health", summary="Service liveness probe")
async def health_check() -> dict[str, str]:
    """Return a simple heartbeat payload."""

    return {"status": "ok"}
