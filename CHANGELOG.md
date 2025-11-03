# TUDAK Afet Yönetim Sistemi — Değişiklik Günlüğü

Bu dosya, uygulama ve dokümantasyon değişikliklerinin özetini tutmak için kullanılır.

## Kullanım Kuralları
- Her önemli değişiklik için tarih, sürüm veya git etiketi ve kısa açıklama ekleyin.
- İlgili faz veya yönetişim bölümüne bağlantı verin.
- Aynı sürüm altında birden fazla değişiklik olduğunda madde işareti kullanın.

## Taslak Örnek
### 2024-07-05
- README yönetişim bölümleri genişletildi.
- Açık veri paylaşımları için `open-data/` dizini oluşturuldu.
### 2024-07-08
- Operasyonel runbook’lar faz bazlı tetikleyici, iletişim ve kontrol listeleriyle güncellendi.
### 2024-07-09
- OpsCenter alarm eskalasyonu ve hareketsizlik müdahalesi için yeni runbook’lar eklendi.
- Dijital ikiz veri seti kataloğu, senaryo rehberi ve doğrulama checklist’i yayımlandı.
- Tehdit programı besleme, playbook ve rapor klasörleri içeriklerle dolduruldu.
### 2024-07-10
- Test yönetişim rehberi ve faz → senaryo kapsama matrisi yayınlandı.
- OpsCenter performans benchmark raporu oluşturuldu, kapasite günlüğü güncellendi.
- `analytics/benchmark/` dizini için şablon ve rapor yapısı tanımlandı.
### 2024-07-11
- Etik kurul ajandası için `docs/ethics/` klasörü ve inceleme takvimi yayımlandı.
- Personel refah programı belgeleri ve checklist’i (`docs/wellbeing/`, `hr/wellbeing-program.md`) eklendi.
- Topluluk geri bildirim yönetişimi ve katılım logları (`docs/community/`, `community/engagement-log.csv`) oluşturuldu.
### 2024-07-12
- Gözlemlenebilirlik programı için `observability/` rehberi, alarm kuralları ve servis sağlık checklist’i yayınlandı.
- OpsCenter dashboard tanımı güncellenerek benchmark ve runbook bağlantıları eklendi.
### 2024-07-13
- Metrik kataloğu (`observability/metrics-catalog.md`) ve SLO kayıt defteri (`observability/slo-register.md`) yayımlandı.
- Gözlem inceleme notları için `observability/reviews/` dizini ve ilk OpsCenter raporu oluşturuldu; kapasite günlüğü güncellendi.

### 2024-07-14
- Kaos mühendisliği programı planlandı ve ilk ağ bölünmesi tatbikatı kayda alındı.
- On-call vardiya yönetimi bölümü hazırlandı; handover şablonu ve rotasyon kayıtları eklendi.
### 2024-07-15
- `.gitattributes` ile `.ics` takvim dosyalarının metin olarak işlenmesi sağlandı; PR oluştururken yaşanan ikili dosya hatası giderildi.
### 2024-07-16
- “Devam et” talimatlarıyla oluşan dokümantasyon ve kayıt klasörlerini açıklayan rehber yayımlandı (`docs/governance/devam-et-yapi-rehberi.md`).
- README’ye özet tablo ve referans eklendi; sürüm tablosu güncellendi.
### 2024-07-17
- Siber sigorta programı detaylılaştırıldı (`docs/governance/cyber-insurance.md`) ve README bağlantısı eklendi.
- Delil zinciri işletim rehberi yayımlandı (`security/chain-of-custody.md`); yönetişim rehberi ve versiyon geçmişi güncellendi.
### 2024-07-18
- Mühendislik uygulama rehberleri için `docs/engineering/` klasörü oluşturuldu (kod inceleme, branching, CI/CD kalite kapıları).
- README yönetişim bölümü mühendislik standartlarıyla genişletildi; “Devam Et” rehberi yeni klasörü referanslayacak şekilde güncellendi.
### 2024-07-19
- Kodlama standartları, statik analiz ve bağımlılık yönetimi rehberleri yayımlandı (`docs/engineering/coding-standards.md`, `static-analysis.md`, `dependency-management.md`).
- README ve “Devam Et” rehberi yeni mühendislik dokümanlarıyla güncellendi; sürüm tablosuna v0.17 kaydı eklendi.
### 2024-07-20
- Kod kalite araç konfigürasyonları depoya eklendi (`.php-cs-fixer.dist.php`, `phpcs.xml`, `phpstan.neon.dist`, `psalm.xml`, `.eslintrc.cjs`, `stylelint.config.cjs`).
- `docs/engineering/tooling-configuration.md` rehberi yayımlandı; README, Devam Et rehberi ve mühendislik indeksine yeni bağlantılar eklendi.
### 2024-07-21
- PR otomasyonunun ikili dosya hatasına takılmaması için `.xlsx` ve `.pdf` placeholder'ları CSV/Markdown formatına dönüştürüldü; ilgili belgeler güncellendi.
### 2024-07-22
- PR öncesi kontrol listesi yayımlandı (`docs/engineering/pr-checklist.md`) ve README yönetişim bölümü güncellendi.
- `tools/check-binary-files.sh` ile ikili dosya taraması için komut satırı aracı eklendi; Devam Et rehberinde yeni yapı kaydı oluşturuldu.

