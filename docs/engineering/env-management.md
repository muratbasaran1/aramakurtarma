# Ortam Değişkeni Yönetimi Rehberi

Bu rehber, TUDAK Afet Yönetim Sistemi geliştirme ekiplerinin ortam değişkenlerini faz kararlarıyla uyumlu, izlenebilir ve güvenli şekilde yönetmesini sağlamak için hazırlanmıştır. İçerik; Faz 0 teknoloji tercihleri, Faz 1 güvenlik politikaları ve Faz 11 DevOps süreçlerinin gerektirdiği .env şablonlarını, YAML konfigürasyonlarını ve gizli anahtar operasyonlarını kapsar.

## 1. Şablonlar ve Dosya Yerleşimleri

| Dosya | Konum | Amaç |
| --- | --- | --- |
| `.env.example` | Depo kökü | Çoklu modül için başlangıç değişkenlerini içerir; Laravel backend örneğiyle eşleşir. |
| `backend/.env.example` | Laravel backend | Uygulamanın varsayılan ayarlarını içerir; `cp backend/.env.example backend/.env` ile çoğaltılır. |
| `.env.local` *(takım tarafından oluşturulur)* | Depo kökü (ignore) | Geliştiriciye özel değerleri taşır; asla commit edilmez. |
| `backend/.env` *(geliştirici oluşturur)* | Laravel backend (ignore) | Uygulamanın aktif konfigürasyonudur; gizli değerler burada tutulur. |
| `config/environment/*.yaml` | Konfig dizini | Ortam bazlı (dev/staging/prod) genel ayarları içerir; gizli bilgi içermez. |
| `storage/app/secrets/` *(opsiyonel)* | Uygulama storage | Şifrelenmiş gizli anahtar paketleri (örn. Firebase). |

> _Not:_ `.env` türevleri Git tarafından `.gitignore` üzerinden korunur; `.env.example` ise güncel varsayılanları göstermesi için versiyon kontrolünde tutulur.

## 2. Kurulum Akışı

1. `cp backend/.env.example backend/.env` komutu ile Laravel uygulamasının yerel dosyasını oluşturun.
2. Takım içi paylaşım için gerekiyorsa `cp .env.example .env.local` komutu ile depo kökünde referans bir dosya hazırlayın; yeni değişkenleri önce burada belgelendirin.
3. `APP_KEY` üretmek için `cd backend && php artisan key:generate` komutunu çalıştırın.
4. `config/environment/example.yaml` dosyasını kopyalayarak ortamınıza uygun `config/environment/dev.yaml` dosyasını hazırlayın.
5. Çoklu tenant ve izleme parametrelerini (`TENANT_DEFAULT_PROVINCE`, `TRACKING_PING_INTERVAL_*`) ekip standartlarına göre uyarlayın.
6. İlk çalıştırmada `./tools/run-quality-suite.sh` script’ini koşturup eksik bağımlılık ve konfigürasyon hatalarını erken yakalayın.

## 3. Güvenlik ve Rotasyon İlkeleri

- **Gizli Değerler:** Parolalar, token’lar ve API anahtarları `backend/.env` veya `.env.local` gibi ignore edilen dosyalarda ya da gizli yönetim sistemlerinde tutulmalı; YAML dosyaları yalın metin olarak kalmalıdır.
- **Rotasyon:** NETGSM, SMTP ve Firebase kimlik bilgileri en az 90 günde bir yenilenmeli; değişiklikler `security/vuln-register.csv` ve `docs/threat-program/lessons-learned.md` kayıtlarıyla ilişkilendirilmelidir.
- **Audit:** `backend/.env`/`.env.local` değişiklikleri sonrası kritik değişkenler (`APP_URL`, `QUEUE_CONNECTION`, `FEATURE_*`) `runbooks/opscenter/alarm-console-escalation.md` ve `observability/metrics-catalog.md` ile çakışma yaratmadığından emin olun.
- **Paylaşım:** Ortam dosyalarını hiçbir koşulda e-posta veya chat ile düz metin olarak paylaşmayın; gerektiğinde şifreli kasa (örn. Vault) kullanın.

## 4. Doğrulama Kontrolleri

| Kontrol | Komut / İşlem | Sıklık |
| --- | --- | --- |
| `.env.local` ile `.env.example` farkları gözden geçirilir | `git diff -- .env.example` | Her sprint başı |
| Backend `.env` doğrulaması | `cd backend && php artisan config:cache` çalıştırılır; hata alınırsa `.env`/`.env.local` güncellenir | Her deploy öncesi |
| Tenant konfig doğrulaması | `cd backend && php artisan tenants:list` *(planlanıyor)* | Aylık |
| İzleme parametreleri uyumu | `docs/engineering/static-analysis.md` ve `observability/alerts/opscenter.yml` referans alınır | Kritik değişiklik sonrası |

## 5. Olay Müdahalesi

- Yanlış yapılandırılan ortam değişkeni prod ortamını etkilerse `runbook/incident-response.md` ve `runbook/data-restore.md` adımlarını takip edin.
- Hareketsizlik alarmı veya OpsCenter bileşenleri etkilenirse ilgili runbook’lar (`runbooks/tracking/no-motion-alert.md`, `runbooks/opscenter/alarm-console-escalation.md`) tetiklenir.
- Rotasyon sırasında hata yaşanırsa `docs/governance/cyber-insurance.md` ve `security/chain-of-custody.md` kayıtları güncellenir.

## 6. İzlenebilirlik & Dokümantasyon

- Ortam şablonlarında yapılan değişiklikler `CHANGELOG.md` ve README sürüm tablosuna işlenmelidir.
- Takımlar kendi `.env.local` varyantlarında yeni anahtarlar tanımlarsa `docs/engineering/dependency-management.md` ve `docs/tests/` kayıtları güncellenmelidir.
- Denetimlerde kullanılmak üzere `.env.example` revizyonları `docs/governance/devam-et-yapi-rehberi.md` tablosunda listelenir.

## 7. Referanslar

- `docs/engineering/local-development.md`
- `config/environment/example.yaml`
- `docs/engineering/tooling-configuration.md`
- `runbooks/data-restore.md`
- `README.md` → “Mühendislik Uygulama Yönetişimi”, “Toplu Kalite Kontrol Suite”
