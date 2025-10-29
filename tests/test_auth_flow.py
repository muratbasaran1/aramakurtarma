import uuid
from datetime import datetime, timedelta, timezone

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

    update_payload = {
        "message": "Durum değerlendirmesi yapıldı, ekipler yönlendiriliyor.",
        "status": "in_progress",
    }
    update_response = await client.post(
        f"/api/v1/incidents/{incident_data['id']}/updates",
        json=update_payload,
        headers={
            "Authorization": f"Bearer {tenant_token}",
            "X-Tenant-ID": "ankara-afad",
        },
    )
    assert update_response.status_code == 201
    timeline_entry = update_response.json()
    assert timeline_entry["message"] == update_payload["message"]
    assert timeline_entry["status"] == update_payload["status"]

    updated_incident = await client.get(
        f"/api/v1/incidents/{incident_data['id']}",
        headers={
            "Authorization": f"Bearer {tenant_token}",
            "X-Tenant-ID": "ankara-afad",
        },
    )
    assert updated_incident.status_code == 200
    assert updated_incident.json()["status"] == update_payload["status"]

    updates_list = await client.get(
        f"/api/v1/incidents/{incident_data['id']}/updates",
        headers={
            "Authorization": f"Bearer {tenant_token}",
            "X-Tenant-ID": "ankara-afad",
        },
    )
    assert updates_list.status_code == 200
    assert len(updates_list.json()) == 1

    resource_request_payload = {
        "summary": "5 adet tam donanımlı ilk yardım çantası",
        "details": "Yaralıların stabilizasyonu için acil ihtiyaç.",
        "quantity": 5,
        "incident_id": incident_data["id"],
    }
    resource_request_response = await client.post(
        "/api/v1/resource-requests/",
        json=resource_request_payload,
        headers={
            "Authorization": f"Bearer {tenant_token}",
            "X-Tenant-ID": "ankara-afad",
        },
    )
    assert resource_request_response.status_code == 201
    request_data = resource_request_response.json()
    assert request_data["summary"] == resource_request_payload["summary"]
    assert request_data["quantity"] == resource_request_payload["quantity"]
    assert request_data["status"] == "pending"
    assert request_data["incident_id"] == incident_data["id"]

    resource_request_list = await client.get(
        "/api/v1/resource-requests/",
        params={"incident_id": incident_data["id"]},
        headers={
            "Authorization": f"Bearer {tenant_token}",
            "X-Tenant-ID": "ankara-afad",
        },
    )
    assert resource_request_list.status_code == 200
    assert len(resource_request_list.json()) == 1

    resource_request_detail = await client.get(
        f"/api/v1/resource-requests/{request_data['id']}",
        headers={
            "Authorization": f"Bearer {tenant_token}",
            "X-Tenant-ID": "ankara-afad",
        },
    )
    assert resource_request_detail.status_code == 200
    assert resource_request_detail.json()["status"] == "pending"

    resource_request_update = await client.patch(
        f"/api/v1/resource-requests/{request_data['id']}",
        json={"status": "dispatched", "quantity": 3},
        headers={
            "Authorization": f"Bearer {tenant_token}",
            "X-Tenant-ID": "ankara-afad",
        },
    )
    assert resource_request_update.status_code == 200
    updated_request = resource_request_update.json()
    assert updated_request["status"] == "dispatched"
    assert updated_request["quantity"] == 3

    dispatched_list = await client.get(
        "/api/v1/resource-requests/",
        params={"status": "dispatched"},
        headers={
            "Authorization": f"Bearer {tenant_token}",
            "X-Tenant-ID": "ankara-afad",
        },
    )
    assert dispatched_list.status_code == 200
    assert len(dispatched_list.json()) == 1

    # Create a task linked to the incident
    task_payload = {
        "title": "Yaralı tahliye koordinasyonu",
        "description": "Ambulans yönlendirmesi ve yaralı transferi organize edilecek.",
        "incident_id": incident_data["id"],
        "assignee_id": str(new_user_id),
    }
    task_response = await client.post(
        "/api/v1/tasks/",
        json=task_payload,
        headers={
            "Authorization": f"Bearer {tenant_token}",
            "X-Tenant-ID": "ankara-afad",
        },
    )
    assert task_response.status_code == 201
    task_data = task_response.json()
    assert task_data["title"] == task_payload["title"]
    assert task_data["incident_id"] == incident_data["id"]
    assert task_data["assignee_id"] == str(new_user_id)
    assert "created_at" in task_data
    task_created_at = datetime.fromisoformat(task_data["created_at"])
    assert task_created_at.tzinfo == timezone.utc

    task_list = await client.get(
        "/api/v1/tasks/",
        headers={
            "Authorization": f"Bearer {tenant_token}",
            "X-Tenant-ID": "ankara-afad",
        },
    )
    assert task_list.status_code == 200
    assert len(task_list.json()) == 1

    task_update_response = await client.patch(
        f"/api/v1/tasks/{task_data['id']}",
        json={"status": "in_progress"},
        headers={
            "Authorization": f"Bearer {tenant_token}",
            "X-Tenant-ID": "ankara-afad",
        },
    )
    assert task_update_response.status_code == 200
    assert task_update_response.json()["status"] == "in_progress"

    task_detail = await client.get(
        f"/api/v1/tasks/{task_data['id']}",
        headers={
            "Authorization": f"Bearer {tenant_token}",
            "X-Tenant-ID": "ankara-afad",
        },
    )
    assert task_detail.status_code == 200
    assert task_detail.json()["status"] == "in_progress"
    detail_created_at = datetime.fromisoformat(task_detail.json()["created_at"])
    assert detail_created_at == task_created_at

    deployment_payload = {
        "name": "Sağlık Ekip 1",
        "status": "en_route",
        "contact_info": "+90 555 123 45 67",
        "incident_id": incident_data["id"],
        "resource_request_id": request_data["id"],
        "leader_id": str(new_user_id),
        "eta": (datetime.now(timezone.utc) + timedelta(hours=1)).isoformat(),
    }
    deployment_response = await client.post(
        "/api/v1/deployments/",
        json=deployment_payload,
        headers={
            "Authorization": f"Bearer {tenant_token}",
            "X-Tenant-ID": "ankara-afad",
        },
    )
    assert deployment_response.status_code == 201
    deployment_data = deployment_response.json()
    assert deployment_data["name"] == deployment_payload["name"]
    assert deployment_data["status"] == deployment_payload["status"]
    assert deployment_data["resource_request_id"] == request_data["id"]

    deployment_list = await client.get(
        "/api/v1/deployments/",
        params={"incident_id": incident_data["id"]},
        headers={
            "Authorization": f"Bearer {tenant_token}",
            "X-Tenant-ID": "ankara-afad",
        },
    )
    assert deployment_list.status_code == 200
    assert len(deployment_list.json()) == 1

    deployment_detail = await client.get(
        f"/api/v1/deployments/{deployment_data['id']}",
        headers={
            "Authorization": f"Bearer {tenant_token}",
            "X-Tenant-ID": "ankara-afad",
        },
    )
    assert deployment_detail.status_code == 200
    assert deployment_detail.json()["leader_id"] == str(new_user_id)

    deployment_update = await client.patch(
        f"/api/v1/deployments/{deployment_data['id']}",
        json={
            "status": "on_scene",
            "arrived_at": datetime.now(timezone.utc).isoformat(),
            "contact_info": "+90 555 000 00 00",
        },
        headers={
            "Authorization": f"Bearer {tenant_token}",
            "X-Tenant-ID": "ankara-afad",
        },
    )
    assert deployment_update.status_code == 200
    updated_deployment = deployment_update.json()
    assert updated_deployment["status"] == "on_scene"
    assert updated_deployment["contact_info"] == "+90 555 000 00 00"

    on_scene_list = await client.get(
        "/api/v1/deployments/",
        params={"status": "on_scene"},
        headers={
            "Authorization": f"Bearer {tenant_token}",
            "X-Tenant-ID": "ankara-afad",
        },
    )
    assert on_scene_list.status_code == 200
    assert len(on_scene_list.json()) == 1

    report_payload = {
        "title": "Saha Durum Raporu 1",
        "summary": "Ekip olay yerinde ilk müdahaleyi tamamladı.",
        "incident_id": incident_data["id"],
        "deployment_id": deployment_data["id"],
        "status": "submitted",
        "personnel_count": 6,
    }
    report_response = await client.post(
        "/api/v1/deployment-reports/",
        json=report_payload,
        headers={
            "Authorization": f"Bearer {tenant_token}",
            "X-Tenant-ID": "ankara-afad",
        },
    )
    assert report_response.status_code == 201
    report_data = report_response.json()
    assert report_data["status"] == "submitted"
    assert report_data["deployment_id"] == deployment_data["id"]
    assert report_data["personnel_count"] == 6
    assert report_data["author_id"] == str(new_user_id)

    reports_list = await client.get(
        "/api/v1/deployment-reports/",
        params={"incident_id": incident_data["id"]},
        headers={
            "Authorization": f"Bearer {tenant_token}",
            "X-Tenant-ID": "ankara-afad",
        },
    )
    assert reports_list.status_code == 200
    assert len(reports_list.json()) == 1

    reports_by_status = await client.get(
        "/api/v1/deployment-reports/",
        params={"status": "submitted"},
        headers={
            "Authorization": f"Bearer {tenant_token}",
            "X-Tenant-ID": "ankara-afad",
        },
    )
    assert reports_by_status.status_code == 200
    assert len(reports_by_status.json()) == 1

    report_detail = await client.get(
        f"/api/v1/deployment-reports/{report_data['id']}",
        headers={
            "Authorization": f"Bearer {tenant_token}",
            "X-Tenant-ID": "ankara-afad",
        },
    )
    assert report_detail.status_code == 200
    assert report_detail.json()["deployment_id"] == deployment_data["id"]

    report_submission_time = datetime.now(timezone.utc)
    report_update = await client.patch(
        f"/api/v1/deployment-reports/{report_data['id']}",
        json={
            "status": "approved",
            "follow_up_actions": "Personel rotasyon planını hazırla.",
            "personnel_count": 8,
            "submitted_at": report_submission_time.isoformat(),
        },
        headers={
            "Authorization": f"Bearer {tenant_token}",
            "X-Tenant-ID": "ankara-afad",
        },
    )
    assert report_update.status_code == 200
    updated_report = report_update.json()
    assert updated_report["status"] == "approved"
    assert updated_report["personnel_count"] == 8
    assert updated_report["follow_up_actions"].startswith("Personel rotasyon")

    approved_reports = await client.get(
        "/api/v1/deployment-reports/",
        params={"status": "approved"},
        headers={
            "Authorization": f"Bearer {tenant_token}",
            "X-Tenant-ID": "ankara-afad",
        },
    )
    assert approved_reports.status_code == 200
    assert len(approved_reports.json()) == 1

    delivery_payload = {
        "title": "İlk yardım malzemesi sevkiyatı",
        "status": "in_transit",
        "destination": "Gölbaşı lojistik noktası",
        "items_description": "5 ilk yardım çantası ve destek ekipmanları",
        "quantity_delivered": 0,
        "incident_id": incident_data["id"],
        "resource_request_id": request_data["id"],
        "deployment_id": deployment_data["id"],
        "handled_by_id": str(new_user_id),
        "dispatched_at": datetime.now(timezone.utc).isoformat(),
    }
    delivery_response = await client.post(
        "/api/v1/relief-deliveries/",
        json=delivery_payload,
        headers={
            "Authorization": f"Bearer {tenant_token}",
            "X-Tenant-ID": "ankara-afad",
        },
    )
    assert delivery_response.status_code == 201
    delivery_data = delivery_response.json()
    assert delivery_data["status"] == "in_transit"
    assert delivery_data["resource_request_id"] == request_data["id"]
    assert delivery_data["handled_by_id"] == str(new_user_id)

    deliveries_list = await client.get(
        "/api/v1/relief-deliveries/",
        params={"incident_id": incident_data["id"]},
        headers={
            "Authorization": f"Bearer {tenant_token}",
            "X-Tenant-ID": "ankara-afad",
        },
    )
    assert deliveries_list.status_code == 200
    assert len(deliveries_list.json()) == 1

    delivery_detail = await client.get(
        f"/api/v1/relief-deliveries/{delivery_data['id']}",
        headers={
            "Authorization": f"Bearer {tenant_token}",
            "X-Tenant-ID": "ankara-afad",
        },
    )
    assert delivery_detail.status_code == 200
    assert delivery_detail.json()["deployment_id"] == deployment_data["id"]

    verification_time = datetime.now(timezone.utc)
    delivery_update = await client.patch(
        f"/api/v1/relief-deliveries/{delivery_data['id']}",
        json={
            "status": "verified",
            "quantity_delivered": 5,
            "delivered_at": verification_time.isoformat(),
            "verified_at": (verification_time + timedelta(minutes=30)).isoformat(),
            "destination": "Olay bölgesi saha kontrol noktası",
        },
        headers={
            "Authorization": f"Bearer {tenant_token}",
            "X-Tenant-ID": "ankara-afad",
        },
    )
    assert delivery_update.status_code == 200
    updated_delivery = delivery_update.json()
    assert updated_delivery["status"] == "verified"
    assert updated_delivery["quantity_delivered"] == 5
    assert updated_delivery["destination"].startswith("Olay bölgesi")

    verified_deliveries = await client.get(
        "/api/v1/relief-deliveries/",
        params={"status": "verified"},
        headers={
            "Authorization": f"Bearer {tenant_token}",
            "X-Tenant-ID": "ankara-afad",
        },
    )
    assert verified_deliveries.status_code == 200
    assert len(verified_deliveries.json()) == 1

    plan_payload = {
        "title": "Olay sonrası rehabilitasyon planı",
        "description": "Enkaz kaldırma ve psikososyal destek koordinasyonu",
        "status": "in_progress",
        "incident_id": incident_data["id"],
        "started_at": datetime.now(timezone.utc).isoformat(),
        "priority": "high",
    }
    plan_response = await client.post(
        "/api/v1/recovery-plans/",
        json=plan_payload,
        headers={
            "Authorization": f"Bearer {tenant_token}",
            "X-Tenant-ID": "ankara-afad",
        },
    )
    assert plan_response.status_code == 201
    plan_data = plan_response.json()
    assert plan_data["status"] == "in_progress"
    assert plan_data["owner_id"] == str(new_user_id)

    plans_list = await client.get(
        "/api/v1/recovery-plans/",
        params={"incident_id": incident_data["id"]},
        headers={
            "Authorization": f"Bearer {tenant_token}",
            "X-Tenant-ID": "ankara-afad",
        },
    )
    assert plans_list.status_code == 200
    assert len(plans_list.json()) == 1

    plan_detail = await client.get(
        f"/api/v1/recovery-plans/{plan_data['id']}",
        headers={
            "Authorization": f"Bearer {tenant_token}",
            "X-Tenant-ID": "ankara-afad",
        },
    )
    assert plan_detail.status_code == 200
    assert plan_detail.json()["title"] == plan_payload["title"]

    completion_time = datetime.now(timezone.utc) + timedelta(days=2)
    plan_update = await client.patch(
        f"/api/v1/recovery-plans/{plan_data['id']}",
        json={
            "status": "completed",
            "completed_at": completion_time.isoformat(),
            "priority": "medium",
        },
        headers={
            "Authorization": f"Bearer {tenant_token}",
            "X-Tenant-ID": "ankara-afad",
        },
    )
    assert plan_update.status_code == 200
    updated_plan = plan_update.json()
    assert updated_plan["status"] == "completed"
    returned_completion = datetime.fromisoformat(
        updated_plan["completed_at"].replace("Z", "+00:00")
    )
    assert returned_completion == completion_time
    assert updated_plan["priority"] == "medium"

    progress_response = await client.get(
        f"/api/v1/incidents/{incident_data['id']}/progress",
        headers={
            "Authorization": f"Bearer {tenant_token}",
            "X-Tenant-ID": "ankara-afad",
        },
    )
    assert progress_response.status_code == 200
    progress = progress_response.json()
    assert progress["completed_steps"] == progress["total_steps"]
    assert progress["completion_ratio"] == pytest.approx(1.0)
    assert any(step["key"] == "recovery_plan_completed" and step["completed"] for step in progress["steps"])

    readiness_response = await client.get(
        f"/api/v1/incidents/{incident_data['id']}/readiness",
        headers={
            "Authorization": f"Bearer {tenant_token}",
            "X-Tenant-ID": "ankara-afad",
        },
    )
    assert readiness_response.status_code == 200
    readiness = readiness_response.json()
    assert readiness["ready"] is True
    assert readiness["remaining_steps"] == 0
    assert readiness["current_stage_key"] == "completed"
    assert readiness["completed_steps"] == readiness["total_steps"]
    assert readiness["summary"].startswith("Tüm operasyon adımları tamamlandı")
    assert "Kalan adım: 0" in readiness["summary"]
    assert readiness["pending_steps"] == []

    readiness_overview_response = await client.get(
        "/api/v1/incidents/readiness/overview",
        headers={
            "Authorization": f"Bearer {tenant_token}",
            "X-Tenant-ID": "ankara-afad",
        },
    )
    assert readiness_overview_response.status_code == 200
    overview = readiness_overview_response.json()
    assert overview["tenant_id"] == tenant_id
    assert overview["total_incidents"] == 1
    assert overview["ready_incidents"] == 1
    assert overview["not_ready_incidents"] == 0
    assert overview["average_completion_ratio"] == pytest.approx(1.0)
    assert len(overview["incidents"]) == 1
    overview_entry = overview["incidents"][0]
    assert overview_entry["incident_id"] == incident_data["id"]
    assert overview_entry["incident_title"] == incident_payload["title"]
    assert overview_entry["ready"] is True
    assert overview_entry["pending_steps"] == []

    global_readiness_response = await client.get(
        "/api/v1/tenants/readiness/overview",
        headers={"Authorization": f"Bearer {superuser_token}"},
    )
    assert global_readiness_response.status_code == 200
    global_overview = global_readiness_response.json()
    assert global_overview["total_tenants"] == 2
    assert global_overview["ready_tenants"] == 2
    assert global_overview["not_ready_tenants"] == 0
    assert global_overview["total_incidents"] == 1
    assert global_overview["ready_incidents"] == 1
    assert global_overview["not_ready_incidents"] == 0
    assert global_overview["average_completion_ratio"] == pytest.approx(1.0)
    assert len(global_overview["tenants"]) == 2
    tenant_snapshot = next(
        item
        for item in global_overview["tenants"]
        if item["tenant_id"] == tenant_id
    )
    assert tenant_snapshot["tenant_name"] == tenant_payload["name"]
    assert tenant_snapshot["tenant_slug"] == tenant_payload["slug"]
    assert tenant_snapshot["total_incidents"] == 1
    assert tenant_snapshot["ready_incidents"] == 1
    assert tenant_snapshot["not_ready_incidents"] == 0
    assert tenant_snapshot["average_completion_ratio"] == pytest.approx(1.0)

    tenant_operations_response = await client.get(
        "/api/v1/operations/summary",
        headers={
            "Authorization": f"Bearer {tenant_token}",
            "X-Tenant-ID": "ankara-afad",
        },
    )
    assert tenant_operations_response.status_code == 200
    tenant_operations = tenant_operations_response.json()
    assert tenant_operations["tenant_id"] == tenant_id
    assert tenant_operations["incidents"]["total"] == 1
    assert tenant_operations["incidents"]["by_status"]["in_progress"] == 1
    assert tenant_operations["resource_requests"]["total"] == 1
    assert tenant_operations["resource_requests"]["by_status"]["dispatched"] == 1
    assert tenant_operations["deployments"]["total"] == 1
    assert tenant_operations["deployments"]["by_status"]["on_scene"] == 1
    assert tenant_operations["deployment_reports"]["total"] == 1
    assert tenant_operations["deployment_reports"]["by_status"]["approved"] == 1
    assert tenant_operations["relief_deliveries"]["total"] == 1
    assert tenant_operations["relief_deliveries"]["by_status"]["verified"] == 1
    assert tenant_operations["recovery_plans"]["total"] == 1
    assert tenant_operations["recovery_plans"]["by_status"]["completed"] == 1
    assert tenant_operations["tasks"]["total"] == 1
    assert tenant_operations["tasks"]["by_status"]["in_progress"] == 1

    global_operations_response = await client.get(
        "/api/v1/operations/overview",
        headers={"Authorization": f"Bearer {superuser_token}"},
    )
    assert global_operations_response.status_code == 200
    global_operations = global_operations_response.json()
    assert global_operations["total_tenants"] == 2
    assert global_operations["incidents"]["total"] == 1
    assert global_operations["resource_requests"]["total"] == 1
    assert global_operations["deployments"]["total"] == 1
    assert global_operations["deployment_reports"]["total"] == 1
    assert global_operations["relief_deliveries"]["total"] == 1
    assert global_operations["recovery_plans"]["total"] == 1
    assert global_operations["tasks"]["total"] == 1
    tenant_operations_entry = next(
        item for item in global_operations["tenants"] if item["tenant_id"] == tenant_id
    )
    assert tenant_operations_entry["tenant_slug"] == tenant_payload["slug"]
    assert tenant_operations_entry["incidents"]["by_status"]["in_progress"] == 1
    assert tenant_operations_entry["tasks"]["by_status"]["in_progress"] == 1

    backlog_response = await client.get(
        "/api/v1/operations/backlog",
        headers={
            "Authorization": f"Bearer {tenant_token}",
            "X-Tenant-ID": "ankara-afad",
        },
    )
    assert backlog_response.status_code == 200
    backlog = backlog_response.json()
    assert backlog["tenant_id"] == tenant_id
    assert backlog["tenant_slug"] == tenant_payload["slug"]
    assert backlog["total_pending"] == sum(item["pending"] for item in backlog["items"])

    backlog_items = {item["category"]: item for item in backlog["items"]}

    incident_backlog = backlog_items["incidents"]
    assert incident_backlog["pending"] == 1
    assert incident_backlog["open_statuses"] == ["new", "triaged", "in_progress"]
    assert incident_backlog["oldest_item_title"] == incident_payload["title"]
    assert incident_backlog["oldest_pending_since"] is not None

    resource_backlog = backlog_items["resource_requests"]
    assert resource_backlog["pending"] == 1
    assert resource_backlog["open_statuses"] == ["pending", "approved", "dispatched"]
    assert resource_backlog["oldest_item_title"] == resource_request_payload["summary"]
    assert resource_backlog["oldest_pending_since"] is not None

    deployment_backlog = backlog_items["deployments"]
    assert deployment_backlog["pending"] == 1
    assert deployment_backlog["open_statuses"] == ["preparing", "en_route", "on_scene"]
    assert deployment_backlog["oldest_item_title"] == deployment_payload["name"]
    assert deployment_backlog["oldest_pending_since"] is not None

    assert backlog_items["deployment_reports"]["pending"] == 0
    assert backlog_items["deployment_reports"]["oldest_item_id"] is None
    assert backlog_items["relief_deliveries"]["pending"] == 0
    assert backlog_items["relief_deliveries"]["oldest_item_id"] is None
    assert backlog_items["recovery_plans"]["pending"] == 0
    assert backlog_items["recovery_plans"]["oldest_item_id"] is None

    task_backlog = backlog_items["tasks"]
    assert task_backlog["pending"] == 1
    assert task_backlog["open_statuses"] == ["pending", "assigned", "in_progress"]
    assert task_backlog["oldest_item_title"] == task_payload["title"]
    assert task_backlog["oldest_pending_since"] is not None

    global_backlog_response = await client.get(
        "/api/v1/operations/backlog/overview",
        headers={"Authorization": f"Bearer {superuser_token}"},
    )
    assert global_backlog_response.status_code == 200
    global_backlog = global_backlog_response.json()

    assert global_backlog["total_pending"] == backlog["total_pending"]
    assert len(global_backlog["tenants"]) == 2

    tenant_backlog_snapshot = next(
        item for item in global_backlog["tenants"] if item["tenant_id"] == tenant_id
    )
    assert tenant_backlog_snapshot["total_pending"] == backlog["total_pending"]

    categories = {item["category"]: item for item in global_backlog["categories"]}
    assert set(categories.keys()) == set(backlog_items.keys())

    global_incident_backlog = categories["incidents"]
    assert global_incident_backlog["pending"] == 1
    assert global_incident_backlog["tenant_count"] == 1
    assert global_incident_backlog["worst_tenant_id"] == tenant_id
    assert global_incident_backlog["worst_tenant_pending"] == 1
    assert global_incident_backlog["oldest_item_title"] == incident_payload["title"]

    global_resource_backlog = categories["resource_requests"]
    assert global_resource_backlog["pending"] == 1
    assert global_resource_backlog["tenant_count"] == 1
    assert global_resource_backlog["worst_tenant_slug"] == tenant_payload["slug"]
    assert global_resource_backlog["oldest_item_title"] == resource_request_payload["summary"]

    global_deployment_backlog = categories["deployments"]
    assert global_deployment_backlog["pending"] == 1
    assert global_deployment_backlog["tenant_count"] == 1
    assert global_deployment_backlog["oldest_item_title"] == deployment_payload["name"]

    assert categories["deployment_reports"]["pending"] == 0
    assert categories["deployment_reports"]["tenant_count"] == 0
    assert categories["deployment_reports"]["worst_tenant_id"] is None

    assert categories["relief_deliveries"]["pending"] == 0
    assert categories["relief_deliveries"]["worst_tenant_id"] is None

    assert categories["recovery_plans"]["pending"] == 0
    assert categories["recovery_plans"]["worst_tenant_pending"] is None

    assert categories["tasks"]["pending"] == 1
    assert categories["tasks"]["tenant_count"] == 1
    assert categories["tasks"]["worst_tenant_name"] == tenant_payload["name"]