### 2024-07-23
- `tools/run-quality-suite.sh` ile kalite kontrollerini tek komutta koşturan script eklendi.
- `docs/engineering/quality-suite.md` rehberi yayımlandı; README ve Devam Et rehberine yeni otomasyon akışı işlendi.

### 2024-07-24
- PHP kalite araçları için Composer dev bağımlılıkları (`composer.json`, `composer.lock`) eklendi ve `vendor/bin` komutları standartlaştırıldı.
- `.php-cs-fixer.dist.php`, `phpcs.xml`, `phpstan.neon.dist` ve `psalm.xml` dosyaları mevcut dizinlere göre dinamik kapsamla güncellendi.
- Mühendislik rehberleri, PR checklist’i ve Devam Et yapısı Composer kurulumu ve yeni kalite akışına göre revize edildi; README sürüm tablosuna v0.21 kaydı eklendi.

### 2024-07-25
- `tools/run-quality-suite.sh` script’i PHP araçları bulunmadığında `composer install --no-ansi --no-interaction --no-progress --prefer-dist` komutunu otomatik çalıştıracak şekilde güncellendi.
- README, PR checklist’i, kalite suite rehberi ve Devam Et yapı tablosu yeni otomatik Composer kurulum davranışını yansıtacak şekilde revize edildi.

### 2024-07-26
- `docs/engineering/local-development.md` ile yerel geliştirme kurulum adımları, günlük komutlar ve sorun giderme notları standartlaştırıldı.
- README, mühendislik indeksleri ve Devam Et yapı rehberi yeni yerel geliştirme rehberine referans verecek şekilde güncellendi.
### 2024-07-27
- `.env.example` şablonu depoya eklendi; ortam değişkenleri için standart değerler ve entegrasyon placeholder'ları tanımlandı.
- `docs/engineering/env-management.md` rehberi yayımlanarak `.env`/YAML yapılandırmaları, rotasyon ve audit süreçleri belgelenmiş oldu.
- README, mühendislik indeksleri ve Devam Et rehberi yeni ortam yönetimi yönergelerini referanslayacak şekilde güncellendi; sürüm tablosuna v0.24 kaydı eklendi.

### 2024-07-28
- `package.json` ve `package-lock.json` ile ESLint/Stylelint bağımlılıkları tanımlandı, `npm run lint` komutu tek noktadan erişilebilir hale getirildi.
- `tools/run-quality-suite.sh` script’i `npm install --no-audit --progress false` komutunu otomatik tetikleyerek frontend lint araçlarını hazırlar hale getirildi.
- README, kalite suite, tooling ve PR rehberleri yeni otomasyon akışını belgeledi; Devam Et rehberine frontend bağımlılık kaydı eklendi ve sürüm tablosuna v0.25 işlendi.

