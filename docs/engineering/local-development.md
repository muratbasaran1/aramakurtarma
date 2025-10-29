# Yerel Geliştirme Rehberi

Bu rehber, TUDAK Afet Yönetim Sistemi üzerinde çalışan ekiplerin yerel geliştirme ortamlarını hızlı ve tutarlı şekilde kurabilmeleri için hazırlanmıştır. İçerik, Faz 0 teknoloji kararları ile Faz 11/12’de tanımlanan DevOps ve test yükümlülüklerini günlük geliştirme döngüsüne entegre eder.

## 1. Ön Koşullar
- **İşletim Sistemi:** macOS, Linux (Ubuntu 22.04+) veya WSL2 ile Windows 11.
- **PHP:** 8.3.x, `intl`, `mbstring`, `zip`, `pdo_mysql`, `pcntl`, `bcmath` eklentileri aktif.
- **Composer:** 2.6+ sürümü.
- **Node.js:** 20.x LTS (Tailwind/Vue bileşenleri için).
- **MySQL:** 8.0.x (InnoDB + Spatial). Yerelde Docker veya yönetilen hizmet kullanılabilir.
- **Redis:** Kuyruk ve WebSocket testleri için önerilir.
- **Ek Araçlar:** Git, cURL, Make (opsiyonel), `jq` (log ve JSON çıktıları için).

> _Not:_ Kurumsal cihazlarda MDM politikaları gereği ek izinler gerekiyorsa `docs/engineering/tooling-configuration.md` ve `docs/governance/cyber-insurance.md` yönergelerine başvurun.

## 2. İlk Kurulum Adımları
1. **Depoyu klonlayın**
   ```bash
   git clone git@github.com:tudak/afet-yonetim-sistemi.git
   cd afet-yonetim-sistemi
   ```
2. **Ortam dosyalarını hazırlayın**
   - Depo kökündeki `.env.example` dosyasını inceleyip ihtiyaçlarınıza göre güncelleyin.
   - Laravel uygulaması için `cp backend/.env.example backend/.env` komutunu çalıştırın ve gizli değerleri doldurun.
   - `config/environment/example.yaml` içindeki tenant/sunucu örneklerini kendi ortamınıza uyarlayın.
3. **PHP bağımlılıklarını yükleyin (kalite araçları)**
   ```bash
   composer install --no-ansi --no-interaction --no-progress --prefer-dist
   ```
4. **PHP bağımlılıklarını yükleyin (Laravel backend)**
   ```bash
   composer --working-dir=backend install --no-ansi --no-interaction --no-progress --prefer-dist
   ```
5. **Frontend bağımlılıkları**
   ```bash
   npm install --no-audit --progress false
   ```
   > `./tools/run-quality-suite.sh` script’i bu adımı otomatik tetikler; yerel kurulumda bekleme süresini azaltmak için ilk çalıştırmadan önce manuel olarak uygulamanız önerilir.
   ```bash
   cd backend
   npm install
   ```
   > Laravel Vite varlıklarını derlemek için backend dizininde yer alan bağımlılıkları da kurmayı unutmayın.
6. **Uygulama anahtarını oluşturun**
   ```bash
   cd backend
   php artisan key:generate
   ```
7. **Veritabanını hazırlayın**
   ```bash
   php artisan migrate --seed
   ```
8. **Queues & websocket servislerini başlatın** (gerektiğinde)
   ```bash
   php artisan horizon
   php artisan websockets:serve
   ```
   > Tüm artisan komutları `backend/` dizininde çalıştırılmalıdır. Farklı `.env` varyantları kullanıyorsanız `php artisan <komut> --env=<dosya>` seçeneğini ekleyin.

## 3. Günlük Geliştirme Akışı
| Adım | Komut | Açıklama |
| --- | --- | --- |
| Kalite suite | `./tools/run-quality-suite.sh` | İkili taraması, PHP lint/analiz ve frontend lint işlemlerini tek komutta yürütür; eksik vendor araçlarını otomatik olarak hem kök hem de `backend/` dizininde `composer install` ile yükler. |
| PHP testleri | `cd backend && php artisan test` *(planlanıyor)* veya ilgili `phpunit`/`pest` komutu | Modül bazlı testleri çalıştırarak regresyon riskini azaltın. |
| Laravel sunucusu | `cd backend && php artisan serve` | API ve Blade arayüzünü yerelde doğrulamak için. |
| Frontend derlemesi | `npm run dev` | Vite tabanlı geliştirme sunucusunu açar. |
| Queue işleyicisi | `cd backend && php artisan queue:work` | Offline kuyruk senaryolarını doğrulamak için. |

## 4. Kontrol Listesi
- [ ] `./tools/check-binary-files.sh` ile PR öncesi ikili dosya taraması yapıldı.
- [ ] `composer install` ve `composer --working-dir=backend install` komutları sonrası `composer diagnose` çıktısı uyarısız.
- [ ] `npm audit --production` kritik bulgu üretmiyor; varsa `docs/engineering/dependency-management.md` politikalarına göre işlem yapıldı.
- [ ] `backend/.env` veya türetilmiş `.env.local` dosyalarında hassas bilgiler commit edilmedi (gitignore kontrolü).
- [ ] `storage/logs/laravel.log` içinde hata kalmadı; kritik bulgular `observability/` kayıtlarıyla eşleştirildi.

## 5. Sık Karşılaşılan Sorunlar
- **Vendor binary bulunamadı hatası:** `./tools/run-quality-suite.sh` script’i eksik PHP araçlarını otomatik kurar; hâlâ hata alıyorsanız `composer install` komutunu elle çalıştırıp `vendor/bin` dizinine erişim izinlerini kontrol edin.
- **MySQL Spatial eklentisi eksik:** Docker kullanıyorsanız `mysql:8-oracle` imajını tercih edin; yerel kurulumda `INSTALL PLUGIN` komutlarıyla `ha_srs` eklentisini aktive edin.
- **Queue bağlantı hatası:** `.env.local` içinde `QUEUE_CONNECTION=database` seçeneğini kullanın; prod yapılandırması için `docs/runbook/offline-edge-recovery.md` referans alın.
- **Node modülü uyuşmazlığı:** `rm -rf node_modules && npm install` komutlarıyla temiz kurulum yapın; yine sorun varsa `package.json` sürüm pinlerini kontrol edin ve RFC sürecine danışın.

## 6. Sorumluluklar & İzlenebilirlik
- Yerel ortam kurulumunda yapılan değişiklikler (ör. yeni script, ek bağımlılık) `docs/engineering/pr-checklist.md` ve `CHANGELOG.md` dosyalarına işlenmelidir.
- Yeni makine kurulumlarında bu belgeye göre checklist tamamlandığına dair not, takım içi onboarding kayıtlarına (`docs/onboarding/`) eklenir.
- Yapılan güncellemeler README’deki [Mühendislik Uygulama Yönetişimi](../../README.md#mühendislik-uygulama-yönetişimi) bölümüne ve `docs/governance/devam-et-yapi-rehberi.md` tablosuna yansıtılmalıdır.

## 7. Referanslar
- `docs/engineering/env-management.md`
- `docs/engineering/tooling-configuration.md`
- `docs/engineering/quality-suite.md`
- `docs/engineering/pr-checklist.md`
- `docs/governance/devam-et-yapi-rehberi.md`
- `README.md` → “Toplu Kalite Kontrol Suite”, “Kodlama Standartları”, “Statik Analiz & Otomatik Kontroller” bölümleri

