# TUDAK Afet Yönetim Sistemi — Laravel Backend

Bu dizin, TUDAK Afet Yönetim Sistemi için Laravel 11 tabanlı çoklu tenant backend uygulamasını içerir. Amaç; Faz 2 ve Faz 3 çıktılarıyla uyumlu olay, görev, envanter ve kullanıcı yönetimi temelini oluşturarak ilerleyen fazlardaki modüllerin güvenli ve izlenebilir bir şekilde inşa edilmesini sağlamaktır.

## Gereksinimler
- PHP 8.3+
- Composer 2.x
- Node.js 20+ (asset ve lint adımları için)
- MySQL 8+ (geliştirme ortamında SQLite fallback çalışsa da uzamsal alanlar MySQL üzerinde test edilmelidir)

## Kurulum
```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
```

Varsayılan seeder, iki örnek tenant altında birim, kullanıcı, olay, görev ve envanter kayıtları üretir. Bu kayıtlar kalite kontrolleri ve demo uçtan uca akışlar için başlangıç verisi sağlar.

## Çekirdek Şema
Aşağıdaki tablolar Faz 2 (veri sertleştirme) ve Faz 3 (çekirdek modüller) gereksinimlerini karşılayacak şekilde tasarlanmıştır:

| Tablo | Amaç | Öne Çıkan Alanlar |
| --- | --- | --- |
| `tenants` | İl bazlı tenant ayrımı | `slug`, `timezone`, `settings` (JSON)
| `units` | Birim/bölüm yönetimi | `tenant_id`, `slug`, `type`, `metadata`
| `users` | Fortify/Spatie entegrasyonuna hazır kullanıcı kayıtları | `tenant_id`, `unit_id`, `role`, `documents`, `documents_expires_at`, `status`
| `incidents` | Olay yönetimi | `code` (tenant içinde benzersiz), `status` (`open/active/closed`), `priority`, `impact_area` (Polygon / JSON)
| `tasks` | Görev yaşam döngüsü | `status` (`planned/assigned/in_progress/done/verified`), `route` (LineString / JSON), `requires_double_confirmation`
| `inventories` | Envanter takibi | `status` (`active/service/retired`), `last_service_at`, `attributes` (JSON)

> **Not:** Uzamsal veri tipleri (`impact_area`, `route`) SQLite test ortamında otomatik olarak JSON sütunlarına düşer. MySQL 8+ ortamında gerçek `POLYGON` ve `LINESTRING` tipleri kullanılır.

## İlişkiler
- Tenant → Units/Users/Incidents/Inventories: `hasMany`
- Unit → Users/Tasks: `hasMany`
- Incident → Tasks: `hasMany`
- Task → Tenant/Incident/Unit/User: `belongsTo`

Model sınıfları (`app/Models/*`) ilgili ilişki ve JSON alan `casts` tanımlarını içerir. Bu sayede Eloquent üzerinden faz gereksinimlerinde geçen kural ve doğrulamalar kolayca uygulanabilir.

## Çoklu Tenant Kapsamı & Middleware
- `App\Models\Concerns\BelongsToTenant` trait’i, `tenant_id` sütunu bulunan modelleri otomatik olarak aktif tenant ile sınırlar. `TenantContext` tanımlıysa tüm sorgular `WHERE tenant_id = ?` filtresiyle çalışır ve kayıt oluşturulurken boş kalan `tenant_id` otomatik doldurulur.
- `App\Http\Middleware\EnsureTenant` middleware’i HTTP isteklerinde `X-Tenant` başlığı, `tenant` rota parametresi veya `tenant` sorgu parametresinden tenant’ı çözerek `TenantContext`’e işler. Tenant bulunamazsa 400 yanıtı döndürülür.
- Yönetici/raporlama senaryoları için `Model::forTenantQuery($identifier)` yardımcı metodu veya `scopeForTenant` kullanılabilir. Bu yardımcılar global tenant filtresini geçici olarak kaldırıp hedef tenant’a göre sorgu çalıştırır.

> **API Kullanımı:** Tenant zorunlu rotalar `middleware('tenant')` ile korunmalıdır. İstekler `X-Tenant: il-adi` başlığı ile gönderildiğinde ilgili tenant verileri döner; seeder rastgele slug üretir, bu nedenle entegrasyon testlerinde ihtiyaç duyulan slug değerlerini factory üzerinden açıkça belirtin.

Testler için varsayılan `.env.testing` dosyası depoya eklendi; anahtar ve SQLite konfigürasyonu bu dosya üzerinden yüklenir. Gerektiğinde `APP_KEY` veya `DB_CONNECTION` değerlerini güncelleyerek farklı senaryoları koşturabilirsiniz.