### 2024-07-29
- `backend/` dizininde Laravel 11 tabanlı uygulama iskeleti oluşturuldu; `.env` şablonları ve gitignore kuralları backend yapısına göre güncellendi.
- Kod kalite konfigürasyonları, run-quality suite script’i ve PR/local development/env yönetimi rehberleri backend Composer kurulumlarını otomatikleyecek şekilde revize edildi.
- README, Devam Et rehberi ve sürüm geçmişi yeni kod tabanı yapısını ve kalite akışını yansıtacak şekilde güncellendi.
### 2024-07-30
- Tenant, unit, user, incident, task ve inventory tablolarını içeren çoklu tenant çekirdek şema oluşturuldu; uzamsal alanlar için SQLite fallback’leri tanımlandı.
- Eloquent modelleri, ilişkiler ve factory/seed zinciri güncellenerek faz 2–3 akışları için örnek veri üretimi sağlandı.
- Backend README’si proje bağlamına uyarlanarak şema özeti, kurulum adımları ve sonraki faz planlarıyla genişletildi.
### 2024-07-31
- TenantContext ve EnsureTenant middleware’i eklenerek HTTP isteklerinin tenant bazlı ayrıştırılması ve modellerin otomatik tenant kapsamı sağlandı.
- BelongsToTenant trait’i ile incident, task, inventory, unit ve user modelleri global tenant filtresi ve otomatik `tenant_id` atanmasıyla güçlendirildi.
- PHPUnit konfigürasyonu SQLite bellek veritabanına geçirildi, tenant kapsamı testleri yazıldı ve backend README/ana README güncellendi.

### 2024-08-01
- `psalm.xml` kapsamı `backend/app/` dizinini içerecek şekilde genişletildi; Laravel stubları güncel tenant kodunun taranmasını destekleyecek şekilde genişletildi.
- Factory jenerik uyarıları bilgi seviyesine çekildi, yeni stub tanımları ve dokümantasyon güncellemeleriyle Psalm çıktıları güçlendirildi.
- Statik analiz rehberleri ve Devam Et yapı tablosu Psalm kapsamındaki değişiklikleri yansıtacak biçimde revize edildi.

