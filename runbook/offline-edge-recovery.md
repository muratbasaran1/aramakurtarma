# Offline Edge Recovery Runbook

| Alan | Bilgi |
| --- | --- |
| İlgili Fazlar | Faz 4 (Canlı Takip), Faz 9 (Offline & Edge), Faz 14 (Mobil & Saha) |
| Son Gözden Geçirme | 2024-07-08 |
| Sorumlu Takım | Saha Platformu & Edge Operasyon Ekibi |

## Amaç ve Kapsam
Bu runbook, saha edge düğümlerinin bağlantı kaybı yaşaması halinde veri kaybını önleyerek
merkez ile yeniden senkronizasyonu sağlar. Prosedür, PMTiles paketleri ve offline kuyruk
politikalarının (Faz 4 ve Faz 9) eksiksiz uygulanmasını garanti eder.

## Tetikleyiciler
- Edge node sağlık kontrolü (`/healthz`) 3 ardışık denetimde başarısız.
- Kuyruktaki yazma işlemi yaşı `> 6 saat`.
- Saha ekibinden alınan manuel bağlantı kaybı bildirimi.

## Ön Koşullar
1. Edge node fiziksel erişim logu `chain-of-custody.csv` dosyasında kayıtlı olmalı.
2. Son dağıtılan PMTiles paketi `open-data/releases/vYYYY.MM/` altında erişilebilir.
3. Edge cihazındaki saat senkronizasyonu manuel olarak kontrol edilmiş olmalı.

## Operasyon Akışı
### 1. Stabilizasyon (0–15 dk)
1. Edge node güç, ağ ve GPS anten bağlantıları doğrulanır.
2. Offline kuyruk servisleri (`queue-offline.service`) durdurulur.
3. Yerel `metrics.json` dosyası alınarak `analytics/voice-of-customer/` klasörüne
temp. rapor olarak yüklenir.

### 2. Veri Güvenliği (15–40 dk)
1. Yerel SQLite/LevelDB kuyruk dosyaları `tar.gz` ile paketlenir, SHA256 hash
   `chain-of-custody.csv`ye eklenir.
2. Şifreli yedek USB veya güvenli ağ üzerinden merkez depo (`edge-backup/`) altına alınır.
3. Hareketsizlik alarmı konfigürasyonları `config/feature-flags.php` ile geçici olarak devre dışı bırakılmaz; loglama devam eder.

### 3. Senkronizasyon Hazırlığı (40–70 dk)
1. Edge node’a güncel PMTiles paketleri yeniden yüklenir.
2. `config/environment/example.yaml` referansı ile bağlantı parametreleri doğrulanır.
3. Kuyrukta bulunan istekler `requests.queue` dosyasından türüne göre (incident/task/inventory)
   ayrıştırılarak `runbook/data-restore.md` ile çakışan kayıt olup olmadığı kontrol edilir.

### 4. Yeniden Bağlantı (70–110 dk)
1. `queue-offline.service` yeniden başlatılır ve `tail -f` ile hata logları izlenir.
2. İlk 100 istek manuel tetiklenir; başarısız olanlar `security-incidents/queue/` altına `pending` olarak kaydedilir.
3. 15 dk boyunca hata alınmazsa kuyruk otomatik moda alınır.

### 5. Doğrulama (110–150 dk)
1. OpsCenter haritasında edge bölgesine ait en son görev ve envanter kayıtlarının
görüntülendiği doğrulanır.
2. Mobil uygulama kullanıcılarına kısa test görevi atanır; QR taraması ile saha
   doğrulaması yapılır.
3. `feedback-tracker.md` dosyasına saha ekibinden alınan gözlemler eklenir.

## Eskalasyon Kriterleri
- 60 dk içinde bağlantı sağlanamazsa saha koordinatörü fiziksel destek için görevlendirilir.
- Kuyrukta 500’den fazla istek varsa veri platformu desteği devreye girer.
- GPS modülü arızalıysa yedek cihaz sevki yapılır.

## İletişim Planı
| Rol | Kanal | Not |
| --- | --- | --- |
| Saha Koordinatörü | Radyo + Signal | İlk müdahale |
| Edge Operasyon Lideri | Slack #edge-support | Durum raporu 30 dk arayla |
| OpsCenter | PagerDuty | Görev yeniden atamaları |
| Mobil Ürün Ekibi | Jira | Uygulama güncelleme gereksinimleri |

## Başarı Kriterleri
- Kuyrukta bekleyen tüm kayıtlar 2 saat içinde işlenmiş olmalı.
- Edge node sağlık kontrolleri tekrar yeşil (`HTTP 200`).
- Saha ekipleri tarafından ekstra veri kaybı raporlanmamalı.

## Referanslar
- `docs/mobile/` (varsa) ve Faz 14 gereksinimleri.
- `docs/dr/README.md`
- `analytics/README.md`
- `observability/capacity-journal.md`
