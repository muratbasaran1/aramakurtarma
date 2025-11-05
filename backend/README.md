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
| `audit_logs` | İşlem geçmişi | `tenant_id`, `event`, `auditable_type`, `auditable_id`, `payload` (değişiklikler + meta)

> **Not:** Uzamsal veri tipleri (`impact_area`, `route`) SQLite test ortamında otomatik olarak JSON sütunlarına düşer. MySQL 8+ ortamında gerçek `POLYGON` ve `LINESTRING` tipleri kullanılır.

## İlişkiler
- Tenant → Units/Users/Incidents/Inventories: `hasMany`
- Unit → Users/Tasks: `hasMany`
- Incident → Tasks: `hasMany`
- Task → Tenant/Incident/Unit/User: `belongsTo`

Model sınıfları (`app/Models/*`) ilgili ilişki ve JSON alan `casts` tanımlarını içerir. Bu sayede Eloquent üzerinden faz gereksinimlerinde geçen kural ve doğrulamalar kolayca uygulanabilir. `AuditLog` modeli ise API denetim kayıtlarını JSON payload olarak saklar.

## Çoklu Tenant Kapsamı & Middleware
- `App\Models\Concerns\BelongsToTenant` trait’i, `tenant_id` sütunu bulunan modelleri otomatik olarak aktif tenant ile sınırlar. `TenantContext` tanımlıysa tüm sorgular `WHERE tenant_id = ?` filtresiyle çalışır ve kayıt oluşturulurken boş kalan `tenant_id` otomatik doldurulur.
- `App\Http\Middleware\EnsureTenant` middleware’i HTTP isteklerinde `X-Tenant` başlığı, `tenant` rota parametresi veya `tenant` sorgu parametresinden tenant’ı çözerek `TenantContext`’e işler. Tenant bulunamazsa 400 yanıtı döndürülür.
- Yönetici/raporlama senaryoları için `Model::forTenantQuery($identifier)` yardımcı metodu veya `scopeForTenant` kullanılabilir. Bu yardımcılar global tenant filtresini geçici olarak kaldırıp hedef tenant’a göre sorgu çalıştırır.

> **API Kullanımı:** Tenant zorunlu rotalar `middleware('tenant')` ile korunmalıdır. İstekler `X-Tenant: il-adi` başlığı ile gönderildiğinde ilgili tenant verileri döner; seeder rastgele slug üretir, bu nedenle entegrasyon testlerinde ihtiyaç duyulan slug değerlerini factory üzerinden açıkça belirtin.

Testler için varsayılan `.env.testing` dosyası depoya eklendi; anahtar ve SQLite konfigürasyonu bu dosya üzerinden yüklenir. Gerektiğinde `APP_KEY` veya `DB_CONNECTION` değerlerini güncelleyerek farklı senaryoları koşturabilirsiniz.

