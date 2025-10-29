# Statik Analiz & Otomatik Kontroller Rehberi

Bu doküman, TUDAK Afet Yönetim Sistemi kod tabanında kullanılan statik analiz, güvenlik taraması ve otomatik kalite kontrollerini
tanımlar. Rehber; Faz 11 (DevOps & İzlenebilirlik) ve Faz 12 (Testler) çıktılarıyla, `docs/engineering/deployment-gates.md`
belgesindeki kalite kapılarıyla entegredir.

## Kullanılan Araçlar

| Araç | Kapsam | Çalışma Noktası |
| --- | --- | --- |
| **PHPStan** | PHP konfigürasyon ve yardımcı dosyalarında statik analiz | Pre-commit, CI `analyse` işi |
| **PHP-CS-Fixer** | Stil/format kontrolü | Pre-commit, CI `style` işi |
| **Psalm (opsiyonel)** | Güvenlik hassas veri akış analizi | Haftalık güvenlik taraması |
| **ESLint + Vue ESLint** | Vue/JS kod kalitesi ve best practice kontrolleri | Pre-commit, CI `frontend-lint` |
| **Stylelint** | Tailwind ve genel CSS kuralları | Pre-commit |
| **Trivy / Composer Audit** | Bağımlılık zafiyet taraması | Gece yarısı cron, release öncesi |

## Konfigürasyon Dosyaları

- **`phpstan.neon.dist`:** Larastan bağımlılığı olmadan `config/` dizininde seviye 5 analizi çalıştırır; `build/phpstan/` dizininde geçici dosyalar üretir.
- **`psalm.xml`:** Faz 4, 7 ve 18’e ait hassas akışları aylık denetimler için `errorLevel=3` hassasiyetinde tarar.
- **`phpcs.xml` & `.php-cs-fixer.dist.php`:** PSR-12 tabanlı stil kurallarını, kısa dizi sözdizimini ve `declare(strict_types=1)` zorunluluğunu içerir.
- **`.eslintrc.cjs`:** Vue 3 + TypeScript projeleri için tavsiye edilen kuralları ve jest test dosyalarına özel ortam ayarlarını etkinleştirir.
- **`stylelint.config.cjs`:** Tailwind ağırlıklı CSS için erişilebilirlik ve sıralama kurallarını uygular; `!important` kullanımını engeller.

## Toplu Çalıştırma (Yerel)

- `./tools/run-quality-suite.sh` komutu, yukarıdaki araçların tamamını ardışık olarak çalıştırır.
- Script mevcut olmayan araçları atlayarak ⚠️ uyarısı üretir; ayrıntılı kullanım için `docs/engineering/quality-suite.md` rehberini inceleyin.
- Geliştiriciler PR açmadan önce scripti çalıştırmalı, başarısız olan adımlar için düzeltme yaptıktan sonra yeniden denemelidir.

## Pipeline Entegrasyonu

1. **Pre-commit Hook:**
   - `composer analyse` (PHPStan) ve `npm run lint` (ESLint/Stylelint) çalıştırılır.
   - Hatalar düzeltmeden commit oluşturulamaz; gerekiyorsa `docs/engineering/coding-standards.md` referans alınır.

2. **CI Adımları:**
   - `analyse` aşaması `phpstan.neon.dist` dosyasını kullanarak PHPStan’ı seviye 5’te çalıştırır; Laravel uygulaması devreye alındığında seviye artırımı değerlendirilir.
   - `frontend-lint` aşaması ESLint’i `--max-warnings=0` ile koşturur.
   - `security-audit` aşaması `composer audit` ve `npm audit --production` komutlarını içerir; kritik bulgularda pipeline başarısız olur.
   - Rapor sonuçları `observability/metrics-catalog.md` içinde “Static Analysis Findings” metriğine yazılır.

3. **Planlı Taramalar:**
   - Haftalık cron job, `Trivy` ile konteyner imajını tarar; çıktılar `security/vuln-register.csv` dosyasına işlenir.
   - Aylık olarak `Psalm` yüksek riskli veri akış modüllerinde (Faz 4, 7, 18) çalıştırılır.

## Çıktı Yönetimi

- Pipeline raporları `docs/tests/latest` dosyasında referans gösterilir.
- Kritik bulgular için issue açılır ve `security/threat-hunt/` incelemelerine bağlanır.
- Tekrarlayan hata kategorileri retrospektif aksiyon planına eklenir (`docs/retrospective/README.md`).

## Hata Önleme

- Statik analiz seviyeleri düşürülemez; yeni kural muafiyetleri için RFC zorunludur.
- `ignore` anotasyonları kod gözden geçirmesinde gerekçelendirilmelidir.
- Audit log / PII içeren modüllerde (Faz 2, 4, 7) `@sensitive` etiketi kullanılarak kod inceleme sırasında ekstra doğrulama yapılır.

## Güncelleme Döngüsü

1. Yeni araç veya kural eklenecekse `docs/engineering/deployment-gates.md` ve ilgili CI konfigürasyon dosyalarını güncelleyin.
2. Değişiklikleri `CHANGELOG.md` ve README versiyon tablosuna kaydedin.
3. Engineering kalite oturumlarında raporlanan false-positive kayıtlarını değerlendirip rehberi revize edin.

> _Not:_ Rehberdeki araç sürümleri `config/environment/example.yaml` dosyasında minimum versiyon olarak takip edilir.