### 2024-08-02
- Çoklu tenant sorguları için `BelongsToTenant::forTenantQuery` yardımcı metodu eklendi; controller akışları yeni yardımcıyı kullanacak şekilde güncellendi ve tenant slug doğrulaması merkezî hale getirildi.
- Laravel bootstrap yapılandırması API rotalarını etkinleştirecek şekilde genişletildi; Psalm/PHPStan stubları dinamik sorgu kapatma (closure) ve JsonResource koleksiyonlarını destekleyecek biçimde güçlendirildi.
- Backend README’si ve statik analiz araçları yeni tenant yardımcıları ve genişletilen stubları açıklayacak şekilde güncellendi; kalite süitine eklenen bellek limiti ayarı korunarak taramalar başarılı çalıştırıldı.
- Tenant API’sine olay oluşturma ucu (`POST /api/tenants/{tenant}/incidents`) eklendi; GeoJSON doğrulaması ve benzersiz kod kontrolünü içeren `StoreIncidentRequest` oluşturuldu, backend README ve testler güncellendi.
- `PATCH /api/tenants/{tenant}/incidents/{incident}` ucu eklenerek olay başlığı, durum, öncelik ve GeoJSON alanlarının tenant doğrulamasıyla güncellenmesi sağlandı; `UpdateIncidentRequest` kapatma zamanı kurallarını ve kod benzersizliğini denetler hale getirildi.
- Çoklu tenant olay güncelleme akışı için feature testleri yazıldı; çapraz tenant erişimlerinin 404 dönmesi garanti altına alındı ve backend README ile ana README sürüm tablosu yeni özelliği referanslayacak şekilde güncellendi.
- Görev API’sine `POST /api/tenants/{tenant}/tasks` ve `PATCH /api/tenants/{tenant}/tasks/{task}` uçları eklendi; GeoJSON LineString rotası, çift onay zorunluluğu ve tarih tutarlılığı `StoreTaskRequest`/`UpdateTaskRequest` ile doğrulanıyor.
- TaskController güncellenerek rota dönüştürme yardımcıları ve tenant izolasyonu güçlendirildi; Task modeli `STATUSES` sabiti ve `route` cast’i ile tutarlı veri üretir hale getirildi.
- Görev API feature testleri oluşturma, güncelleme ve doğrulama ihlali senaryolarını kapsayacak şekilde genişletildi; backend README, CHANGELOG ve ana README sürüm tablosu yeni yetenekleri belgeledi.
### 2024-08-03
- Envanter API’sine `GET /api/tenants/{tenant}/inventories/{inventory}` detay ucu ile `POST`/`PATCH` işlemleri eklendi; tenant izolasyonu, kod benzersizliği ve servis tarihi doğrulamaları `StoreInventoryRequest` ve `UpdateInventoryRequest` sınıflarıyla sağlandı.
- InventoryController varsayılan durumu `active` olarak atayacak ve çapraz tenant erişimlerini 404 ile engelleyecek şekilde güncellendi; rota tanımları yeni uçları kapsayacak şekilde genişletildi.
- Envanter API feature testleri oluşturularak oluşturma, güncelleme, doğrulama ve tenant ayrımı senaryoları güvence altına alındı; backend README ve sürüm geçmişi yeni yetenekleri belgeledi.
- `/opscenter` rotası altında hafif OpsCenter paneli yayınlanarak tenant seçimi, olay/görev/envanter özetleri ve birim istatistikleri tek ekranda gösterildi.
- OpsCenterController, Blade görünümü ve kapsayıcı feature testleri eklendi; web rotaları güncellenirken backend README’ye paneli çalıştırma adımları ve kullanıcı talimatları işlendi.
<<<<<<< HEAD
=======
- OpsCenter özet servisi oluşturularak web paneli ile JSON API aynı veri kaynağını kullanacak şekilde birleşik hale getirildi; `GET /api/tenants/{tenant}/opscenter/summary` ucu eklendi ve testlerle güvence altına alındı.
### 2024-08-04
- Kullanıcı API’sine detay, oluşturma ve güncelleme uçları eklendi; `StoreUserRequest` ve `UpdateUserRequest` tenant kapsamı, belge geçerliliği ve şifre politikalarını doğruluyor.
- UserController ve UserResource güncellenerek tenant izolasyonu güçlendirildi, yeni uçlar JSON kaynaklarıyla tutarlı hale getirildi.
- Kullanıcı API feature testleri genişletilerek oluşturma, güncelleme, hatalı belge tarihi, çapraz tenant erişimi ve şifre hash doğrulaması senaryoları kapsandı.
- Backend README, ana README sürüm tablosu ve Devam Et yönetişim rehberi yeni kullanıcı uçlarına referans verecek şekilde güncellendi.
### 2024-08-05
- Birim API’sine `POST /api/tenants/{tenant}/units` ve `PATCH /api/tenants/{tenant}/units/{unit}` uçları eklendi; slug benzersizliği, tip doğrulaması ve metadata temizliği `StoreUnitRequest`/`UpdateUnitRequest` ile sağlandı.
- UnitController yeniden düzenlenerek slug veya ID ile erişim, tenant izolasyonu ve tekrar kullanılabilir ilişki yüklemeleri tek noktada toplandı.
- Unit API feature testleri oluşturma, eş tenant doğrulaması ve slug güncelleme senaryolarını kapsayacak şekilde genişletildi; backend README ve ana README sürüm tablosu yeni uçları yansıtacak biçimde güncellendi.
- `BelongsToTenant::findForTenantByIdentifier` yardımı eklenerek birim uçlarının sayısal slug’ları da tanıyacak şekilde güçlendirilmesi sağlandı; UpdateUnitRequest/UnitController akışları yardımcıyı kullanacak biçimde sadeleştirildi ve ek feature testleriyle doğrulandı.
- PHP-CS-Fixer konfigürasyonuna `imports_order` tanımı eklenerek fonksiyon ve sınıf `use` sıraları PHPCS ile hizalandı; kalite suite çakışmaları giderildi.
### 2024-08-06
- `TenantController` ve `TenantResource` eklenerek `GET /api/tenants` ile `GET /api/tenants/{tenant}` uçları tenant metriklerini ve OpsCenter özetlerini JSON formatında sunar hale getirildi; `include_summary=0` parametresiyle özet çıktısı isteğe bağlı hale getirildi.
- Tenant modeli ve rotaları güncellenerek yeni metrik sayaçları (`withCount`) ve arama/slug filtreleri desteklendi, OpsCenterSummary servisi yeniden kullanılarak detay uçlarından tutarlı veri sağlandı.
- `TenantApiTest` ile listeleme, filtreleme, detay ve özet devre dışı bırakma senaryoları kapsandı; backend README, Devam Et yapı rehberi, ana README sürüm tablosu ve CHANGELOG yeni tenant keşif akışını belgeleyecek şekilde güncellendi.
>>>>>>> b5aab88 (Add tenant discovery API with summary metrics)
