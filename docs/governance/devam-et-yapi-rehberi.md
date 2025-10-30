# Devam Et Yapıları Rehberi

Bu rehber, dokümantasyon boyunca tekrar eden “devam et” genişletmeleriyle oluşturulan klasör ve kayıt yapılarını Türkçe olarak özetler. Amaç; yeni katılan ekip üyelerinin yönetişim artefaktlarının nerede tutulduğunu hızlıca anlamasını sağlamak ve her yapının hangi faz çıktısını desteklediğini netleştirmektir.

## Dizin Genel Bakışı

| Yapı | Konum | Açıklama | İlişkili Faz/Süreç |
| --- | --- | --- | --- |
| Küresel Runbook’lar | `runbook/` & `runbooks/` | Kritik olaylara yönelik adım adım talimatlar ve spesifik senaryolar için alt klasörler. | Faz 1, 3, 6, 7, 11 |
| Gözlemlenebilirlik Kataloğu | `observability/` | Metrik sözlüğü, alarm kuralları, dashboard JSON’ları ve inceleme kayıtları. | Faz 6, 11, 12 |
| Dijital İkiz Çalışmaları | `digital-twin/` | Senaryo tanımları, veri kataloğu ve doğrulama checklist’leri. | Faz 16, 23 |
| Güvenlik & Tehdit Programı | `docs/threat-program/` & `security/` | Tehdit akışları, playbook’lar, raporlar ve zafiyet kayıtları. | Faz 1, 7, 22 |
| Uyum & Denetim Kayıtları | `compliance/` & `docs/audit/` | Denetim raporları, kontrol listeleri ve eğitim/sertifikasyon kayıtları. | Faz 11, 21 |
| Siber Sigorta Programı | `docs/governance/cyber-insurance.md` & `compliance/finance/` | Sigorta teminat planları, poliçe notları ve hasar tatbikat kayıtları. | Faz 20, 21 |
| Açık Veri & Analitik | `open-data/`, `analytics/` | Yayınlanmış veri setleri, benchmark raporları ve müşteri geri bildirimleri. | Faz 8, 20 |
| Topluluk & Refah Programları | `docs/community/`, `community/`, `docs/wellbeing/`, `hr/` | Katılım logları, refah checklist’leri ve iletişim rehberleri. | Faz 13, 17 |
| Dayanıklılık & Kaos Çalışmaları | `resilience/`, `infra/`, `ops/oncall/` | Dayanıklılık yol haritaları, deney raporları ve vardiya yönetimi artefaktları. | Faz 6, 9, 22 |
| Mühendislik Uygulama Rehberleri | `docs/engineering/` | Kod inceleme, dal stratejisi ve pipeline kalite kapılarını standartlaştıran rehberler. | Faz 0, 11, 12 |
| Kodlama & Analiz Standartları | `docs/engineering/coding-standards.md`, `docs/engineering/static-analysis.md`, `docs/engineering/dependency-management.md` | Kod kalitesi, statik analiz ve bağımlılık yönetimi politikalarını detaylandırır. | Faz 0, 3, 11, 12, 22 |
| Yerel Geliştirme Rehberi | `docs/engineering/local-development.md` | Yerel ortam kurulumu, günlük komutlar ve sorun giderme checklist’ini standartlaştırır. | Faz 0, 11, 12 |
| Ortam Değişkeni Şablonları | `.env.example`, `docs/engineering/env-management.md`, `config/environment/` | `.env` şablonları, ortam YAML dosyaları ve gizli anahtar rotasyon kurallarını açıklar. | Faz 0, 1, 11 |
| Kod Kalite Konfigürasyonları | `.php-cs-fixer.dist.php`, `phpcs.xml`, `phpstan.neon.dist`, `psalm.xml`, `.eslintrc.cjs`, `stylelint.config.cjs`, `docs/engineering/tooling-configuration.md` | Kod kalite araçlarının proje kapsamındaki çalışma kurallarını ve script eşleşmelerini tanımlar. | Faz 0, 11, 12 |
| Laravel Backend Uygulaması | `backend/` | Laravel 11 tabanlı API & görev motoru iskeleti; tenant, birim, kullanıcı, olay, görev ve envanter tabloları ile örnek seed verisini içerir. | Faz 3, 4, 6, 11 |
| OpsCenter Web Paneli | `backend/app/Http/Controllers/OpsCenterController.php`, `backend/resources/views/opscenter.blade.php` | Tenant seçimiyle olay/görev/envanter özetlerini gösteren hafif web arayüzü; Faz 6 OpsCenter gereksinimlerini prototip olarak doğrular. | Faz 4, 6 |
| Tenant Kapsamı Bileşenleri | `backend/app/Tenant/`, `backend/app/Http/Middleware/EnsureTenant.php`, `backend/app/Models/Concerns/BelongsToTenant.php` | HTTP isteklerinden tenant çözümleyip modelleri otomatik `tenant_id` filtresiyle sınırlar; multi-tenant güvenlik gereksinimlerini pekiştirir. | Faz 2, 3, 6 |
| Statik Analiz Stubları | `stubs/laravel-model.stubphp` | Psalm, `backend/app/` içindeki çoklu tenant modellerini tararken Laravel Eloquent bağımlılıklarının sembolik tanımlarını sağlar; factory jenerik uyarıları bilgi seviyesine çekilmiştir ve kalite süiti backend kodunu kapsayacak şekilde tamamlanır. | Faz 11, 12 |
| PR Kontrol Araçları | `tools/check-binary-files.sh`, `docs/engineering/pr-checklist.md` | PR açmadan önce ikili dosya taraması ve kalite adımlarının tamamlandığını doğrular. | Faz 0, 11, 12 |
| Kalite Suite Otomasyonu | `tools/run-quality-suite.sh`, `docs/engineering/quality-suite.md` | Kod kalite kontrollerini tek komutta koşturur; eksik vendor veya frontend bağımlılıklarında `composer install` ve `npm install --no-audit --progress false` komutlarını otomatik tetikler. | Faz 0, 11, 12 |
| PHP Kalite Bağımlılıkları | `composer.json`, `composer.lock` | Kod kalite araçlarının Composer ile kurulmasını ve `composer run quality` komutunu standartlaştırır. | Faz 0, 11, 12 |
| Frontend Kalite Bağımlılıkları | `package.json`, `package-lock.json` | ESLint/Stylelint bağımlılıklarını sabitler; kalite suite `npm install --no-audit --progress false` komutuyla otomatik kurar. | Faz 0, 11, 12 |
| Veri Yönetimi & Maskeleme | `governance/data-quality-dashboard/`, `open-data/releases/` | Veri kalitesi raporları, maskeleme checklist’leri ve paylaşım politikaları. | Faz 2, 21 |