## Factory & Seeder
- `database/factories/*.php` dosyaları çoklu tenant verisini uyumlu şekilde üretmek üzere birbirine bağlıdır.
- `DatabaseSeeder` her tenant için üçer birim, ikişer kullanıcı, iki aktif olay, üçer görev ve beş envanter kaydı üretir. Görev atamaları, ilgili birim ve kullanıcılarla eşleşecek şekilde rastgele seçilir.

Seeder çıktıları aşağıdaki akışları test etmek için kullanılabilir:
1. Olay açma → görev atama → görev durumunu güncelleme
2. Envanter zimmet süreçlerinin doğrulanması (ilerleyen fazlarda genişletilecek)
3. Tenant bazlı kullanıcı ve birim erişim kontrolleri

## Kalite ve Test
- Kod standartları ve statik analiz için kök dizindeki `tools/run-quality-suite.sh` komutu çalıştırılabilir. Script, Composer/NPM bağımlılıklarını otomatik kurar ve PHP-CS-Fixer, PHPCS, PHPStan, Psalm, ESLint ve Stylelint kontrollerini ardışık olarak yürütür.
- `php artisan test` komutu, SQLite fallback ile de çalışır; ancak uzamsal veri tiplerinin gerçekçi davranışı için MySQL üzerinde test edilmesi tavsiye edilir.

## Tenant API Uçları (Ön İzleme)
Tenant bağlamında çalışan REST uçları `routes/api.php` dosyasında tanımlıdır ve aşağıdaki kaynakları kapsar:

| Kaynak | Uç | Açıklama |
| --- | --- | --- |
| Olaylar | `GET /api/tenants/{tenant}/incidents` | Durum, öncelik ve kod filtreleriyle olay listesini döndürür. |
| Olay Oluştur | `POST /api/tenants/{tenant}/incidents` | Benzersiz kod, GeoJSON (Polygon/MultiPolygon) ve zaman doğrulamasıyla yeni olay kaydı oluşturur. |
| Olay Güncelle | `PATCH /api/tenants/{tenant}/incidents/{incident}` | Tenant doğrulamasıyla başlık, durum, öncelik ve GeoJSON alanlarını günceller; `closed` durumuna geçişte `closed_at` zorunludur. |
| Olay Detayı | `GET /api/tenants/{tenant}/incidents/{incident}` | Göreve bağlanan kayıtları ve görev sayısını içerir. |
| Görevler | `GET /api/tenants/{tenant}/tasks` | Durum, olay ve çift onay filtreleriyle görev listesini döndürür. |
| Görev Detayı | `GET /api/tenants/{tenant}/tasks/{task}` | İlgili olay, atanmış birim ve personel bilgilerini içerir. |
| Görev Oluştur | `POST /api/tenants/{tenant}/tasks` | Tenant bağlamında olay doğrulaması yaparak görev açar; GeoJSON rota, atamalar ve planlanan başlangıç zamanı isteğe bağlıdır. |
| Görev Güncelle | `PATCH /api/tenants/{tenant}/tasks/{task}` | Durum geçişlerinde tamamlanma/doğrulama tarihlerini ve çift onay zorunluluklarını doğrular, rota güncellemelerinde GeoJSON kontrolü yapar. |
| Envanter | `GET /api/tenants/{tenant}/inventories` | Kod, durum ve serbest metin arama destekli envanter listesini döndürür. |
| Kullanıcılar | `GET /api/tenants/{tenant}/users` | Durum, rol, birim ve arama parametreleri ile kullanıcı listesini döndürür. |
| Birimler | `GET /api/tenants/{tenant}/units` | Tür ve arama filtresiyle birim listesini, görev/kullanıcı istatistikleriyle birlikte döndürür. |
| Birim Detayı | `GET /api/tenants/{tenant}/units/{unit}` | Son 10 görevi, kullanıcı listesini ve aktif görev sayısını içerir. |

> **Not:** Birim detayı uç noktası ID veya slug ile erişilebilir. Her iki durumda da istek bağlamındaki tenant doğrulanır ve farklı tenant’a ait kayıtlar 404 döner.

## Sonraki Adımlar
Bu temel şema üzerine aşağıdaki yetenekler kademeli olarak eklenecektir:
- Fortify tabanlı kimlik doğrulama, 2FA ve Spatie Permissions entegrasyonu
- Görev doğrulama (çift onay), zimmet transaction’ları ve audit log mekanizmaları
- Canlı takip pingi, geofence ve hareketsizlik alarmı API uçları

Detaylı yol haritası ve faz bağımlılıkları proje kökündeki `README.md` ve yönetişim dokümanlarında yer almaktadır.
