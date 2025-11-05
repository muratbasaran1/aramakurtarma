# CI/CD Kalite Kapıları

Faz 11 ve Faz 12 kapsamında tanımlanan CI/CD boru hattı, üretim ortamının güvenliğini ve stabilitesini korumak için belirli kalite kapıları içerir. Bu belge, her aşamada uygulanması gereken kontrolleri ve başarısızlık durumunda izlenecek adımları açıklar.

## Pipeline Aşamaları

1. **Ön Doğrulama (Pre-commit)**
   - Statik analiz (PHPStan/Psalm, ESLint) — bkz. `docs/engineering/static-analysis.md`
   - Gizli anahtar taraması
   - Lokal testlerin çalıştırılması
2. **Sürekli Entegrasyon (CI)**  
   - Birim/özellik testleri paralel çalıştırılır.  
   - Veritabanı migrasyonlarının dry-run kontrolü.  
   - Kod kapsam raporu (`docs/tests/matrix.md`) ile eşleştirme.
3. **Kalite Kapısı (Quality Gate)**  
   - Performans regresyon testleri (OpsCenter WS yükü, API BBOX sorguları).  
   - Güvenlik taraması (SAST/Dependency check).  
   - Gözlemlenebilirlik kontrolleri (yeni metrik/alert tanımları).
4. **Release Adayı (Staging)**  
   - QA checklist: QR yoklama, geofence alarm, offline kuyruk senaryoları.  
   - Tatbikat kayıtlarının güncellenmesi (`docs/tatbikat/`).  
   - Kullanıcı dokümantasyonu ve çeviri kontrolleri.
5. **Üretim Yayını**  
   - On-call bilgilendirmesi ve rollback planının doğrulanması.  
   - `release-notes/` ve `CHANGELOG.md` güncellemesi.  
   - Monitoring dashboard’larında (Grafana) release notu etiketi.

## Başarısızlık Durumları ve Aksiyonlar

| Aşama | Tipik Hata | Aksiyon |
| --- | --- | --- |
| Pre-commit | Statik analiz hatası | Geliştirici düzeltir, commit engellenir. |
| CI | Test başarısızlığı veya migrasyon çakışması | PR kırmızı kalır, root-cause analizi `docs/tests/README.md`ye işlenir. |
| Quality Gate | Güvenlik zaafiyeti veya performans regresyonu | PR bloke edilir, risk kaydı `governance/risk-register.csv`ye eklenir. |
| Staging | QA checklist maddesi başarısız | Release ertelenir, `docs/runbook/rule-engine-hotfix.md` gibi ilgili runbook kontrol edilir. |
| Production | Monitoring alarmı veya rollback | On-call prosedürü devreye alınır, postmortem başlatılır. |

## Raporlama ve İzlenebilirlik

- Pipeline sonuçları `observability/metrics-catalog.md`de tanımlı “Deployment Success Rate” metriğiyle takip edilir.
- Her başarısız kalite kapısı olayı `postmortem/initial.md` şablonuyla kayıt altına alınır.
- Haftalık operasyon brifinginde (`ops/weekly-ops-briefing.md`) son yayınların durumu gözden geçirilir.

## Süreklilik

Kalite kapıları yılda en az iki kez (Haziran/Aralık) gözden geçirilir. Yeni test katmanları veya araçlar eklendiğinde bu belge güncellenir ve README yönetişim bölümü referansları revize edilir.
