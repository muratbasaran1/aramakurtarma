# TUDAK Afet Yönetim Sistemi — Değişiklik Günlüğü

Bu dosya, uygulama ve dokümantasyon değişikliklerinin özetini tutmak için kullanılır.

## Kullanım Kuralları
- Her önemli değişiklik için tarih, sürüm veya git etiketi ve kısa açıklama ekleyin.
- İlgili faz veya yönetişim bölümüne bağlantı verin.
- Aynı sürüm altında birden fazla değişiklik olduğunda madde işareti kullanın.

## Taslak Örnek
### 2024-07-05
- README yönetişim bölümleri genişletildi.
- Açık veri paylaşımları için `open-data/` dizini oluşturuldu.
### 2024-07-08
- Operasyonel runbook’lar faz bazlı tetikleyici, iletişim ve kontrol listeleriyle güncellendi.
### 2024-07-09
- OpsCenter alarm eskalasyonu ve hareketsizlik müdahalesi için yeni runbook’lar eklendi.
- Dijital ikiz veri seti kataloğu, senaryo rehberi ve doğrulama checklist’i yayımlandı.
- Tehdit programı besleme, playbook ve rapor klasörleri içeriklerle dolduruldu.
### 2024-07-10
- Test yönetişim rehberi ve faz → senaryo kapsama matrisi yayınlandı.
- OpsCenter performans benchmark raporu oluşturuldu, kapasite günlüğü güncellendi.
- `analytics/benchmark/` dizini için şablon ve rapor yapısı tanımlandı.
### 2024-07-11
- Etik kurul ajandası için `docs/ethics/` klasörü ve inceleme takvimi yayımlandı.
- Personel refah programı belgeleri ve checklist’i (`docs/wellbeing/`, `hr/wellbeing-program.md`) eklendi.
- Topluluk geri bildirim yönetişimi ve katılım logları (`docs/community/`, `community/engagement-log.csv`) oluşturuldu.
### 2024-07-12
- Gözlemlenebilirlik programı için `observability/` rehberi, alarm kuralları ve servis sağlık checklist’i yayınlandı.
- OpsCenter dashboard tanımı güncellenerek benchmark ve runbook bağlantıları eklendi.
### 2024-07-13
- Metrik kataloğu (`observability/metrics-catalog.md`) ve SLO kayıt defteri (`observability/slo-register.md`) yayımlandı.
- Gözlem inceleme notları için `observability/reviews/` dizini ve ilk OpsCenter raporu oluşturuldu; kapasite günlüğü güncellendi.

### 2024-07-14
- Kaos mühendisliği programı planlandı ve ilk ağ bölünmesi tatbikatı kayda alındı.
- On-call vardiya yönetimi bölümü hazırlandı; handover şablonu ve rotasyon kayıtları eklendi.
### 2024-07-15
- `.gitattributes` ile `.ics` takvim dosyalarının metin olarak işlenmesi sağlandı; PR oluştururken yaşanan ikili dosya hatası giderildi.
### 2024-07-16
- “Devam et” talimatlarıyla oluşan dokümantasyon ve kayıt klasörlerini açıklayan rehber yayımlandı (`docs/governance/devam-et-yapi-rehberi.md`).
- README’ye özet tablo ve referans eklendi; sürüm tablosu güncellendi.
### 2024-07-17
- Siber sigorta programı detaylılaştırıldı (`docs/governance/cyber-insurance.md`) ve README bağlantısı eklendi.
- Delil zinciri işletim rehberi yayımlandı (`security/chain-of-custody.md`); yönetişim rehberi ve versiyon geçmişi güncellendi.
### 2024-07-18
- Mühendislik uygulama rehberleri için `docs/engineering/` klasörü oluşturuldu (kod inceleme, branching, CI/CD kalite kapıları).
- README yönetişim bölümü mühendislik standartlarıyla genişletildi; “Devam Et” rehberi yeni klasörü referanslayacak şekilde güncellendi.
### 2024-07-19
- Kodlama standartları, statik analiz ve bağımlılık yönetimi rehberleri yayımlandı (`docs/engineering/coding-standards.md`, `static-analysis.md`, `dependency-management.md`).
- README ve “Devam Et” rehberi yeni mühendislik dokümanlarıyla güncellendi; sürüm tablosuna v0.17 kaydı eklendi.
### 2024-07-20
- Kod kalite araç konfigürasyonları depoya eklendi (`.php-cs-fixer.dist.php`, `phpcs.xml`, `phpstan.neon.dist`, `psalm.xml`, `.eslintrc.cjs`, `stylelint.config.cjs`).
- `docs/engineering/tooling-configuration.md` rehberi yayımlandı; README, Devam Et rehberi ve mühendislik indeksine yeni bağlantılar eklendi.
### 2024-07-21
- PR otomasyonunun ikili dosya hatasına takılmaması için `.xlsx` ve `.pdf` placeholder'ları CSV/Markdown formatına dönüştürüldü; ilgili belgeler güncellendi.