## Factory & Seeder
- `database/factories/*.php` dosyaları çoklu tenant verisini uyumlu şekilde üretmek üzere birbirine bağlıdır.
- `DatabaseSeeder` her tenant için üçer birim, ikişer kullanıcı, iki aktif olay, üçer görev, beş envanter kaydı ve çift örnekli izleme pingleri üretir. Görev atamaları ve pingler ilgili birim/kullanıcı eşleşmeleriyle uyumlu olacak biçimde rastgele seçilir.
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
| Tenant Listesi | `GET /api/tenants` | Tenant’ların slug, ad ve temel metriklerini listeler; `search` veya `slug` parametreleriyle filtrelenebilir. |
| Tenant Detayı | `GET /api/tenants/{tenant}` | Belirtilen tenant’ın sayaçlarını ve OpsCenter özetini JSON olarak döndürür (`include_summary=0` ile özet kapatılabilir). |
| Tenant Oluştur | `POST /api/tenants` | Yeni tenant kaydı oluşturur; isim, slug ve timezone doğrulaması yapar, OpsCenter özetini başlangıç metrikleriyle döndürür. |
| Tenant Güncelle | `PATCH /api/tenants/{tenant}` | Ad, slug, timezone ve ayar alanlarını günceller; `include_summary=0` parametresiyle özet çıktısı devre dışı bırakılabilir. |
| Tenant Sil | `DELETE /api/tenants/{tenant}` | Bağlı birim, kullanıcı, olay, görev veya envanteri olmayan tenantları kalıcı olarak siler; bağımlılık varsa 422 döner. |
| Olaylar | `GET /api/tenants/{tenant}/incidents` | Durum, öncelik ve kod filtreleriyle olay listesini döndürür. |
| Olay Oluştur | `POST /api/tenants/{tenant}/incidents` | Benzersiz kod, GeoJSON (Polygon/MultiPolygon) ve zaman doğrulamasıyla yeni olay kaydı oluşturur. |
| Olay Güncelle | `PATCH /api/tenants/{tenant}/incidents/{incident}` | Tenant doğrulamasıyla başlık, durum, öncelik ve GeoJSON alanlarını günceller; `closed` durumuna geçişte `closed_at` zorunludur. |
| Olay Detayı | `GET /api/tenants/{tenant}/incidents/{incident}` | Göreve bağlanan kayıtları ve görev sayısını içerir. |
| Olay Sil | `DELETE /api/tenants/{tenant}/incidents/{incident}` | Yalnızca `open` durumunda ve görevi bulunmayan kayıtları kalıcı olarak siler; aksi durumda 422 döner. |
| Görevler | `GET /api/tenants/{tenant}/tasks` | Durum, olay ve çift onay filtreleriyle görev listesini döndürür. |
| Görev Detayı | `GET /api/tenants/{tenant}/tasks/{task}` | İlgili olay, atanmış birim ve personel bilgilerini içerir. |
| Görev Oluştur | `POST /api/tenants/{tenant}/tasks` | Tenant bağlamında olay doğrulaması yaparak görev açar; GeoJSON rota, atamalar ve planlanan başlangıç zamanı isteğe bağlıdır. |
| Görev Güncelle | `PATCH /api/tenants/{tenant}/tasks/{task}` | Durum geçişlerinde tamamlanma/doğrulama tarihlerini ve çift onay zorunluluklarını doğrular, rota güncellemelerinde GeoJSON kontrolü yapar. |
| Görev Sil | `DELETE /api/tenants/{tenant}/tasks/{task}` | `planned` veya `assigned` durumundaki görevleri siler; ilerlemiş görevlerde 422 döner. |
| Envanter | `GET /api/tenants/{tenant}/inventories` | Kod, durum ve serbest metin arama destekli envanter listesini döndürür. |
| Envanter Detayı | `GET /api/tenants/{tenant}/inventories/{inventory}` | Tenant doğrulamasıyla tekil envanter kaydını döndürür. |
| Envanter Oluştur | `POST /api/tenants/{tenant}/inventories` | Kod benzersizliği, durum ve servis tarihi doğrulamasıyla yeni envanter kaydı oluşturur. |
| Envanter Güncelle | `PATCH /api/tenants/{tenant}/inventories/{inventory}` | Tenant izolasyonu korunarak kod, durum, isim ve servis tarihi alanlarını günceller. |
| Envanter Sil | `DELETE /api/tenants/{tenant}/inventories/{inventory}` | Yalnızca `retired` durumundaki kayıtları siler; aktif veya serviste olan envanter 422 döner. |
| Kullanıcılar | `GET /api/tenants/{tenant}/users` | Durum, rol, birim ve arama parametreleri ile kullanıcı listesini döndürür. |
| Kullanıcı Detayı | `GET /api/tenants/{tenant}/users/{user}` | Tenant doğrulamasıyla kullanıcı bilgilerini, birim eşleşmesini ve belge durumunu döndürür. |
| Kullanıcı Oluştur | `POST /api/tenants/{tenant}/users` | Tenant kapsamında benzersiz e-posta, belge geçerlilik tarihi ve güçlü şifre doğrulamasıyla yeni kullanıcı oluşturur. |
| Kullanıcı Güncelle | `PATCH /api/tenants/{tenant}/users/{user}` | Tenant izolasyonunu koruyarak ad, durum, birim ataması ve şifre güncellemelerini uygular. |
| Kullanıcı Sil | `DELETE /api/tenants/{tenant}/users/{user}` | Aktif görev ataması bulunmayan kullanıcıları siler; ataması olan kayıtlar 422 döner ve işlem audit log’a kaydedilir. |
| Birimler | `GET /api/tenants/{tenant}/units` | Tür ve arama filtresiyle birim listesini, görev/kullanıcı istatistikleriyle birlikte döndürür. |
| Birim Detayı | `GET /api/tenants/{tenant}/units/{unit}` | Son 10 görevi, kullanıcı listesini ve aktif görev sayısını içerir (ID veya slug — sayısal slug’lar dahil). |
| Birim Oluştur | `POST /api/tenants/{tenant}/units` | Otomatik/manuel slug üretimiyle benzersiz birim kaydı oluşturur; tip seçimi ve meta veriler doğrulanır. |
| Birim Güncelle | `PATCH /api/tenants/{tenant}/units/{unit}` | Slug, ad, tip ve metadata güncellemelerini tenant izolasyonu korunarak uygular (ID veya slug — sayısal slug’lar dahil). |
| Birim Sil | `DELETE /api/tenants/{tenant}/units/{unit}` | Aktif görevi veya kullanıcı kaydı bulunmayan birimleri siler; koşullar sağlanmazsa 422 döner ve audit log kaydı oluşturur. |
| OpsCenter Özeti | `GET /api/tenants/{tenant}/opscenter/summary` | Panelde gösterilen olay/görev/envanter sayıları ile son kayıtları JSON formatında döndürür. |
| Audit Logları | `GET /api/tenants/{tenant}/audit-logs` | Tenant’a ait işlem geçmişini sayfalı olarak listeler; `event`, `auditable_type`, `auditable_id`, `since`, `until` ve `has_user` filtreleri desteklenir. |
| Takip Pingleri | `GET /api/tenants/{tenant}/tracking/pings` | Kullanıcı/görev filtreleri ve zaman aralığıyla canlı takip pinglerini listeler; sonuçlar en yeni kayıt üzerinden sayfalanır. |
| Son Konumlar | `GET /api/tenants/{tenant}/tracking/pings/latest` | Her kullanıcı için en güncel ping kaydını döndürür; kullanıcı/görev filtresi ve limit parametresi desteklenir. |
| Takip Ping Oluştur | `POST /api/tenants/{tenant}/tracking/pings` | Tenant doğrulaması, konum aralığı ve opsiyonel hız/başlık doğrulamasıyla GPS pinglerini kaydeder; pingler yalnızca görevi aktif ve kullanıcı/birim eşleşmesi doğrulanmış kayıtlar için kabul edilir, audit log düşer ve hareketsizlik kuralı değerlendirilir. |

