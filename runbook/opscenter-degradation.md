# OpsCenter Degrade Mod Runbook

| Alan | Bilgi |
| --- | --- |
| İlgili Fazlar | Faz 4 (Canlı Takip), Faz 6 (OpsCenter), Faz 7 (Kural Motoru), Faz 11 (DevOps) |
| Son Gözden Geçirme | 2024-07-08 |
| Sorumlu Takım | OpsCenter Platform Ekibi |

## Amaç ve Kapsam
OpsCenter’ın kritik bileşenlerinde kısmi kesinti yaşandığında saha ekiplerinin asgari
operasyon kabiliyetini korumak için degrade modunun nasıl etkinleştirileceğini tanımlar.
Harita, alarm konsolu ve komuta kısa yolları minimum fonksiyonla çalışmaya devam etmelidir.

## Tetikleyiciler
- BBOX API yanıt süresi 3 dakikadan uzun veya hata oranı %20’yi aşar.
- WebSocket canlı takip akışında 5 dakikadan fazla mesaj kaybı.
- OpsCenter UI erişiminde `HTTP 5xx` artışı (Prometheus uyarısı `opscenter_ui_5xx`).

## Ön Koşullar
1. En son statik harita paketleri (`PMTiles`) cache dizininde hazır.
2. `docs/runbook/README.md` rehberine uygun degrade konfigürasyon dosyaları güncel.
3. Alarm konsolu için manuel görev atama listeleri `ops/weekly-ops-briefing.md` dosyasında.

## Adımlar
### 1. Durum Analizi (0–10 dk)
1. Prometheus uyarılarını doğrula, hata kaynağını (`API`, `WS`, `UI`) sınıflandır.
2. `observability/capacity-journal.md` üzerinde ilgili metrikleri not al.
3. OpsCenter lideri degrade mod ilan eder ve `communications/public/STATEMENT_TEMPLATE.md`
   üzerinden iç paydaşlara bilgilendirme gönderir.

### 2. Degrade Modu Aktifleştirme (10–25 dk)
1. CDN üzerinde kayıtlı statik harita `feature-toggle=map-static` flag’iyle yüklenir.
2. Canlı takip WebSocket akışı kapatılıp `tracking` katmanından son bilinen konumlar REST
   API ile 60 saniyede bir çekilir.
3. Alarm konsolu akışı `kural motoru` bildirim kuyruğuna aktarılır ve `runbook/incident-response.md`
   gereği kritik alarmlar manuel işlenir.

### 3. Alternatif İşleyiş (25–90 dk)
1. Görev atamaları `forms/incident.json` şemasına göre manuel olarak kaydedilir.
2. Hareketsizlik alarmı tetiklenirse saha ekipleriyle telefon teyidi yapılır.
3. OpsCenter operatörleri `reports/performance/2024-06-performance.md` hedeflerini
   izler, anlık SLA sapmalarını `feedback-tracker.md`ye işler.

### 4. Toparlanma (90–150 dk)
1. Sorun giderildikten sonra bileşenler sırayla devreye alınır (`API` → `WS` → `UI`).
2. Canlı akış doğrulaması için 3 örnek görev seçilir ve hareketli ekiplerden ping alınır.
3. Degrade moddan çıkış duyurusu ilgili kanallar üzerinden yapılır.

## İletişim
| Rol | Kanal | Not |
| --- | --- | --- |
| OpsCenter Lideri | Signal + PagerDuty | Degrade duyurusu |
| Teknik Operasyon | Slack #opscenter-core | Teknik çözüm planı |
| Saha Ekipleri | Telefon + WhatsApp Broadcast | Görev/güzergâh paylaşımı |
| Yönetim | Haftalık rapor | SLA sapmaları |

## Başarı Kriterleri
- Degrade mod etkinleştirme ≤ 15 dk.
- Kritik alarm kaçırma oranı %0.
- OpsCenter operatör geri bildirim skoru ≥ 4/5.

## Sonrası
1. `postmortem/initial.md` şablonuna kayıt aç.
2. `docs/tests/README.md`deki E2E testleri tekrar çalıştır.
3. `CHANGELOG.md` dosyasına olay özetini ekle.

## Referanslar
- `docs/governance/2024-06-strategy-board.md`
- `analytics/README.md`
- `open-data/releases/README.md`
- `policies/task-assignment.md`
