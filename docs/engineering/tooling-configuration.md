# Kod Kalite Araç Konfigürasyonları

Bu rehber, TUDAK Afet Yönetim Sistemi için depoya eklenen statik analiz ve stil araçlarının nasıl yapılandırıldığını belgeleyerek
yazılım ekiplerinin pre-commit ve CI kontrollerini güvenle çalıştırmasını amaçlar. Konfigürasyonlar Faz 0 teknoloji tercihleri,
Faz 11 DevOps kalite kapıları ve Faz 12 test kapsamı ile uyumludur.

## PHP Araçları

| Dosya | Amaç | Çalıştırma Komutu |
| --- | --- | --- |
| `.php-cs-fixer.dist.php` | Depo kökü `config/` diziniyle birlikte `backend/` altındaki Laravel kodunu PSR-12 + `strict_types` kurallarıyla denetler. | `vendor/bin/php-cs-fixer fix --config=.php-cs-fixer.dist.php` |
| `phpcs.xml` | `config/` ve `backend/` altındaki PHP dosyalarını tarar; `Slevomat` ek kurallarıyla tip deklarasyonlarını doğrular. | `vendor/bin/phpcs --standard=phpcs.xml` |
| `phpstan.neon.dist` | Larastan olmadan `backend/` kod tabanı ve ortak konfig dizinleri için seviye 5 statik analiz çalıştırır; geçici dosyaları `build/phpstan/` altında tutar. | `vendor/bin/phpstan analyse -c phpstan.neon.dist` |
| `psalm.xml` | `config/` ve `backend/` kapsamındaki PHP dosyaları için `errorLevel=3` hassasiyetinde güvenlik odaklı analiz çalıştırır. | `vendor/bin/psalm` |

> **Not:** `composer.json` içindeki `quality` script’i tüm PHP kontrollerini ardışık olarak çalıştırır; tek adımda doğrulama için `composer run quality` komutu kullanılabilir.

- `.php-cs-fixer.dist.php` içindeki `imports_order` ayarı, sınıf → fonksiyon → sabit `use` sıralamasını zorunlu kılar; böylece PHP-CS-Fixer ile PHPCS aynı sıralamayı uygular ve run-quality-suite içinde çakışma yaşanmaz.

## Front-end Araçları

| Dosya | Amaç | Çalıştırma Komutu |
| --- | --- | --- |
| `.eslintrc.cjs` | Vue 3 + TypeScript bileşenleri için tavsiye edilen ESLint kurallarını etkinleştirir; jest test dosyalarını tanır. | `npm run lint:eslint` *(globlar `--no-error-on-unmatched-pattern` ile boş dizinlerde hata üretmez)* |
| `stylelint.config.cjs` | Tailwind ağırlıklı CSS için sınıf adlandırma ve özellik sıralama standartlarını uygular. | `npm run lint:stylelint` *( `--allow-empty-input` sayesinde henüz CSS dosyası yoksa başarıyla biter )* |

> `package.json` içindeki `lint` script’i iki komutu ardışık çalıştırır; CI pipeline’ında `npm run lint` çağrısı bu kombinasyonu kullanmalıdır. Bağımlılıklar `npm install --no-audit --progress false` ile kurulur ve `package-lock.json` güncel tutulur.

> **Not:** `package.json` scriptleri `"lint:eslint"`, `"lint:stylelint"` ve bunları tetikleyen `"lint"` birleşimini içerir; CI pipeline’ında `npm run lint`
> komutu her iki kontrolü de ardışık çalıştırır.

## Klasör Kapsamı & Hariç Tutmalar

- `vendor/`, `storage/`, `build/`, `node_modules/`, `backend/vendor/` ve `backend/storage/` dizinleri tüm araçlar için hariç tutulmuştur.
- Laravel backend kodu `backend/` dizininde tutulur; yapılandırmalar bu klasörü kapsayacak şekilde güncellendi. Yeni alt dizinler eklediğinizde `.php-cs-fixer.dist.php`, `phpcs.xml`, `phpstan.neon.dist` ve `psalm.xml` dosyalarına yansıtın.
- Ön yüz varlıkları `resources/js`, `resources/css` ve `resources/views` altında konumlandırıldığında lint kuralları doğrudan uygulanır.

## Bakım Döngüsü

1. Laravel veya Vue ana sürüm güncellemelerinde konfigürasyon dosyaları gözden geçirilir.
2. Yeni kural ekleme veya hariç tutma ihtiyaçlarında RFC oluşturulur, etkisi `docs/engineering/code-review.md` içinde duyurulur.
3. Güncellenen dosyalar `CHANGELOG.md` ve README sürüm tablosuna işlenerek izlenebilirlik korunur.

## Toplu Komut (Run Quality Suite)

- Yerel doğrulama için `./tools/run-quality-suite.sh` script’i kullanılabilir.
- Script, bu belgede listelenen PHP ve front-end araçlarını sırayla çalıştırır; eksik kurulumlarda ⚠️ uyarısı üretir.
- PHP kalite araçları henüz kurulmadıysa script `composer install --no-ansi --no-interaction --no-progress --prefer-dist`, frontend lint araçları eksikse `npm install --no-audit --progress false` komutlarını otomatik tetikler.
- Script çıktıları PR kontrol listesinde (`docs/engineering/pr-checklist.md`) raporlanarak inceleme sürecine eklenmelidir.

## Hata Önleme

- `ignore` veya `baseline` tanımları gerekçesiz eklenemez; kod incelemesinde referans issue/link sunulmalıdır.
- CI başarısız olduğunda araç versiyonları doğrulanır ve `config/environment/` dizinindeki örnekler güncellenir.
- Takımlar yeni modül oluşturduğunda ilgili dizinlerin lint kapsamına alındığını kontrol eder; gerekirse bu rehbere PR gönderilir.
