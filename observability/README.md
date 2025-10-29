# Gözlemlenebilirlik Programı

TUDAK Afet Yönetim Sistemi için gözlemlenebilirlik mimarisi; metrik, log, iz (trace) ve olay (event) bileşenlerinden oluşan bütünleşik bir yapı olarak tasarlanır. Bu klasör, Faz 11 — DevOps & İzlenebilirlik kapsamında zorunlu olan izleme standartlarını, dashboard yapılarını ve alarm kurallarını içerir.

## Bileşenler

| Katman | Teknoloji | Kapsam | Sahiplik |
| --- | --- | --- | --- |
| Metrikler | Prometheus + Grafana | API yanıt süreleri, WebSocket bağlantıları, kuyruk gecikmesi, kaynak kullanımı | Observability Ekibi |
| Loglar | ELK/Opensearch | Audit log, uygulama hataları, kural motoru tetikleri | Uygulama & Güvenlik |
| İzler | OpenTelemetry | Kritik kullanıcı yolculukları (OpsCenter alarmı, QR yoklama) | Backend Ekibi |
| Olaylar | PagerDuty / Opsgenie | SOS, geofence ihlali, hareketsizlik alarmı, altyapı arızaları | SRE |

## Sorumluluk Matrisi

- **Observability Ekibi:** Dashboard bakımı, metrik şemalarının yönetimi, Prometheus ölçeklendirmesi.
- **SRE:** Alarm eşikleri, eskalasyon politikaları ve rota doğrulama (OpsCenter → On-call).
- **Uygulama Ekipleri:** Log zenginleştirme, trace etiketleme (tenant, incident_id), hata durumlarında runbook tetikleme.
- **Güvenlik Ekibi:** Güvenlik alarm kanalları (tehdit avı, IAM ihlalleri) ile Faz 22 uyumunun sağlanması.

## Artefaktlar

- `dashboards/` — Grafana panel tanımları ve sürüm notları.
- `alerts/` — Prometheus ve diğer alarm platformları için kural dosyaları.
- `capacity-journal.md` — Performans ölçümleri ve load test çıktılarını takip eden günlük.
- `service-health-checklist.md` — Servis durum değerlendirmesi için kontrol listesi.
- `metrics-catalog.md` — Metrik sahipliği, kaynak ve runbook eşleşmelerinin bulunduğu referans.
- `slo-register.md` — SLO sonuçlarının ve alınan aksiyonların kaydı.
- `reviews/` — Haftalık/çeyreklik gözden geçirme raporları ve aksiyon planları.

## Döngüler

1. **Günlük:** Kritik metrikler (API latency, WS concurrency) ve hata oranı incelemesi; OpsCenter vardiya toplantısında paylaşılır.
2. **Haftalık:** Alarm inceleme toplantısı; yanlış pozitifler için eşik ayarı, runbook güncellemeleri. Kararlar `observability/reviews/` klasörüne işlenir.
3. **Aylık:** Kapasite trend analizi; `observability/capacity-journal.md` güncellenir ve `analytics/benchmark/` raporlarıyla karşılaştırılır. SLO sonuçları `observability/slo-register.md` üzerinden raporlanır.
4. **Çeyreklik:** Faz 11 gereği izleme mimarisi gözden geçirilir, yeni faz çıktıları (örn. Faz 20 dashboard’ları) entegrasyona dahil edilir; metrik değişiklikleri `metrics-catalog.md` üzerinde sürümlenir.

## İlgili Belgeler

- `runbook/opscenter-degradation.md` — Dashboard alarmları “Kırmızı” olduğunda uygulanacak adımlar.
- `runbooks/opscenter/alarm-console-escalation.md` — Alarm eskalasyon zinciri.
- `docs/tests/matrix.md` — Load test senaryoları ile metrik eşleşmeleri.
- `docs/threat-program/playbooks/no-motion-high-risk.md` — Hareketsizlik alarmı ile güvenlik uyarılarının korelasyonu.
