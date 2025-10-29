# Bağımlılık Yönetimi Politikası

Bu politika, TUDAK Afet Yönetim Sistemi’nin PHP, JavaScript ve sistem seviyesindeki bağımlılıklarını güvenli, güncel ve izlenebilir
şekilde yönetmek için hazırlanmıştır. Faz 0 teknoloji kararları, Faz 10 dış servis entegrasyonları ve Faz 22 siber güvenlik
gereksinimleriyle uyumludur.

## Sorumluluklar

- **Teknik Lider:** Stratejik bağımlılık seçimleri, sürüm politikalarının onayı.
- **Modül Takımları:** Kullanılan paketlerin dokümantasyonu, değişiklik etkisi analizi ve upgrade planlarının hazırlanması.
- **DevOps Ekibi:** CI/CD pipeline’larında audit entegrasyonu, üretim ortamı güncellemeleri ve geri alma (rollback) planları.

## Envanterleme

1. **Composer:**
   - `composer.lock` kaynak gerçeğidir; manuel düzenlenmez.
   - Her modül, kullanılan önemli paketleri `docs/engineering/dependency-management.md` altına referans tablosu olarak ekler.
   - `composer outdated --direct` çıktısı aylık olarak `tech-debt/backlog.csv` dosyasına işlenir.

2. **NPM/Yarn:**
   - `package-lock.json` veya `pnpm-lock.yaml` sürüm sabitlemesi sağlar.
   - Kritik UI/harita kütüphaneleri (`maplibre-gl`, `leaflet`, `pinia`) için minor sürüm geçişleri QA ortamında smoke test ile doğrulanır.

3. **Sistem Paketleri:**
   - Docker imajlarında kullanılan taban OS paketleri `config/environment/example.yaml` içinde referans olarak listelenir.
   - Güvenlik yamaları için haftalık `apt update` raporu `security/vuln-register.csv`ye eklenir.

## Güncelleme Politikası

- **Patch Sürümleri:** Kritik güvenlik veya hata düzeltmesi içeren patch sürümleri en geç 48 saat içinde uygulanır.
- **Minor Sürümler:** Ayda bir planlanan bakım penceresinde değerlendirilir; riskli modüller için feature flag kullanılır.
- **Major Sürümler:** RFC süreci gerektirir; breaking change analizi ve rollback planı zorunludur.

## Güvenlik Kontrolleri

- `composer audit` ve `npm audit --production` çıktıları `security/vuln-register.csv`de referans numarasıyla kayıt altına alınır.
- Harici servis SDK’ları için imzalı sürüm doğrulaması yapılır; kaynak doğrulama hash’i `chain-of-custody.csv` dosyasına eklenir.
- Lisans değişiklikleri `docs/governance/cyber-insurance.md` ve `open-data/` politikalarıyla çelişmemelidir; uyumsuzluk halinde hukuk ekibi bilgilendirilir.

## Test & Yayın Gereksinimleri

1. Upgrade branch’lerinde ilgili modül testleri (`phpunit`, `npm run test`) zorunludur.
2. OpsCenter etkileyen bağımlılıklar için yük testi (`docs/tests/matrix.md`) gözden geçirilir.
3. Release notlarında yeni paket sürümleri ve etkilediği fazlar listelenir (`release-notes/`).

## Kayıt Tutma

- Yapılan her upgrade için `docs/changelog/` altında kayıt açılır.
- Riskli güncellemeler `governance/risk-register.csv` dosyasında takip edilir.
- Geri döndürme senaryoları `runbook/rule-engine-hotfix.md` ve ilgili runbook’larda saklanır.

## İhlal Yönetimi

- Patch süresi aşıldığında otomatik olarak risk kaydı oluşturulur ve operasyon direktörüne eskale edilir.
- Güvenlik taramasında kritik zafiyet tespit edilirse, 24 saat içinde hotfix planı ve müşteri iletişimi hazırlanır.
- Lisans uyumsuzluğu bulunduğunda proje yönetimi ve hukuk ekipleri ile düzeltici aksiyon planı hazırlanır.

> _Not:_ Politika yılda iki kez (Mart & Eylül) gözden geçirilir; yeni bağımlılıkların entegrasyonu öncesinde bu dokümana eklenir.
