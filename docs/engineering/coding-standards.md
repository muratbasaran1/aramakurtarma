# Kodlama Standartları Rehberi

Bu rehber, TUDAK Afet Yönetim Sistemi kod tabanında tutarlı ve sürdürülebilir yazılım geliştirme pratiklerini sağlamak için
hazırlanmıştır. Standartlar; Faz 0 teknoloji kararları, Faz 3 çekirdek modülleri ve Faz 11 DevOps süreçlerindeki gereksinimlerle
tutarlıdır. Güncellemeler için RFC süreci kullanılmalı ve yapılan değişiklikler `CHANGELOG.md` ile README sürüm tablosuna
işlenmelidir.

## Genel İlkeler

1. **Okunabilirlik Önceliklidir:** Her sınıf, fonksiyon ve dosya tek bir sorumluluğa odaklanmalı; anlamlı isimlendirme kullanılmalıdır.
2. **Çoklu Tenant Bilinci:** Tüm veri erişimleri `tenant_id` filtreleriyle yapılmalı; scope dışı tenant verisi okunmamalıdır.
3. **Güvenlik Varsayılanı:** Girdi doğrulaması, yetkilendirme ve logging kararları kod seviyesinde açıkça uygulanmalıdır.
4. **Uluslararasılaştırma:** Kullanıcıya gösterilen metinler `lang/` dosyalarından veya lokalizasyon helper’larından alınmalıdır.
5. **İzlenebilirlik:** Kritik metodlar audit log veya metrik kayıtlarıyla ilişkilendirilmelidir.

## PHP / Laravel Standartları

- **Dil Sürümü:** PHP 8.3 özellikleri (readonly sınıflar, enum, intersection types) mümkün olduğunda tercih edilir.
- **Kod Stili:** PSR-12 + Laravel topluluk rehberi esas alınır; `phpcs.xml` kural seti referans kabul edilir.
- **Klasör Yapısı:** Domain odaklı modüller için `app/Domain/<Modül>` dizini; servis katmanı için `app/Application/` kullanılır.
- **Controller Kuralları:** İnce controller, kalın service yaklaşımı; validasyon form request sınıflarında tanımlanır.
- **Eloquent Kullanımı:** Query scope’ları tenant filtreleri ve soft-delete kontrolleri ile tanımlanır; eager loading varsayılandır.
- **Event & Listener:** Domain olayları `app/Domain/.../Events` içinde tutulur, dinleyiciler queue destekli çalışır.
- **Testler:** Feature testleri `tests/Feature/<Modül>` altında, veri fabrikaları `database/factories` dizininde sürdürülür.

## JavaScript / Vue / Blade Standartları

- **Component Mimari:** Vue bileşenleri tek sorumluluk prensibiyle tasarlanır; `setup` API tercih edilir.
- **State Yönetimi:** Tenant veya kullanıcıya özgü durumlar için Pinia store’ları modüler tutulur; global state’ten kaçınılır.
- **Blade Şablonları:** Blade dosyalarında business logic bulunmaz; karmaşık işlemler view-model veya controller’da çözümlenir.
- **Tailwind Kullanımı:** Yardımcı sınıflar `resources/css/utilities.css` dosyasında gruplanır; inline `!important` kullanımı yasaktır.
- **Erişilebilirlik:** ARIA etiketleri zorunludur; renk paleti WCAG AA kontrast kriterlerini karşılamalıdır.
- **Harita Bileşenleri:** MapLibre/Leaflet entegrasyonlarında koordinat dönüşümleri ve BBOX filtreleri helper fonksiyonlar üzerinden yapılmalıdır.

## Kod Yorumları & Dokümantasyon

- Her public metod için kısa açıklama ve parametre/return PHPDoc etiketleri zorunludur.
- Karmaşık algoritmalar için akış diyagramı veya pseudocode `docs/engineering/` içinde ayrı notlarla desteklenmelidir.
- `TODO` notları issue referansıyla yazılır ve sprint planına alınmadan kapatılamaz.

## Standartlara Uyum Kontrolleri

- Pre-commit aşamasında `php-cs-fixer` ve `eslint --max-warnings=0` çalıştırılır.
- CI pipeline’ında stil ihlali bulunan commit’ler başarısız sayılır (`docs/engineering/deployment-gates.md` referans alınır).
- Kod incelemelerinde bu rehbere uygunluk kontrol listesine eklenmiştir; sapmalar `docs/engineering/code-review.md` süreçlerine göre ele alınır.

## Güncelleme Süreci

1. Önerilen değişiklik için RFC açın ve etkilenen modül/fazları belirtin.
2. Onay sonrası `phpcs.xml`, `.eslintrc` gibi konfigürasyon dosyalarını güncelleyin.
3. Yapılan değişiklikleri README versiyon tablosu ve `CHANGELOG.md` içinde kaydedin.

> _Not:_ Rehber, yılda iki kez (Haziran & Aralık) mühendislik kalite oturumlarında gözden geçirilir.
