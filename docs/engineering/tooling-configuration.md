# Kod Kalite Araç Konfigürasyonları

Bu rehber, TUDAK Afet Yönetim Sistemi için depoya eklenen statik analiz ve stil araçlarının nasıl yapılandırıldığını belgeleyerek
yazılım ekiplerinin pre-commit ve CI kontrollerini güvenle çalıştırmasını amaçlar. Konfigürasyonlar Faz 0 teknoloji tercihleri,
Faz 11 DevOps kalite kapıları ve Faz 12 test kapsamı ile uyumludur.

## PHP Araçları

| Dosya | Amaç | Çalıştırma Komutu |
| --- | --- | --- |
| `.php-cs-fixer.dist.php` | Mevcut dizinleri dinamik olarak bularak PSR-12 tabanlı stil kurallarını ve `declare(strict_types=1)` şartını uygular. | `vendor/bin/php-cs-fixer fix --config=.php-cs-fixer.dist.php` |
| `phpcs.xml` | `config/` dizini ve paylaşılan PHP yardımcılarını tarar; `Slevomat` ek kurallarıyla tip deklarasyonlarını doğrular. | `vendor/bin/phpcs --standard=phpcs.xml` |
| `phpstan.neon.dist` | Larastan’a ihtiyaç duymadan `config/` dizininde seviye 5 statik analiz çalıştırır, geçici dosyaları `build/phpstan/` altında tutar. | `vendor/bin/phpstan analyse -c phpstan.neon.dist` |
| `psalm.xml` | `config/` kapsamındaki PHP dosyaları için `errorLevel=3` hassasiyetinde güvenlik odaklı analiz çalıştırır. | `vendor/bin/psalm` |

> **Not:** `composer.json` içindeki `quality` script’i tüm PHP kontrollerini ardışık olarak çalıştırır; tek adımda doğrulama için `composer run quality` komutu kullanılabilir.

## Front-end Araçları

| Dosya | Amaç | Çalıştırma Komutu |
| --- | --- | --- |
| `.eslintrc.cjs` | Vue 3 + TypeScript bileşenleri için tavsiye edilen ESLint kurallarını etkinleştirir; jest test dosyalarını tanır. | `npx eslint --ext .ts,.js,.vue resources/js` |
| `stylelint.config.cjs` | Tailwind ağırlıklı CSS için sınıf adlandırma ve özellik sıralama standartlarını uygular. | `npx stylelint "resources/css/**/*.{css,scss}"` |

> **Not:** `package.json` scriptleri `"lint:js"`, `"lint:css"` ve `"lint"` birleşimini içermeli; CI pipeline’ında `npm run lint`
> komutu her iki kontrolü de çalıştırmalıdır.

## Klasör Kapsamı & Hariç Tutmalar

- `vendor/`, `storage/`, `build/` ve `node_modules/` dizinleri tüm araçlar için hariç tutulmuştur.
- Laravel uygulaması oluşturulana kadar PHP kontrolleri `config/` ve paylaşılan yardımcı dosyalar üzerinde koşar; yeni dizinler eklendiğinde `.php-cs-fixer.dist.php` ve `phpcs.xml` dosyalarına yansıtın.
- Ön yüz varlıkları `resources/js`, `resources/css` ve `resources/views` altında konumlandırıldığında lint kuralları doğrudan uygulanır.

## Bakım Döngüsü

1. Laravel veya Vue ana sürüm güncellemelerinde konfigürasyon dosyaları gözden geçirilir.
2. Yeni kural ekleme veya hariç tutma ihtiyaçlarında RFC oluşturulur, etkisi `docs/engineering/code-review.md` içinde duyurulur.
3. Güncellenen dosyalar `CHANGELOG.md` ve README sürüm tablosuna işlenerek izlenebilirlik korunur.

## Toplu Komut (Run Quality Suite)

- Yerel doğrulama için `./tools/run-quality-suite.sh` script’i kullanılabilir.
- Script, bu belgede listelenen PHP ve front-end araçlarını sırayla çalıştırır; eksik kurulumlarda ⚠️ uyarısı üretir.
- PHP kalite araçları henüz kurulmadıysa script `composer install --no-ansi --no-interaction --no-progress --prefer-dist` komutunu otomatik tetikler.
- Script çıktıları PR kontrol listesinde (`docs/engineering/pr-checklist.md`) raporlanarak inceleme sürecine eklenmelidir.

## Hata Önleme

- `ignore` veya `baseline` tanımları gerekçesiz eklenemez; kod incelemesinde referans issue/link sunulmalıdır.
- CI başarısız olduğunda araç versiyonları doğrulanır ve `config/environment/` dizinindeki örnekler güncellenir.
- Takımlar yeni modül oluşturduğunda ilgili dizinlerin lint kapsamına alındığını kontrol eder; gerekirse bu rehbere PR gönderilir.
