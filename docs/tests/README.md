# Test Dokümantasyonu

TUDAK Afet Yönetim Sistemi için test katmanları, kapsama hedefleri ve raporlama döngülerini bu klasörde saklayın. Her test çalıştırması, hangi faz gereksinimini doğruladığını ve hangi ortamlarda koştuğunu net biçimde belirtmelidir.

## Test Katmanları

| Katman | Açıklama | Sorumlu Ekip | Minimum Sıklık | Artefakt |
| --- | --- | --- | --- | --- |
| Unit | Laravel hizmetleri, veri doğrulama kuralları ve yardımcı sınıfların izolasyon testleri. | Backend Geliştiricileri | Her commit | `reports/unit/<tarih>.json` |
| Feature | REST, WebSocket ve Blade/Vue uçlarının işlevsel akışları. | Uygulama Ekibi | Her PR | `reports/feature/<tarih>.md` |
| Integration | Harita motoru, SMS/e-posta/push entegrasyonları, tenant izolasyonu. | Entegrasyon Ekibi | Haftalık | `reports/integration/<tarih>.md` |
| E2E | QR yoklama, hareketsizlik alarmı, OpsCenter alarm eskalasyonu. | QA Ekibi | Release adayı | `reports/e2e/<sürüm>.md` |
| Load | 10k WS istemci ve 1k ping/s hedefleri. | Performans Ekibi | Aylık | `reports/load/<tarih>.md` |
| Security | Pen test, statik/dinamik analiz ve yetki sızması kontrolü. | Güvenlik Ekibi | Çeyreklik | `reports/security/<tarih>.md` |

## Kabul Ölçütleri

1. **Faz doğrulama**: Her test raporunda ilgili faz referansı (`Faz 4 — Canlı Takip`, `Faz 7 — Kural Motoru`) yer almalıdır.
2. **Çıkış kriterleri**: Prod deploy öncesi en az bir E2E, bir load ve bir security testi “geçti” durumunda olmalıdır.
3. **Hata takibi**: Başarısız testler `tech-debt/backlog.csv` veya `security/vuln-register.csv` dosyalarına referans verecek şekilde issue oluşturur.

## Raporlama Döngüsü

- CI pipeline tamamlandığında `latest` dosyası güncellenir.
- Her ayın sonunda QA ekibi `reports/summary-YYYY-MM.md` dokümanını hazırlar.
- Kritik hatalar için `runbooks/` klasöründeki ilgili aksiyon adımları tetiklenir.

## Gözden Geçirme

- Test stratejisi, Faz 12 kapsamında üç ayda bir gözden geçirilir.
- Değişiklik önerileri `docs/rfc/` sürecinden geçtikten sonra uygulanabilir.

## Ek Kaynaklar

- `runbooks/rule-engine-hotfix.md` — başarısız kural motoru testlerinde izlenecek adımlar.
- `observability/capacity-journal.md` — load test sonuçlarıyla ilişkilendirilen kapasite metriği kayıtları.
- `docs/api-contracts/` — entegrasyon testleri için API sözleşme referansları.
