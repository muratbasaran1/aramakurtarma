# PR Öncesi Kontrol Listesi

Bu kontrol listesi, TUDAK Afet Yönetim Sistemi deposunda **GitHub PR** oluştururken karşılaşılan "ikili dosya desteklenmez" hataları ve kalite kapılarını proaktif olarak yakalamanıza yardım eder.

> **Hızlı Yol:** Aşağıdaki adımlara başlamadan önce `./tools/run-quality-suite.sh` komutunu çalıştırarak kalite kontrollerini tek seferde koşturabilirsiniz. Script `vendor/bin/` altında araçları bulamazsa `composer install`, `package.json` mevcut olup `node_modules` eksikse `npm install --no-audit --progress false` komutlarını otomatik çalıştırır. Ayrıntılar için `docs/engineering/quality-suite.md` rehberine bakın.
> **Hızlı Yol:** Aşağıdaki adımlara başlamadan önce `./tools/run-quality-suite.sh` komutunu çalıştırarak kalite kontrollerini tek seferde koşturabilirsiniz. Script `vendor/bin/` altında araçları bulamazsa `composer install` komutunu otomatik çalıştırır. Ayrıntılar için `docs/engineering/quality-suite.md` rehberine bakın.

## 1. Hazırlık
- [ ] Son değişiklikleri `work` dalında çek (`git pull --rebase`).
- [ ] Değişikliklerin faz/yönetişim rehberleriyle uyumlu olduğunu doğrula.
- [ ] Gerekliyse ilgili runbook, rehber veya kayıt dosyalarını güncelle.

## 2. İkili Dosya Kontrolü
- [ ] `./tools/check-binary-files.sh` komutunu çalıştırarak staged dosyalarda ikili içerik olmadığını doğrula.
  - Komut `❌` sonucu üretirse, listelenen dosyaları metin tabanlı formata dönüştür veya `.gitattributes` dosyasına uygun diff tanımı ekle.
  - Dönüşüm işlemini tamamladıktan sonra komutu yeniden çalıştır.

## 3. Kod Kalitesi ve Statik Analiz
- [ ] PHP kalite araçları için `composer install` komutunun çalıştırıldığını doğrula (ilk kurulum veya yeni bağımlılık eklenmişse).
- [ ] Laravel backend için `composer --working-dir=backend install` komutunun güncel olduğundan emin ol.
- [ ] PHP kodu için `vendor/bin/php-cs-fixer fix --dry-run --diff` (veya `composer php-cs-fixer` kısayolu) komutunu çalıştır.
- [ ] `vendor/bin/phpcs` ve `vendor/bin/phpstan analyse` komutlarını (proje gereksinimlerine göre) uygula.
- [ ] `vendor/bin/psalm` çıktılarını kontrol et.
- [ ] JavaScript/TypeScript/Blade bileşenleri için `npm run lint` (ESLint + Stylelint) komutlarını çalıştır.
- [ ] Konfigürasyon değişikliklerinde ilgili `docs/engineering/tooling-configuration.md` rehberini takip ederek yapılandırma örneklerini güncelle.

> Not: CLI kısayolları için `docs/engineering/tooling-configuration.md` belgesindeki komut takma adlarına göz atın.

## 4. Test ve Senaryo Kapsamı
- [ ] Etkilenen modüllere göre uygun test katmanlarını (`phpunit`, `pest`, `npm test`, e2e vb.) çalıştır.
- [ ] Çıktıları `docs/tests/` klasöründeki matrise işle veya mevcut kayıtları güncelle.

## 5. Dokümantasyon ve Kayıtlar
- [ ] README veya yönetişim belgelerinde referans verilen artefaktları güncel tut.
- [ ] Yeni rehberler için `docs/governance/devam-et-yapi-rehberi.md` içinde kısa açıklama satırı ekle.
- [ ] Güncellemeleri `CHANGELOG.md` ve README sürüm geçmişine kaydet.

## 6. PR Açılışı
- [ ] PR açıklamasında değişiklik özetini, ilgili faz/rehber bağlantılarını ve çalıştırılan komutları listele.
- [ ] İnceleme sürecinde kullanılmak üzere gerekli CSV/Markdown kayıtlarına bağlantı ekle.

Bu kontrol listesi tamamlandıktan sonra PR oluşturduğunuzda Codex destekli otomasyonlar ikili dosya hatası vermeden değişiklikleri değerlendirebilir.