## Audit Log Olayları

- `App\Support\Audit\AuditLogger` servisi olay, görev, envanter, birim ve kullanıcı uçlarında gerçekleşen `create`, `update` ve `delete` işlemlerini otomatik olarak `audit_logs` tablosuna yazar.
- Payload içerisindeki `changes` alanı hassas değerleri (örn. `password`) maskeleyerek (`***`) saklar; ilişkili kaynak öznitelikleri `attributes` alanında tutulur.
- Audit kayıtları tenant kimliği ve (varsa) kimliği doğrulanmış kullanıcı bilgisi ile ilişkilendirilir; böylece Faz 1 güvenlik ve Faz 11 izlenebilirlik hedefleri desteklenir.
- `tracking.ping_recorded` olayı her ping girişinde, `tracking.no_motion` ise hareketsizlik kuralı tetiklendiğinde oluşur; her iki durumda da tenant, kullanıcı ve görev bilgileri audit log’a yazılır.

> **Not:** Birim detayı uç noktası ID veya slug ile erişilebilir. Her iki durumda da istek bağlamındaki tenant doğrulanır ve farklı tenant’a ait kayıtlar 404 döner. Audit log API’si maskeleme kurallarını (`password` → `***`) uygulayarak veri döndürür; hassas alanlar payload içinde açıkta gösterilmez.

