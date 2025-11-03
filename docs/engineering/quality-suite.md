# Toplu Kalite Kontrol Suite Rehberi

Bu doküman, `tools/run-quality-suite.sh` script’inin kullanımını ve ürettiği çıktıları açıklar. Araç; Faz 11 DevOps kalite kapıları ve Faz 12 test kapsamı kapsamında istenen temel kontrolleri tek komutla çalıştırarak PR hazırlık sürecini hızlandırır.

## Amaç
- PR açmadan önce manuel olarak koşulan kalite adımlarını tek komutta toplamak.
- Eksik araç kurulumlarında geliştiriciyi uyarmak ve hangi kontrolün atlandığını raporlamak.
- `docs/engineering/pr-checklist.md` içindeki kontrollerin otomasyonla desteklenmesini sağlamak.

## Çalışma Şekli
Script aşağıdaki adımları sırasıyla çalıştırır:

- **Ön kontrol (Composer - kök):** `vendor/bin/` altında PHP kalite araçları bulunmuyorsa `composer install --no-ansi --no-interaction --no-progress --prefer-dist` komutu otomatik çalıştırılır.
- **Ön kontrol (Composer - backend):** `backend/composer.json` bulunduğu halde `backend/vendor/autoload.php` yoksa `composer --working-dir=backend install --no-ansi --no-interaction --no-progress --prefer-dist` komutu tetiklenir.
- **Ön kontrol (npm):** `package.json` mevcut olup `node_modules` altında ESLint/Stylelint ikilileri bulunmuyorsa `npm install --no-audit --progress false` komutu tetiklenir.
1. `tools/check-binary-files.sh` ile staged dosyalarda ikili içerik taraması.
2. `vendor/bin/php-cs-fixer` ile PSR-12 format kontrolü (`--dry-run --diff`).
3. `vendor/bin/phpcs` ile PHP kod standartları denetimi (`phpcs.xml`).
4. `vendor/bin/phpstan` ile statik analiz (`phpstan.neon.dist`).
5. `vendor/bin/psalm` ile güvenlik hassas veri akışı taraması.
6. `npm run lint` ile ESLint + Stylelint kontrolleri.

> PHP adımlarının tamamı `composer.json` içindeki dev bağımlılıklara dayanır. Script, `vendor/bin/` altındaki araçları bulamazsa `composer install` komutunu otomatik çalıştırır; güncellemeler `composer.lock` üzerinden takip edilir.

> Script, ilgili araç veya yapılandırma bulunamadığında adımı **atlar** ve kullanıcıya ⚠️ mesajı üretir. Bu sayede CI ortamı kurulmadan yerel doğrulama yapılabilir.

> `npm install` adımının otomatik çalışabilmesi için makinede Node.js 20+ ve npm komutlarının yüklü olması gerekir; kurulum tamamlandıktan sonra `package-lock.json` güncel tutulmalıdır.

## Kullanım
```bash
./tools/run-quality-suite.sh
```

- Komut depo kök dizininde çalıştırılmalıdır.
- Script başarıyla tamamlandığında `exit 0` döndürür. Herhangi bir araç hatayla dönerse script `exit 1` ile sonuçlanır.
- `npm run lint` adımının çalışabilmesi için `package.json` içinde `"lint"` script’inin tanımlı olması gerekir.

## Çıktı Örneği
```
=== PHP-CS-Fixer (dry-run) ===
✅ PHP-CS-Fixer (dry-run) tamamlandı

=== PHPStan ===
❌ PHPStan başarısız (kod: 1)
```

- Başarısız adımlar için hataların düzeltilmesi beklenir. Düzeltilen her adım sonrası script yeniden çalıştırılmalıdır.

## Kayıt ve İzleme
- Script’e ilişkin güncellemeler `CHANGELOG.md` ve README sürüm geçmişine işlenmelidir.
- Araç çıktılarına dair hatırlatıcılar `docs/engineering/pr-checklist.md` içinde referanslanır.
- CI pipeline’ında scriptin birebir çıktısı kullanılmıyorsa bile, `deployment-gates.md` içinde yer alan kalite kapılarıyla eşleşen kontrollerin koşulduğu doğrulanmalıdır.
