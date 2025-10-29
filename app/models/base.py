"""Shared SQLAlchemy declarative base helpers."""

from __future__ import annotations

from datetime import UTC, datetime

from sqlalchemy import DateTime
from sqlalchemy.orm import DeclarativeBase
from sqlalchemy.types import TypeDecorator


def utc_now() -> datetime:
    """Return a timezone-aware UTC timestamp for model defaults."""

    return datetime.now(UTC)


class Base(DeclarativeBase):
    """Base declarative model for SQLAlchemy."""

    pass


class UTCDateTime(TypeDecorator[datetime]):
    """SQLite-friendly timezone-aware datetime column."""

    impl = DateTime
    cache_ok = True

    def process_bind_param(self, value: datetime | None, dialect):  # type: ignore[override]
        if value is None:
            return value
        if value.tzinfo is None:
            raise ValueError("UTCDateTime requires timezone-aware datetimes")
        return value.astimezone(UTC).replace(tzinfo=None)

    def process_result_value(self, value: datetime | None, dialect):  # type: ignore[override]
        if value is None:
            return value
        return value.replace(tzinfo=UTC)
