# Metrik Kataloğu

TUDAK Afet Yönetim Sistemi için gözlemlenen temel metriklerin kaynağı, hesaplanma şekli ve kullanıldığı runbook bağlantıları aşağıda listelenmiştir. Katalog; Faz 4 (Canlı Takip), Faz 6 (OpsCenter), Faz 7 (Kural Motoru) ve Faz 11 (DevOps & İzlenebilirlik) kapsamındaki ölçüm gereksinimlerini tek noktada toplar.

## OpsCenter & API Katmanı

| Metrik Kodu | Açıklama | Tip | Kaynak | SLO Hedefi | Runbook / Aksiyon |
| --- | --- | --- | --- | --- | --- |
| `api_latency_p95` | REST uçlarının 95. yüzdelik yanıt süresi. Tenant etiketi ile ayrıştırılır. | Histogram | Prometheus (`http_request_duration_seconds`) | ≤ 1200 ms | `runbook/opscenter-degradation.md` |
| `api_error_rate` | 5 dakikalık pencerede 5xx hata oranı. | Counter | Prometheus (`http_requests_total`) | ≤ %1 | `runbook/incident-response.md` |
| `bbox_query_duration` | Geo sorgu (BBOX) ortalama yanıt süresi. | Summary | Grafana Loki + Tempo birleşik | ≤ 1500 ms | `docs/tests/matrix.md` senaryo-OC-04 |

## WebSocket & Canlı Takip

| Metrik Kodu | Açıklama | Tip | Kaynak | SLO Hedefi | Runbook / Aksiyon |
| --- | --- | --- | --- | --- | --- |
| `ws_delivery_success` | Gönderilen pinglerin başarı oranı. | Gauge | Prometheus (Edge node exporter) | ≥ %99.3 | `runbook/offline-edge-recovery.md` |
| `ws_connection_count` | Aktif WS bağlantı sayısı. | Gauge | Prometheus (Gateway exporter) | 0.8x – 1.2x kapasite bandı | `observability/capacity-journal.md` |
| `no_motion_ack_seconds` | Hareketsizlik alarmının kapanma süresi. | Histogram | Prometheus Alertmanager webhook | ≤ 60 sn (ortalama) | `runbooks/tracking/no-motion-alert.md` |

## Kural Motoru & Kuyruklar

| Metrik Kodu | Açıklama | Tip | Kaynak | SLO Hedefi | Runbook / Aksiyon |
| --- | --- | --- | --- | --- | --- |
| `rule_engine_queue_latency` | Mesaj kuyruğunun gecikmesi. | Gauge | Prometheus (`queue_latency_seconds`) | ≤ 5 sn | `runbook/rule-engine-hotfix.md` |
| `notification_delivery_rate` | Bildirim aksiyonlarının başarı oranı. | Counter | Prometheus + webhook teslim raporları | ≥ %99.5 | `docs/threat-program/README.md` |
| `rule_engine_failure_count` | Başarısız aksiyon sayısı. | Counter | Prometheus (`rule_failures_total`) | 0 | `security/threat-intel-register.md` eskalasyonu |

## Veri & Depolama Katmanı

| Metrik Kodu | Açıklama | Tip | Kaynak | SLO Hedefi | Runbook / Aksiyon |
| --- | --- | --- | --- | --- | --- |
| `db_replica_lag_seconds` | Replika gecikmesi. | Gauge | MySQL performance schema | ≤ 3 sn | `runbook/data-restore.md` |
| `backup_job_duration` | Yedekleme işinin süresi. | Histogram | Backup orchestration logs | ≤ 120 dk | `dr/exercises/README.md` doğrulaması |
| `storage_utilization` | Depolama kullanımı. | Gauge | Node exporter | ≤ %75 | `infra/rate-limit.tf` ile ölçeklendirme değerlendirmesi |

## İzleme & Alerting Hijyeni

| Metrik Kodu | Açıklama | Tip | Kaynak | SLO Hedefi | Runbook / Aksiyon |
| --- | --- | --- | --- | --- | --- |
| `alert_fatigue_index` | Son 7 günde tekrar eden (ack sonrası 1 saat içinde dönen) alarm yüzdesi. | Gauge | Prometheus Alertmanager API | ≤ %10 | `observability/reviews/2024-07-opscenter.md` aksiyon planı |
| `dashboard_freshness_hours` | Dashboard verisinin güncellik süresi. | Gauge | Grafana metadata API | ≤ 1 saat | `observability/dashboards/opscenter.json` sürüm kontrolü |
| `log_ingest_delay` | Log pipeline gecikmesi. | Gauge | OpenSearch ingestion metrics | ≤ 30 sn | `runbook/incident-response.md` |

> _Not: Yeni metrik talepleri `docs/rfc/` sürecinden geçmeli ve katalogda açıklama + sahiplik ile yer almalıdır._
