# Test Kapsam Matrisi

Bu tablo, faz hedeflerinin hangi test senaryolarıyla doğrulandığını gösterir. Her satır güncellendiğinde ilgili rapor dosyasına bağlantı verilmelidir.

| Faz | Kritik Hedef | Test Tipi | Senaryo Kodu | Son Rapor |
| --- | --- | --- | --- | --- |
| Faz 0 — Kararlar | Laravel 11 + PHP 8.3 uyumluluğu | Unit | `CFG-BOOT-001` | `reports/unit/2024-07-01.json` |
| Faz 1 — Güvenlik & Altyapı | Fortify kimlik doğrulama akışı | Feature | `SEC-AUTH-005` | `reports/feature/2024-07-01.md` |
| Faz 2 — Veri & Migrasyon | CHECK/FOREIGN KEY validasyonu | Integration | `DB-VAL-010` | `reports/integration/2024-07-02.md` |
| Faz 3 — Çekirdek Modüller | Görev → Envanter transaction senaryosu | E2E | `CORE-FLOW-021` | `reports/e2e/v0.8.0.md` |
| Faz 4 — Canlı Takip | 10 sn ping frekansı ve geofence alarmı | Load/E2E | `TRACK-LOAD-003` | `reports/load/2024-07-03.md` |
| Faz 5 — QR & Yoklama | Nonce anti-replay doğrulaması | Feature/E2E | `QR-SEC-004` | `reports/e2e/v0.8.0.md` |
| Faz 6 — OpsCenter | BBOX filtreli GeoJSON yanıtları | Integration | `MAP-API-002` | `reports/integration/2024-07-04.md` |
| Faz 7 — Kural Motoru | Hareketsizlik tetik zinciri | E2E/Security | `RULE-NOMOTION-001` | `reports/security/2024-Q3.md` |
| Faz 9 — Offline & Edge | Kuyruk senkronizasyonu + idempotency | Feature | `EDGE-QUEUE-007` | `reports/feature/2024-07-05.md` |
| Faz 14 — Mobil Uygulama & Saha | Offline-first görev akışı | E2E | `MOB-OFFLINE-009` | `reports/e2e/v0.8.0.md` |
| Faz 20 — Dashboard | KPI ön-toplam doğruluğu | Integration | `DASH-AGG-003` | `reports/integration/2024-07-06.md` |
| Faz 22 — Siber Güvenlik | WAF rate limit bypass testi | Security | `SEC-WAF-002` | `reports/security/2024-Q3.md` |

> _Not: Eksik rapor bağlantıları için `TBD` ifadesi kullanılmalı, 7 gün içinde güncelleme yapılmalıdır._
