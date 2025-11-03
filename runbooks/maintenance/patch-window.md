# Üretim Yaması Bakım Penceresi Runbook'u

## Amaç
Laravel, sistem bağımlılıkları ve altyapı bileşenleri için planlı yamaların güvenli şekilde uygulanması ve hizmet kesintisinin minimumda tutulması.

## Planlama Zamanlaması
- **Sıklık:** Aylık (her ayın 2. Pazartesi 00:00–02:00 UTC)
- **Onay:** Stratejik Yürütme Kurulu + Operasyon Koordinasyon Masası (en az 5 gün önce)
- **Ön Koşullar:** Faz 11 CI/CD raporları yeşil, migration guard temiz, rollback planı güncel

## Hazırlık Kontrolleri (T-24 Saat)
1. Güncel yamaların listesi `tech-debt/backlog.csv` ve güvenlik bültenleri ile karşılaştırılır.
2. Etkilenecek servislerin SLA/SLO hedefleri `README.md` üzerindeki tablolarla karşılanabilir olmalı.
3. Kullanıcı bildirim taslakları `communications/public/STATEMENT_TEMPLATE.md` üzerinden hazırlanır.
4. Rollback kontrol listesi `communication/rollback-checklist.md` ile senkronize edilir.
5. OpsCenter banner ve mobil push duyurusu `archive/push/README.md` prosedürüne göre programlanır.

## Adım Adım İşleyiş
1. **Freeze Başlat (T-60 dk)**
   - Deploy pipeline'larını "maintenance" moduna alın (`config/feature-flags.php` > `deploy_freeze` = true).
   - OpsCenter'da bakım uyarısı banner'ı yayınlayın.
2. **Ön Kontroller (T-10 dk)**
   - `observability/capacity-journal.md` üzerinden anlık yükü doğrulayın.
   - Veri tabanı replikasyon gecikmesinin ≤ 30 sn olduğunu teyit edin.
3. **Yama Uygulaması (T)**
   - Laravel bağımlılıkları: `composer update --with-all-dependencies` (önce staging doğrulaması).
   - Altyapı yamaları: Terraform `infra/rate-limit.tf` değişikliklerini `terraform apply` ile yayınlayın.
   - Veritabanı değişiklikleri: Migration guard onayı sonrası `php artisan migrate --force`.
4. **Doğrulama (T+30 dk)**
   - Otomatik test paketi: `php artisan test --group=smoke`.
   - OpsCenter ve mobil uygulama kritik akışlarını manuel test edin.
   - Tracking ping akışı için test cihazından deneme gönderin; hareketsizlik alarmının çalıştığını doğrulayın.
5. **Hizmete Açma (T+60 dk)**
   - Feature flag'leri normal moda alın, freeze'i kaldırın.
   - OpsCenter banner'ını kaldırıp post-mortem notlarını `ops/weekly-ops-briefing.md` taslağına girin.
6. **Geri Bildirim & Kayıt (T+90 dk)**
   - `docs/changelog/` altına yama özeti ekleyin.
   - SLA ihlali varsa `governance/data-quality-dashboard/README.md` grafikleri güncelleyin.
   - Etkinliğin sonuçlarını `docs/runbook/README.md` altına kısa not olarak işleyin.

## Başarı Kriterleri
- Planlanan bakım süresi aşılmadı (≤ 120 dk).
- Kritik servis KPI sapması yok (OpsCenter P95 ≤ 3 sn, Tracking ping gecikmesi ≤ 15 sn).
- Hata durumunda rollback < 15 dk içinde tamamlanabilir halde.

## İletişim Matrisi
| Paydaş | Kanal | Zamanlama |
| --- | --- | --- |
| Tenant operasyon liderleri | E-posta + OpsCenter banner | T-24 saat, T-15 dk, T+15 dk |
| İç ekip (DevOps, Güvenlik, Mobil) | Slack #incident, PagerDuty | T-30 dk, anlık |
| Yönetim | Haftalık rapor + bakım özeti | T+1 gün |

## Tatbikat ve Gözden Geçirme
- Her çeyrek, bakım penceresi simülasyonu düzenlenir; sonuçlar `docs/tatbikat/` altında raporlanır.
- Geri bildirimler `feedback-tracker.md` aracılığıyla toplanır ve bir sonraki bakım planına yansıtılır.
