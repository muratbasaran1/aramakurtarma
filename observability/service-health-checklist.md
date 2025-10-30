# Servis Sağlık Kontrol Listesi

Faz 11 gereği her vardiya başında ve kritik yayınlardan sonra servislerin sağlığını aşağıdaki adımlarla doğrulayın. Tüm bulguları OpsCenter vardiya notlarına ve `observability/capacity-journal.md` dosyasına işleyin.

## Kontrol Adımları

| Adım | Sorumlu | Doğrulama | Kaynak |
| --- | --- | --- | --- |
| API latency | SRE | `ApiLatencyP99High` alarmı pasif, dashboard P99 < 1.5s | Grafana: `opscenter-latency` |
| WebSocket oturumu | OpsCenter Vardiya Lideri | Aktif bağlantı sayısı son 30dk ortalamasının %80-120 aralığında | Grafana: `tracking-overview` |
| Kural motoru kuyruğu | Backend On-call | Kuyruk gecikmesi < 5s, `RuleEngineQueueLatencyHigh` alarmı yok | Prometheus Query |
| Hareketsizlik alarmı | Güvenlik Görevlisi | `NoMotionUnacknowledged` alarmı yok, son 24 saatte tüm alarmlar kapanmış | Alarm Konsolu |
| Veritabanı replikasyonu | DBA | `DatabaseReplicaLag` alarmı yok, gecikme < 3s | pg_stat_replication |
| Backup doğrulama | DBA | Son backup job “success”; `runbook/data-restore.md` quick check | Backup Dashboard |
| Offline kuyruk | Saha Operasyon | Edge kuyruk uzunluğu < 50 işlem | Offline Edge İzleme |

## Günlük Kayıt

- Her kontrol sonrası sonucu **Geçti / Uyarı / Kaldı** olarak işaretleyin.
- “Uyarı” ve “Kaldı” durumlarında ilgili runbook tetiklenmeli ve `CHANGELOG.md` / `docs/tests/latest` üzerinde not düşülmelidir.
- Yanlış pozitif görülürse alarm eşiği için RFC açın ve `observability/alerts/README.md` içerisinde değerlendirildi olarak işaretleyin.

## Haftalık Rapor

- Vardiya lideri, haftalık operasyon toplantısında trendleri paylaşır.
- OpsCenter performans benchmark raporları (`analytics/benchmark/`) ile kıyaslanarak kapasite planlama güncellenir.

## İlgili Belgeler

- `observability/alerts/opscenter.yml`
- `runbook/opscenter-degradation.md`
- `docs/tests/README.md`
- `analytics/benchmark/2024-07-10-opscenter.md`
