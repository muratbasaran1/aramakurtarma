# Kod Kalite Araç Konfigürasyonları

Bu rehber, TUDAK Afet Yönetim Sistemi için depoya eklenen statik analiz ve stil araçlarının nasıl yapılandırıldığını belgeleyerek
yazılım ekiplerinin pre-commit ve CI kontrollerini güvenle çalıştırmasını amaçlar. Konfigürasyonlar Faz 0 teknoloji tercihleri,
Faz 11 DevOps kalite kapıları ve Faz 12 test kapsamı ile uyumludur.

## PHP Araçları

| Dosya | Amaç | Çalıştırma Komutu |
| --- | --- | --- |
| `.php-cs-fixer.dist.php` | PSR-12 tabanlı stil kurallarını, kısa dizi sözdizimini ve `declare(strict_types=1)` şartını uygular. | `vendor/bin/php-cs-fixer fix --config=.php-cs-fixer.dist.php` |
| `phpcs.xml` | CI’da zorunlu kod standartları taramasını tanımlar; `Slevomat` eklentileri ile tip deklarasyonlarını doğrular. | `vendor/bin/phpcs --standard=phpcs.xml` |
| `phpstan.neon.dist` | Larastan uzantılarıyla seviye 8 statik analizi yapılandırır; sonuçlar `build/phpstan/` klasörüne yazılır. | `vendor/bin/phpstan analyse -c phpstan.neon.dist` |
| `psalm.xml` | Güvenlik hassas akışları aylık denetimler için `errorLevel=3` hassasiyetinde tarar. | `vendor/bin/psalm` |

> **Not:** `composer.json` içinde aşağıdaki script tanımları ile komutlar standartlaştırılmalıdır:
>
> ```json
> {
>   "scripts": {
>     "format": "php-cs-fixer fix",
>     "lint": "phpcs",
>     "analyse": "phpstan analyse",
>     "psalm": "psalm"
>   }
> }
> ```

## Front-end Araçları

| Dosya | Amaç | Çalıştırma Komutu |
| --- | --- | --- |
| `.eslintrc.cjs` | Vue 3 + TypeScript bileşenleri için tavsiye edilen ESLint kurallarını etkinleştirir; jest test dosyalarını tanır. | `npx eslint --ext .ts,.js,.vue resources/js` |
| `stylelint.config.cjs` | Tailwind ağırlıklı CSS için sınıf adlandırma ve özellik sıralama standartlarını uygular. | `npx stylelint "resources/css/**/*.{css,scss}"` |

> **Not:** `package.json` scriptleri `"lint:js"`, `"lint:css"` ve `"lint"` birleşimini içermeli; CI pipeline’ında `npm run lint`
> komutu her iki kontrolü de çalıştırmalıdır.

## Klasör Kapsamı & Hariç Tutmalar

- `vendor/`, `storage/` ve `node_modules/` dizinleri tüm araçlar için hariç tutulmuştur.
- Domain odaklı PHP kodu için `app/Domain/*`, uygulama servisleri için `app/Application/*` alt dizinleri varsayılan kapsamda yer alır.
- Ön yüz varlıkları `resources/js`, `resources/css` ve `resources/views` altında konumlandırıldığında lint kuralları doğrudan uygulanır.

## Bakım Döngüsü

1. Laravel veya Vue ana sürüm güncellemelerinde konfigürasyon dosyaları gözden geçirilir.
2. Yeni kural ekleme veya hariç tutma ihtiyaçlarında RFC oluşturulur, etkisi `docs/engineering/code-review.md` içinde duyurulur.
3. Güncellenen dosyalar `CHANGELOG.md` ve README sürüm tablosuna işlenerek izlenebilirlik korunur.

## Hata Önleme

- `ignore` veya `baseline` tanımları gerekçesiz eklenemez; kod incelemesinde referans issue/link sunulmalıdır.
- CI başarısız olduğunda araç versiyonları doğrulanır ve `config/environment/` dizinindeki örnekler güncellenir.
- Takımlar yeni modül oluşturduğunda ilgili dizinlerin lint kapsamına alındığını kontrol eder; gerekirse bu rehbere PR gönderilir.