## Kategori Bazlı Açıklamalar

### 1. Runbook Yapıları
- `runbook/` kök klasörü fazlar arası ortak prosedürleri içerir (ör. `incident-response.md`, `data-restore.md`).
- `runbooks/` alt klasörü ise senaryoya özel rehberleri (ör. kimlik bilgisi ihlali, hareketsizlik alarmı) faz/servis bazında gruplar.
- Her runbook; tetikleyici olay, iletişim planı, başarı ölçütü ve çapraz doküman bağlantılarıyla standartlaştırıldı.

### 2. Gözlemlenebilirlik Ekosistemi
- `observability/metrics-catalog.md` ile metrik isimleri, sorgular ve sahiplik bilgileri kayıt altına alınır.
- `observability/alerts/opscenter.yml` dosyası Prometheus kural setlerini içerir; karşılık gelen aksiyon adımları runbook’lara bağlanır.
- `observability/reviews/` dizini aylık gözden geçirme tutanaklarını saklar; raporlarda alınan aksiyonlar kapasite günlüğüne işlenir.

### 3. Dijital İkiz ve Senaryo Yönetimi
- `digital-twin/datasets/catalog.yaml` sahte veri setlerini, güncellik tarihlerini ve kaynak fazlarını listeler.
- `digital-twin/scenarios/` altındaki dosyalar (örn. `earthquake-ops.md`) tatbikat sırasında yürütülecek olay akışlarını tanımlar.
- `digital-twin/validation/checklist.md` gerçek veriye dokunmadan önce yapılması gereken doğrulama kontrollerini adım adım belirtir.

### 4. Güvenlik ve Tehdit Programı
- `docs/threat-program/` klasöründe istihbarat beslemeleri, lessons learned kayıtları ve yüksek risk playbook’ları bulunur.
- `security/threat-intel-register.md` dosyası aktif beslemeleri, öncelik puanlarını ve sorumluları listeler.
- `security/vuln-register.csv` ve `security/breach-reports/` klasörleri zafiyet triage’ı ve olay raporlarını arşivler.
- `security/chain-of-custody.md` dijital delil akışını, hash doğrulamalarını ve transfer kayıtlarını yönetir.

### 5. Uyum, Denetim ve Eğitim Kayıtları
- `compliance/` altındaki dizinler (ISO, finans, drill) düzenleyici gereksinimlerin kanıt setlerini barındırır.
- `docs/audit/` ve `audit/findings-tracker.csv` denetim bulgularını, kapanış tarihlerini ve sorumlu ekipleri takip eder.
- `compliance/training/` ve `training/attendance.csv` çalışanların zorunlu eğitim/sertifika durumunu izler.
- `hr/training-tracker.csv` yöneticilerin yenileme tarihlerini ve eğitim planlarını güncel tutması için kullanılan ana kayıttır.
- `docs/governance/cyber-insurance.md` sigorta teminat süreçlerini, `compliance/finance/` ise poliçe yenileme raporlamasını yönetir.
- `docs/contracts/registry.csv` sözleşme yaşam döngüsü takibini sağlar; paydaş ilişkileri `integration/partner-matrix.csv` dosyasında eşlenir.