## Canlı Takip & Hareketsizlik Kuralı

- `TrackingPingController` GPS ping’lerini tenant bağlamında alır; kullanıcı ve (varsa) görev kimlikleri tenant doğrulamasından geçer, konum bilgisi aralık kontrollerinden sonra kaydedilir.
- `StoreTrackingPingRequest`, görev belirtilen ping’lerde görevin durumunun `assigned`/`in_progress` olmasını ve kullanıcının doğrudan atama veya birim eşleşmesiyle ilişkilendirilmesini zorunlu kılar; aksi durumda istek 422 doğrulama hatası üretir.
- `tracking/pings/latest` uç noktası, aynı tenant içinde her kullanıcı için en güncel ping’i hızlıca döndürmek amacıyla depodaki verileri sıralar; isteğe bağlı `user_id`, `task_id`, `since` ve `limit` parametreleri operasyonel listeleri sınırlandırmanızı sağlar.
- `MotionMonitor` servisi, aynı kullanıcı ve görev için gelen önceki kayıtları inceler. Ping’ler arası süre ≥120 saniye, hız ≤0.3 m/s ve konum değişimi ≤5 m ise hareketsizlik kabul edilir. Önceki ping dizisi zaten hareketsizse tekrar alarm üretmez.
- Hareketsizlik tetiklerinde `tracking.no_motion` audit olayı düşer ve payload içerisine süre, kullanıcı/görev kimliği ile referans ping bilgileri eklenir. Bu kayıt, Faz 4 hareketsizlik kuralı gereksinimini doğrulamak ve Faz 7 kural motoruna tetik sağlamak için kullanılabilir.
- Seeder, örnek tenant verisi üretirken aktif görevlere iki örnek ping eklediğinden OpsCenter panelinde veya API üzerinden hazır veri ile senaryoları test edebilirsiniz.

> **Not:** Birim detayı uç noktası ID veya slug ile erişilebilir. Her iki durumda da istek bağlamındaki tenant doğrulanır ve farklı tenant’a ait kayıtlar 404 döner.

## OpsCenter Paneli (Ön İzleme)
Tenant verilerini hızlıca gözlemlemek için `/opscenter` rotası altında hafif bir web paneli yayınlandı. Panel, seçilen tenant’a ait son olayları, görev güncellemelerini, envanter durumunu ve birim istatistiklerini tek ekranda listeler.

### Çalıştırma Adımları
```bash
php artisan serve --host=0.0.0.0 --port=8000
# Ayrı bir terminalde
php artisan migrate --seed   # İlk defa kurulum yapıyorsanız
```

Ardından tarayıcınızdan `http://localhost:8000/opscenter` adresine gidin. Birden fazla tenant varsa sağ üstteki açılır menüden slug seçerek paneller arasında geçiş yapabilirsiniz. Seeder çalıştırılmadıysa panel boş durum mesajı gösterir.

> **İpucu:** Panel yalnızca okuma amaçlıdır; REST uçları üzerinden yaptığınız olay/görev/envanter değişiklikleri sayfayı yenilediğinizde anında yansır.

## Sonraki Adımlar
Bu temel şema üzerine aşağıdaki yetenekler kademeli olarak eklenecektir:
- Fortify tabanlı kimlik doğrulama, 2FA ve Spatie Permissions entegrasyonu
- Görev doğrulama (çift onay), zimmet transaction’ları ve audit log iyileştirmeleri (aksiyon sınıflandırması, webhook tetikleri)
- Geofence ihlali, SOS tetikleri ve canlı ping akışının WebSocket entegrasyonu
- Canlı takip pingi, geofence ve hareketsizlik alarmı API uçları

Detaylı yol haritası ve faz bağımlılıkları proje kökündeki `README.md` ve yönetişim dokümanlarında yer almaktadır.
