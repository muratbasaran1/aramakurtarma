# SLO Kayıt Defteri

Bu dosya, TUDAK Afet Yönetim Sistemi için Servis Seviyesi Hedefleri’nin (SLO) periyodik olarak nasıl izlendiğini ve ihlal durumlarında hangi aksiyonların alındığını kaydeder. Her satır, README’deki [Servis Seviyesi Hedefleri (SLO & SLA)](#servis-seviyesi-hedefleri-slo--sla) tablosuyla hizalanmalı ve gözden geçirme döngüsü tamamlandığında güncellenmelidir.

| Tarih | Servis | Hedef | Gerçekleşen | Durum | Aksiyon | Referans |
| --- | --- | --- | --- | --- | --- | --- |
| 2024-07-05 | OpsCenter Web Uygulaması | Çalışma süresi %99.7 | %99.82 | ✅ | Rota cache TTL 15 dakikadan 10 dakikaya düşürüldü. | `analytics/benchmark/2024-07-10-opscenter.md` |
| 2024-07-05 | WebSocket Canlı Takip | Paket teslim ≥ %99.3 | %99.1 | ⚠️ | Edge node tamponu artırıldı, `observability/alerts/opscenter.yml` eşikleri güncellendi. | `observability/capacity-journal.md` |
| 2024-07-05 | Mobil Offline Kuyruk Servisi | Kuyruk boşaltma ≤ 5 dk (P90) | 6.2 dk | ❌ | `runbook/offline-edge-recovery.md` uygulandı, saha ekibi yeniden eğitime alındı. | `docs/tatbikat/2024-06-report.md` |
| 2024-07-12 | Kural Motoru Bildirimleri | Teslim ≥ %99.5 | %99.7 | ✅ | Ek aksiyon yok; alarm yanlış pozitif analizi `observability/reviews/2024-07-opscenter.md` dosyasına işlendi. | `observability/alerts/opscenter.yml` |
| 2024-07-12 | Yedekleme & Geri Yükleme | Tamamlama ≤ 2 saat | 1.6 saat | ✅ | `dr/exercises/regional-dc-202406.md` tatbikatı başarıyla tamamlandı. | `runbook/data-restore.md` |

> _Durum sütunu: ✅ (hedef karşılandı), ⚠️ (hedefe yakın uyarı), ❌ (hedef kaçırıldı)._ 
