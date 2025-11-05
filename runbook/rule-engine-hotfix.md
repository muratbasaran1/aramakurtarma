# Kural Motoru Hotfix Runbook

| Alan | Bilgi |
| --- | --- |
| İlgili Fazlar | Faz 4 (Canlı Takip), Faz 7 (Kural Motoru & Bildirimler), Faz 11 (DevOps) |
| Son Gözden Geçirme | 2024-07-08 |
| Sorumlu Takım | Bildirim Platformu Ekibi |

## Amaç ve Kapsam
Kural motorunda tespit edilen kritik hataların (yanlış tetik, teslim edilemeyen bildirim,
performans düşüşü) üretim ortamında servis kesintisine yol açmadan giderilmesi.
Hotfix, normal release takviminden bağımsız yürütülür fakat tüm değişiklikler sonrasında
RFC sürecine raporlanır.

## Tetikleyiciler
- `rule-engine` pod’larında CPU > %80 ve kuyruk gecikmesi > 120 sn.
- Yanlış pozitif hareketsizlik alarmı sayısı 15 dk içinde ≥ 5.
- SMS/e-posta teslim raporlarında hata oranı %10’u aşıyor.

## Ön Koşullar
1. `docs/rfc/` içinde ilgili değişiklik için taslak başlatılmış olmalı.
2. `config/feature-flags.php` ve `config/limit-profile.yml` sürümleri kayıtlı.
3. Son regresyon test raporu `docs/tests/latest` erişilebilir durumda.

## Adımlar
### 1. Sorun Analizi (0–20 dk)
1. İlgili kural seti `docs/threat-program/README.md` veya `policies/task-assignment.md`
   üzerinden tespit edilir.
2. Yanlış tetik kayıtları `security/threat-hunt/2024-06.md` ve `feedback-tracker.md`
   ile karşılaştırılır.
3. Uygulanacak hotfix kapsamı (şablon, koşul veya aksiyon) belirlenir.

### 2. Hazırlık (20–40 dk)
1. Mevcut kural JSON’ları `docs/data-sharing/policy.md` formatına göre yedeklenir.
2. `docs/tests/README.md`de belirtilen dry-run test veri seti hazırlanır.
3. Deploy pipeline’ı için `release-notes/README.md` altına kısa giriş eklenir.

### 3. Hotfix Uygulaması (40–70 dk)
1. İlgili kural `PUT /rules/{id}` API çağrısı ile güncellenir veya feature flag devreye alınır.
2. Dry-run modunda `simulate=true` parametresiyle 10 örnek tetik çalıştırılır.
3. Başarılı sonuç sonrası üretim kuyruğu kademeli açılır (SMS → push → webhook).

### 4. Doğrulama (70–110 dk)
1. `analytics/README.md` ve `analytics/voice-of-customer/` geri bildirimleri izlenir.
2. OpsCenter alarm konsolu 30 dk boyunca gözlenir; manuel doğrulama yapılır.
3. `reports/performance/2024-06-performance.md`deki KPI eşikleri karşılaştırılır.

### 5. Kayıt ve İzleme (110–150 dk)
1. `docs/changelog/README.md` altına hotfix kaydı eklenir.
2. `CHANGELOG.md` dosyasında ilgili sürüm notu güncellenir.
3. RFC outcome taslağı hazırlanır ve 24 saat içinde yayınlanır.

## İletişim Planı
| Rol | Kanal | Bildirim |
| --- | --- | --- |
| Bildirim Platformu Lideri | Slack #rule-engine | Başlangıç ve kapanış |
| OpsCenter Operasyon | PagerDuty | Manuel alarm teyidi |
| Güvenlik | Signal | Yanlış tetik doğrulaması |
| Ürün Sahibi | E-posta | Etki özeti |

## Başarı Kriterleri
- Yanlış tetik oranı ≤ %1.
- Kuyruk gecikmesi ≤ 60 sn.
- Kullanıcı şikayetlerinde azalma (24 saat içinde yeni kayıt yok).

## Referanslar
- `docs/tests/README.md`
- `docs/changelog/README.md`
- `analytics/voice-of-customer/README.md`
- `security/threat-intel-register.md`