### 6. Açık Veri ve Analitik Yayınları
- `open-data/releases/` klasörü maskelenmiş veri paketlerinin sürüm bazlı dokümantasyonunu içerir.
- `analytics/benchmark/` altında performans raporları, test metodolojisi ve sonuç özetleri tutulur.
- `analytics/voice-of-customer/` dizini saha ve kullanıcı geri bildirimlerinin segment bazlı analizlerini sağlar.

### 7. Topluluk, Refah ve Paydaş Yönetimi
- `community/engagement-log.csv` topluluk etkinlikleri ve görüşmelerinin tarihçesini saklar.
- `docs/wellbeing/` refah programı politikaları ve check-listelerini, `hr/wellbeing-program.md` ise uygulama adımlarını içerir.
- `communications/public/` klasörü medya açıklama şablonları ve kamuya açık durum güncellemeleri için kullanılır.

### 8. Dayanıklılık, On-call ve Kaos Tatbikatları
- `resilience/` klasörü dayanıklılık yol haritası, deney şablonları ve raporlarını içerir.
- `ops/oncall/` vardiya planları, devir teslim şablonları ve rota kayıtları ile operasyonel sürekliliği yönetir.
- `infra/rate-limit.tf` ve benzeri dosyalar altyapı limit profillerini ve kaos deneylerine hazırlık konfigürasyonlarını tanımlar.

### 9. Mühendislik Uygulama Rehberleri
- `docs/engineering/` klasörü kod inceleme, dal stratejisi, kodlama standartları ve pipeline kalite kapıları için hazırlanmış rehberleri barındırır.
- `docs/engineering/code-review.md` PR kontrollerini; `branching-model.md` Git akışını; `deployment-gates.md` kalite kapılarını; `coding-standards.md` kod stilini; `static-analysis.md` otomatik kontrolleri; `tooling-configuration.md` konfigürasyon dosyalarını; `dependency-management.md` ise paket yönetimini tanımlar.
- Laravel backend kodu `backend/` dizininde tutulur; artisan komutları, migrasyonlar ve modul iskeletleri bu klasörden yönetilir.
- `docs/engineering/local-development.md` yeni cihaz kurulumu, `.env` hazırlığı ve günlük komutlar için zorunlu checklist’i içerir; kalite suite ve PR kontrolleri tamamlanmadan merge talebi oluşturulamaz.
- `.env.example` ve `docs/engineering/env-management.md` ortam şablonlarının güncelliğini, gizli anahtar rotasyon ilkelerini ve audit kayıtlarının nasıl tutulacağını açıklar; değişiklikler `config/environment/` altındaki YAML dosyalarıyla birlikte takip edilir.
- `docs/engineering/pr-checklist.md`, `tools/check-binary-files.sh` ve `tools/run-quality-suite.sh` PR açılışından önce uygulanması gereken kontrolleri hem manuel hem otomasyonlu şekilde sunar; Codex tabanlı süreçlerin kesintisiz işlemesine yardımcı olur.
- `package.json` ve `package-lock.json` dosyaları frontend kalite araçlarını sabitler; kalite suite çalıştırıldığında eksik `node_modules` için `npm install --no-audit --progress false` komutu otomatik devreye girer.
- Sprint planlama, release hazırlığı ve postmortem süreçlerinde bu rehberlere atıf yapılması zorunludur; güncellemeler README sürüm tablosu ve `CHANGELOG.md` üzerinden izlenir.

### 10. Veri Kalitesi ve Maskeleme Standartları
- `governance/data-quality-dashboard/` veri bütünlüğü inceleme raporlarının özetini sunar.
- `open-data/releases/*/masking-checklist.md` her veri yayını için maskeleme ve anonimleştirme kontrollerini listeler.
- `data-quality/daily-report.json` otomatik kalite ölçümlerinin günlük çıktısını saklar.

## Kullanım Önerileri
1. Yeni bir artefakt eklerken ilgili klasörün README dosyasındaki standartları kontrol edin.
2. Runbook veya politika güncellemesi sonrası `CHANGELOG.md` ve `README.md` üzerindeki versiyon geçmişini güncelleyin.
3. Fazlar arası bağımlılıkları takip etmek için tablodaki “İlişkili Faz/Süreç” kolonundan yararlanın.
4. Denetim veya tatbikat hazırlıklarında bu rehberi referans alarak gerekli kayıtların eksiksiz olduğundan emin olun.

## İletişim
Yapılarda eksik veya güncel olmayan bir içerik fark ederseniz ilgili klasörün README dosyasında listelenen sorumlu kişi veya ekiple iletişime geçin. Genel öneriler için `feedback-tracker.md` dosyasındaki talimatları izleyebilirsiniz.
