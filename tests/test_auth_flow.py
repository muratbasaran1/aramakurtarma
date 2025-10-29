import uuid

import pytest


@pytest.mark.asyncio
async def test_superuser_bootstrap_and_incident_flow(client):
    # Health check should work without authentication
    health_response = await client.get("/health")
    assert health_response.status_code == 200
    assert health_response.json()["status"] == "ok"

    # Login with bootstrap superuser
    login_response = await client.post(
        "/api/v1/auth/token",
        data={"username": "admin@test.local", "password": "SuperSecure123!"},
        headers={"Content-Type": "application/x-www-form-urlencoded"},
    )
    assert login_response.status_code == 200
    superuser_token = login_response.json()["access_token"]

    # Create a tenant
    tenant_payload = {"name": "Ankara AFAD", "slug": "ankara-afad"}
    tenant_response = await client.post(
        "/api/v1/tenants/",
        json=tenant_payload,
        headers={"Authorization": f"Bearer {superuser_token}"},
    )
    assert tenant_response.status_code == 201
    tenant_id = tenant_response.json()["id"]

    # Create a tenant user
    user_payload = {
        "email": "coord@ankara-afad.gov",
        "password": "Coordinate123!",
        "full_name": "Ankara Koordinatörü",
        "tenant_id": tenant_id,
        "role": "coordinator",
        "is_superuser": False,
    }
    user_response = await client.post(
        "/api/v1/users/",
        json=user_payload,
        headers={"Authorization": f"Bearer {superuser_token}"},
    )
    assert user_response.status_code == 201
    new_user_id = uuid.UUID(user_response.json()["id"])

    # Login as tenant user
    user_login = await client.post(
        "/api/v1/auth/token",
        data={"username": user_payload["email"], "password": user_payload["password"]},
        headers={"Content-Type": "application/x-www-form-urlencoded"},
    )
    assert user_login.status_code == 200
    tenant_token = user_login.json()["access_token"]

    incident_payload = {
        "title": "Çoklu yaralanmalı trafik kazası",
        "description": "Ankara Eskişehir yolu üzerinde zincirleme kaza bildirildi.",
        "severity": "high",
    }
    incident_response = await client.post(
        "/api/v1/incidents/",
        json=incident_payload,
        headers={
            "Authorization": f"Bearer {tenant_token}",
            "X-Tenant-ID": "ankara-afad",
        },
    )
    assert incident_response.status_code == 201
    incident_data = incident_response.json()
    assert incident_data["title"] == incident_payload["title"]
    assert incident_data["tenant_id"] == tenant_id
    assert uuid.UUID(incident_data["reporter_id"]) == new_user_id

    incidents_list = await client.get(
        "/api/v1/incidents/",
        headers={
            "Authorization": f"Bearer {tenant_token}",
            "X-Tenant-ID": "ankara-afad",
        },
    )
    assert incidents_list.status_code == 200
    assert len(incidents_list.json()) == 1
