# Incident Response Runbook

| Alan | Bilgi |
| --- | --- |
| İlgili Fazlar | Faz 1 (Güvenlik & Altyapı), Faz 7 (Kural Motoru), Faz 11 (DevOps) |
| Son Gözden Geçirme | 2024-07-08 |
| Sorumlu Takım | Güvenlik Operasyon Ekibi |

## Amaç ve Kapsam
Bu runbook, TUDAK platformunda tespit edilen siber güvenlik olaylarına 0–4 saat içinde
koordine yanıt verilmesini sağlar. Denetim izlerinin korunması, kullanıcı bildirimi ve
yasal yükümlülükler Faz 21 ve Faz 22 gereksinimleriyle uyumludur.

## Tetikleyiciler
- **Otomatik alarm**: Sentry, WAF ya da IDS üzerinden `SEV1/SEV2` etiketli uyarılar.
- **Kural motoru**: `security.incident_detected` tetikleri.
- **Kullanıcı raporu**: OpsCenter veya vatandaş portalı üzerinden teyitli ihlal bildirimleri.

## Ön Koşullar
1. Son 24 saatlik audit log ve konfigürasyon yedekleri erişilebilir durumda olmalı.
2. `security/incidents/` dizininde güncel olay numaralandırma şablonu bulunmalı.
3. İlgili servislerin (OpsCenter, API, Edge Node) sağlık kontrolleri Prometheus üzerinde izleniyor olmalı.

## Yanıt Adımları
### 1. Tanımlama (0–15 dk)
1. Alarmı alan nöbetçi analist, olayın kapsamını `security/threat-intel-register.md`
   üzerinden doğrular.
2. `security/incidents/README.md` yönergesine göre yeni olay kimliği oluşturulur
   ve ilk gözlem `T+5 dk` içinde kaydedilir.
3. Yetkisiz erişimin devam edip etmediği kontrol edilir; gerekiyorsa ilgili API anahtarları devre dışı bırakılır.

### 2. Sınırlama (15–45 dk)
1. Etkilenen tenant ve servis bileşenleri belirlenir; `infra/rate-limit.tf` profilinde
temp. kısıtlama uygulanır.
2. Şüpheli oturumlar `config/limit-profile.yml` kuralı ile kapatılır ve zorunlu parola
   sıfırlama tetiklenir.
3. `runbook/opscenter-degradation.md` gereği kullanıcıya minimum hizmet sağlayacak
degrade mod aktive edilir.

### 3. Müdahale (45–120 dk)
1. Olayın kaynağına göre düzeltici yamalar veya `config/feature-flags.php` ile
gelir kısıtlaması uygulanır.
2. Kanıt toplama için `chain-of-custody.csv` dosyası güncellenir; harici medya yazılım
   ve loglar imzalı olarak arşivlenir.
3. Gerekiyorsa `runbook/data-restore.md` prosedürüyle temiz ortam kurulumu başlatılır.

### 4. Geri Yükleme (120–240 dk)
1. Hizmete dönüş planı `dr/backlog.csv` kayıtları üzerinden seçilir.
2. Performans metrikleri `observability/capacity-journal.md` ile takip edilir.
3. Normal operasyona geçildiğinde kullanıcı oturumları yeniden açılır, MFA zorunlu tutulur.

### 5. Kapanış (>240 dk)
1. `postmortem/initial.md` ve `postmortem/final.md` şablonları doldurulur.
2. `security/breach-reports/README.md` gereği ihlal bildirimleri (KVKK/partner) yapılır.
3. Öğrenilen dersler `docs/threat-program/lessons-learned.md` dosyasına aktarılır.

## İletişim & Eskalasyon
| Rol | Kanal | Yanıt Süresi |
| --- | --- | --- |
| OpsCenter Lideri | Signal/Telefon | ≤ 10 dk |
| Güvenlik Direktörü | Şifreli mail + telefon | ≤ 30 dk |
| Hukuk & KVKK Ekibi | `legal@tudak` + ticket | ≤ 60 dk |
| Kamu İletişimi | `communications/public/STATEMENT_TEMPLATE.md` | Gerektiğinde |

## Başarı Kriterleri
- Olay kimliği açıldıktan sonra 30 dk içinde sınırlama adımları tamamlanmış olmalı.
- Kapanış sonrası 24 saat içinde postmortem paylaşılmalı.
- İlgili tenant’lar için veri sızıntısı doğrulanmamış olmalı (0 doğrulanmış sızıntı).

## Referanslar
- `security/vuln-register.csv`
- `policies/task-assignment.md`
- `docs/tests/README.md` (güvenlik testleri)
- `docs/governance/2024-06-strategy-board.md`
