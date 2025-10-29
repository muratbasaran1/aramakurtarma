# TUDAK Afet Yönetim Sistemi

Bu dokümantasyon, TUDAK Afet Yönetim Sistemi için faz bazlı geliştirme planını ve güvenlik kritik kuralları özetler. Her faz, sistemin sürdürülebilir, güvenli ve ölçeklenebilir şekilde ilerlemesini sağlamak için belirlenmiştir.

## İçindekiler
- [Genel Bakış](#genel-bakış)
- [Kılavuz İlkeler](#kılavuz-ilkeler)
- [Roller ve Sorumluluklar](#roller-ve-sorumluluklar)
- [Fazlar Arası Bağımlılıklar](#fazlar-arası-bağımlılıklar)
- [RFC ve Karar Yönetimi](#rfc-ve-karar-yönetimi)
- [Sürümleme ve Yayın Döngüsü](#sürümleme-ve-yayın-döngüsü)
- [Üretim Süreci](#üretim-süreci)
- [Operasyon Sonrası Değerlendirme](#operasyon-sonrası-değerlendirme)
- [Bakım Pencereleri & İletişim](#bakım-pencereleri--iletişim)
- [Kontrol Listeleri](#kontrol-listeleri)
- [Yetkilendirme Matrisi](#yetkilendirme-matrisi)
- [Operasyonel KPI'lar & Alarm Eşikleri](#operasyonel-kpilar--alarm-eşikleri)
- [Saha Eğitim ve Tatbikat Takvimi](#saha-eğitim-ve-tatbikat-takvimi)
- [Risk ve Bağımlılık Matrisi](#risk-ve-bağımlılık-matrisi)
- [Servis Seviyesi Hedefleri (SLO & SLA)](#servis-seviyesi-hedefleri-slo--sla)
- [Gözlemlenebilirlik Metrik Kataloğu & İnceleme Programı](#gözlemlenebilirlik-metrik-kataloğu--inceleme-programı)
- [Denetim & Uyum Takvimi](#denetim--uyum-takvimi)
- [İletişim Kanalları & Eskalasyon Tablosu](#iletişim-kanalları--eskalasyon-tablosu)
- [Operasyonel Runbooklar](#operasyonel-runbooklar)
- [Olay Şiddet Seviyeleri & Müdahale Süreleri](#olay-şiddet-seviyeleri--müdahale-süreleri)
- [Konfigürasyon Yönetimi & Ortam Standartları](#konfigürasyon-yönetimi--ortam-standartları)
- [Veri Yaşam Döngüsü ve Arşivleme](#veri-yaşam-döngüsü-ve-arşivleme)
- [Monitoring & Alerting Playbook](#monitoring--alerting-playbook)
- [On-call & Vardiya Yönetimi](#on-call--vardiya-yönetimi)
- [Kaos Mühendisliği & Dayanıklılık Testleri](#kaos-mühendisliği--dayanıklılık-testleri)
- [Dokümantasyon Bakım Ritmi](#dokümantasyon-bakım-ritmi)
- [Yönetim Kurulları & Toplantı Ritmi](#yönetim-kurulları--toplantı-ritmi)
- [Bilgi Güvenliği Yetkinlik Matrisi](#bilgi-güvenliği-yetkinlik-matrisi)
- [Çok Katmanlı Haberleşme Protokolleri](#çok-katmanlı-haberleşme-protokolleri)
- [Sürekli İyileştirme & Geri Bildirim Döngüsü](#sürekli-iyileştirme--geri-bildirim-döngüsü)
- [Veri Kalitesi Sağlama Döngüleri](#veri-kalitesi-sağlama-döngüleri)
- [Bilgi Yönetimi & Knowledge Base](#bilgi-yönetimi--knowledge-base)
- [Faz Geçiş Kriterleri](#faz-geçiş-kriterleri)
- [Kapasite & Kaynak Planlama](#kapasite--kaynak-planlama)
- [Veri Kataloğu & Metadata Standartları](#veri-kataloğu--metadata-standartları)
- [Sürüm & Değişiklik Yönetimi Takvimi](#sürüm--değişiklik-yönetimi-takvimi)
- [Bilgi Varlık Envanteri & Sınıflandırma](#bilgi-varlık-envanteri--sınıflandırma)
- [DPIA & KVKK Etki Analizi Süreci](#dpia--kvkk-etki-analizi-süreci)
- [Saha Destek Hizmet Seviyesi Programı](#saha-destek-hizmet-seviyesi-programı)
- [Finansal ve Sözleşmesel Yükümlülük Yönetimi](#finansal-ve-sözleşmesel-yükümlülük-yönetimi)
- [Çapraz Faz Doğrulama Takvimi](#çapraz-faz-doğrulama-takvimi)
- [Operasyonel Veri Paylaşım Protokolleri](#operasyonel-veri-paylaşım-protokolleri)
- [API & Veri Sözleşmesi Standartları](#api--veri-sözleşmesi-standartları)
- [Kriz İletişim Planı](#kriz-iletişim-planı)
- [İş Sürekliliği & Felaket Kurtarma Planı](#iş-sürekliliği--felaket-kurtarma-planı)
- [Ekip Onboarding & Yetkinlik Matrisi](#ekip-onboarding--yetkinlik-matrisi)
- [Saha Güvenlik Protokolleri](#saha-güvenlik-protokolleri)
- [Kalite Güvence Kontrol Noktaları](#kalite-güvence-kontrol-noktaları)
- [Güvenlik Zafiyet Yönetimi & Patch Süreci](#güvenlik-zafiyet-yönetimi--patch-süreci)
- [Veri İhlali Bildirim & Olay Müdahale Planı](#veri-ihlali-bildirim--olay-müdahale-planı)
- [Performans & Kapasite İzleme Çerçevesi](#performans--kapasite-izleme-çerçevesi)
- [Çok Lokasyonlu Felaket Tatbikat Programı](#çok-lokasyonlu-felaket-tatbikat-programı)
- [Ekler](#ekler)
  - [Ek A — Faz Geçiş Kontrol Formu](#ek-a--faz-geçiş-kontrol-formu)
  - [Ek B — Kritik Rol İletişim Şablonları](#ek-b--kritik-rol-iletişim-şablonları)
  - [Ek C — Hızlı Referans Metrikleri](#ek-c--hızlı-referans-metrikleri)
  - [Ek D — Veri İhlali Bildirim Şablonu](#ek-d--veri-ihlali-bildirim-şablonu)
- [Siber Tehdit İstihbaratı Entegrasyonu](#siber-tehdit-istihbaratı-entegrasyonu)
- [Tehdit Avı & Purple Team Tatbikat Programı](#tehdit-avı--purple-team-tatbikat-programı)
- [Etik Kurul & İç Denetim Süreçleri](#etik-kurul--iç-denetim-süreçleri)
- [Personel Refahı & Psikolojik Destek Programı](#personel-refahı--psikolojik-destek-programı)
- [Topluluk ve Geri Bildirim Platformu](#topluluk-ve-geri-bildirim-platformu)
- [Saha Teknoloji Kitleri & Lojistik Destek](#saha-teknoloji-kitleri--lojistik-destek)
- [Üçüncü Taraf & Tedarikçi Yönetimi](#üçüncü-taraf--tedarikçi-yönetimi)
- [Model Yönetimi & Yapay Zeka Yönetişimi](#model-yönetimi--yapay-zeka-yönetişimi)
- [Enerji Sürekliliği & Sürdürülebilirlik Planı](#enerji-sürekliliği--sürdürülebilirlik-planı)
- [Paydaş Koordinasyon & Harici Tatbikatlar](#paydaş-koordinasyon--harici-tatbikatlar)
- [Kullanıcı Geri Bildirim & Memnuniyet Ölçümü](#kullanıcı-geri-bildirim--memnuniyet-ölçümü)
- [Sürdürülebilirlik & ESG Göstergeleri](#sürdürülebilirlik--esg-göstergeleri)
- [Uyumluluk Kanıt Setleri & Denetim Artefaktları](#uyumluluk-kanıt-setleri--denetim-artefaktları)
- [Kriz Sonrası Öğrenilen Dersler Programı](#kriz-sonrası-öğrenilen-dersler-programı)
- [Veri Girişi Standartları & Validasyon Çerçevesi](#veri-girişi-standartları--validasyon-çerçevesi)
- [Erişilebilirlik & Kullanılabilirlik Standartları](#erişilebilirlik--kullanılabilirlik-standartları)
- [Risk Kayıt Defteri & Önceliklendirme Süreci](#risk-kayıt-defteri--önceliklendirme-süreci)
- [Olay Sonrası Raporlama & Paydaş Sunumları](#olay-sonrası-raporlama--paydaş-sunumları)
- [Saha Operasyon Performans Puan Kartı](#saha-operasyon-performans-puan-kartı)
- [Dijital İletişim Arşivleme Standartları](#dijital-iletişim-arşivleme-standartları)
- [Bilgi Güvenliği Olay Bildirim Hattı](#bilgi-güvenliği-olay-bildirim-hattı)
- [Teknoloji Borcu Yönetimi](#teknoloji-borcu-yönetimi)
- [Yenilik & Ar-Ge Portföyü](#yenilik--ar-ge-portföyü)
- [Medya & Kamu İletişimi Yönetimi](#medya--kamu-iletişimi-yönetimi)
- [Veri Egemenliği & Veri Yerleşimi Politikası](#veri-egemenliği--veri-yerleşimi-politikası)
- [Sosyal Etki Ölçüm Çerçevesi](#sosyal-etki-ölçüm-çerçevesi)
- [Saha Güvenlik Sertifikasyon Programı](#saha-güvenlik-sertifikasyon-programı)
- [Kritik Tedarik Zinciri Sürekliliği Planı](#kritik-tedarik-zinciri-sürekliliği-planı)
- [Çok Faktörlü Kimlik Doğrulama Politikası](#çok-faktörlü-kimlik-doğrulama-politikası)
- [Harici Denetim & Sertifikasyon Yol Haritası](#harici-denetim--sertifikasyon-yol-haritası)
- [Veri Maskeleme & Anonimleştirme Standartları](#veri-maskeleme--anonimleştirme-standartları)
- [Mikroservis & API Rate Limit Politikası](#mikroservis--api-rate-limit-politikası)
- [Açık Veri & Paydaş İşbirliği Çerçevesi](#açık-veri--paydaş-işbirliği-çerçevesi)
- [Çevik Seremoni & Sprint Yönetimi](#çevik-seremoni--sprint-yönetimi)
- [Mühendislik Uygulama Yönetişimi](#mühendislik-uygulama-yönetişimi)
- [Kodlama Standartları](#kodlama-standartları)
- [Statik Analiz & Otomatik Kontroller](#statik-analiz--otomatik-kontroller)
- [Kod Kalite Konfigürasyonları](#kod-kalite-konfigürasyonları)
- [PR Öncesi Kontrol Listesi](#pr-öncesi-kontrol-listesi)
- [Toplu Kalite Kontrol Suite](#toplu-kalite-kontrol-suite)
- [Yerel Geliştirme Rehberi](#yerel-geliştirme-rehberi)
- [Bağımlılık Yönetimi Politikası](#bağımlılık-yönetimi-politikası)
- [Kod İnceleme Standartları](#kod-inceleme-standartları)
- [Branching & Release Gate Politikaları](#branching--release-gate-politikaları)
- [CI/CD Kalite Kapıları & İzleme](#cicd-kalite-kapıları--izleme)
- [Bilgi Güvenliği Metrikleri & Raporlama](#bilgi-güvenliği-metrikleri--raporlama)
- [Yönetişim Risk & Uyum Raporlama Döngüsü](#yönetişim-risk--uyum-raporlama-döngüsü)
- [Sürdürülebilir Tedarik İlkeleri](#sürdürülebilir-tedarik-ilkeleri)
- [Cihaz Yönetimi & Mobil Güvenlik Politikası](#cihaz-yönetimi--mobil-güvenlik-politikası)
- [Eğitim İçerik Yönetimi & Sertifikasyon İzleme](#eğitim-içerik-yönetimi--sertifikasyon-izleme)
- [Test Veri Yönetimi & Gizlilik Koruması](#test-veri-yönetimi--gizlilik-koruması)
- [Saha Geri Bildirim Entegrasyon Döngüsü](#saha-geri-bildirim-entegrasyon-döngüsü)
- [Siber Sigorta & Risk Transfer Stratejisi](#siber-sigorta--risk-transfer-stratejisi)
- [Delil Zinciri & Adli Bilişim Protokolleri](#delil-zinciri--adli-bilişim-protokolleri)
- [Vatandaş Veri Hakları & KVKK Başvuru Süreci](#vatandaş-veri-hakları--kvkk-başvuru-süreci)
- [Dashboard & Analitik Yayın Yönetimi](#dashboard--analitik-yayın-yönetimi)
- [Performans Benchmark & Kapasite Test Programı](#performans-benchmark--kapasite-test-programı)
- [Saha Haberleşme Donanımı Kalibrasyon Planı](#saha-haberleşme-donanımı-kalibrasyon-planı)
- [Açık Kaynak & Lisans Uyumluluğu Programı](#açık-kaynak--lisans-uyumluluğu-programı)
- [Dijital İkiz & Senaryo Modelleme Yönetişimi](#dijital-ikiz--senaryo-modelleme-yönetişimi)
- [Kriz Sonrası İyileştirme & Rehabilitasyon Çerçevesi](#kriz-sonrası-iyileştirme--rehabilitasyon-çerçevesi)
- [Devam Et Yapıları Rehberi](#devam-et-yapıları-rehberi)
- [Faz 0 — Kararlar (Temel Direkler)](#faz-0--kararlar-temel-direkler)
- [Faz 1 — Güvenlik & Altyapı](#faz-1--güvenlik--altyapı)
- [Faz 2 — Veri & Migrasyon (Şema Sertleştirme)](#faz-2--veri--migrasyon-şema-sertleştirme)
- [Faz 3 — Çekirdek Modüller (Olay–Görev–Envanter–Kullanıcı)](#faz-3--çekirdek-modüller-olay–görev–envanter–kullanıcı)
- [Faz 4 — Canlı Takip (Tracking)](#faz-4--canlı-takip-tracking)
- [Faz 5 — QR & Yoklama (Anti-Replay)](#faz-5--qr--yoklama-anti-replay)
- [Faz 6 — OpsCenter (Harita & Operasyon Merkezi)](#faz-6--opscenter-harita--operasyon-merkezi)
- [Faz 7 — Kural Motoru & Bildirimler](#faz-7--kural-motoru--bildirimler)
- [Faz 8 — İleri Analiz & Araçlar (ICS, Playback, Risk)](#faz-8--ileri-analiz--araçlar-ics-playback-risk)
- [Faz 9 — Offline & Edge](#faz-9--offline--edge)
- [Faz 10 — Dış Servis Entegrasyonları](#faz-10--dış-servis-entegrasyonları)
- [Faz 11 — DevOps & İzlenebilirlik](#faz-11--devops--izlenebilirlik)
- [Faz 12 — Testler (Unit/Feature/Integration/E2E/Load/Security)](#faz-12--testler-unitfeatureintegratione2eloadsecurity)
- [Faz 13 — Canlıya Hazırlık (GoLive)](#faz-13--canlıya-hazırlık-golive)
- [Faz 14 — Mobil Uygulama & Saha](#faz-14--mobil-uygulama--saha)
- [Faz 15 — Yapay Zeka & Karar Destek](#faz-15--yapay-zeka--karar-destek)
- [Faz 16 — Simülasyon & Tatbikat](#faz-16--simülasyon--tatbikat)
- [Faz 17 — Vatandaş & Gönüllü Portalı](#faz-17--vatandaş--gönüllü-portalı)
- [Faz 18 — Lojistik & Kaynak Yönetimi](#faz-18--lojistik--kaynak-yönetimi)
- [Faz 19 — Kurumlar Arası Entegrasyon](#faz-19--kurumlar-arası-entegrasyon)
- [Faz 20 — İleri Görselleştirme & Dashboard](#faz-20--ileri-görselleştirme--dashboard)
- [Faz 21 — Hukuk & Mevzuat Uyum](#faz-21--hukuk--mevzuat-uyum)
- [Faz 22 — Siber Güvenlik & Dayanıklılık](#faz-22--siber-güvenlik--dayanıklılık)
- [Faz 23 — Akıllı Donanım Entegrasyonu (IoT/Drone/Robot/Uydu)](#faz-23--akıllı-donanım-entegrasyonu-iotdronerobotuydu)
- [Faz 24 — Büyük Veri & Analitik](#faz-24--büyük-veri--analitik)
- [Faz 25 — Uluslararasılaştırma & Çok Dilli (i18n) ve Çoklu Tenant](#faz-25--uluslararasılaştırma--çok-dilli-i18n-ve-çoklu-tenant)
- [Faz 26 — Stratejik Yönetim & Politika](#faz-26--stratejik-yönetim--politika)
- [Hareketsizlik Güvenlik Kuralı (Sistem Geneli Kullanım)](#hareketsizlik-güvenlik-kuralı-sistem-geneli-kullanım)
- [Güncelleme Prosedürü](#güncelleme-prosedürü)
- [Terimler Sözlüğü](#terimler-sözlüğü)
- [Belge Versiyon Geçmişi](#belge-versiyon-geçmişi)
- [Referanslar & Kaynaklar](#referanslar--kaynaklar)

## Genel Bakış

TUDAK Afet Yönetim Sistemi, kurumların afet öncesi hazırlık, afet anı koordinasyonu ve afet sonrası iyileştirme süreçlerini uçtan uca yönetebilmesi için tasarlanmış çok katmanlı bir platformdur. Bu belge, sistemin geliştirme yol haritasını, fazlar arası bağımlılıklarını ve güvenlik kritik işleyiş kurallarını tek bir kaynakta toplar.

Belge, düzenli olarak güncellenen “tek gerçek kaynak” (single source of truth) niteliğindedir. Yeni ihtiyaçlar veya değişiklikler, burada tanımlı RFC süreci üzerinden değerlendirilir. Tüm ekipler için zorunlu referans dökümandır.

## Kılavuz İlkeler

1. **Güvenlik Önceliklidir:** Kimlik doğrulama, veri bütünlüğü ve operasyonel güvenlik kuralları tüm fazlarda birinci önceliktir.
2. **Tenant İzolasyonu:** İl bazlı çoklu tenant yapısı, veri ve operasyon ayrışmasını garanti eder.
3. **Gözlemlenebilirlik:** Audit log, metrikler ve alarm mekanizmaları her fazda etkin tutulur.
4. **Çevrimdışı Dayanıklılık:** Offline çalışma senaryoları yalnızca Faz 9’da değil tüm modüllerde dikkate alınır.
5. **İnsan-Onaylı Otomasyon:** Kural motoru ve yapay zeka çıktıları nihai kararı vermeden önce insan onayına sunulur.
6. **Şeffaflık ve İzlenebilirlik:** Her kritik aksiyon, kim tarafından ne zaman yapıldığıyla birlikte kayıt altındadır.

## Roller ve Sorumluluklar

| Rol | Sorumluluklar |
| --- | --- |
| **Ürün Sahibi** | Faz önceliklendirmesi, RFC onayı, kurum içi iletişim |
| **Teknik Lider** | Mimari kararlar, kod standartları, güvenlik denetimleri |
| **Modül Takımları** | Faz bazlı geliştirme, test, dokümantasyon |
| **DevOps** | CI/CD, gözlemlenebilirlik, altyapı ölçekleme |
| **Güvenlik Ekibi** | Pentest, zafiyet takibi, mevzuat uyum kontrolleri |
| **Saha Operasyonları** | OpsCenter kullanımı, tatbikat geri bildirimleri |

## Fazlar Arası Bağımlılıklar

- **Faz 0** tamamlanmadan diğer fazlar başlatılamaz.
- **Faz 1** güvenlik bileşenleri, Faz 3 ve sonrası modüller için zorunludur.
- **Faz 2** veri şeması, Faz 3–8 arası tüm fonksiyonlar tarafından kullanılır.
- **Faz 4** canlı takip verileri, Faz 6 harita ve Faz 7 kural tetiklerinin girdisidir.
- **Faz 6** OpsCenter, Faz 7 (kurallar), Faz 8 (analiz) ve Faz 18 (lojistik) verilerini birleştirir.
- **Faz 9** offline alt yapısı olmadan Faz 14 mobil uygulama sahada güvenli çalıştırılamaz.
- **Faz 12** test katmanları, CI/CD pipeline’ı (Faz 11) ile birlikte çalışır.
- **Faz 15** yapay zekâ modelleri, Faz 4, 6, 8 ve 18’den veri akışı olmadan eğitim alamaz.

## RFC ve Karar Yönetimi

1. Değişiklik teklifleri `docs/rfc/` altında numaralandırılmış Markdown dosyaları olarak açılır.
2. Teknik lider ve ilgili modül sahipleri 5 iş günü içinde inceleme yapar.
3. Onaylanan kararlar README’de ilgili faz altına özetlenir; eski kararlar “Deprecation” notuyla işaretlenir.
4. Kritik güvenlik değişiklikleri (Faz 1, Faz 22) için olağanüstü toplantı yapılır; kararlar aynı gün uygulanır.

## Sürümleme ve Yayın Döngüsü

- Semantic versioning (`MAJOR.MINOR.PATCH`) kullanılır.
- **MINOR** artırımı yeni faz veya büyük modül devreye alındığında yapılır.
- **PATCH** artırımı hata düzeltmeleri, güvenlik yamaları veya küçük iyileştirmeleri temsil eder.
- Her yayın için `release-notes/` dizininde fazlara göre gruplanmış değişiklik listesi bulunmalıdır.

## Üretim Süreci

Üretim (production) ortamına geçiş, aşağıdaki üç aşamada yönetilir. Her adım, ilgili fazların çıktıları üzerine inşa edilir ve geri dönüş planlarıyla desteklenir.

### 1. Hazırlık (Staging → Release Candidate)

| Kontrol Alanı | Beklenen Çıktı | İlgili Fazlar |
| --- | --- | --- |
| Güvenlik & Altyapı | Fortify, 2FA, Spatie yetki modeli staging ortamında aktif ve penetration test raporları güncel | Faz 1, Faz 22 |
| Veri Şeması & Migrasyon | Tüm zorunlu tablolar ve constraint’ler staging ve prod eşlenik; migrasyon guard simülasyonu tamamlandı | Faz 2 |
| Test Seti | Unit/feature/integration/E2E testleri yeşil; yük testi raporu `docs/tests/latest` dizininde | Faz 12 |
| Operasyonel Hazırlık | OpsCenter konfigürasyonu, alarm konsolu senaryoları, kural motoru dry-run kayıtları hazır | Faz 6, Faz 7 |
| Dokümantasyon | README, `CHANGELOG.md`, `release-notes/` ve SOP’ler güncel; on-call listesi yayımlandı | Faz 13 |

### 2. Geçiş (Release Candidate → Production)

1. **Değişiklik Dondurma:** RC etiketi sonrası kod sadece kritik düzeltmeler için açılır; kararlar RFC acil protokolü ile alınır.
2. **Canlı Veri Yedekleri:** Production veritabanı tam yedeklenir, şifreli olarak saklanır; geri yükleme testi yapılır (Faz 1).
3. **Gözetimli Deploy:** DevOps ekibi, CI/CD pipeline’ı üzerinden staging ile aynı artefaktı prod’a taşır. Pipeline logları 6 ay saklanır (Faz 11).
4. **Sağlık Kontrolleri:** `/health`, metrikler ve log akışları 30 dakika boyunca izlenir; anormallik halinde otomatik rollback tetiklenir.
5. **Yetkilendirilmiş Onay:** Ürün sahibi ve teknik lider üretim açılışını ortak olarak imzalar; OpsCenter’da “Canlı” modu işaretlenir.

### 3. Canlı İzleme & Sürekli İyileştirme

- **İlk 24 Saat:** Kural motoru tetikleri ve hareketsizlik alarmları manuel olarak gözden geçirilir; yanlış pozitifler için kural revizyonu yapılır (Faz 4, Faz 7).
- **Haftalık Değerlendirme:** Audit log örneklemesi, envanter hareketleri ve mobil uygulama kuyruk performansı raporlanır (Faz 3, Faz 9, Faz 14).
- **Aylık Tatbikat:** OpsCenter, mobil uygulama ve offline senaryoları kapsayan entegre tatbikat gerçekleştirilir; sonuçlar Faz 16 KPI panosuna işlenir.
- **Sürüm Kapanışı:** Her sürüm sonunda `release-notes/` girişi tamamlanır, ICS formları (201/202/204) otomatik güncellenir ve stratejik göstergeler Faz 26 panellerine aktarılır.

## Operasyon Sonrası Değerlendirme

Üretim geçişi tamamlandıktan sonra öğrenilen derslerin sistematik biçimde toplanması, sonraki faz güncellemelerinin daha güvenli ilerlemesi için kritik önemdedir.

1. **Ders Çıkarma Oturumu (T+48 Saat):** Ürün, teknik, güvenlik ve saha ekipleri bir araya gelerek yeni sürümde yaşanan olayları değerlendirir. Her bulgu ilgili faz başlığına bağlanır ve `docs/retrospective/` altında kayıt altına alınır.
2. **Metriğe Dayalı Analiz:** Prometheus/Grafana panolarından elde edilen hata oranı, ping gecikmesi, alarm doğrulama süresi gibi metrikler Faz 11 ve Faz 4 hedefleriyle karşılaştırılır. Eşik aşımı olan metrikler için aksiyon planı açılır.
3. **Kullanıcı Geri Bildirimi:** OpsCenter operatörleri ve saha ekiplerinden gelen yorumlar Faz 6 ve Faz 14 backlog’una etiketlenir; kritik geri bildirimler için RFC açılması zorunludur.
4. **Güvenlik İncelemesi:** 2FA, audit log ve WAF kayıtları incelenir; olağan dışı aktiviteler için Faz 1 ve Faz 22 protokolleri devreye alınır.

Bu değerlendirme çıktıları, bir sonraki sürüm planlamasında önceliklendirme girdisi olarak kullanılır ve stratejik panellere (Faz 26) özetlenmiş şekilde yansıtılır.

## Bakım Pencereleri & İletişim

Planlı bakım çalışmalarının operasyonlara minimum etkiyle yürütülmesi için şeffaf bir iletişim modeli uygulanır.

- **Planlama:** Bakım talepleri en az 7 gün önce Change Advisory Board (CAB) takvimine işlenir. Faz 11 süreçleri gereği bakım paketleri staging ortamında doğrulanmadan canlıya alınmaz.
- **Duyuru:** Tenant yöneticileri ve kritik roller (OpsCenter lideri, saha koordinatörü) en az 72 saat önce bilgilendirilir. Bildirimler e-posta, push ve OpsCenter duyuru panelinden eş zamanlı gönderilir (Faz 7).
- **Bakım Modu:** Bakım sırasında sistem “degrade” moduna alınır; harita katmanları, kural motoru veya mobil kuyruk gibi etkilenecek bileşenler önceden listelenir. Kullanıcılara alternatif iş akışı sağlanır (örn. offline kuyruk aktivasyonu – Faz 9).
- **Tamamlama:** Bakım tamamlandığında sağlık kontrolleri çalıştırılır, sonuçlar CAB kaydına eklenir ve kullanıcılar normale dönüş konusunda bilgilendirilir.

## Kontrol Listeleri

### Yayın Öncesi Kontrol Listesi

| Kontrol | Sorumlu Rol | Faz Referansı |
| --- | --- | --- |
| Penetrasyon testi ve WAF kural güncellemeleri tamamlandı | Güvenlik Ekibi | Faz 1, Faz 22 |
| Migrasyon senaryoları idempotent olarak doğrulandı, rollback scriptleri hazır | Teknik Lider | Faz 2, Faz 11 |
| OpsCenter alarm senaryoları ve kural motoru dry-run kayıtları arşivlendi | OpsCenter Ekibi | Faz 6, Faz 7 |
| Mobil uygulama offline kuyruğu ve ping sıklığı stres testlerinden geçti | Mobil Takım | Faz 4, Faz 9, Faz 14 |
| Release notları, ICS formları ve eğitim materyali güncel | Ürün Sahibi | Faz 8, Faz 13 |

### Rollback Kontrol Listesi

1. **Tetik:** Kritik hata, güvenlik ihlali veya SLA aşımı Sentry/Prometheus alarmıyla doğrulanır (Faz 11).
2. **Karar:** Ürün sahibi, teknik lider ve DevOps 15 dakika içinde rollback kararı verir; karar audit log’a işlenir.
3. **Uygulama:** Son kararlı sürüme otomatik geri dönüş yapılır; DB değişiklikleri için migration guard skriptleri çalıştırılır (Faz 2).
4. **Doğrulama:** `/health` uç noktası, WebSocket bağlantıları ve hareketsizlik kuralı tetikleri kontrol edilir (Faz 4, Faz 7).
5. **Bilgilendirme:** Tüm tenant’lara durum güncellemesi ve tahmini çözüm süresi bildirilir; olay raporu 24 saat içinde paylaşılır.

### Kritik Olay Yönetim Kontrol Listesi

- **Olay Kaydı:** Incident Response Runbook (Faz 13) üzerinden olay ID’si açılır.
- **İletişim Zinciri:** OpsCenter lideri, güvenlik ekibi ve üst yönetim için çok kanallı bildirim tetiklenir (SMS, push, e-posta) (Faz 7, Faz 22).
- **Kanıt Toplama:** Audit log, kural motoru tetik kayıtları ve takip pingleri toplanarak olay klasörüne eklenir.
- **Geçici Çözümler:** Offline mod, geofence gevşetme veya manuel zimmet gibi geçici önlemler değerlendirilir (Faz 4, Faz 5, Faz 9).
- **Kapatma:** Olay çözümü sonrası root-cause analizi yapılır, öğrenilen dersler Operasyon Sonrası Değerlendirme bölümünde kullanılan formata uygun şekilde yayımlanır.

## Yetkilendirme Matrisi

| Aksiyon | Zorunlu Roller | Ek Onay | İlgili Fazlar |
| --- | --- | --- | --- |
| Kritik mimari karar | Teknik Lider, Ürün Sahibi | Güvenlik Ekibi (Faz 1) | Faz 0, Faz 1 |
| Yeni kural motoru kuralı yayını | Modül Sahibi | OpsCenter Lideri | Faz 4, Faz 7 |
| Production deploy | DevOps | Ürün Sahibi, Teknik Lider | Faz 1, Faz 11, Faz 13 |
| Offline kuyruk politikası değişikliği | Mobil Takım Lead | Güvenlik Ekibi | Faz 4, Faz 9, Faz 14 |
| Dış servis entegrasyonu aktivasyonu | Entegrasyon Ekibi | Teknik Lider, Güvenlik Ekibi | Faz 10, Faz 22 |
| KVKK hassas veri alanı güncellemesi | Veri Koruma Sorumlusu | Hukuk Birimi | Faz 1, Faz 21 |

Her aksiyon, audit log’da onaylayan kişi ve zaman damgasıyla kaydedilmek zorundadır. Yetki matrisi güncellendiğinde RFC açılır ve değişiklik README’de tarihlenir.

## Operasyonel KPI'lar & Alarm Eşikleri

| KPI | Hedef/Eşik | İzleme Yöntemi | İlgili Fazlar |
| --- | --- | --- | --- |
| Hareketsizlik alarmına cevap süresi | ≤ 60 sn (ortalama) | OpsCenter alarm konsolu, Prometheus metriği `no_motion_ack_seconds` | Faz 4, Faz 7 |
| OpsCenter harita yüklenme süresi | ≤ 2 sn (P95) | Frontend RUM, Grafana paneli | Faz 6, Faz 20 |
| Offline kuyruk işleme başarı oranı | ≥ %99 | Mobil telemetry, edge node logları | Faz 9, Faz 14 |
| Kritik stok alarm doğrulama süresi | ≤ 15 dk | Lojistik dashboard, webhook teslim raporu | Faz 18 |
| WAF engellenen tehdit-hata oranı | ≥ %95 | WAF raporları, Sentry güvenlik dashboard’u | Faz 1, Faz 22 |
| KVKK erişim talebi kapanış süresi | ≤ 72 saat | Mevzuat uyum panosu | Faz 21 |

Hedefler aşıldığında ilgili modül takımı post-mortem hazırlar. KPI revizyonu, stratejik paneller (Faz 26) ve sürüm planlama toplantılarında değerlendirilir.

## Saha Eğitim ve Tatbikat Takvimi

- **Aylık Mikro Tatbikat (OpsCenter + Mobil):** Faz 4, 6 ve 14 akışlarını kapsar; 2 saatlik senaryo ile hareketsizlik alarmı, QR yoklama ve offline kuyruk senaryoları test edilir.
- **Çeyreklik Entegre Tatbikat:** Faz 3–9 arası tüm süreçleri kapsayan 1 günlük saha tatbikatı; ICS formları otomatik doldurulur, playback raporu çıkarılır (Faz 8, Faz 16).
- **Yıllık Stratejik Simülasyon:** Faz 18–26 odaklı masa başı egzersizi; lojistik, kurumlar arası entegrasyon ve politika hedefleri gözden geçirilir.
- **Ad-hoc Güvenlik Drilleri:** WAF bypass, kimlik hırsızlığı ve offline edge senaryoları için Faz 1, 9 ve 22 ekiplerinin ortak çalışması; bulgular güvenlik backlog’una eklenir.

Her tatbikat sonrası geri bildirimler `docs/tatbikat/` dizinine tarih bazlı olarak eklenir ve Operasyon Sonrası Değerlendirme sürecinde takip edilir.

## Risk ve Bağımlılık Matrisi

| Risk | Etki | Bağımlı Fazlar | Azaltım Stratejisi |
| --- | --- | --- | --- |
| Dış servis kesintisi (AFAD/USGS) | Yüksek | Faz 6, Faz 10, Faz 20 | Cache + son geçerli veriyi saklama; manuel veri girişi formu; alarm throttling |
| Offline kuyruk dolması | Orta | Faz 4, Faz 9, Faz 14 | Kuyruk boyut limitleri, otomatik purge uyarısı, saha ekibine offline paket boşaltma eğitimi |
| Rol bazlı yetki yanlış konfigürasyonu | Yüksek | Faz 1, Faz 3, Faz 7 | Yetkilendirme Matrisi doğrulaması, otomatik rol testleri, çift onay |
| Hareketsizlik algoritmasında yanlış pozitif | Orta | Faz 4, Faz 7 | Kısa mola modu, sensör kalibrasyon raporları, KPI izleme |
| KVKK uyumsuz veri saklama | Yüksek | Faz 2, Faz 21 | Saklama politikası otomasyonu, veri imha raporları, hukuk onayı |
| OpsCenter performans düşüşü | Orta | Faz 6, Faz 20 | BBOX zorunluluğu, CDN cache, performans testleri (Faz 12) |

Risk listesi aylık olarak gözden geçirilir; yeni riskler için RFC açılır ve mitigasyon planı backlog’a eklenir.

## Servis Seviyesi Hedefleri (SLO & SLA)

| Servis/Kapsam | SLA (Dış Paydaş) | SLO (İç Hedef) | Ölçümleme Kaynağı | Operasyon Notları |
| --- | --- | --- | --- | --- |
| OpsCenter Web Uygulaması | %99.5 aylık çalışma süresi | %99.7 çalışma süresi, P95 yanıt süresi ≤ 2 sn | Uptime robotu, Grafana paneli, RUM ölçümleri | Planlı bakımda degrade mod duyurusu ≥ 72 saat önce yapılır; cache devrede tutulur (Faz 6, Faz 11, Faz 20). |
| WebSocket Canlı Takip | %99 aylık | Paket teslim başarısı ≥ %99.3, gecikme ≤ 3 sn | Prometheus `ws_delivery_success`, edge node logları | Hareketsizlik alarmı gecikme KPI’ı ile entegre; edge node failover senaryoları aylık test edilir (Faz 4, Faz 9). |
| Mobil Offline Kuyruk Servisi | %98.5 | Kuyruk boşaltma süresi ≤ 5 dk (P90) | Mobil telemetri, kuyruk tüketim raporları | Yüksek riskli bölgelerde edge node replikasyonu zorunlu; kuyruk kapasite alarmı 80% eşik (Faz 9, Faz 14). |
| Kural Motoru Bildirimleri | %99 | Aksiyon teslim oranı ≥ %99.5, hata oranı ≤ %0.5 | Queue metrikleri, webhook teslim raporları | Rate limit ve retry politikaları her sürümde doğrulanır; başarısız aksiyonlar 15 dk içinde yeniden kuyruğa alınır (Faz 7). |
| Yedekleme & Geri Yükleme Servisi | Tamamlanma süresi ≤ 4 saat | Tamamlanma süresi ≤ 2 saat, haftalık geri yükleme başarı oranı %100 | Backup logları, geri yükleme tatbikat raporları | Felaket kurtarma tatbikatı (Faz 1) sonrası sonuçlar CAB’e raporlanır; şifreleme anahtarı rotasyonu 90 günde bir yapılır (Faz 22). |

SLA’ler dış paydaşlara karşı taahhüttür; ihlal durumunda kritik olay süreci tetiklenir. SLO’lar ise iç ekiplerin sürekli iyileştirme hedeflerini temsil eder ve her çeyrek başında gözden geçirilir. Yeni fazlar devreye alındığında SLO tabloları genişletilir.

## Gözlemlenebilirlik Metrik Kataloğu & İnceleme Programı

- **Metrik Kataloğu:** `observability/metrics-catalog.md` dosyası, metrik kaynaklarını, sahiplerini ve tetiklediği runbook’ları listeler. Yeni metrikler RFC onayı almadan kataloğa eklenemez.
- **SLO Kayıt Defteri:** `observability/slo-register.md`, her inceleme döngüsündeki gerçekleşen değerleri ve alınan aksiyonları saklar; sapma olduğunda ilgili runbook’a referans verilmesi zorunludur.
- **İnceleme Notları:** Haftalık ve çeyreklik alarm gözden geçirmeleri `observability/reviews/` dizinine kaydedilir; takip öğeleri sprint planlarına taşınır.
- **Kapasite Günlüğü Eşleşmesi:** Kapasite artışları veya throttle değişiklikleri `observability/capacity-journal.md` üzerinde izlenir ve inceleme notlarında bağlantı verilir.

> _Önemli: İnceleme döngülerinde alınan tüm kararlar changelog’a ve README sürüm geçmişine yansıtılmalıdır._

## Denetim & Uyum Takvimi

| Dönem | Denetim Tipi | Kapsam | Sorumlu Takım | Çıktı |
| --- | --- | --- | --- | --- |
| Aylık (her ayın ilk haftası) | Operasyonel Denetim | OpsCenter logları, hareketsizlik alarm örneklemesi, offline kuyruk raporu | OpsCenter + Mobil Takımı | `docs/audit/operational/YYYY-MM.md` raporu, aksiyon listesi |
| Çeyreklik (Q1/Q2/Q3/Q4) | Güvenlik & KVKK Denetimi | Erişim logları, veri saklama politikaları, WAF kural seti | Güvenlik + Hukuk | KVKK uyum raporu, veri imha protokol güncellemesi, risk kaydı |
| Yarıyıllık | Altyapı & Performans Denetimi | CI/CD pipeline, kapasite planları, SLO raporları | DevOps + Teknik Liderlik | Kapasite değerlendirme raporu, ölçekleme önerileri, SLA uyum analizi |
| Yıllık | Stratejik ve Strateji Uyum Denetimi | Faz 18–26 süreçleri, ICS formları, kurumlar arası entegrasyonlar | Ürün Sahibi + Yönetim Kurulu | Stratejik değerlendirme raporu, politika revizyon önerileri |
| Tatbikat Sonrası | Tatbikat Doğrulama | Tatbikat KPI’ları, simülasyon verileri, kullanıcı geri bildirimi | Saha Operasyonları + Eğitim Ekibi | `docs/tatbikat/YYYY-MM-report.md`, takip aksiyonları |

Denetim sonuçları, risk kayıtları ve KPI panoları ile çapraz kontrol edilir. Kritik bulgular için 7 gün içinde RFC açılması zorunludur. Denetim raporları en az 3 yıl süreyle saklanır ve yetkilendirme matrisi gereği erişim kontrolü uygulanır.

## İletişim Kanalları & Eskalasyon Tablosu

| Durum | Birincil Kanal | Yedek Kanal | Maks. Yanıt Süresi | Eskalasyon Seviyesi |
| --- | --- | --- | --- | --- |
| Planlı Bakım Duyurusu | E-posta + OpsCenter bildirim paneli | SMS (kritik tenant yöneticileri) | 24 saat | Ürün Sahibi → DevOps Lead |
| Hareketsizlik Alarmı (İlk Bildirim) | Mobil push bildirimi | SMS | 60 sn | Görev Lideri → OpsCenter Operatörü |
| Kural Motoru Hata Alarmı | PagerDuty / Ops bildirim | Slack/Matrix acil kanal | 10 dk | DevOps → Teknik Lider |
| Güvenlik İhlali Şüphesi | Telefon + Şifreli sohbet kanalı | E-posta (PGP) | 5 dk | Güvenlik Ekibi → Yönetim → Hukuk |
| Edge Node/Offline Kuyruk Kesintisi | Mobil uygulama banner’ı | Saha telsiz ağı | 15 dk | Mobil Lead → Saha Koordinatörü → OpsCenter |

Eskalasyon zinciri içinde yanıt alınamazsa bir üst seviyeye geçmek için maksimum yanıt süresinin yarısı beklendikten sonra tekrar iletişim kurulmalıdır. Tüm iletişim kayıtları audit log veya uygun iletişim aracı üzerinden saklanır; kritik olaylarda çağrı kayıtları ve mesaj içerikleri olay klasörüne eklenir.

## Operasyonel Runbooklar

Her kritik operasyon için güncel runbook’lar `docs/runbook/` dizininde muhafaza edilir ve sürüm numarasıyla takip edilir. Runbook sahipleri ilgili modül takımlarıdır ve değişiklikler RFC sürecinden sonra yürürlüğe girer.

| Runbook | Amaç | Başlangıç Tetikleyicisi | İlgili Faz Çıktıları |
| --- | --- | --- | --- |
| `runbook/incident-response.md` | Güvenlik ihlali veya kritik altyapı kesintisinde uygulanacak adımlar | Sentry kritik alarmı, WAF ihlal kaydı | Faz 1, Faz 11, Faz 22 |
| `runbooks/incident-response/credential-compromise.md` | Kimlik bilgisi sızıntısı ve yetkisiz erişim senaryolarında yapılacaklar | SIEM MFA başarısızlığı, dark web uyarısı | Faz 1, Faz 22 |
| `runbook/offline-edge-recovery.md` | Edge node veya offline kuyruk arızasında sahayı ayakta tutmak | Edge heartbeat kaybı ≥ 3 ping, kuyruk doluluk ≥ %90 | Faz 4, Faz 9, Faz 14 |
| `runbooks/maintenance/patch-window.md` | Planlı yama ve bakım penceresinin işletilmesi | Aylık bakım takvimi, güvenlik bülteni | Faz 11, Faz 22 |
| `runbook/opscenter-degradation.md` | OpsCenter performans sorunlarında degrade moduna geçiş | Harita yanıt süresi KPI sapması, BBOX sorgu hataları | Faz 6, Faz 12, Faz 20 |
| `runbooks/opscenter/alarm-console-escalation.md` | Alarm konsolu üzerinden gelen bildirimlerin yönlendirilmesi | Hareketsizlik, geofence, SOS, ping kaybı alarmı | Faz 4, Faz 6, Faz 7 |
| `runbook/data-restore.md` | Veri kaybı veya yanlış migrasyon sonrası geri yükleme | Yedekleme tutarsızlığı, migration guard hatası | Faz 2, Faz 11 |
| `runbook/rule-engine-hotfix.md` | Kural motoru zincir hatalarında acil düzeltme | Bildirim kuyruğu başarısızlık oranı ≥ %2 | Faz 4, Faz 7 |
| `runbooks/tracking/no-motion-alert.md` | Hareketsizlik alarmı tespit edildiğinde uygulanacak plan | `tracking.no_motion` tetikleyicisi | Faz 4, Faz 6, Faz 7 |

Tüm runbook’lar yılda iki kez masa başı tatbikatında denenir; tatbikat notları Denetim & Uyum Takvimi ile ilişkilendirilerek saklanır.

## Olay Şiddet Seviyeleri & Müdahale Süreleri

Şiddet seviyeleri, alarm eskalasyonu ve kaynak tahsisini standardize etmek için belirlenmiştir. OpsCenter, olay oluştururken aşağıdaki seviyelerden birini seçmek zorundadır.

| Seviye | Tanım | Örnek Senaryolar | Hedef İlk Yanıt Süresi | Müdahale Gereksinimleri |
| --- | --- | --- | --- | --- |
| **Seviye 1 — Bilgilendirme** | Operasyonu etkilemeyen, izleme amaçlı kayıt | Kural motoru dry-run sonucu, düşük öncelikli API hata artışı | ≤ 30 dk | Takip metriklerini güncelle, dokümantasyon notu bırak |
| **Seviye 2 — Operasyonel Uyarı** | Belirli tenant veya modülde kısıtlı etki | Tek tenant offline kuyruk doluluğu, OpsCenter katman gecikmesi | ≤ 15 dk | Sorumlu modül liderine haber ver, runbook tetikle |
| **Seviye 3 — Kritik Hizmet Kesintisi** | Temel modüllerin tamamını etkileyen kesinti | WebSocket yayın kesintisi, kural motoru teslim başarısızlığı | ≤ 5 dk | Incident Response runbook, CAB bilgilendirmesi, potansiyel rollback |
| **Seviye 4 — Güvenlik / Emniyet Riski** | Can kaybı riski veya yetkisiz erişim şüphesi | Hareketsizlik alarmına yanıt alınamaması, KVKK ihlali bulgusu | ≤ 2 dk | SOS eskalasyonu, hukuk ve üst yönetim bilgilendirmesi, dış paydaş iletişimi |

Yanıt süreleri KPI panosunda ölçülür; ihlal durumunda Operasyon Sonrası Değerlendirme sürecine otomatik madde eklenir.

## Konfigürasyon Yönetimi & Ortam Standartları

Çevreler arası sürprizleri azaltmak için tüm konfigürasyon anahtarları `config/` altında şablonlanır ve `.env.example` dosyası Faz 0 kararlarıyla uyumlu olacak şekilde sürümlenir.

- **Ortam Katmanları:** `dev`, `staging`, `production`, `edge`. Her ortam için `config/environment/<env>.yaml` dosyası bulunur ve CI pipeline’ında bütünlük kontrolü yapılır (Faz 11).
- **Gizli Anahtar Yönetimi:** Üretim anahtarları sadece KMS üzerinde saklanır, erişimler Faz 1 yetki modeliyle sınırlandırılır. Anahtar rotasyonu 90 günde bir yapılır (Faz 22).
- **Konfigürasyon İncelemesi:** Her yayın öncesi `config-drift` script’i çalıştırılarak staging ve production farkları raporlanır. Farklar giderilmeden deploy yapılamaz.
- **Feature Flag Politikası:** Yeni faz deneysel modüller için `config/feature-flags.php` kullanılır. Flag’ler tenant bazlı açılıp kapatılabilir ve en geç 2 sürüm içinde ya kalıcı hale getirilir ya da kaldırılır.

Konfigürasyon değişiklikleri audit log’da tutulur; yetkisiz değişiklik tespitinde Seviye 3 olay tetiklenir.

## Veri Yaşam Döngüsü ve Arşivleme

Veri sınıfları, saklama süreleri ve arşiv süreçleri Faz 21 mevzuat gereksinimleriyle uyumlu olacak şekilde yapılandırılmıştır.

| Veri Sınıfı | Saklama Süresi | Arşivleme Yöntemi | Silme/Anonimleştirme İşlemi | Sorumlu Takım |
| --- | --- | --- | --- | --- |
| Operasyonel Olay & Görev Verileri | 10 yıl | Soğuk depoda şifreli PMTiles + SQL dump | Süre sonunda anonimleştirme + özet istatistik üretimi | Ürün + Veri Koruma |
| Hareketsizlik & Takip Logları | 2 yıl | Sıkıştırılmış zaman serisi deposu (Influx/Prometheus TSDB export) | 6 ayda bir kısmi anonimleştirme, süre sonunda imha | OpsCenter + Güvenlik |
| Audit Log & Yetkilendirme Kaydı | 5 yıl | WORM depolama, imzalı hash zinciri | Süre sonunda hukuk onayıyla imha, hash kayıtları saklanır | Güvenlik |
| Eğitim & Tatbikat Kayıtları | 3 yıl | `docs/tatbikat/` + medya arşivi | Süre sonunda özet rapor sakla, ham veriyi sil | Eğitim Ekibi |
| Vatandaş Portalı Başvuruları | 2 yıl (onaylanmamış), 5 yıl (onaylanmış) | KVKK uyumlu depolama, kişisel veri maskeleme | Talep halinde 30 gün içinde ihraç ve silme | Portal Takımı + Hukuk |

Veri yaşam döngüsü politikaları çeyreklik KVKK denetiminde doğrulanır. Otomatik silme işlemleri için cron-job raporları Denetim & Uyum Takvimi kayıtlarına eklenir.

## Monitoring & Alerting Playbook

Sistem gözlemlenebilirliği aşağıdaki katmanlarda yapılandırılmıştır. Her alarm türü için hedef aksiyon 3 adımdan oluşur: **tespit**, **değerlendirme**, **aksiyon**.

1. **Altyapı Seviyesi (Faz 11):** Prometheus metrikleri (CPU, bellek, queue lag) 1 dakikalık aralıklarla toplanır. Kritik eşikler aşıldığında PagerDuty çağrısı açılır, DevOps runbook’una yönlendirilir.
2. **Uygulama Seviyesi (Faz 3–7):** Laravel Horizon, kural motoru kuyruğu ve WebSocket teslim oranları izlenir. 5 dakikalık hareketli ortalama sapması ≥ %10 olduğunda otomatik olarak Rule Engine Hotfix runbook’u tetiklenir.
3. **Kullanıcı Deneyimi (Faz 6, Faz 14, Faz 20):** RUM ajanı OpsCenter ve mobil uygulamada P95 yanıt süresini raporlar. 15 dakikalık pencere içinde hedef aşıldığında OpsCenter degrade planı devreye girer ve kullanıcı banner’ı yayınlanır.
4. **Güvenlik (Faz 1, Faz 22):** WAF, 2FA başarısızlıkları ve anomali algılama sonuçları SIEM’e akar. Kritik alarmda 2 dk içinde Güvenlik Ekibi’nin telefon hattı çalar ve Seviye 4 protokol devreye girer.

Her alarmın değerlendirme adımı 5 dakikayı aşamaz; aksi halde olay otomatik olarak bir üst şiddet seviyesine taşınır. Alarm kapatma notları SLO raporlarıyla entegre edilir.

**Artefaktlar ve Başvuru Dokümanları**

- `observability/README.md` — Gözlemlenebilirlik mimarisinin sahiplik modeli ve inceleme döngüleri.
- `observability/alerts/opscenter.yml` — OpsCenter, kural motoru ve hareketsizlik alarmı için Prometheus kuralları.
- `observability/service-health-checklist.md` — Vardiya başı sağlık kontrolleri ve raporlama yönergeleri.
- `observability/dashboards/opscenter.json` — Grafana panel tanımı; eşik değerleri runbook bağlantılarıyla ilişkilidir.
- `observability/capacity-journal.md` — Benchmark ve load test sonuçlarına karşı kapasite trendlerini kaydeder.
- `observability/metrics-catalog.md` — Kritik metriklerin sahipliği, hedefleri ve runbook eşleşmeleri.
- `observability/slo-register.md` — SLO performansı ve aksiyon kayıtları.

## On-call & Vardiya Yönetimi

24/7 operasyon kabiliyetini sürdürebilmek için on-call ekipleri Faz 1 güvenlik gereksinimleri, Faz 6 OpsCenter süreçleri ve Faz 11 devops otomasyonuyla uyumlu olacak şekilde vardiya planlarına ayrılır. Her vardiya, operasyonel sağlık kontrolleriyle başlar ve devralınan açık iş öğeleri kapatılmadan bitirilemez.

### Vardiya Kapasitesi ve Sorumluluklar

| Rol | Kapsam | Faz Bağlantısı | Mesai Dışı Kapama Kuralı |
| --- | --- | --- | --- |
| OpsCenter Komutanı | Alarm konsolu, saha koordinasyonu, tenant bildirimleri | Faz 4, Faz 6, Faz 7 | OpsCenter alarm kuyruğu sıfırlanmadan vardiya devredilemez. |
| DevOps Gözcüsü | CI/CD, altyapı, kuyruk izleme ve rollback hazırlığı | Faz 1, Faz 9, Faz 11 | Deploy planı + rollback checklist’i güncellenmeli; kritik pipeline hatası çözülmeli. |
| Güvenlik Gözcüsü | 2FA, WAF, kural motoru ve tehdit akışları | Faz 1, Faz 7, Faz 22 | Açık güvenlik alarmı veya teyitsiz ihbar kaldırılamaz; rapor `security/threat-intel-register.md`’ye işlenir. |
| Tenant İletişim Temsilcisi | İl/kurum bazlı duyurular, vatandaş portalı koordinasyonu | Faz 5, Faz 17, Faz 25 | Açık iletişim talebi kapatılmalı; kamu açıklamaları `communications/public/` arşivine eklenmeli. |

### Handover Akışı

1. **Gözlemlenebilirlik özeti:** `observability/metrics-catalog.md` ve açık SLO ihlalleri gözden geçirilir.
2. **Aktif olaylar:** OpsCenter olay panosundaki Seviye ≥2 kayıtlar devralınır, notlar `ops/weekly-ops-briefing.md` ile eşleşir.
3. **Planlanmış bakım & deneyler:** DevOps on-call, `resilience/` deney takvimini ve `config/` değişiklik planlarını kontrol eder.
4. **Vardiya kapanışı:** Handover şablonu doldurulur, kritik notlar hem Slack/Matrix kanalına hem de arşiv klasörüne eklenir.

### Artefaktlar

- `ops/oncall/README.md` — On-call politikaları, rotasyon ve sorumluluk zinciri.
- `ops/oncall/handoff-template.md` — Vardiya teslim formu; çıktı runbook referanslarıyla birlikte kaydedilir.
- `ops/oncall/rotation-schedule.csv` — Tenant ve uzmanlık bazlı örnek rotasyon planı.
- `observability/service-health-checklist.md` — Vardiya başlangıcı sağlık kontrolleri için referans.

## Kaos Mühendisliği & Dayanıklılık Testleri

Dayanıklılık programı, Faz 4 canlı takip, Faz 6 OpsCenter, Faz 9 offline/edge ve Faz 22 siber dayanıklılık hedefleriyle uyumlu olarak planlanır. Amaç, kritik hizmetlerin felaket senaryolarında kabul edilebilir seviyede kalmasını ölçmektir.

### Program Prensipleri

- **Güvenli Planlama:** Denemeler yalnızca staging veya izole tenant ortamlarında yürütülür; üretimde yapılacak tatbikatlar için Yönetim Kurulu onayı gerekir.
- **Hipotez Bazlı Yaklaşım:** Her deney hipotez, etki alanı, geri döndürme planı ve ölçüm metrikleriyle tanımlanır (`resilience/experiments/chaos-template.md`).
- **Gözlem Entegrasyonu:** Sonuçlar SLO raporları ve `observability/capacity-journal.md` ile eşleştirilir; başarısız deneyler runbook güncellemesine yol açar.
- **İyileştirme Takibi:** Bulgular `tech-debt/backlog.csv` ve `governance/iteration-kanban.md` üzerinde aksiyona dönüştürülür.

### 2024 Program Çıkartmaları

| Tarih | Deney | Hedeflenen Sonuç | İyileştirme Kararı |
| --- | --- | --- | --- |
| 2024-07-14 | `network-partition` (WebSocket + offline kuyruk) | OpsCenter alarmı ≤ 2 dk içinde degrade moda geçmeli, hareketsizlik alarmı kuyrukta kaybolmamalı. | Edge retry politikasında `max_retries=5` → `8`; ops runbook’una manuel SMS fallback eklendi. |
| 2024-08 (plan) | `database-failover` (MySQL bölünmesi) | Migration guard hatasız devre dışı kalmalı; OpsCenter veri snapshot’ı 5 dk içinde güncellenmeli. | Açık | 

### Artefaktlar

- `resilience/README.md` — Dayanıklılık programı kapsamı, rol dağılımı ve onay süreci.
- `resilience/reliability-roadmap.md` — Çeyreklik hedefler, bağımlılıklar ve runbook güncelleme planı.
- `resilience/experiments/chaos-template.md` — Deney başlatma şablonu; hipotez, risk azaltma ve metrik bölümleri içerir.
- `resilience/experiments/2024-07-network-partition.md` — İlk ağ bölünmesi tatbikatı raporu.
- `runbook/opscenter-degradation.md` & `runbook/offline-edge-recovery.md` — Deney çıktılarıyla güncellenen operasyonel planlar.
- `observability/reviews/` — Alarm ve metrik gözden geçirme çıktıları.

## Dokümantasyon Bakım Ritmi

Belge ve destekleyici dokümanlar yaşayan artefaktlardır. Tutarlılığı sağlamak için aşağıdaki bakım ritmi uygulanır:

- **Aylık Tarama:** Ürün sahibi ve teknik lider README’deki tüm tabloların güncelliğini kontrol eder, gerekiyorsa RFC açar.
- **Sürüm Sonrası Güncelleme:** Her yayın sonunda yeni faz çıktıları ve öğrenilen dersler ilgili bölümlere işlenir; `docs/changelog/` klasörü güncellenir.
- **Denetim Eşleşmesi:** Denetim raporları yayımlandığında ilgili tablolar (SLA, KPI, risk) üzerinde düzeltmeler yapılır.
- **Çeviri & Çok Dilli Gereksinimler:** Faz 25 kapsamında çeyrek başına en az bir kere dil dosyaları ve terminoloji sözlüğü kontrol edilir; yeni terimler sözlüğe eklenir.
- **Arşiv:** Eski kararlar veya kullanım dışı faz çıktıları “Deprecated” notuyla belgelenir ve tarih damgası eklenir.

Bakım ritmine uyulmaması durumunda ilgili ekipler Denetim & Uyum Takvimi kapsamında uyarılır ve düzeltici aksiyon planı hazırlanır.

## Yönetim Kurulları & Toplantı Ritmi

Faz yönetişimi, karar alma ve operasyonel koordinasyonu sağlamak için kurulan kurullar aşağıdaki ritimde toplanır. Her kurul, karar kayıtlarını `docs/governance/` altında saklar ve aksiyonların kapanışını izler.

| Kurul | Katılımcılar | Sıklık | Gündem Maddeleri | Faz Referansı | Çıktı Artefaktı |
| --- | --- | --- | --- | --- | --- |
| Stratejik Yürütme Kurulu | Ürün Sahibi, Yönetim, Teknik Lider | Aylık | Faz önceliklendirme, bütçe, risk onayı | Faz 0, 26 | `governance/exec-minutes-{yyyy-mm}.md` |
| Teknik Mimari Kurulu | Teknik Lider, Modül Lead’leri, DevOps | 2 haftada bir | RFC değerlendirme, altyapı değişiklikleri | Faz 0, 1, 11 | `architecture/rfc-decisions.csv` |
| Güvenlik & Uyumluluk Konseyi | Güvenlik Ekibi, Hukuk, Denetim | Çeyreklik | Zafiyet sonuçları, KVKK aksiyonları, pentest planı | Faz 1, 21, 22 | `security/compliance-report-{q}.pdf` |
| Operasyon Koordinasyon Masası | OpsCenter Lideri, Saha Operasyonları, Mobil Takım | Haftalık | SLA/SLO istatistikleri, runbook güncellemeleri | Faz 4, 6, 14 | `ops/weekly-ops-briefing.md` |
| Paydaş & Entegrasyon Forumu | Kurumlar Arası Entegrasyon Ekibi, Dış Paydaş Temsilcileri | İki ayda bir | API sözleşmeleri, veri paylaşım protokolleri | Faz 19, 20 | `integration/partner-matrix.csv` |

Toplantılar, gündemden sapma olmaması için maksimum 60 dakika sürer. Kritik kararlar toplantı sonrası 24 saat içinde karar kaydına işlenmezse geçerlilik kazanmaz.

## Bilgi Güvenliği Yetkinlik Matrisi

Roller için zorunlu güvenlik eğitimleri ve sertifikasyonlar aşağıdaki tabloda tanımlıdır. Eğitim eksiklikleri Faz Geçiş Kriterleri’nin ön koşuludur ve tamamlanmadan ilerlenemez.

| Rol | Temel Eğitimler | İleri Eğitimler | Yenileme Periyodu | İzleme Metodu |
| --- | --- | --- | --- | --- |
| Teknik Lider | Güvenli Kod Geliştirme, OWASP Top 10 | Tehdit Modelleme, Zero Trust Atölyesi | 12 ay | LMS raporu + imzalı sertifika |
| Backend/Mobil Geliştirici | Güvenli Kod, API Güvenliği | Kriptografi Temelleri, Offline Senaryo Güvenliği | 12 ay | Kod inceleme kontrol listesi |
| DevOps | Secrets Yönetimi, CI/CD Güvenliği | Sıfır Güven Ağ Segmentasyonu | 6 ay | Pipeline denetim raporu |
| Güvenlik Ekibi | SOC Operasyonları, Forensics 101 | Red Team / Blue Team Ortak Eğitimi | 6 ay | Tatbikat performans ölçümü |
| OpsCenter Operatörü | Kimlik Doğrulama Prosedürleri | Sosyal Mühendislik Farkındalığı | 12 ay | OpsCenter quiz sonuçları |
| Saha Personeli | Mobil Cihaz Güvenliği, KVKK | Offline Veri Sızdırma Önlemleri | 12 ay | Yoklama & eğitim logları |

Eğitim kayıtları `hr/training-tracker.csv` dosyasında tutulur ve Denetim & Uyum Takvimi sırasında örnekleme yapılır. Eksik veya süresi geçmiş eğitimlerde ilgili yöneticiye 5 iş günü içinde kapanış planı bildirilir.

## Çok Katmanlı Haberleşme Protokolleri

Kriz ve rutin operasyonlarda iletişim sürekliliğini sağlamak için birincil, ikincil ve alternatif kanallar tanımlanır. Kanallar arası geçiş koşulları ve sorumlular aşağıdadır.

1. **Birincil Kanal (OpsCenter WS + VoIP):** Normal operasyonlarda tüm görev yönlendirmeleri WebSocket üzerinden yapılır. Sesli teyit gerektiğinde OpsCenter VoIP hattı kullanılır. Her mesaj, OpsCenter audit log’una yazılır.
2. **İkincil Kanal (NETGSM SMS + E-posta):** Birincil kanal kesintiye uğrarsa otomatik olarak SMS alarmı tetiklenir, e-posta ile özet gönderilir. DevOps, 10 dakika içinde WS hizmetini yeniden ayağa kaldırmaktan sorumludur.
3. **Alternatif Kanal (Radyo + Sahra Sunucusu):** İnternet kopukluğu durumunda Faz 9 edge node, yerel Wi-Fi ve VHF/UHF telsiz ağı devreye alınır. Radyo mesajları `Form-ICS213` formatında kayıt altına alınır.

Her kanal değişimi “Haberleşme Geçiş Kaydı” olarak loglanır. Geçişler tamamlandığında ilgili ekipler geri dönüş testini (`communication/rollback-checklist.md`) çalıştırır.

## Sürekli İyileştirme & Geri Bildirim Döngüsü

Ürün ve operasyon süreçlerinde sürekli gelişim sağlamak için geri bildirimler aşağıdaki döngüye göre ele alınır:

1. **Toplama:** OpsCenter anketleri, tatbikat geribildirim formları, saha raporları ve topluluk portalı yorumları `feedback/inbox/` dizinine düşer.
2. **Sınıflandırma:** Ürün Sahibi haftalık olarak geri bildirimleri kategorize eder (güvenlik, kullanılabilirlik, performans, mevzuat). Kritik güvenlik bulguları 4 saat içinde Güvenlik Ekibi’ne yönlendirilir.
3. **Değerlendirme:** Teknik Mimari Kurulu ve ilgili modül ekipleri, geri bildirimleri RFC veya backlog maddesine dönüştürür. Her geri bildirim için hedef faz ve planlanan sürüm belirlenir.
4. **Uygulama:** Onaylanan maddeler sprint planlarına alınır, tamamlandığında geri bildirim sahibine (varsa) sonuç raporu gönderilir.
5. **Kapanış & Öğrenilen Dersler:** Operasyon Sonrası Değerlendirme bölümünde en kritik üç öğrenim paylaşılır ve knowledge base’e eklenir.

Takip edilmemiş geri bildirimler çeyreklik denetimde raporlanır ve yönetime eskale edilir.

## Veri Kalitesi Sağlama Döngüleri

Veri bütünlüğünün yüksek kalması için uygulama ve veritabanı katmanında tanımlı kontroller düzenli döngülerle çalıştırılır.

- **Günlük Otomatik Kontroller:** Faz 2’de tanımlanan CHECK ve FOREIGN KEY kısıtları ile veri anormallikleri `data-quality/daily-report.json` dosyasında raporlanır. Rapor DevOps pipeline’ı tarafından Slack kanalına gönderilir.
- **Haftalık Uçtan Uca Mutabakat:** Olay–görev–envanter zinciri için örnekleme yapılır; zimmet kayıtları ile envanter durumu karşılaştırılır. Tespit edilen farklar Faz 3 runbook’una göre düzeltilir.
- **Aylık Analitik Karşılaştırma:** Faz 8 ve Faz 24 veri gölü özetleri ile operasyonel veritabanı karşılaştırılır; fark %1’i aşarsa veri mühendisliği ekibi RCA (root cause analysis) dokümanı hazırlar.
- **Mevzuat Uyumu Kontrolleri:** Faz 21 gereği saklama süresi dolmuş kayıtlar anonimleştirilir veya silinir. Süresi dolmuş ancak sistemde kalan kayıt varsa 48 saat içinde manuel müdahale yapılır.

Tüm veri kalitesi raporları `governance/data-quality-dashboard` panelinde saklanır ve Faz 20 panolarına özet metrik olarak yansıtılır.

## Bilgi Yönetimi & Knowledge Base

Kurumsal bilgi birikiminin kaybolmaması ve ekipler arasında tutarlı paylaşılması için merkezi bir knowledge base işletilir. Sistem, dokümantasyon ritmi ile entegre çalışır ve operasyon sırasında hızlı erişim sağlar.

- **Yapı:** Knowledge base, `docs/kb/` altında fazlara göre etiketlenmiş sayfalardan oluşur. Her kayıt; özet, detay adımlar, kullanılan araçlar, ilgili runbook ve sorumlu kişi bilgilerini içerir.
- **Arama & Erişim:** OpsCenter içinde faz etiketiyle filtrelenebilen bir arama modülü sağlanır. Tenant bazlı hassas kayıtlar için Faz 1 yetki modeli uygulanır.
- **Katkı Akışı:** Yeni bilgi ekleyen ekip üyesi, kayıt için RFC açar; teknik lider veya ilgili modül sahibi 3 iş günü içinde inceleme yapar. Onaylanan içerik `kb-index.json` dosyasına eklenir.
- **Versiyonlama:** Bilgi tabanı girdileri Git tarihçesiyle takip edilir. Kritik runbook referansları değiştiğinde, knowledge base kaydı otomatik PR tetikleyicisi ile güncellenir.
- **Kullanım Ölçümü:** OpsCenter arama günlükleri aylık olarak analiz edilerek hangi konuların sık arandığı belirlenir; eksik başlıklar backlog’a alınır.
- **Eğitim Entegrasyonu:** Onboarding sürecinde (Faz 13) knowledge base üzerindeki “zorunlu okuma” listesi tamamlanmadan yetki ataması yapılmaz.

Knowledge base, tatbikat ve kriz sonrası değerlendirmelerin ana referans kaynağıdır; güncelliğini kaybeden kayıtlar arşive taşınır ve yerlerine güncel versiyonları eklenir.

## Faz Geçiş Kriterleri

Fazlar arasındaki geçişler, aşağıdaki minimum çıktılar doğrulanmadan tamamlanamaz. Tablo, proje yönetimi ve CAB oturumları için zorunlu referanstır.

| Faz | Zorunlu Çıktılar | Doğrulama Metodu | Engelleyici Koşullar |
| --- | --- | --- | --- |
| 0 → 1 | Teknoloji karar dokümanı, `.env.example`, güvenlik mimarisi diyagramı | Mimari inceleme oturumu, imzalı karar kaydı | Eksik karar maddesi, çelişen teknoloji seçimi |
| 1 → 2 | Fortify/2FA devrede, audit log şeması, yedekleme testi | Penetrasyon testi raporu, yedek geri yükleme tatbikatı | 2FA devre dışı tenant, başarısız geri yükleme |
| 2 → 3 | Migrasyon script’leri, CHECK/FOREIGN KEY doğrulamaları, örnek veri seti | `php artisan migrate:fresh --seed` denemesi, veri kalite raporu | Constraint ihlali, seed başarısızlığı |
| 3 → 4 | Olay/görev/zimmet akışları, çift onay mekanizması | Entegre E2E senaryosu, çift onay log doğrulaması | Kapalı görev olmadan olay kapatma, zimmet transaction hatası |
| 4 → 5 | Canlı takip pingleri, hareketsizlik alarmı, geofence testleri | Simüle sahada ping/görev akışı, alarm KPI ölçümü | Ping kaybı > %1, yanlış pozitif alarm |
| 5 → 6 | QR anti-replay testi, yoklama logları | Mobil + OpsCenter entegrasyon testi, nonce tekrar denemesi | Yeniden kullanımda 409 tetiklenmemesi |
| 6 → 7 | OpsCenter katmanları, alarm konsolu, hızlı aksiyon modülleri | BBOX performans testi, alarm senaryosu dry-run | BBOX parametresiz sorgu, alarm gecikmesi |
| 7 → 8 | Kural motoru sürümleme, şablon yönetimi, teslim raporları | Rule dry-run raporu, rate limit testleri | Retry kuyruğu başarısızlığı, spam alarmlar |
| 8 → 9 | ICS otomasyon raporları, playback doğrulaması | Rapor iki onay kaydı, playback ekran incelemesi | Eksik ICS alanı, playback sapması |
| 9 → 10 | Offline kuyruk, edge node senaryoları | Kuyruk boşaltma testi, çakışma raporu | Kuyrukta veri kaybı, checksum farkı |
| 10 → 11 | Dış servis adapterleri, anti-duplicate hash | Rate limit simülasyonu, hash çakışma testi | Yinelenen kayıt, dış servis hata logu |
| 11 → 12 | CI/CD pipeline, izleme panoları, health endpoint | Pipeline dry-run, Grafana pano incelemesi | Failing health check, eksik pano |
| 12 → 13 | Test katmanları, yük testi raporu, güvenlik taraması | CI raporu, yük testi sonuçları, pentest özeti | Kırmızı test, kritik zafiyet |
| 13 → 14 | GoLive runbook, on-call planı, eğitim tamamlandı | Pilot tatbikat raporu, SOP imzası | Tatbikat başarısız, eksik eğitim |
| 14 → 15 | Mobil offline senaryosu, medya kuyruğu, hareketsizlik cihaz entegrasyonu | Mobil stres testi, sensör kalibrasyon raporu | Kuyruk başarısız, yanlış pozitif |
| 15 → 16 | Yapay zekâ öneri modeli, açıklanabilirlik raporu | Model değerlendirme raporu, insan onayı kaydı | Açıklanamayan karar, onay eksikliği |
| 16 → 17 | Simülasyon modu, tatbikat KPI panosu | Simülasyon veri doğrulaması, KPI raporu | Gerçek veriye sızıntı, KPI eksik |
| 17 → 18 | Portal modülleri, moderasyon süreci, KVKK kontrolleri | Moderasyon logları, KVKK raporu | Onaysız içerik, PII ihlali |
| 18 → 19 | Lojistik QR zinciri, kritik stok alarmları | Sevkiyat denemesi, alarm doğrulaması | Zincir kopukluğu, alarm gecikmesi |
| 19 → 20 | Kurumlar arası mapping, imzalı entegrasyon | Adapter testleri, imza doğrulama | Yetkisiz erişim, mapping hatası |
| 20 → 21 | Dashboard panoları, pre-aggregation planı | Performans testi, cache hit raporu | KPI yüklenme gecikmesi |
| 21 → 22 | KVKK politikaları, denetim logları | Hukuk onayı, log örneklemesi | Saklama ihlali, erişim kayıt eksikliği |
| 22 → 23 | Zero Trust politikaları, pentest raporu | Pentest kapanış toplantısı, WAF konfig testi | Açık zafiyet, eksik KMS politikası |
| 23 → 24 | IoT gateway, veri normalizasyonu | Sensör simulasyonu, veri bütünlüğü testi | Veri kaybı, kimlik doğrulama eksikliği |
| 24 → 25 | Veri gölü pipeline’ı, anonimleştirme süreci | ETL denemesi, veri kalite raporu | PII maskelenmemiş kayıt |
| 25 → 26 | i18n dosyaları, tenant tema konfigleri | Dil kapsam testi, tema incelemesi | Eksik çeviri, tenant veri sızıntısı |

## Kapasite & Kaynak Planlama

Operasyonel süreklilik için insan, altyapı ve finansal kaynaklar faz hedefleriyle eşleştirilir. Kapasite planı her çeyrekte güncellenir ve stratejik panellere (Faz 26) yansıtılır.

| Kaynak Alanı | Ölçüt | Minimum Kapasite | İzleme Sıklığı | Sorumlu | İlgili Fazlar |
| --- | --- | --- | --- | --- | --- |
| DevOps & Altyapı | CI/CD paralel job sayısı | ≥ 5 eşzamanlı job | Aylık | DevOps Lead | Faz 11, Faz 12 |
| Güvenlik | Pentest personel günü | Çeyreklik ≥ 10 gün | Çeyreklik | Güvenlik Ekibi | Faz 1, Faz 22 |
| OpsCenter Operatörleri | 7/24 vardiya kapsaması | En az 3 vardiya x 2 kişi | Haftalık | OpsCenter Lideri | Faz 4, Faz 6 |
| Mobil Saha Destek | Bölgesel destek noktası | Her riskli il için 1 saha sorumlusu | Aylık | Mobil Takım Lead | Faz 9, Faz 14 |
| Veri & Analiz | BI geliştirici sayısı | ≥ 2 kıdemli analist | Çeyreklik | Veri Takımı | Faz 8, Faz 20, Faz 24 |
| Eğitim & Tatbikat | Eğitmen havuzu | ≥ 4 sertifikalı eğitmen | Çeyreklik | Eğitim Koordinatörü | Faz 13, Faz 16 |
| Finansal Rezerv | Felaket bütçe payı | Operasyon bütçesinin ≥ %15’i | Yıllık | Yönetim | Faz 18, Faz 22, Faz 26 |

Eksik kapasite tespitinde risk matrisi güncellenir ve ilgili faz için teslimat tarihleri revize edilir. Kritik alanlarda (güvenlik, OpsCenter) 30 gün içinde iyileştirme planı sunulmazsa Faz Geçiş Kriterleri gereği bir sonraki faza ilerlenemez.

## Veri Kataloğu & Metadata Standartları

Tüm veri kümeleri, yeniden kullanım ve mevzuat uyumunu kolaylaştırmak için merkezi bir katalogda tutulur. Metadata şablonu `docs/data-catalog/template.yaml` dosyasında sürümlenir.

| Metadata Alanı | Açıklama | Zorunlu Faz | Doğrulama |
| --- | --- | --- | --- |
| `dataset_id` | Benzersiz kimlik, `{faz}-{modül}-{versiyon}` formatında | Faz 2 ve sonrası | Otomatik UUID ve format denetimi |
| `data_owner` | İş sahibi rol veya ekip | Faz 0 | Yetkilendirme matrisi eşleşmesi |
| `security_class` | `PUBLIC`, `INTERNAL`, `RESTRICTED`, `SECRET` | Faz 1, Faz 21 | Güvenlik onayı |
| `retention_policy` | Saklama süresi referansı | Faz 21 | Veri yaşam döngüsü tablosu uyumu |
| `source_system` | Veri üretim fazı ve modülü | Faz 3+ | OpsCenter log korelasyonu |
| `quality_checks` | Otomatik doğrulama betikleri | Faz 2, Faz 12 | CI pipeline raporu |
| `pii_fields` | Maskeleme gerektiren alan listesi | Faz 21 | KVKK denetim onayı |
| `lineage` | Üst/alt akış veri kümeleri | Faz 8, Faz 24 | Veri gölü ETL raporu |

Metadata girişi yapılmadan veri kümesi prod ortamına taşınamaz. Katalog kayıtları aylık auditlerde örnekleme ile kontrol edilir; eksik alan tespitinde ilgili ekip 5 iş günü içinde düzeltme yapmak zorundadır.

## Sürüm & Değişiklik Yönetimi Takvimi

Sürüm planları; hazırlık, geçiş ve bakım dönemlerini kapsayacak şekilde yıllık döngüye bağlanır. Aşağıdaki takvim, sürüm pencerelerinin çakışmasını önlemek için kullanılır.

| Dönem | Faaliyet | Sorumlu | Notlar |
| --- | --- | --- | --- |
| Ocak | Yıllık strateji ve kapasite revizyonu, SLO hedef güncellemesi | Ürün Sahibi + Yönetim | Faz 26 panelleri üzerinden hedefler güncellenir. |
| Şubat-Nisan | Faz geliştirmeleri (Q1-Q2) | Modül Takımları | Haftalık CAB, tatbikat senaryoları Mart sonunda test edilir. |
| Mayıs | Yarıyıl audit & performans denetimi | DevOps + Güvenlik | Faz 11 ve Faz 22 kontrolleri; gerekli kapamalar planlanır. |
| Haziran-Ağustos | Faz geliştirmeleri (Q3) + mobil saha güncellemeleri | Mobil Takım + OpsCenter | Yaz dönemi saha yükü sebebiyle faz geçişleri sıralı yapılır. |
| Eylül | Tatbikat sezonu, entegre simülasyon | Eğitim Ekibi | Faz 16 raporları stratejik panellere eklenir. |
| Ekim-Kasım | Q4 sürüm hazırlıkları, i18n güncellemeleri | Tüm ekipler | Dil dosyaları, kurumlar arası entegrasyon testleri. |
| Aralık | Donmuş dönem, sadece kritik yamalar | CAB | Rollback planları gözden geçirilir, yeni yıl hedefleri doğrulanır. |

Her sürüm penceresi için `release-calendar/<year>.ics` dosyası paylaşılır. Plan dışı acil değişiklikler için Faz 1 ve Faz 22 acil protokolleri kullanılır; aynı dönemde en fazla iki acil değişiklik yapılabilir.

## Bilgi Varlık Envanteri & Sınıflandırma

Bilgi varlıkları, iş kritikliği ve mevzuat yükümlülükleri göz önüne alınarak sınıflandırılır. Envanter `docs/inventory/assets.yaml` dosyasında sürümlenir ve değişiklikler CAB toplantısında onaylanır.

| Varlık Tipi | Örnekler | Sınıf | Sahip | Koruma Önlemleri | İlgili Fazlar |
| --- | --- | --- | --- | --- | --- |
| Uygulama Kaynak Kodu | Laravel API, mobil uygulama repo’ları | INTERNAL | Teknik Lider | Kod incelemesi, imzalı commit, erişim logları | Faz 0, Faz 11 |
| Operasyonel Veriler | Olay, görev, envanter kayıtları | RESTRICTED | Ürün Sahibi | Şifreleme, veri maskeleme, erişim denetimi | Faz 2, Faz 3, Faz 21 |
| Konum ve Takip Telemetrisi | GPS pingleri, hareketsizlik sinyalleri | SECRET | OpsCenter Lideri | Edge şifreleme, kural motoru anonimleştirme | Faz 4, Faz 7, Faz 9 |
| Stratejik Raporlar | ICS formları, strateji panoları | INTERNAL | Yönetim | İki aşamalı onay, watermark, erişim logu | Faz 8, Faz 26 |
| Vatandaş Portalı Verileri | İhbar, gönüllü başvuruları | RESTRICTED | Portal Takımı | KVKK maskeleme, rate limit, moderasyon | Faz 17, Faz 21 |

Envanter girdileri, çeyreklik güvenlik denetiminde rastgele örnekleme ile doğrulanır. Yanlış sınıflandırılan varlık bulunduğunda 48 saat içinde düzeltme yapılır ve olay kayıt altına alınır.

## DPIA & KVKK Etki Analizi Süreci

Kişisel veri işleme faaliyetleri için Data Protection Impact Assessment (DPIA) zorunludur. Süreç, yeni modül geliştirmeleri veya veri yaşam döngüsü değişikliklerinde tetiklenir.

1. **Ön Değerlendirme:** Veri sınıfları, iş amacı ve saklama süresi Faz 21 gereksinimlerine göre belgelenir.
2. **Risk Analizi:** Potansiyel etkiler (`yüksek`, `orta`, `düşük`) ve olasılık puanları belirlenir; risk matrisi ile çaprazlanır.
3. **Azaltım Planı:** Şifreleme, maskeleme, erişim kısıtı ve eğitim gereksinimleri tanımlanır; sorumlular atanır.
4. **Onay & İzleme:** Hukuk birimi ve güvenlik ekibi ortak imza atar; aksiyonların tamamlanması denetim takvimine işlenir.

| Aşama | Teslimat | Kanıt | Sorumlu |
| --- | --- | --- | --- |
| Ön Değerlendirme | Veri işleme özeti formu (`dpias/<module>.md`) | README referansı, veri envanteri kaydı | Ürün Sahibi |
| Risk Analizi | DPIA risk matrisi | Risk & Bağımlılık Matrisi güncellemesi | Güvenlik Ekibi |
| Azaltım Planı | Teknik ve idari kontrol listesi | Runbook referansı, eğitim planı | Teknik Lider + Eğitim Ekibi |
| Onay & İzleme | Hukuk imzalı DPIA raporu | Denetim & Uyum Takvimi kaydı | Hukuk Birimi |

Onaylanmamış DPIA bulunan modül production ortamına alınamaz. DPIA raporları yılda bir kez gözden geçirilir; değişiklik yoksa “revizyon gerekmiyor” ibaresi eklenir.

## Saha Destek Hizmet Seviyesi Programı

Saha operasyonlarını destekleyen süreçler, çoklu tenant yapısında eşit hizmet vermek için standartlaştırılmıştır. Program, saha destek masası, mobil ekip ve edge node bakımını kapsar.

| Hizmet | Kapsam | Hedef Yanıt Süresi | Operasyon Notları | Ölçüm | İlgili Fazlar |
| --- | --- | --- | --- | --- | --- |
| Saha Destek Masası | Kullanıcı çağrıları, QR sorunları | ≤ 10 dk (P90) | 7/24 vardiya; çağrılar OpsCenter CRM’e kaydedilir | Çağrı merkezi raporu | Faz 5, Faz 14 |
| Edge Node Yerinde Müdahale | Offline kuyruk tıkanması, donanım arızası | ≤ 4 saat | Bölge bazlı hazır ekip; yedek node taşıma kiti | Edge müdahale logu | Faz 9 |
| Mobil Uygulama Güncelleme Desteği | Saha cihazlarında sürüm geçişi | 48 saat | Versiyon duyuruları, zorunlu güncelleme penceresi | MDM raporu | Faz 14 |
| Tatbikat Saha Koordinasyonu | Tatbikat günü yerinde teknik destek | Planlanan saatten ≥ 30 dk önce hazır | Tatbikat sonrası rapor, KPI girişi | Tatbikat raporu | Faz 16 |

Program performansı, Operasyonel KPI tablosundaki metriklerle entegre edilir; hedef sapması olduğunda saha koordinatörü düzeltici aksiyon planı sunar.

## Finansal ve Sözleşmesel Yükümlülük Yönetimi

Tedarikçiler, dış servis sağlayıcıları ve lisans sözleşmeleri merkezi bir kayıt altında takip edilir. Finansal uyum, Faz 18 lojistik ve Faz 26 stratejik panellerine veri sağlar.

- **Sözleşme Envanteri:** `docs/contracts/registry.csv` dosyasında tüm sözleşmeler, SLA maddeleri, yenileme tarihleri ve sorumlu sahip listelenir.
- **Bütçe İzleme:** Felaket bütçesi, sürüm başına tahmini harcama ve beklenmeyen giderler için `%5` tampon ayrı tutulur. Çeyrek bazında finans raporu yönetim kuruluna sunulur.
- **Uyum Kontrolleri:** Sözleşmelerdeki veri işleme hükümleri DPIA çıktılarıyla uyumlu değilse hukuk birimi düzeltme ek protokolleri hazırlar.
- **Yenileme Hatırlatıcıları:** Sözleşme bitiş tarihinden 90, 60 ve 30 gün önce otomatik bildirimler Faz 7 kural motoru üzerinden gönderilir; yenilenmeyen sözleşmeler risk matrise eklenir.

Finansal veya sözleşmesel risk tespit edildiğinde, ilgili tedarikçiye geçici kullanım kısıtı getirilebilir; OpsCenter katmanlarında dış veri bağımlılıkları için manuel süreçler devreye alınır.

## Çapraz Faz Doğrulama Takvimi

Fazlar arası entegrasyonların bozulmasını engellemek için yıl boyunca düzenli çapraz doğrulama pencereleri planlanır. Takvim, bağımlı ekiplerin birlikte çalışmasını zorunlu kılar.

| Ay | Odak Faz Kombinasyonu | Doğrulama Senaryosu | Sorumlu Ekipler | Çıktı |
| --- | --- | --- | --- | --- |
| Ocak | 1 ↔ 3 ↔ 7 | Yetki modelinin kural motoru tetiklerine etkisi | Güvenlik + Modül Takımları | Ortak test raporu, rol senaryosu listesi |
| Mart | 4 ↔ 6 ↔ 9 | Canlı takip verisinin OpsCenter ve offline kuyruğa akışı | OpsCenter + Mobil | Hareketlilik raporu, KPI karşılaştırması |
| Mayıs | 8 ↔ 11 ↔ 20 | Playback verisinin dashboard performansına etkisi | Veri + DevOps | Performans testi sonuçları, cache ayarı önerisi |
| Temmuz | 10 ↔ 18 ↔ 23 | Dış servis entegrasyonu ile lojistik IoT akışı | Entegrasyon + Lojistik | İmza doğrulama raporu, fallback prosedürü |
| Ekim | 14 ↔ 21 ↔ 25 | Mobil veri saklama politikaları ve i18n gereksinimleri | Mobil + Hukuk + Çeviri | KVKK uyum kontrol listesi, dil kapsam raporu |

Takvimdeki her pencere sonrası ders çıkarma notu `docs/cross-phase/YYYY-MM.md` dosyasında saklanır ve risk matrisi güncellenir.

## Operasyonel Veri Paylaşım Protokolleri

Tenant’lar, paydaş kurumlar ve kamuoyuyla veri paylaşımı, güvenlik ve mevzuat gerekliliklerini gözeterek yürütülür. Protokoller, Faz 19 entegrasyon ve Faz 21 uyum gereksinimlerini kapsar.

- **Paylaşım Kategorileri:** `Dahili Operasyon`, `Paydaş Kurum`, `Kamu`. Her kategori için izin verilen veri kümeleri ve anonimleştirme seviyeleri tablo halinde `docs/data-sharing/policy.md` dosyasında tutulur.
- **Talep Süreci:** Veri talep eden taraf `data-request` formu doldurur; Ürün Sahibi, Hukuk ve Güvenlik ekipleri 5 iş günü içinde değerlendirme yapar.
- **Teslim Kanalları:** Paydaş kurumlara API anahtarı ile imzalı webhook; kamuya açık veriler için gecikmeli (T+24 saat) anonimleştirilmiş GeoJSON yayınlanır.
- **Denetim İzleri:** Her paylaşım işlemi `data_sharing_logs` tablosuna tenant, kapsam, zaman damgası ve onay zinciriyle kaydedilir. Yıllık denetimde rastgele örnekleme yapılır.
- **Acil Durum Protokolü:** Afet anında kritik veriler (toplanma alanı durumu, görev ilerleme yüzdesi) İçişleri Bakanlığı ile önceden tanımlı güvenli kanal üzerinden 5 dakikalık aralıklarla paylaşılır; paylaşım sonrası rapor Faz 22 olay dosyasına eklenir.

Yetkisiz veri paylaşımı tespit edilirse Seviye 4 olay şiddeti prosedürü uygulanır ve ilgili erişim derhal askıya alınır.

## API & Veri Sözleşmesi Standartları

Faz 6 OpsCenter, Faz 7 kural motoru ve Faz 19 entegrasyonları arasında tutarlı veri akışı sağlamak için API sözleşmeleri ve veri şemaları standartlaştırılır. Standartlar, veri kalitesi ve geriye dönük uyumluluk risklerini azaltmayı hedefler.

- **Sözleşme Formatı:** Her API için `docs/api-contracts/<service>/<version>.yaml` dosyasında OpenAPI şeması, zorunlu alanlar, tenant filtresi ve hata kodları tanımlanır. Şema sürümleri semantic versioning ile etiketlenir.
- **İnceleme Döngüsü:** API değişiklikleri için RFC açılması zorunludur. Teknik lider, güvenlik ekibi ve ilgili modül sahibi değişikliği 7 gün içinde değerlendirir. KVKK etkisi olan alanlar için Hukuk onayı gerekir.
- **Uyumluluk Testleri:** CI pipeline’ına `contract-tests` aşaması eklenmiştir. Yeni sürüm, önceki sürümle geriye dönük uyumluluk testi (`backwardCompatibility`) geçmeden yayımlanamaz.
- **Veri Kalite Kontrolleri:** `data-contract-checker` script’i, örnek payload’ları CHECK/FOREIGN KEY kurallarına göre doğrular; başarısız kayıtlar Faz 2 veri kalite raporuna eklenir.
- **Dağıtım Politikası:** API sözleşmesi major sürüme geçtiğinde, üretim ortamında en az 2 sürüm (N ve N-1) aynı anda desteklenir. Sunset planı, paydaşlara 90 gün önce bildirilir.
- **Gözlemlenebilirlik:** API gateway metrikleri, sözleşme sürümüne göre ayrıştırılır; hata oranı N sürümünde %0.5’i aşarsa otomatik eskalasyon tetiklenir.

Sözleşme standartları ihlal edildiğinde faz geçişleri durdurulur ve ilgili modül takımı düzeltme planı hazırlayana kadar deploy dondurulur.

## Kriz İletişim Planı

Kriz iletişimi, olayın şiddetine göre dakikalar içinde doğru paydaşlara ulaşacak şekilde standartlaştırılır. Plan, Faz 7 kural motoru ve Faz 6 OpsCenter alarm akışına entegre çalışır.

| Şiddet Seviyesi | İlk Bildirim Süresi | Ana Kanal | Alternatif Kanal | Mesaj Sahibi | Gerekli Ekler |
| --- | --- | --- | --- | --- | --- |
| Seviye 1 (Bilgilendirme) | ≤ 30 dk | Slack #ops | E-posta | Ürün Sahibi | Durum özeti, etkilenen modül |
| Seviye 2 (Hizmet Degradasyonu) | ≤ 15 dk | OpsCenter Broadcast | SMS | DevOps Lideri | Etki alanı, beklenen çözüm süresi |
| Seviye 3 (Kritik Kesinti) | ≤ 5 dk | Telefon Konferansı | Push bildirim | Teknik Lider | Rollback planı, olay ID |
| Seviye 4 (Güvenlik İhlali) | ≤ 5 dk | Şifreli Kanal | Hukuk hattı | Güvenlik Direktörü | Olay raporu, veri kapsamı |

- **Mesaj Şablonları:** Ek B’deki formatlar kullanılır; olay ID, zaman damgası ve sorumlu kişi bilgisi zorunludur.
- **Güncelleme Frekansı:** Seviye 3 ve 4 olaylarında 30 dakikada bir durum güncellemesi yapılır; çözüm sonrası 24 saat içinde post-mortem yayınlanır.
- **Onay Zinciri:** Güvenlik ve hukuk gerektiren duyurular çift onay gerektirir; diğer duyurular için ilgili modül lideri yeterlidir.

## İş Sürekliliği & Felaket Kurtarma Planı

İş sürekliliği planı, kritik hizmetlerin hedeflenen sürelerde yeniden ayağa kalkmasını sağlar. Plan, Faz 1 yedekleme politikaları ve Faz 22 dayanıklılık kontrolleri ile senkronize edilir.

| Hizmet | RTO | RPO | DR Senaryosu | Test Periyodu | Not |
| --- | --- | --- | --- | --- | --- |
| MySQL Ana Veritabanı | ≤ 60 dk | ≤ 5 dk | Bölgesel veri merkezi kaybı | 6 ay | Replikasyon + PITR; şifreli yedekler | 
| OpsCenter Web | ≤ 30 dk | ≤ 10 dk | CDN/WAF kesintisi | 3 ay | Çoklu CDN, statik fallback | 
| Mobil Ping API | ≤ 15 dk | ≤ 1 dk | Queue/WS arızası | 2 ay | Horizon failover, offline kuyruk | 
| Kural Motoru | ≤ 45 dk | ≤ 5 dk | Mesaj kuyruğu çökmesi | 6 ay | Retry kuyruğu, manuel tetik betiği | 
| PMTiles Edge Node | ≤ 8 saat | ≤ 24 saat | Saha node kaybı | 12 ay | Yedek node stoku, manuel yükleme | 

- **DR Tatbikatları:** Yılda en az iki kez tam ölçekli DR testi yapılır; sonuçlar Denetim & Uyum takvimine işlenir.
- **Kritik Bağımlılıklar:** Dış servis entegrasyonları için alternatif veri kaynakları listesi `docs/dr/alternatives.md` dosyasında tutulur.
- **Geri Dönüş Kriterleri:** RTO aşılırsa Seviye 3 kriz iletişimi tetiklenir; RPO aşıldığında veri kaybı değerlendirmesi için hukuk ekibi sürece dahil edilir.

## Ekip Onboarding & Yetkinlik Matrisi

Yeni ekip üyelerinin sisteme hızlı ve güvenli uyum sağlaması için rol bazlı onboarding süreci uygulanır. Yetkinlik matrisi, eğitim ve yetkilendirme sürecinin tamamlandığını doğrular.

| Rol | İlk Hafta | İlk Ay | 90 Gün | Yetkinlik Doğrulaması |
| --- | --- | --- | --- | --- |
| Backend Geliştirici | Güvenlik eğitimi, repo erişimi | Faz 2 şema atölyesi, kod inceleme eşleştirmesi | Runbook gölgeleme | Teknik lider imzalı değerlendirme |
| OpsCenter Operatörü | OpsCenter turu, alarm simülasyonu | Canlı takip gözetimi, kural motoru dry-run | Tatbikat yönetimi | OpsCenter lideri imzalı check-list |
| Saha Destek Uzmanı | Mobil cihaz kurulumu, QR testleri | Edge node bakımı, offline kuyruk temizliği | Tatbikat saha koordinasyonu | Saha koordinatörü onayı |
| Güvenlik Analisti | WAF/IDS eğitimi, log analizi | Pentest rapor inceleme, DPIA süreci | Red team tatbikat katılımı | Güvenlik direktörü sertifikası |

- **Onboarding Dokümantasyonu:** `docs/onboarding/<rol>.md` dosyaları, gerekli eğitim videoları ve quiz bağlantılarını içerir.
- **Yetki Aktivasyonu:** Spatie rol atamaları onboarding check-list’inin tamamlanması ve Ürün Sahibi onayı sonrası etkinleştirilir.
- **Süreklilik:** Yetkinlikler yılda bir kez yeniden doğrulanır; eksik bulunan maddeler için yeniden eğitim planlanır.

## Saha Güvenlik Protokolleri

Saha ekiplerinin güvenliği, fiziksel ve dijital koruma önlemlerinin birleşimiyle sağlanır. Protokoller Faz 4 canlı takip, Faz 5 QR yoklama ve Faz 14 mobil uygulama süreçlerine entegredir.

1. **Kişisel Koruyucu Donanım (KKD):** Göreve çıkmadan önce ekip lideri KKD kontrol listesini (`docs/field/ppe-checklist.md`) imzalar. Eksik ekipmanla görev başlatılamaz.
2. **Sağlık Kontrolleri:** Belgeleri süresi geçmiş veya sağlık durumu riskli işaretlenen kullanıcıya görev ataması yapılamaz (Faz 3).
3. **Konum Doğrulama:** QR yoklama sırasında cihaz GPS verisi geofence dışındaysa yoklama reddedilir ve OpsCenter’a alarm gider.
4. **Hareketsizlik Doğrulaması:** 120 sn hareketsizlik alarmı geldiğinde saha personeline öncelikle mobil push gönderilir; 15 sn içinde yanıt yoksa lider araması zorunludur.
5. **İletişim Güvenliği:** Mobil cihazlar MDM ile yönetilir; kayıp/çalıntı bildirimi geldiğinde cihaz uzaktan silinir.

Saha protokollerine uyumsuzluk, Seviye 2 olaya yükseltilir ve eğitim tekrarına sevk edilir. Tatbikatlarda protokol uyumu puanlanır ve KPI tablolarında raporlanır.

## Kalite Güvence Kontrol Noktaları

Kalite güvence süreci, fazların teslimatlarında minimum kalite standardını sağlar. Kontrol noktaları Faz 12 testleri ile Faz 11 CI/CD sürecine bağlanır.

| Kontrol Noktası | Zamanlama | Sorumlu | Gereken Artefakt | Geçiş Ölçütü |
| --- | --- | --- | --- | --- |
| Kod İnceleme Tamamlandı | Her PR | Kod Sahibi + İnceleyen | Review onay kayıtları | En az 2 onay, güvenlik maddeleri işaretli |
| Test Kapsamı ≥ %80 | Sprint sonu | QA | Coverage raporu | Trend düşüşü yok, kritik testler yeşil |
| Güvenlik Tarama Temiz | Haftalık | Güvenlik | Zafiyet tarama raporu | High/Critical bulgu kalmadı |
| Performans Değerlendirmesi | Faz geçişi öncesi | DevOps | Yük testi sonuçları | SLA hedefleri karşılandı |
| Dokümantasyon Güncellemesi | Release öncesi | Ürün Sahibi | README/CHANGELOG diff | Tüm ilgili faz başlıkları güncel |

Kontrol noktalarında başarısız olan maddeler için düzeltici aksiyon planı açılır ve kapanana kadar faz geçişi yapılmaz.

## Güvenlik Zafiyet Yönetimi & Patch Süreci

Zafiyetlerin hızlı ve ölçülebilir şekilde kapatılması Faz 1 güvenlik temelinin ve Faz 22 siber dayanıklılık hedeflerinin kritik bir bileşenidir. Süreç keşiften kapanış doğrulamasına kadar tek bir yaşam döngüsü olarak işletilir.

1. **Keşif & Kayıt:** Otomatik taramalar, tehdit avı çıktıları veya dış bildirimler `security/vuln-register.csv` dosyasına işlenir. Her kayıt, CVSS skoru, etkilenen bileşen ve tenant kapsamı ile birlikte tutulur.
2. **Önceliklendirme:** Güvenlik ekibi 24 saat içinde bulguyu sınıflandırır; üretim ortamını etkileyen kritik bulgular için acil CAB toplantısı tetiklenir.
3. **Mitigasyon & Patch:** İlgili modül ekibi düzeltme veya geçici mitigasyonu uygular. Patch’ler önce staging ortamında doğrulanır, ardından mavi/yeşil yayın stratejisi ile canlıya alınır.
4. **Doğrulama & Kapanış:** Güvenlik ekibi düzeltmeyi yeniden tarama, manuel test veya purple team doğrulaması ile kapatır; sonuçlar audit log’a işlenir.
5. **Geribildirim:** Kapanan her kritik bulgu için kök neden analizi yapılır ve sonuçlar Faz 26 stratejik raporlarına yansıtılır.

| Öncelik Seviyesi | Tetikleyici Örnekleri | İlk Değerlendirme SLA | Patch/Geçici Mitigasyon SLA | Kapanış Doğrulaması | İletişim Kanalı |
| --- | --- | --- | --- | --- | --- |
| **Kritik** | Uzaktan kod çalıştırma, kimlik bypass, veri sızıntısı | ≤ 4 saat | ≤ 24 saat | Yeniden tarama + purple team spot testi | Kriz iletişim protokolü, yönetim bildirimi |
| **Yüksek** | Yetki yükseltme, tenant izolasyonu ihlali | ≤ 1 iş günü | ≤ 3 iş günü | Otomatik tarama + kod inceleme | CAB haftalık özeti |
| **Orta** | Input validation eksikleri, bilgi ifşası | ≤ 2 iş günü | ≤ 2 hafta | QA + otomatik tarama | Sprint demo |
| **Düşük** | Güvenlik sertleştirme tavsiyeleri | ≤ 5 iş günü | Sprint planına alınır | Dokümantasyon kontrolü | Aylık güvenlik raporu |

- **Yama Takvimi:** Kritik/yüksek bulgular için plan dışı yayın yapılabilir; diğerleri aylık güvenlik yayın penceresine toplanır.
- **Rol ve Sorumluluk:** Modül takımları düzeltme uygulamasından, Güvenlik Ekibi doğrulamadan, DevOps dağıtım planlamasından sorumludur.
- **Metrikler:** `vuln_time_to_fix` ve `patch_backlog_size` metrikleri Faz 20 panolarında izlenir; SLA ihlali durumunda Yönetim Kurulu gündemine alınır.

## Veri İhlali Bildirim & Olay Müdahale Planı

Veri ihlali senaryoları için hızlı müdahale ve zorunlu bildirim adımları Faz 21 mevzuat uyumu ile Faz 11 operasyonel hazır oluş planlarına entegre edilmiştir.

1. **Tespit:** Anormal erişim, audit log anomalileri veya dış bildirimler `incident_response` kanalını tetikler. İlk 30 dakika içinde olay komutanı atanır.
2. **Sınırlama:** Erişim anahtarları döndürülür, etkilenen tenant izole edilir, gerekli ise offline çalışma moduna geçilir (Faz 9).
3. **Değerlendirme:** Etkilenen veri tipleri, kayıt sayısı ve mevzuat kapsamı belirlenir. Hukuk ekibi 4 saat içinde KVKK bildirimi gerekliliğini değerlendirir.
4. **Bildirim:** Yasal süreler (KVKK için 72 saat) gözetilerek ilgili otoriteler, kurum yönetimi ve etkilenen paydaşlar bilgilendirilir. Bildirimler Ek D şablonuna göre hazırlanır.
5. **Geri Yükleme & İzleme:** Sistemler temiz kopyadan geri yüklenir, ek gözlemlenebilirlik kuralları aktive edilir ve olay sonrası rapor hazırlanır.

| İhlal Şiddeti | Paydaş Bildirim Süresi | Zorunlu Aksiyonlar | Ek İzleme |
| --- | --- | --- | --- |
| **Seviye 1** — Kısıtlı iç erişim ihlali | 24 saat | Şifre rotasyonu, etkilenen kullanıcıya bilgilendirme | Ek audit log incelemesi |
| **Seviye 2** — Tenant bazlı veri sızıntısı | 12 saat | KVKK ön bildirimi, OpsCenter uyarısı, hizmet kesintisi duyurusu | IDS/WAF kural yükseltmesi |
| **Seviye 3** — Çoklu tenant/kişisel veri ihlali | 4 saat | Otorite bildirimi, kamu açıklaması, kriz iletişim planı | 7/24 SOC gözetimi + purple team tatbikatı |

- **Dokümantasyon:** Tüm olay raporları `security/breach-reports/` klasöründe saklanır ve Denetim & Uyum takviminde gözden geçirilir.
- **Eğitim:** OpsCenter ve hukuk ekipleri yılda iki kez veri ihlali masa başı tatbikatı yapar; sonuçlar Risk Matrisi’ne işlenir.
- **İyileştirme:** Olay sonrası aksiyonlar ilgili faz backlog’larına görev olarak açılır ve kapanışları CAB tarafından takip edilir.

## Performans & Kapasite İzleme Çerçevesi

Sistem büyüdükçe performansın sürdürülebilir kalması için Faz 11 gözlemlenebilirlik, Faz 6 OpsCenter ve Faz 9 offline yetenekleri ortak bir izleme çerçevesi içinde yönetilir.

- **Temel Metodoloji:** Prometheus, Grafana ve APM araçları üzerinden toplanan metrikler için hedef/erken uyarı eşikleri tanımlanır. Trend analizleri Faz 20 panolarında yayınlanır.
- **Kapasite Planlama:** Aylık kapasite değerlendirme toplantısı DevOps liderliğinde yapılır; büyüme projeksiyonları Faz 26 stratejik planına aktarılır.
- **Ölçüm Katmanları:** Uygulama (Laravel), veri tabanı (MySQL), mesajlaşma (Redis/WebSocket), harita servisleri (MapLibre/PMTiles) ve edge node performansı ayrı dashboard’larda izlenir.
- **Öncelikli Alarm Eşikleri:** SLA ihlali riski görüldüğünde otomatik ölçeklendirme veya degrade mod senaryoları tetiklenir.

| Bileşen | İzlenen Metrik | Erken Uyarı Eşiği | SLA Riski Eşiği | Otomatik Aksiyon |
| --- | --- | --- | --- | --- |
| Laravel API | P95 yanıt süresi | 1.5 sn | 2.5 sn | Otomatik pod ölçeklendirme, read replica yönlendirme |
| MySQL | Replikasyon gecikmesi | 150 ms | 400 ms | Okuma trafiğini alternatif replika/edge node’a kaydırma |
| Redis/WebSocket | Aktif bağlantı sayısı | %70 kapasite | %90 kapasite | Yeni node devreye alma, throttling |
| MapLibre/Tile Cache | Tile cache miss oranı | %20 | %35 | Ön ısıtma scripti, CDN fallback |
| Edge Node | Kuyruk uzunluğu | 50 olay | 100 olay | Yerel uyarı + manuel senkronizasyon |

- **Kapasite Günlükleri:** `observability/capacity-journal.md` dosyasında metrik yorumları ve alınan aksiyonlar kayıt altına alınır.
- **Test Senaryoları:** Faz 12 yük testleri en az çeyreklik olarak güncellenir ve kapasite varsayımlarını doğrular.
- **Maliyet Optimizasyonu:** Kaynak kullanım raporları finans ekibiyle paylaşılır; gereksiz kapasite Faz 18 lojistik ve Faz 26 planlarına göre yeniden tahsis edilir.

## Çok Lokasyonlu Felaket Tatbikat Programı

Farklı veri merkezleri, edge node’lar ve saha kitleri arasında koordineli felaket tatbikatları yürütülerek RTO/RPO hedeflerinin gerçekçi şekilde sınanması sağlanır. Program Faz 11 DR planları ile Faz 9 offline yeteneklerini bütünleştirir.

| Periyot | Senaryo | Kapsam | Başarı Kriterleri | Raporlama |
| --- | --- | --- | --- | --- |
| 6 ayda bir | Bölgesel veri merkezi kaybı | Birincil DC kapatılır, yedek DC devreye alınır | RTO ≤ 2 saat, veri kaybı yok, OpsCenter erişilebilir | `dr/exercises/regional-dc-<YYYYMM>.md` |
| Yılda bir | Edge node izolasyonu | 3 ilde edge-only operasyon, offline kuyruk boşaltma testi | Kuyruk < 30 dk, görev kesintisi yok | CAB + saha raporu |
| Yılda bir | Çoklu dış servis kesintisi | SMS, harita ve IoT beslemeleri aynı anda kesilir | Alternatif süreçler 4 saat içinde aktif, iletişim şablonları kullanıldı | Kriz iletişim kaydı + Faz 10 değerlendirmesi |
| İki yılda bir | Tam saha–merkez ayrışması | OpsCenter karanlık mod, saha kitleri üzerinden yönetim | Manuel runbooklar çalıştı, veri senkronizasyonu 6 saat içinde tamamlandı | Yönetim Kurulu sunumu |

- **Tatbikat Hazırlığı:** Senaryo planları `dr/scenarios/` dizininde saklanır; ilgili faz sahipleri en az 30 gün önce bilgilendirilir.
- **Çapraz Değerlendirme:** Tatbikat sonuçları Çapraz Faz Doğrulama Takvimi’ne işlenir ve Faz 26 stratejik raporlarıyla ilişkilendirilir.
- **İyileştirme İzleme:** Bulunan açıklar için aksiyon maddeleri `dr/backlog.csv` dosyasına eklenir; kapanış SLA’sı 60 gündür.

## Ekler

### Ek A — Faz Geçiş Kontrol Formu

| Kontrol Maddesi | Açıklama | Kanıt Dokümanı | Onaylayan |
| --- | --- | --- | --- |
| Gereksinim kapsamı netleştirildi | Faz hedefleri ve teslimatlar onaylandı mı? | Faz Geçiş Kriterleri tablosu | Ürün Sahibi |
| Test kapsamı tamamlandı | Birim/entegrasyon/yük test sonuçları arşivlendi mi? | CI pipeline raporu | Teknik Lider |
| Güvenlik kontrolleri uygulandı | 2FA, WAF, pentest bulguları kapatıldı mı? | Güvenlik raporu | Güvenlik Ekibi |
| Operasyonel runbook güncel | İlgili runbook versiyonları gözden geçirildi mi? | `docs/runbook/` sürüm notu | OpsCenter Lideri |
| Eğitim ve iletişim planlandı | Saha duyuruları ve eğitim materyali hazır mı? | Eğitim planı, iletişim şablonu | Eğitim Koordinatörü |

Form, her faz geçişinde CAB toplantısında doldurulur ve `docs/release/` altında saklanır. Eksik imza bulunan form ile geçiş yapılamaz.

### Ek B — Kritik Rol İletişim Şablonları

Standart iletişim şablonları, kriz anında hızlı bilgi aktarımını sağlar. Şablonlar `docs/templates/communications/` dizininde yer alır.

- **Bakım Duyurusu (OpsCenter → Tenant):** Planlanan kesinti, etkilenecek modüller, alternatif süreçler ve geri dönüş tahmini içerir; Faz 6 ve Faz 9 referansları belirtilir.
- **Hareketsizlik Alarmı Eskalasyonu (OpsCenter → Lider):** Personel adı, görev ID’si, son ping zamanı, alınan aksiyonlar; yanıtsız kalınırsa 60 sn içinde Seviye 4 protokolü tetikler.
- **Güvenlik İhlali Bildirimi (Güvenlik → Yönetim/Hukuk):** Olay zaman çizelgesi, etkilenen veriler, alınan ilk önlemler; 2 saat içinde güncelleme taahhüdü içerir.
- **Dış Servis Kesintisi Bilgilendirmesi (Entegrasyon → OpsCenter):** Etkilenen katman, geçici veriler, manuel süreç önerileri; Faz 10 mitigasyonlarına referans verir.

Şablonlar değiştirildiğinde sürüm numarası ve tarih kaydı tutulur; değişiklikler Denetim & Uyum Takvimi kapsamında doğrulanır.

### Ek C — Hızlı Referans Metrikleri

Acil durumlarda kullanılmak üzere kritik metriklerin hedefleri aşağıda özetlenmiştir. OpsCenter duvar panosu bu metrikleri sürekli gösterir.

| Metrik | Hedef | Kaynak | Eskalasyon Eşiği |
| --- | --- | --- | --- |
| `no_motion_ack_seconds` | ≤ 60 sn | Prometheus | ≥ 90 sn → Seviye 3 |
| `ws_delivery_success` | ≥ %99.3 | Prometheus/Grafana | < %98 → Seviye 3 |
| OpsCenter P95 yanıt süresi | ≤ 2 sn | RUM | > 3 sn → Degrade plan |
| Offline kuyruk boşaltma süresi | ≤ 5 dk | Mobil telemetri | > 8 dk → Edge runbook |
| Yedekleme tamamlanma süresi | ≤ 2 saat | Backup logları | > 3 saat → CAB uyarısı |

Metrik tabloları haftalık olarak güncellenir ve KPI panoları ile tutarlılığı Denetim & Uyum Takvimi sırasında doğrulanır.

### Ek D — Veri İhlali Bildirim Şablonu

Veri ihlali bildirimleri için standart şablon, mevzuat gereksinimlerinin eksiksiz karşılanmasını sağlar. Şablon `docs/templates/security/breach-notification.md` dosyasında saklanır.

- **Başlık:** Olay kimliği, tespit zamanı, sorumlu ekip.
- **Özet:** İhlalin kapsamı, etkilenen veri türleri, sistemler ve tenant’lar.
- **Alınan Önlemler:** Derhal uygulanan sınırlama adımları, geçici mitigasyonlar, yama planı.
- **Etkilenen Taraflar İçin Adımlar:** Şifre değiştirme, dikkat edilmesi gereken sahtekârlık girişimleri, iletişim kanalları.
- **Yasal Bildirimler:** KVKK/oturite bildirim tarihleri, referans numaraları, iletişim kişileri.
- **Sonraki Adımlar:** Kök neden analizi takvimi, planlanan denetimler, takip iletişimleri.

Şablon güncellendiğinde versiyon numarası artırılır, değişiklikler Güvenlik & Uyum Konseyi toplantısında onaylanır ve Denetim & Uyum Takvimi kayıtlarına işlenir.

## Siber Tehdit İstihbaratı Entegrasyonu

Kurumsal tehdit istihbaratı, Faz 22 kapsamında yürütülen siber dayanıklılık çalışmalarının ayrılmaz parçasıdır. Amaç, tehdit aktörlerini önceden tanımlamak ve kritik servislerde kullanılabilecek TTP (taktik, teknik, prosedür) verilerini merkezi olarak işleyebilmektir.

- **Kaynaklar:** Ulusal Siber Olaylara Müdahale Merkezi (USOM) bildirimleri, MITRE ATT&CK güncellemeleri, iş ortaklarından gelen IoC akışları. Harici beslemeler `threat_feeds/` dizinine JSON olarak kaydedilir.
- **Zincir:** IoC → WAF kuralı → IDS/PaaS log alarmı → OpsCenter güvenlik paneli. Her IoC, tenant bazlı olarak değerlendirilir; gereksiz bloklama riskine karşı dry-run modunda 24 saat test edilir.
- **Otomasyon:** Faz 7 kural motoru, “threat_feed.match” tetiklerine özel aksiyon setleri içerir. Yüksek riskli IoC tespiti, otomatik IP engeli ve güvenlik ekibine SMS gönderimi ile sonuçlanır.
- **Kayıt & Ölçüm:** Her besleme için erişim tarihi, kaynağı, uygulanan kural ve sonuç analizi `security/threat-intel-register.md` dosyasında tutulur. Haftalık raporda başarı/yanlış pozitif oranı paylaşılır.
- **Test:** Yeni istihbarat kuralı canlıya alınmadan önce Faz 12 güvenlik testleri tarafından pentest simülasyonlarında doğrulanır.

## Tehdit Avı & Purple Team Tatbikat Programı

Tehdit istihbaratından gelen sinyallerin doğrulanması ve savunma kontrollerinin stres testinden geçirilmesi için sürekli tehdit avı (threat hunting) ve purple team tatbikat programı uygulanır. Program, Faz 22 siber dayanıklılık hedefleri ile Faz 12 güvenlik test katmanlarının üzerine inşa edilmiştir.

| Periyot | Tatbikat Tipi | Odak Senaryolar | Ölçülen Çıktılar | Raporlama Kanalı |
| --- | --- | --- | --- | --- |
| Aylık | Threat Hunting Sprint | Anomali log incelemesi, hareketsizlik alarmı suiistimali, tenant yetki yükseltme denemeleri | Bulgu sayısı, yanlış pozitif oranı, kapatılan IoC | `security/threat-hunt/YYYY-MM.md` |
| Çeyreklik | Purple Team Egzersizi | OpsCenter degrade senaryosu, offline kuyruk manipülasyonu, kural motoru bypass girişimleri | MITRE ATT&CK boşluk analizi, kontrol olgunluk puanı | CAB + Yönetim sunumu |
| Yıllık | Red/Blue/Purple Entegre Tatbikat | Tam ölçekli SOC simülasyonu, dış servis üzerinden zincirleme saldırı | RTO/RPO sapmaları, olay eskalasyon süresi, kullanıcı etkisi | Stratejik pano (Faz 26) |

- **Tatbikat Hazırlığı:** Tehdit avı görevleri Jira/SOC backlog’una işlenir; hipotezler, kullanılacak log kaynakları ve beklenen göstergeler (KPI) listelenir.
- **Metrik Takibi:** Purple team çıktılarına göre Faz 1 WAF/IDS kural güncellemeleri ve Faz 7 kural motoru iyileştirmeleri için SLA 14 gün olarak belirlenmiştir.
- **Geri Besleme Döngüsü:** Tatbikat sonrası dersler `docs/threat-program/lessons-learned.md` dosyasında toplanır ve risk matrisi güncellemesi zorunludur.

## Etik Kurul & İç Denetim Süreçleri

Sistem, veri erişimi ve otomasyon kararlarında etik dengeyi korumak için faz bağımsız bir denetim mekanizması işletir.

| Süreç | Periyot | Sorumlu | Çıktı |
| --- | --- | --- | --- |
| Etik kurul toplantısı | Aylık | Ürün Sahibi, Güvenlik Ekibi, Hukuk Danışmanı | Karar kayıtları, kural güncelleme talepleri (`docs/ethics/README.md`) |
| İç denetim (erişim logları) | 2 ayda bir | İç Denetim Birimi | KVKK uyum raporu, erişim ihlali aksiyonları |
| Yapay zekâ etik incelemesi | Sürüm öncesi | AI Governance Ekibi | Model risk sınıflandırması, onay/ret kararı |
| Sosyal etki değerlendirmesi | Yılda bir | Yönetim Kurulu | Paydaş geri bildirim özetleri |

- **Ajanda Yönetimi:** Kurul sekreteri toplantıların durumunu ve artefakt listesini `docs/ethics/review-schedule.md` dosyasında günceller; boş karar alanı bırakılamaz.

- **Yetki Yönetimi:** Faz 1 ve Faz 21 çıktıları, etik kurul kararlarına göre güncellenir. Örn. kritik belgeler için erişim süreleri kısaltılabilir.
- **İzlenebilirlik:** Tüm denetim tutanakları `governance/audit/` altında saklanır, değişiklikler imzalı commit’lerle takip edilir.
- **Uyum:** Denetim bulguları, Faz 26 stratejik planına risk azaltıcı aksiyon olarak eklenir; 90 gün içinde kapatılması zorunludur.

## Personel Refahı & Psikolojik Destek Programı

Afet operasyonları yüksek stres içerdiğinden, saha ve merkez ekiplerinin refahı için sürdürülebilir bir destek programı yürütülür.

1. **Refah Göstergeleri:** Faz 4 ping verileri, kullanıcıların vardiya süreleri ve Faz 14 mobil uygulama kullanım istatistikleri ile ilişkilendirilerek aşırı yük tespiti yapılır. 12 saatten uzun kesintisiz görevler otomatik alarm üretir.
2. **Psikolojik İlk Yardım Hattı:** OpsCenter üzerinden 7/24 erişilebilir; kayıtlar KVKK gereği anonimleştirilir ve Faz 21 politikalarıyla uyumlu tutulur.
3. **Dinlenme Pencereleri:** Görev atama motoru (Faz 15) personelin son dinlenme zamanını hesaba katar; minimum 8 saat dinlenme süresi korunur.
4. **Eğitim:** Yılda iki kez travma sonrası destek modülleri düzenlenir; katılım `training/attendance.csv` dosyasında izlenir.
5. **Geri Bildirim Döngüsü:** Refah anketleri `ops-feedback` tenant’ı üzerinden toplanır; sonuçlar Faz 8 analitik raporlarına dahil edilir.

> **Destek Artefaktları:** Detaylı protokoller `docs/wellbeing/README.md` ve `hr/wellbeing-program.md` dosyalarında, günlük kontrol listesi ise `hr/wellbeing-checklist.csv` içinde tutulur.

## Topluluk ve Geri Bildirim Platformu

Sistem, vatandaşlar ve gönüllüler için şeffaf bir iletişim alanı sağlayarak güveni artırmayı hedefler.

- **Platform Bileşenleri:** Web tabanlı portal (Faz 17), mobil uygulama içi geri bildirim modülü (Faz 14) ve OpsCenter moderasyon paneli.
- **Moderasyon Akışı:** İçerikler otomatik risk skorlamasına tabi tutulur; yüksek riskli girdiler karantina kuyruğuna düşer ve en geç 6 saat içinde moderatör onayı gerekir.
- **Şeffaflık Panosu:** Onaylanan ihbar ve çözüm süreleri, Faz 20 dashboard’unda anonimleştirilmiş olarak yayınlanır. Veri paylaşımı Faz 21 uyum kriterlerine göre yapılır.
- **Katılım Teşviki:** Gönüllü katkıları için rozet sistemi uygulanır; rozet kazanımı, eğitim ve tatbikat katılımıyla ilişkilendirilir (Faz 16).
- **API Paylaşımı:** Kamu kurumları için özel anahtarla erişilen veri setleri sağlanır; rate limit ve audit kayıtları Faz 10 ve Faz 19 kurallarıyla uyumludur.

> **Yönetişim Kaynakları:** Topluluk süreçlerinin ayrıntıları `docs/community/README.md` dosyasında, saha buluşmaları ise `community/engagement-log.csv` kaydında izlenir.

## Saha Teknoloji Kitleri & Lojistik Destek

Kırsal veya altyapının hasarlı olduğu bölgelerde kesintisiz hizmet sağlamak için standart saha teknoloji kitleri tanımlanmıştır.

| Kit Bileşeni | Açıklama | Faz Referansı |
| --- | --- | --- |
| Edge sunucu | Offline veri kuyruğu, PMTiles cache | Faz 9 |
| Çok bantlı router + uydu yedek | İkincil bağlantı, QoS profilleri | Faz 22 |
| Güç yönetimi seti | Taşınabilir UPS, güneş paneli, jeneratör bağlantısı | Faz 11, Enerji Planı |
| Cihaz yönetim tabletleri | Görev planlama, QR kontrolü | Faz 5, Faz 14 |
| Güvenli taşıma kasası | Envanter zimmet ve güvenlik | Faz 3, Faz 18 |

- **Lojistik İzleme:** Her kit, QR kodu ile zimmetlenir; hareketler Faz 18 lojistik modülünde takip edilir.
- **Bakım Çevrimi:** Enerji bileşenleri için 6 ayda bir kapasite testi yapılır; sonuçlar Enerji Sürekliliği planına işlenir.
- **Olay Sonrası İnceleme:** Kitlerin performansı, operasyon sonrası değerlendirme oturumlarında gözden geçirilir ve iyileştirme backlog’una aktarılır.

## Üçüncü Taraf & Tedarikçi Yönetimi

Üçüncü taraf servisler (SMS sağlayıcıları, harita servisleri, bulut altyapı, saha ekipman tedarikçileri) için standart bir risk yönetimi uygulanır. Süreç, tedarikçinin seçilmesinden sözleşmenin feshedilmesine kadar dört aşamada yürütülür.

| Aşama | Kontrol Noktaları | Sorumlu Takımlar | İlgili Fazlar |
| --- | --- | --- | --- |
| **Ön Değerlendirme** | Güvenlik anketi, KVKK uyumu, SLA örnekleri, finansal sağlamlık doğrulaması | Güvenlik, Hukuk, Ürün | Faz 1, Faz 21, Faz 22 |
| **Onboarding** | Test ortamı entegrasyonu, API rate limit testi, audit log entegrasyonu | Modül ekibi, DevOps | Faz 6, Faz 7, Faz 10 |
| **Sürdürme** | Çeyreklik performans raporu, SLA uyum analizi, pentest sonucu paylaşımı | Ürün, Güvenlik, Finans | Faz 11, Faz 18, Faz 22 |
| **Fesih/Alternatif** | Veri ihracı ve silme protokolü, erişim iptali, alternatif sağlayıcı devreye alma tatbikatı | Hukuk, DevOps, OpsCenter | Faz 9, Faz 13, Faz 22 |

- **Vendor Kayıtları:** Tüm tedarikçiler `docs/vendors/` klasöründe YAML dosyalarıyla tutulur; dosya içinde sorumlu kişi, sözleşme bitiş tarihi ve SLA şartları yer alır.
- **Risk Skoru:** Tedarikçiler `düşük`, `orta`, `yüksek` risk olarak sınıflandırılır. Yüksek riskli tedarikçiler için yıllık site ziyareti veya bağımsız denetim raporu zorunludur.
- **Otomatik İzleme:** Dış servis API’leri için sağlık kontrolleri (Faz 10) Prometheus’ta ayrı panolara bağlanır; SLA ihlali 3 ardışık ölçümde görülürse Seviye 2 olaya yükseltilir.
- **Sözleşme Öncesi Tatbikat:** Kritik servisler (ör. SMS, harita) için production dışı ortamda 48 saatlik deneme yapılır; sonuçlar CAB toplantısında değerlendirilir.

## Model Yönetimi & Yapay Zeka Yönetişimi

Faz 15 kapsamında devreye alınan yapay zekâ modelleri için yaşam döngüsü yönetimi uygulanır. Amaç, öneri motoru ve medya analitiği gibi bileşenlerin güvenilir, şeffaf ve denetlenebilir şekilde çalışmasını sağlamaktır.

1. **Model Kataloğu:** `docs/ml/models.yaml` dosyasında model sürümleri, eğitim veri setleri, özellik listeleri ve onay tarihleri tutulur.
2. **Veri Yönetimi:** Eğitim verileri Faz 24 veri gölüne anonimleştirilerek taşınır. KVKK gereği kişisel veriler maskelenmiş olmalıdır; aksi halde model eğitimi engellenir.
3. **Değerlendirme Eşiği:** Modeller canlıya alınmadan önce doğruluk, hatalı alarm oranı ve yanıt süresi metrikleri Faz 12 test pipeline’ına eklenir. Eşik altı değerlerde deployment bloklanır.
4. **Drift İzleme:** Canlı ortamda model çıktıları ile gerçek sonuçlar karşılaştırılır; `model_drift_score` metriği P95’te %5’i aşarsa model geri çekilir veya yeniden eğitilir.
5. **Açıklanabilirlik & Kayıt:** Her öneri için açıklama metinleri OpsCenter’da görülebilir olmalı; açıklama oluşturulamayan işlemler audit log’da işaretlenir.
6. **İnsan Onayı:** Kritik görev atama önerileri lider onayına düşer; otomatik atama yalnızca tatbikat modunda izinlidir.

Model değişiklikleri için ayrı RFC süreci yürütülür; değişiklik dosyasına eğitim veri seti, hiperparametreler ve değerlendirme sonuçları eklenmek zorundadır. Hatalı öneriler veya yanlılık şikayetleri Faz 15 backlog’unda takip edilir ve üç iş günü içinde yanıtlanır.

## Enerji Sürekliliği & Sürdürülebilirlik Planı

Afet bölgelerinde ve veri merkezinde enerji sürekliliği kritik öneme sahiptir. Plan, saha operasyonları ile altyapı bileşenlerini kapsar ve sürdürülebilirlik hedefleriyle uyumludur.

| Bileşen | Süreklilik Kontrolü | Minimum Gereksinim | Test Periyodu | İlgili Fazlar |
| --- | --- | --- | --- | --- |
| Veri Merkezi | Çift güç hattı, UPS, jeneratör | 72 saat kesintisiz çalışabilme | Yıllık yük testi | Faz 11, Faz 22 |
| Edge Node | Taşınabilir batarya, güneş paneli opsiyonu | 12 saat aktif görev | Çeyreklik saha testi | Faz 9, Faz 14 |
| Mobil Cihaz | Power bank zorunluluğu, düşük güç modu | 8 saat görev süresi | Her tatbikatta kontrol | Faz 4, Faz 14 |
| OpsCenter | Kritik ekranlar için UPS, jeneratör yakıt takip | 24 saat kesintisiz yayın | Aylık tatbikat | Faz 6, Faz 13 |

- **Enerji Tüketim İzleme:** OpsCenter ve veri merkezi enerji metrikleri Grafana panosunda gösterilir; tüketim anomalisi Faz 26 stratejik raporlarına işlenir.
- **Karbon Ayak İzi:** Yıllık enerji raporunda veri merkezi ve saha operasyonlarının tahmini karbon salımı raporlanır; azaltım hedefleri Faz 26 panellerine eklenir.
- **Yedek Yakıt & Batarya Stoğu:** Saha ekipleri için minimum yedek batarya stok seviyesi belirlenir; stok kritik eşiğin altına düştüğünde Faz 18 lojistik ekibine otomatik talep açılır.
- **Yeşil Enerji Önceliği:** Uzun vadede yenilenebilir enerji kaynaklarının kullanımını artırmak için tedarikçi seçiminde sürdürülebilirlik puanı değerlendirmeye dahil edilir.

## Paydaş Koordinasyon & Harici Tatbikatlar

TUDAK sistemi birden fazla kurumla eşgüdüm gerektirdiğinden, paydaş koordinasyonu planlı tatbikatlarla desteklenir. Bu bölüm Faz 19, Faz 20 ve Faz 26 süreçleriyle entegre çalışır.

| Paydaş | Koordinasyon Kanalı | Tatbikat Sıklığı | Paylaşılan Artefaktlar | Notlar |
| --- | --- | --- | --- | --- |
| AFAD/AYDES | Resmî entegrasyon API’leri, kriz masası toplantıları | 6 ayda bir ortak tatbikat | Olay senaryoları, ICS raporları, veri paylaşım protokolleri | Mutabakat metni `docs/mou/afad.pdf` |
| Belediyeler | OpsCenter davetli erişimi, haftalık durum bülteni | Çeyreklik koordinasyon çağrısı | Lojistik stok raporu, toplanma alanları katmanı | Belediye temsilcisi için sınırlı rol atanır |
| Sağlık & UMKE | Acil kanal (şifreli), saha telsiz yedek | 4 ayda bir saha tatbikatı | Hareketsizlik alarm raporları, saha güvenlik protokolü | Medikal gizlilik sözleşmesi |
| Gönüllü Kuruluşlar | Vatandaş portalı, eğitim webinarları | Aylık bilgilendirme oturumu | Eğitim materyalleri, QR yoklama yönergeleri | Kimlik doğrulama için ön onay süreci |

- **Tatbikat Senaryoları:** Harici paydaş tatbikatlarında gerçek üretim verisi kullanılmaz; simülasyon modu (Faz 16) zorunludur.
- **Gizlilik Protokolleri:** Paydaşlarla paylaşılan veriler, veri paylaşım protokollerine (bkz. ilgili bölüm) uygun şekilde maskelenir ve süreli erişim verilir.
- **Geri Bildirim Döngüsü:** Tatbikat sonrası paydaş geribildirimleri 14 gün içinde değerlendirilir; gerekli güncellemeler Faz 19 entegrasyon backlog’una işlenir.
- **Kriz Masası Aktivasyonu:** Seviye 4 olaylarda paydaşlara ait irtibat kişilerinin listesi `docs/stakeholders/contacts.csv` dosyasından çekilir ve kriz iletişim planı doğrultusunda bilgilendirme yapılır.

## Kullanıcı Geri Bildirim & Memnuniyet Ölçümü

Kullanıcı deneyimi, canlı operasyon kalitesini doğrudan etkiler. Bu bölüm, Faz 6 OpsCenter, Faz 14 mobil uygulama ve Faz 17 vatandaş portalı ekiplerinin ortak sorumluluğundadır.

| Akış | Veri Kaynağı | Ölçüm Sıklığı | Başarı Kriteri | Eskalasyon Eşiği | İlgili Takımlar |
| --- | --- | --- | --- | --- | --- |
| OpsCenter Operatör Memnuniyeti | Aylık NPS anketi (`surveys/opscenter-nps.json`) | Aylık | NPS ≥ 45 | NPS < 30 veya ardışık 2 ay düşüş | Ürün, Operasyon, UX |
| Mobil Ekip Kullanılabilirlik | Görev sonrası 3 soruluk form | Her görev tamamı | Ortalama ≥ 4/5 | 3.5 altına düşerse Faz 14 backlog | Mobil, Eğitim |
| Vatandaş Portalı Geri Bildirimi | Portal içi geribildirim kartları | Haftalık | Çözüm süresi ≤ 72 saat | Açık kart > 15 | Destek, Güvenlik |
| İç Ekip RFC Retrosu | RFC kapanış toplantısı | Her RFC sonrası | Aksiyon maddesi ≤ 5 iş günü | Geciken aksiyon > 3 | Mimari Kurul |

- **Veri Saklama:** Geri bildirim sonuçları `analytics/voice-of-customer/` dizininde JSON formatında saklanır ve Faz 26 strateji panellerine özetlenir.
- **Kapatma Kriterleri:** Her düşük memnuniyet kaydı için kök neden analizi yapılır; aksiyon tamamlanmadan kayıt kapatılamaz.
- **Şeffaflık:** Çeyreklik raporlar yönetim kurullarında paylaşılır; kritik bulgular Faz 7 kural motoru bildirimleri ile ilgili ekiplere iletilir.

## Sürdürülebilirlik & ESG Göstergeleri

Afet yönetim platformunun çevresel ve sosyal etkileri, sürdürülebilirlik göstergeleri ile takip edilir. Bu çerçeve Faz 18 lojistik ve Faz 26 strateji çalışmalarına veri sağlar.

| Göstergeler | Tanım | Veri Kaynağı | Hedef | Raporlama Kanalı |
| --- | --- | --- | --- | --- |
| Enerji Yoğunluğu | OpsCenter enerji tüketimi / aktif olay sayısı | Prometheus metriği `power_usage_kw` | Yıllık %5 düşüş | Sürdürülebilirlik panosu |
| Kağıtsız İşlem Oranı | QR yoklama + dijital imza sayısı / toplam yoklama | Faz 5 raporları | ≥ %95 | Yıllık ESG raporu |
| Gönüllü Eğitim Saatleri | Toplam gönüllü eğitim saati | Eğitim LMS | Kişi başı ≥ 6 saat | Topluluk raporu |
| Yerel Tedarik Payı | Yerli tedarikçi harcaması / toplam tedarik | Finans sistemleri | ≥ %60 | Finansal uyum raporu |

- **Denetim:** ESG metrikleri yılda bir bağımsız denetime tabi tutulur; sonuçlar `docs/esg/audit-report.pdf` içinde yayınlanır.
- **Uyum:** Sürdürülebilirlik hedefleri Faz 11 CI/CD pipeline’ında kalite kapısı olarak değerlendirilir; hedef altında kalan dağıtımlar için manuel onay zorunludur.
- **İyileştirme Planları:** Eşik altı göstergeler için 30 gün içinde düzeltici faaliyet planı hazırlanır ve Faz 26 yönetim paneline bağlanır.

## Uyumluluk Kanıt Setleri & Denetim Artefaktları

Yasal ve kurumsal denetimler için gerekli kanıtların merkezi yönetimi zorunludur. Bu bölüm Faz 21 mevzuat uyumu ve Faz 11 izlenebilirlik süreçleriyle birlikte yürütülür.

| Denetim Türü | Kanıt Deposu | Sorumlu Rol | Güncelleme Sıklığı | Kritik İçerik |
| --- | --- | --- | --- | --- |
| KVKK/Kişisel Veri | `compliance/kvkk/` | Veri Koruma Görevlisi | Aylık | Erişim logları, saklama süreleri, silme raporları |
| ISO 22301 İş Sürekliliği | `compliance/iso22301/` | İş Sürekliliği Lideri | Çeyreklik | Tatbikat raporları, BIA sonuçları |
| ISO 27001 | `compliance/iso27001/` | CISO | Sürekli (değişiklik olduğunda) | Risk değerlendirmesi, kontrol uygulama kanıtları |
| Finansal Uyum | `compliance/finance/` | Finans Direktörü | Aylık | Sözleşme yenilemeleri, tedarikçi değerlendirme raporları |
| Afet Tatbikatları | `compliance/drill/` | Operasyon Direktörü | Her tatbikat sonrası | Katılımcı listeleri, öğrenilen dersler |

- **Versiyonlama:** Kanıt dosyaları Git LFS ile yönetilir; her güncellemede README satır içi linkleri revizyon tarihini içerir.
- **Erişim Kontrolü:** Tenant bazlı RBAC kuralları uygulanır; dış denetçilere verilen erişim süreli ve imzalıdır.
- **Denetim Hazırlığı:** Denetim öncesi 14 gün kala otomatik kontrol listesi Faz 12 test pipeline’ında tetiklenir.

## Kriz Sonrası Öğrenilen Dersler Programı

Her büyük olay sonrasında öğrenilen derslerin sistematik olarak toplanması ve eyleme dönüştürülmesi gerekir. Program, Faz 13 go-live tatbikatları ile Faz 26 strateji planlaması arasında köprü kurar.

| Aşama | Zamanlama | Faaliyet | Çıktı | Sorumlu |
| --- | --- | --- | --- | --- |
| Hızlı Değerlendirme | Olay kapanışından 72 saat sonra | 30 dakikalık odak grup oturumu | İlk bulgular notu (`postmortem/initial.md`) | Operasyon Lideri |
| Derinlemesine Analiz | 14 gün içinde | Kök neden analizi, veri inceleme | Postmortem raporu (`postmortem/final.md`) | SRE + Güvenlik |
| Aksiyon Takibi | 30 gün içinde | JIRA aksiyon kartları, sahip atama | Güncel aksiyon tablosu | Ürün Yönetimi |
| Bilgi Paylaşımı | 45 gün içinde | Webinar, intranet özeti | Knowledge base kaydı | Eğitim Ekibi |

- **Zorunlu Katılımcılar:** Faz 4 canlı takip, Faz 6 OpsCenter ve Faz 7 kural motoru ekiplerinden temsilciler bulunmalıdır.
- **Başarı Ölçümü:** Aksiyonların %90’ının 60 gün içinde kapanması gerekir; aksi halde yönetim kurulunda gündeme alınır.
- **Şeffaflık:** Postmortem raporları hassas veriler maskelenerek tüm tenant liderleriyle paylaşılır.

## Veri Girişi Standartları & Validasyon Çerçevesi

Kritik süreçlerin hatasız işlemesi için veri giriş noktaları ortak bir standart çerçevesine bağlanır. Bu bölüm Faz 2 veri şeması,
Faz 3 çekirdek modüller ve Faz 5 QR/yoklama süreçleri tarafından uygulanır.

| Bileşen | Standart | Validasyon Katmanı | Reddedilme Kriteri | İlgili Artefakt |
| --- | --- | --- | --- | --- |
| Olay Formu | Zorunlu alanlar: `title`, `status`, `priority`, `polygon` | Front-end (Blade/Vue) + API | Polygon self-intersection, boş başlık | `forms/incident.json` |
| Görev Atama | `assigned_to` yetkili mi? belgesi geçerli mi? | API (Form Request) + DB CHECK | Belge süresi geçmiş kullanıcı | `policies/task-assignment.md` |
| Envanter Hareketi | Transaction idempotent, zimmet süreleri | Service layer | Tekrarlanan zimmet talebi, stok < 0 | `runbook/inventory-transfer.md` |
| QR Yoklama | Nonce ve geofence doğrulaması | Mobil uygulama + API | Nonce yeniden kullanımı, dış bölge | `mobile/qr-validation.yml` |

- **Validasyon Zinciri:** Front-end bileşenleri temel doğrulamayı yapar; API katmanı `FormRequest` sınıfları ile iş kurallarını uygular; veritabanı constraint’leri mükerrer veya tutarsız kayıtları engeller.
- **Versiyonlama:** Form şemaları `schema/forms/` dizininde tutulur; değişiklikler RFC gerektirir ve sürüm numarası artırılır.
- **Geribildirim:** Reddedilen kayıtlar için kullanıcıya hata kodu + öneri sağlanır; hata kayıtları Faz 12 testlerinde regresyon senaryosu olarak eklenir.

## Erişilebilirlik & Kullanılabilirlik Standartları

Afet anında tüm kullanıcıların sisteme erişebilmesi hayati önem taşır. Bu standartlar, Faz 6 OpsCenter, Faz 14 mobil uygulama ve Faz 17 vatandaş portalının erişilebilirlik ve kullanılabilirlik gereksinimlerini belirler.

- **WCAG 2.1 AA Uyumu:** Tüm arayüzler için renk kontrastı, klavye navigasyonu ve ekran okuyucu etiketleri zorunludur. Tasarım dosyaları `ux/accessibility-checklist.md` dokümanına göre gözden geçirilir.
- **Çok Dilli Destek:** Dil değişimi tek tıkla yapılabilmeli; kritik uyarılar (alarm, SOS, hareketsizlik) seçilen dilde ve simge ile desteklenmelidir.
- **Düşük Bant Genişliği Modu:** Harita ve medya bileşenleri için “lite” mod sunulur; Faz 9 offline cache ile entegredir.
- **Kritik İşlem Onayı:** Erişilebilirlik ihtiyaçlı kullanıcılar için sesli doğrulama ve büyük buton seçenekleri mevcuttur; mobil uygulamada titreşim geri bildirimi zorunludur.
- **Kullanılabilirlik Testleri:** Çeyreklik olarak saha personeli, görme/işitme engelli kullanıcılar ve gönüllülerle test yapılır; çıktılar `docs/usability/findings.md` dosyasına işlenir ve Faz 26 panolarında raporlanır.

## Risk Kayıt Defteri & Önceliklendirme Süreci

Tüm teknik ve operasyonel riskler merkezi kayıt defterinde tutulur. Süreç, Faz 11 izlenebilirlik hedefleri ve Faz 26 stratejik planlamasıyla uyumlu yürütülür.

| Risk Tipi | Tanım | Etki | Olasılık | Puanlama | Mitigasyon Sahibi | İzleme Sıklığı |
| --- | --- | --- | --- | --- | --- | --- |
| Güvenlik | OpsCenter WS kanalında kimlik doğrulama açığı | Yüksek | Orta | 15 | Güvenlik Ekibi | Haftalık |
| Operasyon | Offline kuyrukların 12 saatten fazla dolu kalması | Orta | Orta | 9 | Mobil + Edge Takımı | Günlük |
| Veri | Tenant arası veri sızıntısı riski | Çok yüksek | Düşük | 12 | Veri Koruma Görevlisi | Aylık |
| Finans | Kritik tedarikçi feshi | Orta | Düşük | 6 | Finans Direktörü | Çeyreklik |

- **Puanlama Modeli:** Etki (1–5) × Olasılık (1–5) formülü kullanılır; 12 üzeri riskler “Kırmızı” kabul edilir ve yönetim kurulunda zorunlu gündemdir.
- **Yaşam Döngüsü:** Risk oluşturma → değerlendirme → aksiyon planı → takip. Kapalı riskler arşive alınır, ancak 12 ay boyunca geri çağrılabilir.
- **Şeffaflık:** Risk kayıtları `governance/risk-register.csv` dosyasında saklanır; değişiklikler Change Advisory Board toplantılarında özetlenir.

## Olay Sonrası Raporlama & Paydaş Sunumları

Operasyon tamamlandığında, paydaşlara tutarlı ve zamanında bilgi verilmesi gerekir. Bu bölüm, Faz 8 raporlama araçları ile Faz 13 go-live sürecinin çıktılarını birleştirir.

| Rapor | Kapsam | Hazırlayan | Yayın Süresi | Dağıtım Kanalı |
| --- | --- | --- | --- | --- |
| Operasyon Özeti | Olay zaman çizelgesi, görev kapatma oranı, hareketsizlik alarm sonuçları | OpsCenter Lideri | Olay kapanışından 48 saat | Yönetim portalı + e-posta |
| ICS 204 Ekleri | Görev planı ve personel listeleri | Planlama Şefi | 72 saat | ICS arşivi (`reports/ics/`) |
| Paydaş Bülteni | Vatandaş portalı bildirimleri, gönüllü katkıları | Topluluk Ekibi | Haftalık | Newsletter, sosyal medya |
| Finansal Etki Analizi | Kullanılan envanter, lojistik giderler, bağış eşleşmesi | Finans & Lojistik | 7 gün | Finans sistemi + CAB |

- **Onay Zinciri:** Raporlar yayınlanmadan önce ilgili faz sahipleri tarafından imzalanır; kritik raporlar için hukuk ve güvenlik ekiplerinin incelemesi zorunludur.
- **Arşivleme:** Tüm raporlar `reports/archive/<year>/<incident-id>/` dizininde saklanır; erişim tenant bazlı sınırlandırılır.
- **Geri Bildirim:** Paydaşlardan gelen yorumlar Kriz Sonrası Öğrenilen Dersler Programı’na aktarılır ve sonraki tatbikat senaryolarında değerlendirilir.

## Saha Operasyon Performans Puan Kartı

Saha ekiplerinin etkinliğini ölçmek için Faz 4 canlı takip, Faz 6 OpsCenter ve Faz 14 mobil uygulama verileri tek bir puan kartında birleştirilir.

| Metrik | Hedef | Veri Kaynağı | Sorumlu Rol | Gözden Geçirme |
| --- | --- | --- | --- | --- |
| Görev Tamamlama Süresi (P90) | ≤ 6 saat | Faz 3 görev kayıtları, Faz 6 OpsCenter kronolojisi | Operasyon Müdürü | Haftalık |
| Hareketsizlik Alarmı Onay Süresi (P95) | ≤ 2 dakika | Faz 4 ping akışı, Faz 7 alarm logları | OpsCenter Süpervizörü | Günlük |
| Offline Kuyruk Boşaltma Süresi | ≤ 15 dakika | Faz 9 queue metrikleri | Edge Destek Ekibi | Tatbikat sonrası |
| QR Yoklama Başarı Oranı | ≥ %99 | Faz 5 yoklama logları | Saha Koordinatörü | Haftalık |
| Ekip Refah Skoru | ≥ 4/5 | Personel refah anketleri | İnsan Kaynakları & Güvenlik | Aylık |

- **Veri Kalitesi:** Metrikler, Faz 2 veri şeması kontrolleri ve Veri Girişi Standartları ile tutarlı olmalıdır; eksik veri bulunan kayıtlar puan kartına dahil edilmez.
- **Trend Analizi:** Faz 8 analitik araçları, metriklerdeki 3 dönem üst üste kötüleşmeyi otomatik olarak yönetim kuruluna raporlar.
- **İyileştirme Döngüsü:** Her hedef sapması için kök neden analizi yapılır ve sonuçlar Sürekli İyileştirme döngüsüne işlenir; aksiyon kapanış süresi en fazla 30 gündür.

## Dijital İletişim Arşivleme Standartları

Tüm dijital iletişim kanalları denetlenebilir ve mevzuata uygun şekilde arşivlenir. Standartlar Faz 1 güvenlik, Faz 7 kural motoru ve Faz 21 mevzuat uyumu çıktılarıyla entegredir.

| Kanal | Saklama Konumu | Süre | Erişim Kontrolü | Not |
| --- | --- | --- | --- | --- |
| E-posta (SMTP) | `archive/mail/` + tenant klasörü | 5 yıl | Faz 1 IAM, rol tabanlı | İçerik hash’i Faz 22 WORM depolamada tutulur |
| SMS (NETGSM) | `archive/sms/` (şifreli JSON) | 3 yıl | Güvenlik ekibi + hukuk | Hareketsizlik/SOS tetikleri ile ilişkilendirilir |
| Push Bildirim | Firebase export → `archive/push/` | 2 yıl | Ürün ekibi | Model öneri mesajları için açıklama metni eşleştirilir |
| WebSocket Mesajı | Faz 6 OpsCenter log pipeline’ı | 18 ay | DevOps + Güvenlik | Kritik alarm kayıtları KVKK maskelemesi ile saklanır |
| CAB Toplantı Kayıtları | `governance/communications/` | 7 yıl | Yönetim Kurulu Sekreterliği | İmzalı karar notları ile eşleştirilir |

- **Format:** Tüm arşivler ISO 8601 zaman damgalı, tenant ID ve olay ID’si ile etiketlenir.
- **Doğrulama:** Aylık olarak rastgele seçilen kayıtlar hash doğrulamasıyla denetlenir; tutarsızlıklar Uyum Takvimi kapsamında raporlanır.
- **Silme Talepleri:** KVKK veya GDPR kapsamında gelen bireysel talepler hukuk ekibi tarafından değerlendirilir; onaylanan kayıtlar 30 gün içinde maskelenir veya silinir.

## Bilgi Güvenliği Olay Bildirim Hattı

Güvenlik olaylarının hızlı ve koordineli şekilde ele alınması için 7/24 çalışan bir bildirim hattı tanımlanmıştır.

1. **Tespit:** Faz 22 WAF/IDS uyarıları, Faz 11 metrikleri ve kullanıcı raporları otomatik olarak `security-incidents/queue` dizinine düşer.
2. **Önceliklendirme:** Olaylar, Olay Şiddet Seviyeleri tablosundaki kriterlere göre S1–S4 arasında etiketlenir. S1 olayları için hedef ilk yanıt süresi 15 dakikadır.
3. **Bildirim Kanalları:** S1 ve S2 olayları için anında SMS + OpsCenter alarmı; S3-S4 için e-posta + haftalık rapor.
4. **İlk Müdahale:** Güvenlik ekibi olayı triage eder; gerekli hallerde ilgili modül takımı ve hukuk birimi konferans köprüsüne dahil edilir.
5. **Kayıt & İzleme:** Tüm adımlar `security/incidents/<year>/<case-id>.md` dosyalarında tutulur; kök neden analizi ve düzeltici aksiyonlar eklenir.
6. **Kapanış & Geri Besleme:** Olay kapandığında öğrenilen dersler Faz 22 tatbikat takvimine ve Kriz Sonrası Öğrenilen Dersler Programı’na işlenir.

- **Test:** Hattın çalışırlığı aylık tabletop egzersizleriyle doğrulanır; iletişim kanallarında erişim sorunu tespit edilirse 24 saat içinde yedek kanal devreye alınır.
- **Şeffaflık:** Kritik olaylar için 72 saat içinde yönetim kuruluna ve gerekiyorsa regülatörlere raporlama yapılır.

## Teknoloji Borcu Yönetimi

Tarihsel kararlar veya hızlı teslimatlar sonucu oluşan teknoloji borçlarının sistemik olarak yönetilmesi, sürdürülebilirlik için kritiktir.

- **Borç Kataloğu:** `tech-debt/backlog.csv` dosyasında borç türü (kod, altyapı, dokümantasyon), etki alanı, risk puanı ve önerilen çözüm tarihi tutulur.
- **Sınıflandırma:** Borçlar `kritik`, `önemli`, `geliştirme` olarak etiketlenir; kritik borçların kapatılma süresi maksimum 2 sprinttir.
- **Gözden Geçirme Ritmi:** Her sprint planlama toplantısında ilk 30 dakika borç backlog’una ayrılır; Faz 26 stratejik hedefleriyle hizalanır.
- **İzleme Metrikleri:** Borç sayısı, toplam puan ve kapatma süresi Faz 20 dashboard’unda gösterilir; trend artışı Yönetim Kurulu risk gündemine taşınır.
- **Köprü Etkisi:** Borç kapanışı, ilgili faz dokümantasyonunu güncellemeyi zorunlu kılar; aksi halde borç “tamamlanmadı” statüsünde kalır.

## Yenilik & Ar-Ge Portföyü

Sistemin uzun vadeli rekabet avantajını korumak için deneysel çalışmalar yapılandırılmış bir Ar-Ge portföyünde yönetilir.

- **Fikir Kaynağı:** OpsCenter geri bildirimleri, saha tatbikat notları, vatandaş portalı önerileri ve akademik iş birlikleri `innovation/ideas.md` dosyasında toplanır.
- **Değerlendirme Komitesi:** Ürün, güvenlik, saha operasyonları ve strateji ekiplerinden oluşan komite ayda bir toplanarak yeni fikirleri teknik uygulanabilirlik, etkisi ve maliyet açısından puanlar.
- **Deney Fazları:** Seçilen fikirler için `proof-of-concept`, `pilot`, `genişleme` aşamaları tanımlanır; her aşama için başarı kriterleri ve ölçümler belirlenir.
- **Güvenlik & Uyum Kontrolleri:** Pilot öncesi Faz 1, Faz 21 ve Faz 22 gereksinimleri kontrol listesi üzerinden gözden geçirilir; uyumsuzluk varsa pilot ertelenir.
- **Bilgi Paylaşımı:** Başarılı deneylerin sonuçları Knowledge Base’e eklenir ve ilgili faz bölümlerine referans olarak işlenir; başarısız deneyler için alınan dersler Sürekli İyileştirme döngüsüne aktarılır.
- **Kaynak Planlama:** Ar-Ge portföyünün yıllık bütçesi Faz 26 stratejik planlama sırasında onaylanır; kaynak kullanımı çeyreklik raporlarda takip edilir.

## Medya & Kamu İletişimi Yönetimi

Kurumsal söylemin tutarlı ve kriz anlarında güvenilir olmasını sağlamak için medya ve kamu iletişimi süreçleri standartlaştırılmıştır.

- **Amaç:** Kriz iletişimi planı ile uyumlu, onaylı ve ölçümlenebilir kamu mesajları üretmek.
- **Yönetişim:** İletişim Direktörü başkanlığındaki Medya Masası, S1–S3 olaylarında 30 dakika içinde, S4 olaylarında 4 saat içinde toplanarak mesaj taslaklarını oluşturur.
- **Onay Akışı:** Teknik doğrulama OpsCenter liderinden, hukuki uygunluk hukuk biriminden, kamu tonu ise kurum sözcüsünden onay alır; üç onay olmadan mesaj yayınlanmaz.
- **Arşiv:** Tüm duyurular `communications/public/<year>/<incident-id>/statement.md` formatında saklanır; sürümler Git etiketiyle işaretlenir.

| Olay Şiddeti | Birincil Kanal | Yedek Kanal | Temel Mesaj Bileşenleri | Ölçüm |
| --- | --- | --- | --- | --- |
| S1 (Kritik) | TV canlı yayını + resmi sosyal medya | SMS kısa duyuru | Durum özeti, güvenlik talimatı, doğrulama kaynakları | 15 dk’da etkileşim, 30 dk’da çağrı merkezi hacmi |
| S2 (Yüksek) | Kurumsal web duyurusu | Push bildirim | Operasyon etkisi, alınan aksiyonlar, iletişim hattı | 1 saatte sayfa görüntüleme, geri dönüş oranı |
| S3 (Orta) | Haftalık paydaş bülteni | E-posta listesi | İyileşme adımları, destek talepleri, KPI özeti | 24 saatte açılma oranı |
| S4 (Düşük) | Aylık rapor | Knowledge Base | Politika değişiklikleri, tatbikat sonuçları | Aylık geri bildirim formu |

- **Medya Eğitimi:** Sözcüler yılda iki kez kriz simülasyonu eğitimi alır; performans değerlendirmeleri Knowledge Base’de saklanır.
- **Gerçek Zamanlı Takip:** Duyurular sonrası sosyal medya ve çağrı merkezi sinyalleri Faz 20 dashboard’unda izlenir; yanlış bilgi tespiti halinde 60 dakika içinde düzeltme yayınlanır.
- **Paydaş Senkronu:** Belediyeler ve AFAD ile ortak açıklamalar için taslaklar Faz 19 entegrasyon kanalı üzerinden paylaşılır, karşı onay alınmadan yayımlanmaz.

## Veri Egemenliği & Veri Yerleşimi Politikası

Veri yerleşimi gereksinimleri tenant ve veri kategorisi bazında belirlenmiş, regülasyonlara uyumlu tutulmuştur.

- **Politika Kapsamı:** Kişisel veriler, operasyon verileri, sensör telemetrisi ve harici entegrasyon kayıtları.
- **Depolama İlkesi:** Veri, tenant’ın ait olduğu ülke sınırları içerisindeki birincil bölgede tutulur; ikincil yedekleme coğrafi olarak farklı fakat aynı hukuki alan içinde konumlanır.
- **Şifreleme:** Hem birincil hem ikincil depolarda AES-256 at-rest, TLS 1.3 in-transit zorunludur; anahtar yönetimi KMS üzerinden yapılır.
- **Denetim:** Yıllık olarak bağımsız denetçi tarafından veri yerleşimi kontrolü yapılır; raporlar Uyum Takvimi’ne eklenir.

| Veri Kategorisi | Birincil Bölge | İkincil Bölge | Özel Koşullar | Failover Politikası |
| --- | --- | --- | --- | --- |
| Kişisel & Sağlık Verisi | Tenant ülkesindeki KVKK uyumlu DC | Aynı ülke içinde farklı şehir | Maskeleme ve erişim logu zorunlu | 4 saat içinde sıcak standby |
| Operasyonel Olay Verisi | Bölgesel cloud bölgesi | Komşu bölge (aynı yargı) | Spatial indeksler replikasyon sonrası yeniden inşa edilir | 2 saat içinde asenkron replika |
| Harita & PMTiles Paketleri | CDN kenar düğümleri | Edge node cache | İçerik bütünlüğü hash ile doğrulanır | CDN fallback + lokal cache |
| Sensör & IoT Telemetrisi | MQTT cluster (tenant ülkesinde) | WORM storage (aynı yargı) | 30 gün ham veri, 1 yıl özet tutulur | 1 saatlik mesaj kuyruğu toleransı |

- **Veri Taşıma Talepleri:** Tenant değişikliği veya ülke dışına taşıma istekleri RFC sürecine tabidir; risk analizi ve hukuki onay olmadan veri aktarımı yapılamaz.
- **Silme Politikası:** Tenant sona erdiğinde kişisel veriler 30 gün içinde kalıcı olarak silinir; operasyonel veriler anonimleştirilmiş şekilde 2 yıl süreyle saklanabilir.
- **Uyum İzleme:** Faz 21 mevzuat güncellemeleri aylık olarak değerlendirilir; regülasyonda değişiklik olursa 45 gün içinde politika revize edilir.

## Sosyal Etki Ölçüm Çerçevesi

Afet müdahalesinin toplumsal etkisini şeffaf biçimde raporlamak için ortak bir ölçüm çerçevesi uygulanır.

- **Amaç:** Müdahalelerin vatandaş, gönüllü ve kurum ekosistemine katkısını ölçmek.
- **Veri Kaynakları:** Vatandaş portalı etkileşimleri, saha anketleri, lojistik teslim kayıtları ve ICS formları.
- **Yayın Ritmi:** Çeyreklik yönetim kurulu toplantılarında ve yıllık sürdürülebilirlik raporlarında paylaşılır.

| Gösterge | Tanım | Veri Kaynağı | Ölçüm Sıklığı | Hedef |
| --- | --- | --- | --- | --- |
| Vatandaş Talep Çözüm Oranı | 30 gün içinde çözülen ihbar/yardım taleplerinin yüzdesi | Faz 17 portal kayıtları + OpsCenter durumları | Aylık | ≥ %85 |
| Gönüllü Devamlılık Skoru | Tatbikat sonrası aktif kalan gönüllülerin toplam gönüllüye oranı | Gönüllü kayıt sistemi + tatbikat logları | Çeyreklik | ≥ %70 |
| Lojistik Adalet Endeksi | Kritik stok talebi karşılanan bölgelerin toplam talebe oranı (ağırlıklı) | Faz 18 stok/rota verisi | Çeyreklik | ≥ %90 |
| Topluluk Memnuniyet Notu | Saha anketlerinde 1–5 arası ortalama skor | Saha destek ekipleri | Yarıyıllık | ≥ 4 |

- **Analiz:** Hedef dışı kalan göstergeler için Faz 8 analitik araçlarıyla segment bazlı kök neden analizi yapılır; sonuçlar Risk Kayıt Defteri’ne işlenir.
- **Şeffaflık:** Özet sonuçlar Knowledge Base ve paydaş bültenlerinde yayınlanır; veri setleri anonimleştirilmiş olarak açık veri portalında paylaşılmadan önce hukuk onayı alınır.
- **Geri Bildirim Döngüsü:** Paydaş yorumları Sürekli İyileştirme & Geri Bildirim Döngüsü bölümüne aktarılır; alınan aksiyonların kapanış süresi 60 günü geçemez.

## Saha Güvenlik Sertifikasyon Programı

Saha personelinin güvenlik, sağlık ve operasyonel standartlara uygun hareket etmesini sağlamak için üç seviyeli sertifikasyon programı uygulanır.

- **Program Sahibi:** İnsan Kaynakları & Güvenlik birimleri ortak sorumludur; içerik Faz 14 mobil uygulama, Faz 4 canlı takip ve Faz 5 yoklama gereksinimleriyle hizalanır.
- **Sınav Döngüsü:** Yeni personel onboarding sürecinde temel sertifika, saha görevi öncesinde ileri sertifika, kritik görev öncesinde uzman sertifika zorunludur.
- **Geçerlilik:** Sertifikalar 12 ay geçerlidir; yenileme eğitimi tamamlanmadan görev ataması yapılamaz.

| Seviye | Gereksinimler | Ölçüm | Yenileme Kriteri |
| --- | --- | --- | --- |
| Temel | Güvenlik brifingi, uygulama kullanımı, QR yoklama pratikleri | Çevrimiçi sınav ≥ %80 başarı | 12 ay, fakat 3 olay sonrası tazeleme önerilir |
| İleri | Hareketsizlik alarm senaryosu, offline kuyruk yönetimi, ekip refahı protokolleri | Saha tatbikatı değerlendirmesi ≥ 4/5 | 12 ay + 1 tatbikat katılımı |
| Uzman | Komuta zinciri, kritik envanter zimmeti, kriz iletişimi | Panel mülakatı + vaka çalışması | 12 ay + 2 S1 olay performans raporu |

- **Kayıt:** Sertifika sonuçları `compliance/training/<year>/<person-id>.json` dosyalarında saklanır; Faz 3 kullanıcı belgeleri tablosuyla ilişkilendirilir.
- **Denetim:** Sertifika geçerliliği, görev ataması yapılmadan önce otomatik kontrol edilir; geçersizse görev ataması engellenir ve OpsCenter’da uyarı çıkar.
- **Sürekli İyileştirme:** Tatbikat geri bildirimleri program müfredatını güncellemek için üç ayda bir gözden geçirilir; değişiklikler Dokümantasyon Bakım Ritmi kapsamında duyurulur.

## Kritik Tedarik Zinciri Sürekliliği Planı
_(Güncelleme: 2024-07-01)_

**Amaç:** Afet ve tedarik kesintilerinde kritik yazılım, donanım ve saha ekipmanının sürekliliğini sağlamak.

**Kapsam:**
- Faz 1, 6, 18 ve 22 kapsamındaki tüm üretim, yedekleme ve lojistik tedarikçileri.
- OpsCenter, canlı takip cihazları, offline kuyruk sunucuları ve saha kitleri.
- Bulut, veri merkezi ve saha depoları arasında çalışan taşıma/servis sözleşmeleri.

**Operasyonel Adımlar:**
1. **Kritik tedarik envanteri** üç ayda bir güncellenir; risk derecesi (A/B/C) ve mevcut stok gün sayısı listelenir.
2. Her tedarikçi için **ikincil kaynak** veya stok tamponu belirlenir; yoksa risk kaydına engelleyici aksiyon olarak işlenir.
3. **Kesinti senaryoları** (ulaşım engeli, döviz şoku, doğal afet) yılda iki kez tabletop tatbikatıyla test edilir ve sonuçları Faz 18 raporuna işlenir.
4. OpsCenter, canlı takip ve yedekleme servisleri için **maksimum kabul edilebilir kesinti süresi (MAO)** ve stok devir hızları SLA tablosuna bağlanır.

**Test & Doğrulama:**
- Tedarikçi performans raporları aylık olarak gözden geçirilir; teslimat sapması %5’i geçerse eskalasyon tetiklenir.
- Tatbikat sonrası, kritik bileşen için 72 saat içinde alternatif kaynak sağlanabildiği senaryo kanıtı istenir.

**Hata Önleme:**
- Tekil tedarikçi bağımlılığına izin verilmez; geçiş planı onaysızsa risk kaydı RED olarak kalır.
- Depo stok verisi ile sistem kayıtları eşleşmezse (±%2 tolerans) otomatik envanter sayımı tetiklenir.

## Çok Faktörlü Kimlik Doğrulama Politikası
_(Güncelleme: 2024-07-01)_

**Amaç:** Tüm tenant ve sistem servislerinde güçlü kimlik doğrulama zorunluluğu getirerek yetkisiz erişim riskini azaltmak.

**Politika İlkeleri:**
- **Zorunlu 2FA:** Faz 1 Fortify modülünden sağlanan SMS/push tabanlı ikinci faktör tüm roller için mecburidir; yalnızca sistem servis hesapları donanım anahtarı (FIDO2) kullanır.
- **Risk bazlı MFA:** OpsCenter ve yönetim panellerine şehir dışından veya yeni cihazdan erişim otomatik olarak ek doğrulama (e-posta onayı + güvenlik sorusu) ister.
- **İlkyükleme & Kurtarma:** Kullanıcılar onboarding sırasında iki yedek doğrulama yöntemi tanımlar; kurtarma kodları KVKK gereği şifreli PDF olarak teslim edilir ve ilk girişte yenilenir.
- **Servis Hesapları:** API anahtarları Vault’ta tutulur; her erişim anahtarı tenant bazlı kapsam ve sona erme tarihi içerir.

**Denetim Noktaları:**
- Haftalık rapor, başarısız MFA denemelerini ve bloke edilen oturumları Faz 7 alarm konsoluyla paylaşır.
- 2FA devre dışı bırakma talepleri yalnızca `Güvenlik Operasyon Yöneticisi` + `Tenant Lideri` onayıyla 24 saatliğine geçici olarak tanımlanır.

**Hata Önleme:**
- Yedek doğrulama metodu olmayan kullanıcılar görev ataması alamaz; sistem otomatik bildirim gönderir.
- 5 ardışık başarısız MFA denemesinde hesap 30 dakika kilitlenir ve audit log’a düşer.

## Harici Denetim & Sertifikasyon Yol Haritası
_(Güncelleme: 2024-07-01)_

**Amaç:** TUDAK platformunun ISO/IEC 27001, ISO 22301 ve KVKK uyum gereksinimlerini planlı biçimde karşılamasını sağlamak.

**Yol Haritası Aşamaları:**
1. **Hazırlık (Q1):** Politika boşluk analizi, risk değerlendirmesi (Faz 21) ve doküman eşlemesi.
2. **Uygulama (Q2):** Açık kontroller için düzeltici aksiyonlar; log yönetimi ve iş sürekliliği testlerinin güncellenmesi.
3. **İç Denetim (Q3):** Bağımsız iç denetim ekibiyle örneklem testleri; bulgular için 30 günlük kapanış süresi.
4. **Sertifikasyon (Q4):** Akredite kuruluşla dış denetim, düzeltici aksiyonların doğrulanması ve sertifika yayınlanması.

**Sorumluluk Matrisi:**
- **Bilgi Güvenliği Sorumlusu:** Program lideri, politika güncellemelerini izler.
- **Operasyon Liderleri:** Faz bazlı kontrol kanıtlarını sağlar.
- **İç Denetim Ekibi:** Düzeltici aksiyon takibini raporlar.

**Kilometre Taşları & Ölçütler:**
- Kritik bulgu sayısı ≤ 3, orta seviye bulgu kapanış süresi ≤ 30 gün, minör bulgular ≤ 60 gün.
- Sertifikasyon yenilemesi için gözetim denetimleri yıllık; kapsam genişletme talepleri için değişiklik yönetimi süreci tetiklenir.

**Hata Önleme:**
- Denetim sırasında eksik kanıt çıkarsa olay günlükleri ve politika sürümleri `Uyumluluk Kanıt Setleri` deposundan otomatik çekilir.
- Yenileme denetimi 60 gün kala tetik hatırlatıcısı gönderilir; tarih kaçırılırsa yönetim kurulu gündemine kritik risk olarak taşınır.

## Veri Maskeleme & Anonimleştirme Standartları
_(Güncelleme: 2024-07-01)_

**Amaç:** Tenant verilerinin test, raporlama ve analiz ortamlarında gizliliğini korumak için tutarlı maskeleme ve anonimleştirme uygulamak.

**Standart Bileşenler:**
- **Maskeleme Kataloğu:** Kimlik, iletişim, sağlık ve lokasyon alanları için maskeleme türü (tokenizasyon, şifreleme, kısmi mask) listelenir.
- **Çevrimdışı Veri Setleri:** Faz 8 ve 24 için üretilen veri gölü kopyaları kimliksizleştirilmiş sürümden türetilir; ham veriye erişim yalnızca `Veri Koruma Subayı` onaylıdır.
- **Test Ortamı Kuralları:** QA/Stage veritabanlarına gerçek PII aktarımı yasaktır; zorunlu ise tek seferlik erişim bileti ve otomatik temizleme script’i uygulanır.
- **Anonimlik Doğrulaması:** K-anonimlik ≥ 10, diferansiyel gizlilik epsilon ≤ 1 parametreleri Faz 21 kontrol listesine eklenir.

**İş Akışı:**
1. Veri talebi `Veri Kataloğu` üzerinden açılır; amaç, kapsam ve saklama süresi belirtilir.
2. Maskeleme script’leri CI pipeline’ında otomatik doğrulanır; başarısızlıkta dağıtım durdurulur.
3. Kullanılan veri seti 90 gün sonunda otomatik olarak temizlenir veya anonim hâlde arşivlenir.

**Hata Önleme:**
- Maskeleme başarısız olursa veri seti devreye alınmaz; olay KVKK ihlal prosedürüne yönlendirilir.
- Maskeleme şablonu değişiklikleri çift onay ister ve sürüm kontrolünde izlenir.


## Mikroservis & API Rate Limit Politikası

**Amaç:** OpsCenter, mobil istemciler ve üçüncü taraf entegrasyonlarının dengeli kaynak tüketimiyle yüksek erişilebilirliği korumak.

**Kapsam:** REST API, GraphQL uçları (planlama aşaması), WebSocket yayınları, GeoJSON BBOX servisleri ve kural motoru webhook tetikleri.

**Politika İlkeleri:**
- Varsayılan limitler tenant başına dakikada **600 REST çağrısı**, saniyede **10 BBOX isteği** ve **60 eşzamanlı WebSocket bağlantısı**dır.
- BBOX parametresi içermeyen harita çağrıları otomatik olarak `429` döner ve olay loguna `bbox_missing` etiketiyle kaydedilir (Faz 6).
- Rate limit eşikleri tenant SLA sınıfına göre `limit-profile.yml` dosyasında tanımlanır; değişiklikler RFC onayı olmadan birleşmez.
- Kural motoru (Faz 7) tetiklerinde rate limit ihlali yaşanırsa aksiyon kuyruğu durmaz; tetik `retry-after` değeriyle sıraya alınır.

**İzleme & Raporlama:**
- Prometheus üzerinde `api_rate_limit_breach_total`, `ws_connection_peak` ve `bbox_request_per_sec` metrikleri izlenir.
- Günlük raporlar OpsCenter dashboard’unda görselleştirilir; üç ardışık ihlalde kapasite planlama toplantısı açılır.

**Hata Önleme:**
- Rate limit konfigürasyonu Infrastructure-as-Code reposunda (`infra/rate-limit.tf`) tutulur; manuel değişiklik tespit edilirse pipeline dağıtımı durdurur.
- Mobil uygulama (Faz 14) için SDK seviyesinde exponential backoff zorunludur; uyumsuz sürümler yayın listesine alınmaz.


## Açık Veri & Paydaş İşbirliği Çerçevesi

**Amaç:** Yetkili kamu kurumları, STK’lar ve akademik ortaklarla veri paylaşımını kontrollü, geri izlenebilir ve güvenli şekilde yürütmek.

**Kapsam:** Anonimleştirilmiş olay metrikleri, lojistik stok durumları, risk analizi çıktı setleri, ICS formlarının özetleri.

**İşleyiş:**
1. Paylaşım talepleri `open-data/requests/<id>.md` dosyasında kayıt altına alınır; Faz 21 uyum sorumlusu ve Faz 26 strateji lideri tarafından onaylanır.
2. Paydaşlara verilen API anahtarları tenant bazlı `read-only` rollerle sınırlandırılır, 90 günde bir otomatik döndürülür.
3. Açık veri paketleri aylık olarak sürümlenir (`open-data/releases/vYYYY.MM/`); değişiklik günlüğü ve maskeleme kontrol listesi eklenir.
4. Paydaş portallarından gelen geri bildirimler Sürekli İyileştirme döngüsüne aktarılır; kapatma süresi maksimum 45 gündür.

**Denetim:** Altı ayda bir paydaş erişim logları incelenir; gereksiz erişimler kapatılır, rapor Denetim & Uyum Takvimi’ne eklenir.

**Hata Önleme:** Veri ihlali veya yetkisiz paylaşım şüphesinde Faz 19 entegrasyon köprüsü askıya alınır, Faz 15 veri ihlali planı tetiklenir.


## Çevik Seremoni & Sprint Yönetimi

**Amaç:** Faz bazlı teslimatların öngörülebilirliğini artırmak, disiplinler arası koordinasyonu ve risk yönetimini güçlendirmek.

**Seremoniler:**
- **Sprint Planlama (2 hafta):** Faz hedefleri ve bağımlılıklar gözden geçirilir, `sprint-goals.md` güncellenir.
- **Günlük Senkronizasyon:** Operasyonel blokajlar, hareketsizlik alarmları ve sahadan gelen kritik bildirimler paylaşılır.
- **Sprint Review:** OpsCenter demo, Faz KPI sapmaları ve paydaş geri bildirimleri değerlendirilir.
- **Retrospektif:** Süreç iyileştirme aksiyonları belirlenir, güvenlik borcu ve teknik borç backlog’ları önceliklendirilir.

**Artefaktlar & Takip:**
- `governance/iteration-kanban.md` güncel tutulur, risk kayıtları ile çapraz referanslanır.
- Burndown grafikleri Faz Geçiş Kriterleri ile uyumlu tamamlanma oranını gösterir.

**Hata Önleme:** Sprint tamamlanma kabulü, Faz Geçiş Kontrol Formu maddeleri karşılanmadan yapılmaz; aksi hâlde release dondurulur.


## Mühendislik Uygulama Yönetişimi

**Amaç:** Faz 0 teknoloji kararları, Faz 11 DevOps süreçleri ve Faz 12 test kapsamının günlük mühendislik operasyonlarına tutarlı şekilde uygulanmasını sağlamak.

**Kapsam:**
- `docs/engineering/README.md` klasör haritası, sorumluluklar ve kullanım notlarını içerir.
- Kod inceleme, dal stratejisi ve pipeline kapıları gibi rehberler sprint planları ve release hazırlıkları sırasında zorunlu referans olarak kullanılır.
- Takımlar, yeni araç veya süreç değişikliklerinde RFC süreci ve `CHANGELOG.md` kaydıyla izlenebilirlik sağlar.

**Hata Önleme:** Rehberlerdeki güncel olmayan noktalar için aylık engineering check-up oturumları yapılır; tespit edilen boşluklar `docs/rfc/` üzerinden güncellenir.


## Kodlama Standartları

**Amaç:** PHP, Vue/Blade ve altyapı kodlarında tutarlı stil, güvenlik ve tenant izolasyonu kurallarını koruyarak teknik borcu azaltmak.

**Kapsam (`docs/engineering/coding-standards.md`):**
- PSR-12 + Laravel yönergeleri, domain bazlı dizinleme ve form request tabanlı validasyon kuralları.
- Vue/Tailwind bileşen yapısı, erişilebilirlik (WCAG AA) gereksinimleri ve harita entegrasyon helper’ları.
- Kod yorumları, PHPDoc etiketi ve audit/metrik entegrasyonu için zorunlu yönergeler.

**Kontrol Listesi:** Pre-commit aşamasında `php-cs-fixer` ve `eslint` çalıştırıldığını doğrulayın; kod incelemesinde tenant filtrasyonu ve localization çağrıları kontrol edilir.

**Hata Önleme:** Standart dışı istisnalar RFC onayı almadan kabul edilmez; sprint retrospektiflerinde tespit edilen sapmalar `docs/engineering/code-review.md` kontrol listesine eklenir.


## Statik Analiz & Otomatik Kontroller

**Amaç:** Kod tabanında erken hata tespiti ve güvenlik zafiyetlerinin otomatik araçlarla yakalanmasını sağlamak.

**Araçlar (`docs/engineering/static-analysis.md`):** PHPStan, ESLint, Stylelint, Trivy ve Composer Audit.

**Pipeline Uygulaması:**
- Pre-commit hook’ları `composer analyse` ve `npm run lint` komutlarını zorunlu kılar.
- CI pipeline’ı `analyse`, `frontend-lint` ve `security-audit` aşamalarını çalıştırır; kritik bulgular release’i bloklar.
- Haftalık konteyner taramaları çıktıları `security/vuln-register.csv` dosyasına, aylık Psalm taramaları ise `security/threat-hunt/` kayıtlarına işlenir.

**İzleme:** Statik analiz bulgu trendi `observability/metrics-catalog.md` içinde takip edilir; false-positive istisnaları aylık engineering oturumlarında değerlendirilir.


## Kod Kalite Konfigürasyonları

**Amaç:** Kod kalite araçlarının depo seviyesinde tutarlı şekilde çalışmasını sağlamak, dokümantasyon ile gerçek yapılandırmalar arasındaki boşluğu kapatmak.

**Konfigürasyonlar (`docs/engineering/tooling-configuration.md`):**
- `.php-cs-fixer.dist.php`: Mevcut dizinleri dinamik olarak bulur, PSR-12 kuralları ve `declare(strict_types=1)` zorunluluğunu uygular.
- `phpcs.xml`: `config/` dizinini tarayan kod standartlarını ve Slevomat tip ipuçlarını denetler.
- `phpstan.neon.dist`: Larastan gerektirmeden seviye 5 statik analiz çalıştırır; sonuçları `build/phpstan/` altında saklar.
- `psalm.xml`: `config/` kapsamındaki dosyalarda hassas veri akışını takip eder.
- `.eslintrc.cjs`: Vue 3 + TypeScript bileşenleri için ESLint yapılandırmasını standartlaştırır.
- `stylelint.config.cjs`: Tailwind ağırlıklı CSS için sınıf isimlendirme ve erişilebilirlik kurallarını denetler.

**Uygulama Notları:**
- `composer.json` ve `package.json` script’leri yukarıdaki dosyalarla eşleşen komutları içermeli, pre-commit ve CI pipeline’ları aynı kuralları tetiklemelidir.
- Yeni modül/dizin eklenirken lint kapsamı gözden geçirilerek gerekli durumlarda ilgili konfigürasyon güncellenir.
- Kural istisnaları RFC onayı olmadan yapılandırma dosyalarına eklenemez; gerekçeler kod inceleme notlarında kayıt altına alınır.


## PR Öncesi Kontrol Listesi

**Amaç:** PR açılışı sırasında Codex tabanlı otomasyonların "ikili dosya desteklenmez" gibi hatalara takılmasını önlemek ve kalite kapılarının manuel olarak doğrulanmasını sağlamak.

**Kapsam (`docs/engineering/pr-checklist.md`):**
- `tools/check-binary-files.sh` script’i ile staged dosyalarda ikili içerik taraması.
- Kod standartları, statik analiz ve test komutlarının hızlı referansı.
- Dokümantasyon/CHANGELOG güncellemeleri ve `docs/governance/devam-et-yapi-rehberi.md` kaydı için hatırlatıcı adımlar.

**Uygulama Notları:** Check-list tamamlanmadan PR açılmaz; tespit edilen ikili dosyalar metin tabanlı formata dönüştürülür veya `.gitattributes` kaydı eklenir. Kontrol listesi çıktıları inceleme sürecine ek bilgi olarak paylaşılır.

## Toplu Kalite Kontrol Suite

**Amaç:** PR hazırlığında tekrarlanan kalite kontrollerini tek komutta koşturmak ve eksik araç kurulumlarını hızlıca tespit etmek.

**Kapsam (`docs/engineering/quality-suite.md` & `tools/run-quality-suite.sh`):**
- `tools/check-binary-files.sh`, PHP-CS-Fixer, PHP_CodeSniffer, PHPStan, Psalm ve `npm run lint` kontrollerini ardışık çalıştırır.
- Mevcut olmayan araçları ⚠️ uyarısıyla raporlar, hatalı adımlar için `exit 1` döndürür.
- `docs/engineering/pr-checklist.md` içindeki manuel adımları otomasyonla destekler.
- `vendor/bin/` altında PHP araçları bulunmazsa `composer install --no-ansi --no-interaction --no-progress --prefer-dist` komutunu otomatik tetikler.

**Uygulama Notları:** Script depo kökünde çalıştırılmalı, başarısız kontroller düzeltilip tekrar koşturulmalıdır. Çıktı özetleri kod inceleme yorumlarında paylaşılır ve gerektiğinde `docs/tests/` kayıtlarına referans verilir.


## Yerel Geliştirme Rehberi

**Amaç:** Yeni ekip üyelerinin ve mevcut geliştiricilerin yerel ortamlarını hızlı, güvenli ve faz hedefleriyle uyumlu şekilde kurmasını sağlamak.

**Kapsam (`docs/engineering/local-development.md`):**
- PHP 8.3, MySQL 8 ve Node.js 20 gereksinimlerini içeren ön koşul listesi.
- `.env.local` hazırlığı, migrasyon, queue ve WebSocket servislerinin başlatılması gibi adım adım kurulum talimatları.
- Günlük geliştirme komutları tablosu, PR öncesi checklist ve sık karşılaşılan sorunlar için çözüm önerileri.

**Hata Önleme:** `./tools/run-quality-suite.sh` script’i eksik vendor araçlarını otomatik kurar; yine de sorun yaşanırsa rehberdeki troubleshooting bölümüne göre işlem yapın ve sonuçları `docs/governance/devam-et-yapi-rehberi.md` kaydına not edin. Yeni bağımlılık eklerken `docs/engineering/dependency-management.md` politikalarını ve PR checklist’ini tamamlamayı unutmayın.


## Bağımlılık Yönetimi Politikası

**Amaç:** Composer, npm ve sistem paketlerinin güvenli, izlenebilir ve zamanında güncellenmesini sağlayarak operasyonel riskleri azaltmak.

**Politika Başlıkları (`docs/engineering/dependency-management.md`):**
- Envanterleme: `composer.lock`, `package-lock.json` ve konteyner OS paket listelerinin periyodik kontrolü.
- Güncelleme döngüsü: Patch ≤48 saat, minor aylık bakım penceresi, major sürümler için RFC + rollback planı.
- Güvenlik kontrolleri: `composer audit`, `npm audit --production`, hash doğrulaması ve lisans uyumu.

**Kayıt Gereksinimleri:** Upgrade kararları `docs/changelog/` altında belgelenir; riskli durumlar `governance/risk-register.csv` ve `tech-debt/backlog.csv` dosyalarına işlenir.

**Hata Önleme:** Patch süreleri aşıldığında otomatik eskalasyon uygulanır; kritik zafiyetlerde 24 saat içinde hotfix planı hazırlanarak ilgili runbook’lar güncellenir.


## Kod İnceleme Standartları

**Amaç:** Güvenlik kritik modüller de dahil olmak üzere tüm kod katkılarının belirlenen kalite ve kontrol listelerine uygun şekilde gözden geçirilmesini sağlamak.

**Öne Çıkan Maddeler (`docs/engineering/code-review.md`):**
- İnceleme ön koşulları: test sonuçları, dokümantasyon revizyonu ve güvenlik etki değerlendirmesi.
- Kontrol listesi: iş gereksinimi, güvenlik, kod kalitesi, test kapsamı ve performans/gözlemlenebilirlik.
- Onay kriterleri: en az iki rol onayı, açık `blocking` bulgu bırakmama ve runbook/CHANGELOG güncellemelerinin doğrulanması.

**İzleme:** İnceleme metrikleri `observability/metrics-catalog.md`de kayıt altına alınır; sapmalar retrospektiflerde değerlendirilir.


## Branching & Release Gate Politikaları

**Amaç:** Paralel faz geliştirmelerini güvenli yönetmek, release hazırlıklarını şeffaflaştırmak ve rollback süreçlerini hızlandırmak.

**Öne Çıkan Maddeler (`docs/engineering/branching-model.md`):**
- `main`, `develop`, `feature/`, `release/` ve `hotfix/` dal tiplerinin kullanım kuralları.
- Sprint başında `develop` ↔ `main` senkronizasyonu ve release dalı açıldıktan sonra kapsam dondurma.
- Rollback planı: son stabil etiket üzerinden dönüş, ardından `hotfix/` dalı ile kalıcı düzeltme.

**Denetim:** Dal koruma kuralları zorunludur; dal temizliği aylık yapılır ve sonuçlar `sprint-goals.md` ile ilişkilendirilir.


## CI/CD Kalite Kapıları & İzleme

**Amaç:** Faz 11–12 kapsamında tanımlanan pipeline kalite kontrollerini standartlaştırarak üretim ortamının güvenilirliğini artırmak.

**Öne Çıkan Maddeler (`docs/engineering/deployment-gates.md`):**
- Pre-commit’ten üretim yayınına kadar beş aşamalı kalite kapısı (statik analiz, test, performans, güvenlik, QA, release hazırlığı).
- Başarısızlık durumları için aksiyon tablosu ve ilgili kayıt sistemlerine (risk kaydı, postmortem, runbook) yönlendirme.
- Monitoring entegrasyonu: release sonuçlarının `observability/metrics-catalog.md` ve `ops/weekly-ops-briefing.md` ile takibi.

**Süreklilik:** Kalite kapıları yılda iki kez gözden geçirilir; yeni araçların entegrasyonunda bu bölüm ve ilgili belgeler güncellenir.


## Bilgi Güvenliği Metrikleri & Raporlama

**Amaç:** Güvenlik duruşunu ölçülebilir göstergelerle yönetmek ve yönetim kurullarına düzenli rapor sunmak.

**Temel Metrikler:**
- **MTTR (Security):** Kritik zafiyet kapanma süresi ≤ 72 saat.
- **2FA Uyum Oranı:** Aktif kullanıcıların ≥ %98’inde 2FA etkin olmalı.
- **Audit Log İnceleme Sıklığı:** Haftalık örneklem en az 100 işlem.
- **Pentest Bulgu Eğilimi:** Yüksek seviye bulgu sayısında çeyreklik azalma.

**Raporlama Döngüsü:**
- Aylık güvenlik brifingi, `reports/security-dashboard.md` olarak arşivlenir.
- Çeyreklik olarak yönetim kuruluna trend analizi sunulur; sapmalar için düzeltici aksiyonlar atanır.

**Hata Önleme:** Metrik sapmalarında Faz 22 güvenlik programı kapsamında kök neden analizi zorunludur; aksiyon kapanışı 30 günü aşamaz.


## Yönetişim Risk & Uyum Raporlama Döngüsü

**Amaç:** Faz ilerleyişi, mevzuat gereksinimleri ve operasyonel riskleri tek ritimde takip etmek.

**Döngü:**
- **Aylık:** Risk komitesi toplantısı; yeni kayıtlar, mitigasyon durumu ve bütçe etkileri değerlendirilir.
- **Çeyreklik:** Uyum raporu yayınlanır; Faz 21 gereksinimleri ve denetim bulguları karşılaştırılır.
- **Yıllık:** Stratejik yönetim paneli (Faz 26) için bütünleşik risk raporu hazırlanır.

**Artefaktlar:** `governance/risk-register.csv`, `compliance/quarterly-report.md`, `audit/findings-tracker.csv`.

**Hata Önleme:** Yüksek riskler için 2 hafta içinde aksiyon planı yayımlanmazsa otomatik yönetim eskalasyonu tetiklenir.


## Sürdürülebilir Tedarik İlkeleri

**Amaç:** Kritik tedarik zinciri sürekliliği planını çevresel ve etik kriterlerle uyumlu hale getirmek.

**İlkeler:**
- Çift kaynak zorunluluğu; tek tedarikçili kritik parçalar için minimum 30 günlük stok tamponu.
- Tedarikçi seçiminde karbon ayak izi raporu ve insan hakları beyanı talep edilir.
- Saha lojistik operasyonlarında geri dönüştürülebilir ambalaj ve atık yönetimi planı aranır.

**İzleme:** ESG komitesi (Faz 26) yılda iki kez tedarikçi sürdürülebilirlik değerlendirmesi yapar; sonuçlar Sürdürülebilirlik & ESG Göstergeleri bölümüne bağlanır.

**Hata Önleme:** Uyum sağlamayan tedarikçiler için çıkış planı hazırlanır; kritik stok alarmları Faz 18 lojistik modülündeki alarm konsoluna entegre edilir.


## Cihaz Yönetimi & Mobil Güvenlik Politikası

**Amaç:** Mobil ve saha cihazlarının güvenli, güncel ve kurumsal politika uyumlu şekilde yönetilmesini sağlamak.

**Kapsam:** Flutter mobil uygulaması (Faz 14), saha tabletleri, dizüstüler, OpsCenter ekranları ve vatandaş portalına erişen kiosk cihazları.

**Politika Maddeleri:**
- **MDM Zorunluluğu:** Tüm kurum cihazları kurumsal MDM üzerine kayıtlı olmalı; jailbreak/root tespitinde cihaz erişimi otomatik askıya alınır.
- **Sürüm Takibi:** Mobil uygulama minimum desteklenen sürümü üç ayda bir güncellenir; destek dışı sürüm tespitinde kullanıcıya blok ekranı sunulur.
- **Ağ Politikası:** Saha cihazları sadece kurum VPN’i veya onaylı APN üzerinden bağlanabilir; halka açık ağda kritik modüller kilitlenir.
- **Şifreleme:** Cihaz disk şifrelemesi, uzaktan silme ve ekran kilidi politikaları zorunludur.
- **Sensör Kullanımı:** Kamera, mikrofon ve konum izinleri görev bazlı politika profilleriyle eşleştirilir; görev bitiminde izinler sıfırlanır.

**İzleme & Raporlama:** Aylık MDM raporu, cihaz uyum puanlarını ve politika ihlallerini Faz 22 güvenlik kurulu ile paylaşır.

**Hata Önleme:** Uyumsuz cihazlar karantinaya alınır, OpsCenter erişimi kesilir; üçten fazla ihlal tespit edilen cihazlar yeniden görüntülenir ve kullanıcı yeniden eğitim alır.

## Eğitim İçerik Yönetimi & Sertifikasyon İzleme

**Amaç:** Faz 13 go-live öncesi ve sonrası tüm ekiplerin güncel prosedürlere hakim olmasını ve zorunlu sertifikasyonların takip edilmesini sağlamak.

**İçerik Döngüsü:**
1. **Analiz:** Saha geri bildirimleri ve Faz 16 tatbikat raporları, eksik bilgi alanlarını tanımlar.
2. **Üretim:** Eğitim içeriği `docs/training/` dizininde SCORM uyumlu formatta hazırlanır; iki farklı uzman tarafından gözden geçirilir.
3. **Yayın:** İçerik sürümleri semantik versiyonlanır (`training-vX.Y`) ve yayın planı aylık olarak duyurulur.
4. **Ölçüm:** Eğitim sonrası quiz ve saha performans puan kartı sonuçları (Faz 18) eşleştirilir.

**Sertifikasyon İzleme Tablosu:**

| Rol | Zorunlu Sertifikalar | Yenileme Sıklığı | Takip Mekanizması |
| --- | --- | --- | --- |
| OpsCenter Operatörü | ICS-200, Veri Koruma Farkındalık | 12 ay | LMS raporu + Faz 7 alarmı |
| Saha Lideri | İlk Yardım, Arama Kurtarma | 24 ay | İnsan Kaynakları entegrasyonu |
| Geliştirici | Güvenli Kodlama, OWASP Top 10 | 12 ay | CI eğitim doğrulaması |

**Hata Önleme:** Sertifikasyon süresi geçen roller görev ataması alamaz; LMS ile OpsCenter arasında nightly senkron çalışır ve hatalı kayıtlar `audit_training` tablosuna düşer.

## Test Veri Yönetimi & Gizlilik Koruması

**Amaç:** Test ve tatbikat ortamlarında kullanılan verilerin kişisel verileri içermemesini ve üretim verisine eşdeğer kalite sunmasını sağlamak.

**İlkeler:**
- **Sentetik Veri Üretimi:** Faz 12 testleri için üretilen veri, üretim şemasını taklit eder; gerçek kişisel veriler kullanılmaz.
- **Maskelenmiş Kopyalar:** Zorunlu olduğu durumlarda veri maskeleme (Faz 20) pipeline’ı ile kimliksizleştirilmiş kopya kullanılır.
- **Veri Etiketleme:** Tüm test veri setleri `metadata.yml` dosyasıyla kaynak, sürüm ve maskeleme metodunu belirtir.
- **Erişim Kontrolü:** Test veri depoları rol tabanlı erişim ile korunur; erişim logları aylık gözden geçirilir.

**Validasyon:** Her yayın öncesi test veri örneklemeleri, veri kalitesi kurallarına karşı doğrulanır; başarısız kurallar CI pipeline’ını kırar.

**Hata Önleme:** Üretim verisi sızdığı tespit edilirse veri ihlali prosedürü (Faz 15) tetiklenir ve ilgili test ortamı karantinaya alınır.

## Saha Geri Bildirim Entegrasyon Döngüsü

**Amaç:** Saha ekiplerinin deneyim ve önerilerini sistematik olarak toplamak, değerlendirmek ve Faz yol haritasına entegre etmek.

**Döngü Adımları:**
1. **Toplama:** Mobil uygulama (Faz 14) üzerinden görev bitim formu, OpsCenter üzerinden anlık geri bildirim butonu, aylık saha odak grupları.
2. **Sınıflandırma:** Geri bildirimler kategori (kullanılabilirlik, performans, süreç, güvenlik) ve şiddet etiketiyle etiketlenir.
3. **Değerlendirme:** Faz kurulu her sprint planlamasında ilk üç yüksek şiddetli geri bildirimi ele alır, RFC ihtiyacı varsa açar.
4. **İzleme:** Uygulanan aksiyonlar `feedback-tracker.md` dosyasında kapatılma tarihi ve sorumlusu ile belgelenir.

**Metrikler:** Ortalama yanıt süresi, kapatılan geri bildirim yüzdesi, tekrar eden şikayet oranı; KPI sonuçları yönetim kuruluna raporlanır.

**Hata Önleme:** Kritik geri bildirim 48 saat içinde ele alınmazsa otomatik eskalasyon Faz 10 medya iletişim sorumlularına bildirilir; yinelenen geri bildirimler kök neden analizi yapılmadan kapatılamaz.

## Siber Sigorta & Risk Transfer Stratejisi

**Amaç:** Kurumun afet yönetimi operasyonlarında finansal riskleri azaltmak için siber sigorta ve risk transfer mekanizmalarını standardize etmek.

**Kapsam:** OpsCenter, kural motoru, veri gölü, mobil uygulamalar ve üçüncü taraf entegrasyonları dahil olmak üzere tüm kritik sistem bileşenleri.

**Sorumluluklar:**
- **Risk & Uyum Ekibi:** Poliçe gereksinimlerini belirler, sigorta sağlayıcılarıyla müzakereleri yürütür.
- **Güvenlik Ekibi:** Zafiyet yönetimi (Faz 22) sonuçlarını sigorta uyum raporlarına işler.
- **Finans Birimi:** Poliçe yenileme bütçesini planlar, teminat limitlerini Faz 20 KPI’ları ile eşleştirir.

**Süreç Adımları:**
1. **Risk Analizi:** Yıllık olarak risk kayıt defteri çıktıları (Risk Kayıt Defteri bölümü) finansal etki ve olasılık puanlarıyla güncellenir.
2. **Teminat Eşleştirme:** Her risk senaryosu için sigorta teminatı, özkaynak rezervi veya sözleşmesel risk paylaşımı kararı alınır.
3. **Uyum Kontrolü:** Sigorta sağlayıcısının talep ettiği teknik kontroller (ör. MFA, yedekleme) Faz 1 ve Faz 9 çıktılarıyla doğrulanır.
4. **Yenileme & Raporlama:** Poliçe yenilemeleri yılda bir yapılır; hasar bildirim simülasyonları Faz 16 tatbikat takvimine eklenir.

**İzleme & Ölçüm:** Teminat kullanım oranı, poliçe kapsam dışı olay sayısı ve yıllık prim değişimi raporlanır; göstergeler Stratejik Yönetim & Politika fazı ile paylaşılır.

**Hata Önleme:** Sigorta şartları değiştiğinde en fazla 30 gün içinde politika güncellemesi yapılmazsa otomatik eskalasyon finans direktörüne gider; teminat kapsamı dışı kalan kritik riskler için RFC zorunludur.

**Detaylı Rehber:** [`docs/governance/cyber-insurance.md`](docs/governance/cyber-insurance.md)

## Delil Zinciri & Adli Bilişim Protokolleri

**Amaç:** Kritik güvenlik olaylarında dijital delillerin toplanması, saklanması ve analizinde bütünlüğü korumak.

**Kapsam:** Audit loglar, sistem imajları, mobil cihaz kayıtları, API trafiği, alarm metrikleri ve üçüncü taraf entegrasyon logları.

**Standartlar:**
- **Delil Etiketleme:** Her delil dosyası benzersiz ID, zaman damgası, toplayan kişi ve hash değeri ile `chain-of-custody.csv` dosyasında kayıtlı olmalıdır.
- **Hash Doğrulama:** SHA-256 hash değerleri iki farklı ekip üyesi tarafından doğrulanır; doğrulama kayıtları Denetim Artefaktları deposuna eklenir.
- **Depolama:** Deliller şifreli kasada (Faz 21 veri maskeleme kontrolleri) en az 24 ay saklanır.
- **Erişim:** Sadece adli bilişim sorumluları (Yetkilendirme Matrisi) çok faktörlü kimlik doğrulama ile erişebilir.

**Olay Akışı:**
1. Güvenlik alarmı tetiklendiğinde (Bilgi Güvenliği Olay Bildirim Hattı) olay yöneticisi delil toplama görevini atar.
2. Tüm deliller imzalanır ve Chain-of-Custody portalına yüklenir.
3. Analiz sonuçları Faz 15 yapay zekâ modellerine sağlanan veri setlerinden izole edilir.
4. Hukuk ekibi ile paylaşılmadan önce KVKK etkileri değerlendirilir (DPIA bölümü).

**Hata Önleme:** Delil zinciri kaydı eksik olan hiçbir dosya soruşturmada kullanılamaz; eksikler 24 saat içinde tamamlanmazsa olay CRITICAL seviyeye yükseltilir.

**Detaylı Rehber:** [`security/chain-of-custody.md`](security/chain-of-custody.md)

## Vatandaş Veri Hakları & KVKK Başvuru Süreci

**Amaç:** Vatandaş ve gönüllülerin kişisel verileri üzerindeki hak taleplerini yasal süreler içinde karşılamak.

**Başvuru Kanalları:** Vatandaş portalı (Faz 17) formu, kayıtlı e-posta, fiziki dilekçe ve çağrı merkezi kayıtları.

**Süreç:**
1. **Kayıt:** Başvuru otomatik olarak `kvkk-requests` tablosuna düşer, benzersiz referans numarası oluşturulur.
2. **Kimlik Doğrulama:** Çok faktörlü doğrulama veya noter onaylı belge aranır; eksikse başvuru beklemeye alınır.
3. **Değerlendirme:** Veri sınıflandırma matrisi referans alınarak verinin saklama durumu, işleme amacı ve paylaşım geçmişi incelenir.
4. **Yanıt:** Silme/düzeltme/erişim talepleri en geç 30 gün içinde dijital imzalı olarak yanıtlanır; uzatma ihtiyacı varsa gerekçesiyle bildirilir.

**İzleme:** Aylık raporlar Hukuk & Mevzuat fazına gönderilir; geciken talepler için otomatik hatırlatıcılar açılır.

**Hata Önleme:** Yanlış kişiye veri ifşası tespit edilirse Veri İhlali Bildirim Planı devreye girer; tekrar eden gecikmeler için kök neden analizi zorunludur.

## Dashboard & Analitik Yayın Yönetimi

**Amaç:** Faz 20 dashboardlarının tutarlı, doğrulanmış ve versiyonlanabilir şekilde yayınlanmasını sağlamak.

**Yayın Döngüsü:**
1. **Hazırlık:** Veri gölü ETL çıktıları (Faz 24) kalite kontrollerinden geçer; rapor taslakları `analytics/` deposunda sürümlenir.
2. **Gözden Geçirme:** Veri analistleri ve iş birimi sahipleri tarafından çift kontrol yapılır; kabul kriterleri KPI bölümüne referans verir.
3. **Onay:** Yönetim kurulu toplantısında (Yönetim Kurulları & Toplantı Ritmi) yayın onayı alınır.
4. **Dağıtım:** Dashboard URL’si ve değişiklik notları OpsCenter ve paydaş e-postalarına gönderilir.

**Versiyonlama & Rollback:** Her dashboard sürümü `dashboard-vX.Y` etiketi ile kaydedilir; sorun halinde önceki sürüm 30 dakika içinde geri alınabilir.

**İzleme:** Kullanım metrikleri (giriş sayısı, veri yenilenme süresi) aylık olarak analiz edilir; hedef dışı değerler Teknoloji Borcu yönetimine bildirilir.

**Hata Önleme:** Onay almadan yayınlanan dashboardlar otomatik olarak devre dışı bırakılır; veri kaynağı değiştiğinde ilgili raporlar için yeniden doğrulama yapılmadan güncelleme yapılamaz.

## Performans Benchmark & Kapasite Test Programı

**Amaç:** Sistem performansını düzenli olarak ölçmek, kapasite sınırlarını belgelendirmek ve ölçek planlarını güncel tutmak.

**Senaryolar:**
- **WebSocket Yük Testi:** Faz 4 canlı takip için 10k istemci hedefi.
- **OpsCenter Render Testi:** 200 eş zamanlı kullanıcı ve 500 obje katmanı.
- **Veri Girişi Testi:** Faz 5 QR taraması için saniyede 50 işlem.
- **Edge Senaryosu:** Faz 9 offline kuyruk için 12 saat veri tamponlama.

**Program:**
1. Çeyreklik test planı DevOps ekibi tarafından hazırlanır; detaylı kapsama `docs/tests/matrix.md` dosyasında takip edilir.
2. Testler otomatik CI/CD pipeline’ında ayrı ortamda tetiklenir.
3. Sonuçlar Performans & Kapasite İzleme bölümünde tanımlı eşiklerle karşılaştırılır ve `analytics/benchmark/` klasöründe raporlanır.
4. Limit aşımı varsa kapasite artırımı veya optimizasyon backlog’a alınır.

**Raporlama:** Benchmark sonuçları `reports/performance/` dizinine kaydedilir, özetler `analytics/benchmark/README.md` rehberine göre tutulur ve trend analizi Stratejik Yönetim fazıyla paylaşılır.

**Hata Önleme:** Test ortamı üretim ile uyumlu değilse test geçersiz sayılır; kritik sapmalarda otomatik olarak yayın pipeline’ı durdurulur.

## Saha Haberleşme Donanımı Kalibrasyon Planı

**Amaç:** Saha telsizleri, uydu telefonları ve sensör ağ geçitlerinin güvenilir iletişim sağlaması için düzenli kalibrasyon ve bakım programını yürütmek.

**Envanter:** Faz 18 lojistik modülündeki envanter kayıtları üzerinden kalibrasyon gerektiren tüm cihazlar işaretlenir.

**Takvim:**
- **Aylık:** Telsiz frekans doğrulaması ve batarya kapasite testi.
- **Çeyreklik:** Uydu telefonları için çağrı kalitesi testi ve SIM yenileme.
- **Yıllık:** Sensör gateway firmware güncellemesi ve anten hizalama kontrolü.

**Süreç:**
1. Kalibrasyon görevleri OpsCenter hızlı işlem panelinden atanır.
2. Teknik ekip, kalibrasyon sonuçlarını `calibration-log.csv` dosyasına işler ve imzalar.
3. Başarısız testler için cihaz envanter durumu "service" olarak güncellenir, görev planı otomatik açılır.

**Bağlantılar:** Kalibrasyon raporları Enerji Sürekliliği planı ve Saha Güvenlik Sertifikasyon programı ile paylaşılır; arızalar Risk Kayıt Defteri’ne işlenir.

**Hata Önleme:** Kalibrasyon süresi geçen cihazlar görev atamasında görünmez; üç ardışık başarısız test alan cihazlar için üretici ile servis süreci başlatılır.

## Açık Kaynak & Lisans Uyumluluğu Programı
_(Güncelleme: 2024-07-02)_

**Amaç:** Açık kaynak bağımlılıkları, üçüncü taraf kütüphaneleri ve dış katkıları güvenli, lisans uyumlu ve sürdürülebilir şekilde yönetmek.

**Kapsam:**
- Laravel ekosistemi paketleri, mobil uygulama SDK’ları (Faz 14), harita eklentileri (Faz 6) ve yapay zekâ modelleri (Faz 15) dahil tüm bağımlılıklar.
- Topluluk veya kamu kurumlarından gelen kod katkıları, dokümantasyon güncellemeleri ve çeviri paketleri.

**Yönetişim Döngüsü:**
1. **Envanter:** `governance/license-inventory.csv` dosyası haftalık olarak CI pipeline’ı (Faz 11) tarafından güncellenir; her paket için lisans türü, sürüm, sorumlu ekip ve son denetim tarihi izlenir.
2. **İnceleme:** Yeni bağımlılıklar RFC süreciyle teklif edilir; güvenlik ekibi (Faz 22) lisans uyumluluğunu, CVE riskini ve kripto ihracat kısıtlarını kontrol eder.
3. **Onay:** Yönetim kurulu toplantısında lisans değişikliği veya copyleft etkileşimi olan paketler için ürün sahibi ve hukuk ekibi ortak onay verir.
4. **İzleme:** Dependabot/Snyk raporları haftalık olarak gözden geçirilir; kritik zafiyetler 72 saat içinde patchlenir veya paket devre dışı bırakılır.

**Katkı Kuralları:**
- Dış katkıda bulunanlar için `CONTRIBUTING.md` ve Kurumsal Katılımcı Sözleşmesi (CLA) imzası zorunludur.
- Kod incelemeleri lisans uyumluluk kontrol listesi ile tamamlanır; uyumsuz kod üretim branch’ine alınmaz.
- Açık kaynak yayınlarında (örn. anonimize edilmiş kural motoru şablonları) tenant verisi veya gizli anahtar bulunamaz.

**İzleme & Raporlama:** Çeyrek dönem sonunda lisans uyumu raporu hazırlanır, Faz 21 uyum takvimine işlenir ve kritik bulgular yönetim kuruluna sunulur.

**Hata Önleme:** Lisans çakışması tespit edilirse paket karantinaya alınır, alternatif belirlenene kadar deployment durdurulur; ihlal tekrarında bağımlılık listesi manuel denetime tabi tutulur.

## Dijital İkiz & Senaryo Modelleme Yönetişimi
_(Güncelleme: 2024-07-09)_

**Amaç:** OpsCenter, lojistik ve saha operasyonları için dijital ikiz ortamlarını doğruluk, güvenlik ve performans kriterleriyle yönetmek.

**Kullanım Alanları:**
- Faz 16 simülasyonları sırasında afet senaryolarının OpsCenter üzerinde güvenli şekilde canlandırılması.
- Faz 4 canlı takip verilerinin sentetik kopyalarıyla hareketsizlik ve geofence kural testleri.
- Faz 18 lojistik planlaması için depo, rota ve envanter kapasite modellemeleri.

**İşletim Modeli:**
1. **Model Geliştirme:** Simülasyon ekibi `digital-twin/` dizininde senaryo verilerini sürümleyerek oluşturur; gerçek veriden türetilen her parametre anonimleştirme standartlarından geçer.
2. **Doğrulama:** Teknik liderlik modeli doğruluk, performans ve güvenlik kriterlerine karşı kontrol eder; sonuçlar `digital-twin/review-log.md` dosyasında saklanır.
3. **Yayın:** Onaylanan modeller staging ortamına alınır; Faz 11 pipeline’ı model sürümünü otomatik etiketler ve OpsCenter’da “Simülasyon” modu açılır.
4. **Geribildirim:** Tatbikat sonrası ölçümler (tepki süresi, alarm doğruluğu) Faz 16 KPI panosuna işlenir, sapmalar için model güncelleme aksiyonu açılır.

**Erişim Kontrolleri:** Simülasyon verisine erişim rol bazlıdır; üretim tenant verisiyle karışmaması için network segmentasyonu uygulanır. Senaryo kayıtları 180 gün sonra arşive taşınır.

**Hata Önleme:** Model ile gerçek üretim sistemleri arasında veri sızıntısı tespit edilirse simülasyon ortamı derhal kapatılır, olay Faz 15 veri ihlali planına göre raporlanır ve yeni sürüm doğrulanana kadar tatbikatlar durdurulur.

## Kriz Sonrası İyileştirme & Rehabilitasyon Çerçevesi
_(Güncelleme: 2024-07-02)_

**Amaç:** Afet sonrası saha rehabilitasyonu, psikososyal destek ve altyapı onarım süreçlerini faz çıktılarıyla entegre şekilde yönetmek.

**Kritik Bileşenler:**
- **Rehabilitasyon Planları:** Faz 18 lojistik verileri ile Faz 8 risk analizleri birleştirilerek etki alanı, ihtiyaç önceliği ve kaynak tahsisi belirlenir.
- **Psikososyal Destek:** Vatandaş & Gönüllü portalı (Faz 17) üzerinden gelen destek talepleri, saha refah programı (Personel Refahı bölümü) ile koordine edilir.
- **Altyapı İzleme:** OpsCenter katmanları üzerinden kritik altyapı durumları takip edilir; drone/IoT tetikleri (Faz 23) iyileştirme görevlerine dönüştürülür.

**Süreç Adımları:**
1. **Değerlendirme (T+24 saat):** ICS formları ve olay sonrası raporlar toplanır; etki değerlendirmesi `rehab/impact-dashboard.md` dosyasında güncellenir.
2. **Planlama (T+72 saat):** Lojistik ve saha liderleri kaynak gereksinimlerini belirler; görevler Faz 3 görev yönetimi modülünde “REHAB” etiketiyle açılır.
3. **Uygulama:** Görevler OpsCenter üzerinden izlenir, hareketsizlik ve geofence kuralları rehabilitasyon sahaları için yeniden kalibre edilir.
4. **Raporlama:** Haftalık raporlar stratejik yönetim panosuna (Faz 26) aktarılır; sosyal etki göstergeleri (Sosyal Etki Ölçüm Çerçevesi) ile çapraz kontrol edilir.

**Başarı Metrikleri:** Ortalama saha onarım süresi, psikososyal destek yanıt oranı, kritik kaynak teslimatında gecikme yüzdesi.

**Hata Önleme:** Rehabilitasyon görevleri 48 saat içinde atanmazsa otomatik eskalasyon çalışır; ihtiyaç fazlası stok tespit edilirse Faz 18 talep/arz eşleştirme motoru yeniden dengeleme önerisi üretir.

## Devam Et Yapıları Rehberi
_(Güncelleme: 2024-07-16)_

**Amaç:** “Devam et” talimatlarıyla eklenen yönetişim artefaktlarının klasör yapısını tek noktadan açıklamak ve ilgili faz referanslarına hızlı erişim sağlamaktır.

**Özet:** Ayrıntılı rehber `docs/governance/devam-et-yapi-rehberi.md` dosyasında yer alır. Aşağıdaki tabloda ana kategoriler özetlenmiştir:

| Kategori | İçerik Odakları | İlgili Dizinler |
| --- | --- | --- |
| Operasyonel Runbook’lar | Olay müdahalesi, offline toparlanma, alarm eskalasyonu | `runbook/`, `runbooks/`
| Gözlemlenebilirlik | Metrikler, alarm kuralları, gözden geçirme kayıtları | `observability/`
| Dijital İkiz & Tatbikat | Senaryolar, veri katalogları, doğrulama checklist’leri | `digital-twin/`
| Güvenlik & Tehdit Programı | Tehdit akışları, zafiyet kayıtları, yüksek risk playbook’ları | `docs/threat-program/`, `security/`
| Uyum ve Denetim | ISO/finans raporları, eğitim kanıtları, bulgu takibi | `compliance/`, `docs/audit/`

**Kullanım:** Yeni bir kayıt eklendiğinde ilgili klasörün README’sindeki standartlara uyun, yapılan değişikliği `CHANGELOG.md` ve [Belge Versiyon Geçmişi](#belge-versiyon-geçmişi) tablolarına işleyin.

## Faz 0 — Kararlar (Temel Direkler)
**Amaç:** Projenin ileride parçalanmaması ve tüm geliştiricilerin aynı standartta çalışması için teknolojik, mimari ve güvenlik kararlarını sabitlemek.

**İçerik:**
- Yazılım altyapısı: PHP 8.3, Laravel 11.
- Veritabanı: MySQL 8 (InnoDB + Spatial veri türleri).
- Ön yüz: Blade + Tailwind veya Vue.js tabanlı bileşenler.
- Harita: MapLibre GL, Leaflet; offline için PMTiles desteği.
- API: REST + WebSocket (canlı yayın için).
- İletişim entegrasyonları: NETGSM (SMS), SMTP (mail), Firebase (push).
- Çoklu tenant desteği: İl bazlı ayrım.
- Sürümleme: Git, semantic versioning.
- `.env` şablonları: Ortam değişkenleri için standart format.

**Nasıl Çalışır:** Bu fazda sistemin iskeleti tanımlanır. Kim hangi teknolojiyi kullanacak, hangi servis zorunlu olacak, hangi kütüphaneler tercih edilecek önceden belirlenir.

**Ne İşe Yarar:**
- Gelecekte uyumsuzlukların önüne geçer.
- Ekipler farklı aşamalarda çalışsa bile sistem bir bütün halinde ilerler.
- Tüm fazların üzerine oturduğu sağlam bir temel oluşturur.

**Hata Önleme:**
- Kararlar yazılı doküman olarak tutulur.
- Keyfi değişiklik yapılmaz, RFC süreci gerekir.

## Faz 1 — Güvenlik & Altyapı
**Amaç:** Sistemin en baştan güvenli, izlenebilir ve sürdürülebilir olması.

**İçerik:**
- Kimlik doğrulama: Laravel Fortify.
- 2FA: SMS veya mobil uygulama ile.
- Rol & Yetki: Spatie Permissions, tenant bazlı unit yetkileri.
- Audit Log: Her işlem kayıt altına alınır.
- Yedekleme: Günlük dump + şifreli saklama.
- Felaket kurtarma: Disaster recovery scriptleri.

**Nasıl Çalışır:**
- Kullanıcı girişlerinde hem şifre hem de 2FA zorunludur.
- Roller birimlere göre atanır. Örn. "Arama Ekibi – Başkan".
- Her işlem `audit_logs` tablosuna işlenir.
- Veritabanı dump dosyaları günlük alınır ve farklı bir sunucuda saklanır.

**Ne İşe Yarar:**
- Yetkisiz erişimleri engeller.
- Tüm işlemler kayıt altında olduğundan geriye dönük inceleme yapılabilir.
- Veri kaybı riskini minimuma indirir.

**Hata Önleme:**
- Şifre politikaları zorunlu (karmaşık, minimum uzunluk).
- 2FA zorunlu hale getirilir.
- Yedeklerden düzenli test geri yüklemesi yapılır.

## Faz 2 — Veri & Migrasyon (Şema Sertleştirme)
**Amaç:** Veri kalitesini garanti altına almak, hatalı kayıtların sisteme girmesini engellemek.

**İçerik:**
- Olay tablosu: `id`, `tenant_id`, `title`, `status`, `priority`, `polygon`, `started_at`, `closed_at`.
- Görev tablosu: `id`, `incident_id`, `assigned_to`, `status`, `rota`.
- Envanter tablosu: `id`, `code`, `status`, `last_service_at`.
- Kullanıcı tablosu: `id`, `unit_id`, `role`, `documents`.
- Kısıtlamalar: `UNIQUE`, `FOREIGN KEY`, `CHECK`, spatial index.

**Nasıl Çalışır:**
- Veritabanı seviyesinde kurallar tanımlanır.
- Örneğin, aynı tenant içinde aynı olay kodu iki kere açılamaz.
- Olay poligonu kendi üst üste çakışıyorsa kayıt reddedilir.

**Ne İşe Yarar:**
- Veri bütünlüğü korunur.
- Yanlış veya hatalı girişler uygulama katmanına ulaşmadan engellenir.
- Sistem gelecekte sağlıklı analiz yapılabilecek bir veri tabanı sağlar.

**Hata Önleme:**
- `CHECK` constraint: Görev durumu sadece belirlenen değerleri alabilir.
- `UNIQUE` constraint: Olay kodu benzersiz olmalı.
- Foreign key: Görev olmayan bir olaya bağlanamaz.

## Faz 3 — Çekirdek Modüller (Olay–Görev–Envanter–Kullanıcı)
**Amaç:** Afet yönetiminin bel kemiğini oluşturan modülleri eksiksiz çalışır hale getirmek.

**İçerik:**
### Olay Yönetimi
- Durumlar: `OPEN → ACTIVE → CLOSED`
- Etki alanı: Poligon (ör. deprem bölgesi)
- Zaman damgaları: `started_at`, `closed_at`
- Kurallar: Olay kapatılmadan görev kapatılamaz

### Görev Yönetimi
- Durumlar: `PLANNED → ASSIGNED → IN_PROGRESS → DONE → VERIFIED`
- Rota: `LINESTRING` ile görev güzergâhı
- Doğrulama: Görev bitişi çift onay gerektirir

### Envanter Yönetimi
- Durumlar: `active | service | retired`
- Hareketler: zimmet, iade, bakım, hurda
- Zimmet: Transaction içinde yapılır

### Kullanıcı Yönetimi
- Birim bazlı rol atamaları
- Belgelerin son kullanma tarihleri
- Süresi geçmiş belge ile görev ataması yapılamaz

**Nasıl Çalışır:** Olay açıldığında görevler o olaya bağlanır. Görevler ekip üyelerine atanır. Görev sırasında kullanılan envanter zimmetlenir. Tüm kullanıcıların belgeleri ve sağlık bilgileri kontrol edilir.

**Ne İşe Yarar:**
- Tüm operasyonel süreç tek noktadan yönetilir.
- Görevler, olaylar ve envanter birbiriyle entegre çalışır.
- Yasal gereklilikler (belge kontrolü) yerine getirilir.

**Hata Önleme:**
- Çift onay mekanizması
- Transaction yönetimi
- Optimistic locking (eş zamanlı düzenlemelerde çakışma önleme)

## Faz 4 — Canlı Takip (Tracking)
**Amaç:** Saha ekiplerinin ve araçların konumunu anlık olarak izlemek.

**İçerik:**
- Mobil cihazlardan ping (GPS koordinatı + hız + yön).
- Görevdeyken 5–10 saniyede bir ping, görev dışında 60–120 saniyede bir.
- SOS butonu ile acil durum bildirimi.
- Geofence kontrolü (görev bölgesi dışına çıkarsa alarm).
- Yeni özellik: Hareketsizlik uyarısı → cihaz 2 dakika boyunca hareket etmezse OpsCenter alarm üretir.

**Nasıl Çalışır:** Ekip cihazları ping gönderir. Pingler geofence içinde mi kontrol edilir. Eğer kişi görevde ama hareketsizse, sistem hem kullanıcıya hem merkeze bildirim yollar. SOS butonu tetiklenirse kırmızı alarm düşer.

**Ne İşe Yarar:**
- Kaybolma, yaralanma veya bilinci kapalı personel anında fark edilir.
- Ekiplerin anlık yerleri ve hareketleri bilinir.
- Komuta merkezi güvenli ve doğru yönlendirme yapabilir.

**Hata Önleme:**
- Tekrarlanan pingler filtrelenir.
- Hareketsizlik alarmı yanlış tetiklenirse (örn. telefon çantada sabit kalmış), doğrulama penceresi açılır.
- SOS spam engellenir.

## Faz 5 — QR & Yoklama (Anti-Replay)
**Amaç:** Personelin görev başı/yoklama kontrolünü güvenli ve hızlı hale getirmek.

**İçerik:**
- Kişiye özel QR kod (şifreli).
- Göreve özel QR kod (geçici, süreli).
- Geofence tabanlı yoklama.
- Nonce tabanlı anti-replay kontrolü.

**Nasıl Çalışır:** Göreve girişte herkes QR kodunu okutmak zorunda. Kod geçerliyse sistem kişiyi görev listesine ekler. QR tekrar okutulursa `409` hatası döner. Görev bölgesi dışında okutulursa `403` hatası döner.

**Ne İşe Yarar:**
- Hızlı yoklama sağlar.
- Kâğıt imza defteri yerine dijital imza listesi üretir.
- Replay saldırılarına karşı güvenli bir sistem oluşturur.

**Hata Önleme:**
- QR kodlar süreli ve şifreli olur.
- Nonce mekanizması tekrar okutmayı engeller.
- Tüm işlemler audit log’a kaydedilir.

## Faz 6 — OpsCenter (Harita & Operasyon Merkezi)
**Amaç:** Tüm veriyi tek ekranda, gerçek zamanlı olarak yönetmek; karar hızını artırmak.

**İçerik:**
- Harita motoru (MapLibre/Leaflet) + PMTiles (offline).
- Katmanlar: Olay poligonları, görev rotaları, ekip konumları (cluster), envanter/depolar, toplanma alanları, dış katmanlar (trafik/radar/FIRMS/WAQI/fay/WFS).
- BBOX/tenant/status filtreli GeoJSON API, server-side pagination + caching.
- Alarm konsolu: geofence ihlali, hareketsizlik uyarısı, ping kaybı, SOS, kritik stok.
- Hızlı işlemler: göreve ata, QR üret, yoklama başlat/bitir, alarmı doğrula.

**Nasıl Çalışır:** Harita paneli `BBOX` parametresiyle yalnızca ekranda görünen alandaki veriyi çeker (performans). Katmanların görünürlüğü ve stil kuralları öncelik/durum bilgisiyle dinamikleşir. Alarm konsolu, Faz 4’ten gelen canlı akış ile Faz 7 kural tetiklerini birleştirir.

**Ne İşe Yarar:**
- Komuta merkezi tek ekrandan yönetir; ekipleri hızla yönlendirir.
- Harita snapshot’ı raporlara gömülür (Faz 8).
- Dış servis kesintisinde bile cache ile çalışmaya devam eder.

**Hata Önleme:**
- Katman yükleri için rate limit + cache; dış API düşerse graceful degrade.
- `BBOX` zorunlu (tüm veriyi bir anda çekmeyi engeller).
- Snapshot boyutu ve çözünürlüğü sınırlandırılır.

## Faz 7 — Kural Motoru & Bildirimler
**Amaç:** Olaylara otomatik ama kontrollü tepki vermek (insan-odaklı otomasyon).

**İçerik:**
- Kurallar: trigger (ör. `incident.created`, `task.no_ping`, `tracking.no_motion`), condition (`JSONLogic`), actions (SMS/e-posta/push/webhook/WS).
- Şablon yönetimi (çok dilli içerik), rate limit, retry, teslim raporu.
- Test modu (dry-run) ve sürümleme.

**Nasıl Çalışır:** Bir sistem olayı (ör. hareketsizlik 120 sn+) tetiklenince kural motoru koşulları değerlendirir (örn. “görevde ve sağlık durumu ‘riskli’ etiketi varsa”). Aksiyonlar sırayla çalışır; başarısız olanlar retry kuyruğuna alınır.

**Ne İşe Yarar:**
- Kritik anlarda otomatik uyarı zinciri (lider → merkez → 112 vb.).
- Tekrarlayan görevleri (rapor çıkarma, atama bildirimleri) otomatikleştirir.

**Hata Önleme:**
- Rule başına rate limit (spam engeli), global throttling.
- Kural hatasında sistem durmaz, tetik loglanır ve atlanır.
- Webhook’larda imzalı çağrı ve timeout/geri çekilme politikası.

## Faz 8 — İleri Analiz & Araçlar (ICS, Playback, Risk)
**Amaç:** Operasyonel veriyi karar destek raporlarına dönüştürmek.

**İçerik:**
- ICS 201/202/204/205/206 formları (otomatik doldurma + iki onay).
- Playback: ekip hareketlerinin zaman çizelgesi (ping/rota).
- Risk analizi: `impact_area ∩ nüfus/altyapı` kesişim metrikleri.
- Mutual Aid: dış kurumdan ekip/ekipman talebi ve onayı.

**Nasıl Çalışır:** Olay/görev/katılım ve harita snapshot verileri rapor şablonlarına işlenir; eksik alan varsa `422` ile geri döndürülür. Playback modülü pingi zaman ekseninde akıtır; risk analizi poligon kesişimiyle kişi/tesisi sayar.

**Ne İşe Yarar:**
- Standart ICS belgeleriyle kurum içi/dışı raporlama.
- “Ne oldu?” sorusuna görsel, kanıtlanabilir yanıt.
- Hızlı etki değerlendirmesi (kaç kişi/tesis etkilenmiş).

**Hata Önleme:**
- Raporlar versiyonlanır, iki onay olmadan “resmi” statü almaz.
- Hesaplamalar deterministik; veri kaynağı/versiyon bilgisi eklenir.

## Faz 9 — Offline & Edge
**Amaç:** İnternet kesintisinde bile operasyonu sürdürmek.

**İçerik:**
- PMTiles paketleri (il/ilçe), Service Worker cache (`GET`), yazma için offline kuyruk.
- Edge node (saha mini sunucu) → lokal okuma + yazma kuyruğu.
- Senkron: idempotent yazım, checksum, `last-writer-wins` + çakışma raporu.

**Nasıl Çalışır:** İstemci çevrimdışıysa harita ve en son `GET` yanıtları cache’ten sunulur; `POST/PATCH` istekleri kuyrukta saklanır. Bağlantı gelince kuyruk sırasıyla çalışır ve `Idempotency-Key` ile çift yazım engellenir.

**Ne İşe Yarar:**
- Kırsal/afet sahasında bile görevler görüntülenir ve kayıt alınır.
- Veri kaybı ve kullanıcı deneyimi bozulmaz.

**Hata Önleme:**
- Kuyrukta boyut/yaş sınırları; çakışma raporu ve manuel çözüm ekranı.
- Edge ile merkez arasında imzalı kanal, saat senkron kontrolü.

## Faz 10 — Dış Servis Entegrasyonları
**Amaç:** Deprem/yangın/hava/uydu vb. harici kaynakları güvenle almak.

**İçerik:**
- USGS/EMSC/AFAD (deprem), FIRMS (yangın), RainViewer (radar), WAQI (hava kalitesi), TomTom (trafik).
- Normalizasyon katmanı, anti-duplicate hash, rate limit + cache.

**Nasıl Çalışır:** Periyodik çekme ya da webhook ile veri gelir; farklı formatlar tek şemaya çevrilir; hash ile yinelenen kayıtlar ayıklanır; olay tetiklerine (örn. otomatik “depreme bağlı durum değerlendirmesi”) paslanır.

**Ne İşe Yarar:**
- Durum farkındalığını artırır; OpsCenter katman zenginliği sağlar.
- Erken uyarı ve hızlı değerlendirme.

**Hata Önleme:**
- Dış servis düşerse son geçerli veri ve uyarı; timeout/geri çekilme.
- Alan doğrulama ve tarih/saat normalizasyonu.

## Faz 11 — DevOps & İzlenebilirlik
**Amaç:** Sürümleme, izleme, ölçek ve bakım süreçlerinin güvenli işletimi.

**İçerik:**
- CI/CD (staging → prod), migration guard, otomatik rollback.
- Sentry (hata), Prometheus/Grafana (metrik), `/health` (durum ucu).
- Windows geliştirme: `QUEUE_CONNECTION=database`; Prod Linux: Horizon + Redis + Supervisor.

**Nasıl Çalışır:** Merge sonrası pipeline testleri çalışır; migration guard veri kaybını engeller; prod deploy öncesi sağlık kontrolleri yeşil değilse durdurulur.

**Ne İşe Yarar:**
- Güvenli yayın ve hızlı geri dönüş.
- Canlı ortamda problemleri erkenden yakalama.

**Hata Önleme:**
- CI’de testler kırmızıysa deploy olmaz.
- Otomatik alarmlar (kuyruk yavaşladı, WS kopuyor, DB latency arttı).

## Faz 12 — Testler (Unit/Feature/Integration/E2E/Load/Security)
**Amaç:** Yayına çıkmadan işlevsel ve performans doğrulaması.

**İçerik:**
- Birim/özellik/entegrasyon testleri (çekirdek akışlar).
- E2E: QR yoklama, geofence alarm, görev → envanter → doğrulama zinciri.
- Yük: 10k WS istemci, 1k ping/s.
- Güvenlik: upload, rate limit, yetki sızması testleri.

**Nasıl Çalışır:** CI pipeline’da farklı test katmanları paralel koşar; kural motoru için sahte tetikler; harita BBOX API’leri için performans ölçümü.

**Ne İşe Yarar:**
- Regresyonu önler; performans sınırlarını ölçer.

**Artefaktlar:**
- `docs/tests/README.md` — test katmanları, kabul kriterleri ve raporlama döngüsü.
- `docs/tests/matrix.md` — faz → senaryo eşleştirmesi ve en güncel rapor bağlantıları.
- `analytics/benchmark/2024-07-10-opscenter.md` — OpsCenter benchmark örneği ve kapasite aksiyon planı.

**Hata Önleme:**
- Yeşil olmadan prod yok; testler sonuçlarını release notuna yazar.

## Faz 13 — Canlıya Hazırlık (GoLive)
**Amaç:** Saha kullanımına sorunsuz geçiş.

**İçerik:**
- Eğitim/SOP, rol dağıtımı, public status sayfası.
- On-call nöbet, incident response runbook.
- Pilot il/ilçe tatbikatı ve geri bildirim döngüsü.

**Nasıl Çalışır:** Pilot senaryolar gerçek sistem üzerinde koşturulur; eksikler backlog’a; on-call süreçleri çalıştırılır; halka açık durum sayfası kurulur.

**Ne İşe Yarar:**
- İlk gün sorunlarını sahada yakalayıp düzeltme.

**Hata Önleme:**
- Geri dönüş planı; kritik arızalara manuel override prosedürü.

## Faz 14 — Mobil Uygulama & Saha
**Amaç:** Saha personelinin mobil cihazla tüm işini çevrimdışı/çevrimiçi yapabilmesi.

**İçerik:**
- Flutter/Android+iOS, PWA; push bildirim; offline-first veri katmanı.
- QR tarama, görev listesi/rota, hareketsizlik ve SOS hızlı erişim.
- Medya yükleme (foto/video) + düşük bant genişliğinde kuyruklama.

**Nasıl Çalışır:** Görev alındığında offline cache’e iner; saha modu internet yokken de çalışır; bağlantı gelince kuyruk senkronize edilir; hareket sensörü ile hareketsizlik tespiti cihazdan da desteklenir.

**Ne İşe Yarar:**
- Saha ekiplerinin hızını artırır; veri kaybını düşürür.

**Hata Önleme:**
- Cihaz saat/log senkronizasyonu; düşük pil modunda ping aralığı artırma.

## Faz 15 — Yapay Zeka & Karar Destek
**Amaç:** Atama/planlama/analiz süreçlerini akıllandırmak.

**İçerik:**
- Görev atama önerisi (yakınlık, yetkinlik, ekip yoğunluğu).
- Medya analizi (drone/termal): enkaz/yangın öbeği tespiti.
- Chatbot: operatör asistanı, vatandaş SSS.

**Nasıl Çalışır:** Model servisleri özellikleri hesaplar (feature set: mesafe, beceri, ekip yükü); öneri skoru üretir; insan onayı olmadan kritik işlem yapmaz.

**Ne İşe Yarar:**
- Yük dengeleme, daha hızlı ve doğru atama.
- Görsel kanıttan hızlı durum tespiti.

**Hata Önleme:**
- Human-in-the-loop, açıklanabilirlik; yanlış pozitifler için geri bildirim döngüsü.

## Faz 16 — Simülasyon & Tatbikat
**Amaç:** Eğitim ve hazırlık için gerçekçi, izole senaryolar.

**İçerik:**
- Deprem/sel/yangın senaryoları; tatbikat veritabanı.
- Simülasyon modu (canlı veriye dokunmaz).
- Tatbikat KPI’ları (müdahale süresi, doğrulama gecikmesi).

**Nasıl Çalışır:** Simülasyon veri jeneratörü olay/görev/konum akışını üretir; OpsCenter “tatbikat” bayrağıyla çalışır; raporlar ayrı işaretlenir.

**Ne İşe Yarar:**
- Eğitim kalitesini artırır; prosedür açıklarını gösterir.

**Hata Önleme:**
- Simülasyon ile prod veri yolları kesin ayrılır.

## Faz 17 — Vatandaş & Gönüllü Portalı
**Amaç:** İhbar, gönüllü, ihtiyaç/bağış akışlarını güvenle toplamak.

**İçerik:**
- İhbar/yardım talepleri; gönüllü kayıt/başvuru.
- Medya yükleme (moderasyonlu), açık harita (bilgilendirme).
- Spam/abuse koruması, KVKK uyumu.

**Nasıl Çalışır:** Formlar tenant’a göre akar; riskli içerik otomatik filtrelenir; moderatör onayı olmadan yayın yok; doğrulanmış ihbarlar olaya dönüştürülebilir.

**Ne İşe Yarar:**
- Saha dışında da bilgi/insan kaynağı akışı sağlar.

**Hata Önleme:**
- Rate limit, CAPTCHA, PII maskeleme; güvenli yükleme.

## Faz 18 — Lojistik & Kaynak Yönetimi
**Amaç:** Stok, dağıtım ve araç yönetimini şeffaf ve izlenebilir kılmak.

**İçerik:**
- Depo ve dağıtım noktaları, araç GPS, sevkiyat planları.
- Koli/varlık QR etiketi; teslim zinciri (chain-of-custody).
- Talep/arz eşleştirme, kritik stok eşiği alarmları.

**Nasıl Çalışır:** Sevkiyat planı rotalara ayrılır; koli/araç her durağa giriş-çıkışta QR ile işaretlenir; OpsCenter lojistik katmanı durum ve ETA gösterir.

**Ne İşe Yarar:**
- Kaynak kaybını azaltır, gecikmeleri görünür kılar.

**Hata Önleme:**
- Her adımda QR zorunluluğu; kayıp/çakışma alarmı; imzalı teslimler.

## Faz 19 — Kurumlar Arası Entegrasyon
**Amaç:** AFAD/AYDES, belediyeler, UMKE ve uluslararası ağlarla veri alışverişi.

**İçerik:**
- Adapter/köprü servisleri; veri eşleştirme (mapping).
- Güvenli anahtar yönetimi, rol tabanlı erişim.
- Standart rapor formatları (INSARAG, OCHA vb.).

**Nasıl Çalışır:** İç şema dış sistemlere dönüştürülür; zaman damgası ve imza eklenir; gelen veriler doğrulama/karantina hattından geçer.

**Ne İşe Yarar:**
- Operasyonlar arası koordinasyon (tek gerçeklik kaynağı).

**Hata Önleme:**
- Rate limit, kuyruk, retry; karantina kuyruğu ve manuel onay.

## Faz 20 — İleri Görselleştirme & Dashboard
**Amaç:** Karar vericilere hızlı algılanabilir göstergeler sunmak.

**İçerik:**
- KPI panelleri, zaman serisi grafikleri, 3D (bina/zemin).
- Büyük ekran “wallboard”; canlı durum akışı.
- Ön-toplamlar (pre-aggregation), cache.

**Nasıl Çalışır:** Akış/işlemsel veri, raporlamaya uygun ara depolarda toplanır; paneller 1–2 sn’de yüklenir; kritik panolar için degrade mod (yalnızca temel metrikler).

**Ne İşe Yarar:**
- Kriz anında tek bakışta durum farkındalığı.

**Hata Önleme:**
- Aşırı sorgu spam’ini önleyen cache ve sorgu sınırlayıcıları.

## Faz 21 — Hukuk & Mevzuat Uyum
**Amaç:** KVKK/GDPR ve afet mevzuatına tam uyum.

**İçerik:**
- Saklama süreleri, anonimleştirme/maskeleme.
- Erişim/kullanım logları, denetim raporları.
- Kişisel veriler için amaçla sınırlı işleme.

**Nasıl Çalışır:** Veri sınıflandırılır; süresi dolan veriler arşivlenir/silinir; kim hangi veriye baktı loglanır; talep halinde veri ihracı.

**Ne İşe Yarar:**
- Denetimlerde güven ve yasal güvence.

**Hata Önleme:**
- Otomatik politika kontrolleri, eksik metadatalarda kayıt reddi.

## Faz 22 — Siber Güvenlik & Dayanıklılık
**Amaç:** Dış tehditlere ve arızalara karşı dayanıklı yapı.

**İçerik:**
- Zero Trust, WAF/CDN/DDOS koruma, gizli anahtar rotasyonu.
- Sürekli zafiyet taraması, pentest, Red/Blue team tatbikatları.
- İş sürekliliği planları (BCP) ve yedekli bölgeler.

**Nasıl Çalışır:** WAF politika setleri, hız limitleri; gizli anahtarlar KMS’de; düzenli pentest takvimi; olağanüstü durumda DR senaryosu.

**Ne İşe Yarar:**
- Saldırı yüzeyi minimize; kesinti ihtimali düşer.

**Hata Önleme:**
- Olay müdahale planı; otomatik güvenlik yamaları.

## Faz 23 — Akıllı Donanım Entegrasyonu (IoT/Drone/Robot/Uydu)
**Amaç:** Sahadan doğrudan veri toplayıp otomasyon sağlamak.

**İçerik:**
- Sensör (su/gaz/yangın), drone görüntüleri (RGB/termal), robot telemetrisi.
- IoT gateway (MQTT/TLS), veri normalizasyonu.
- Olay tetikleri (ör. sensör eşiği → olay oluştur).

**Nasıl Çalışır:** Cihazlar güvenli kanaldan veriyi gönderir; eşik aşımları kural motoruna tetik olur; drone görüntüsü AI ile öbek tespitine gönderilir.

**Ne İşe Yarar:**
- İnsan hatasını ve gecikmeyi azaltır; geniş alan kapsaması sağlar.

**Hata Önleme:**
- Cihaz kimlik doğrulama, kota yönetimi, veri kalitesi kontrolleri.

## Faz 24 — Büyük Veri & Analitik
**Amaç:** Uzun vadeli depolama, trend ve tahmin analizleri.

**İçerik:**
- Veri gölü (Hadoop/Spark veya eşdeğer), ETL/ELT.
- Tahminleme modelleri (mevsimsellik, risk trendleri).
- Veri kataloğu ve şema evrimi.

**Nasıl Çalışır:** Operasyonel veri periyodik olarak veri gölüne aktarılır; kimliksizleştirme uygulanır; panolara beslenen özet tablolar oluşturulur.

**Ne İşe Yarar:**
- Politika/strateji için kanıta dayalı içgörü.

**Hata Önleme:**
- Veri kalite kuralları, sürüm takip, PII maskeleme.

## Faz 25 — Uluslararasılaştırma & Çok Dilli (i18n) ve Çoklu Tenant
**Amaç:** Sistemi çok dilli ve çok kurum/ülke için ölçeklemek.

**İçerik:**
- Dil dosyaları (TR, EN, AZ, RU, vb.), tarih-sayı/ölçü birimi yerelleştirme.
- Tenant izolasyonu, saat dilimi desteği.

**Nasıl Çalışır:** Tüm metinler i18n dosyalarından gelir; tenant bazlı tema/konfig uygulanır; saat dilimleri kullanıcı profiline göre işlenir.

**Ne İşe Yarar:**
- Ulusal/uluslararası yaygın kullanım; farklı kurumlara kiralanabilir yapı.

**Hata Önleme:**
- Çeviri kapsam testleri; tenant veri sızıntısı testleri.

## Faz 26 — Stratejik Yönetim & Politika
**Amaç:** Uzun vadeli planlama, kapasite ve bütçe yönetimi.

**İçerik:**
- Yıllık hedefler, tatbikat/eğitim planları, bütçe ve kaynak yönetimi.
- Kapasite analizi (insan gücü, ekipman, eğitim düzeyi).
- Strateji panelleri ve politika dokümanları.

**Nasıl Çalışır:** Faz 20 ve 24’ten gelen göstergelerle yönetim panelleri oluşur; zayıf alanlar için politika önerileri çıkar; yıllık planlar takvime yazılır.

**Ne İşe Yarar:**
- Kurumun afet hazırlığı sürekli iyileşir; kaynaklar doğru ölçeklenir.

**Hata Önleme:**
- Hedef–gerçekleşen karşılaştırmaları; sapma alarmları; revizyon süreçleri.

## Hareketsizlik Güvenlik Kuralı (Sistem Geneli Kullanım)
- **Tanım:** Görevdeki personel cihazından 120 saniye boyunca hız/ivme/konum değişimi gelmezse "Hareketsizlik" olarak işaretlenir.
- **Algoritma:**
  1. Son ping zamanını ve son ivme/hız değerlerini sakla.
  2. `now - last_motion_ts ≥ 120` saniye ve `status = IN_PROGRESS` ise tetik oluştur.
  3. Tetik → Faz 7 kural motoru: "Önce personele push bildirimi (15 sn cevap penceresi) → lider/eş ekibe bildirim → OpsCenter alarm."
  4. Cevap yoksa SOS-escalation (sesli alarm, SMS, çağrı).
- **Yanlış Pozitif Azaltma:**
  - "Kısa mola" modu (kullanıcı 5–10 dk pasifleştirme talep eder, lider onaylı).
  - Düşük pilde ping aralığı artar ama ivme sensörü yerelde kontrol eder.
  - Kapalı alan/lokasyon hatası için hız/ivme ağırlıklı karar (tek başına GPS değil).
- **Kayda Geçirme:** Alarm, kapanma nedeni (ör. "yanıt verdi", "yanıt yok – ekip gönderildi") ve süresiyle audit log’a düşer.

## Güncelleme Prosedürü

1. Değişiklik yapmak isteyen ekip, ilgili faz başlığının altında yer alan içeriği etkileyen noktaları `CHANGELOG.md` taslağına ekler.
2. Pull request açılmadan önce README’deki tablo, karar veya süreç güncellemeleri için ilgili bölümde revizyon tarihini (örn. _Güncelleme: 2024-06-30_) belirtmek zorundadır.
3. Doküman değişiklikleri, kod değişiklikleriyle aynı PR içinde tutulur; bağımsız doküman güncellemeleri için `docs` etiketi kullanılır.
4. Onaylanan PR sonrasında ürün sahibi, yeni sürümü `release-notes/` dizinine işler ve README’yi sürüm numarasıyla etiketler.

## Terimler Sözlüğü

| Terim | Açıklama |
| --- | --- |
| **OpsCenter** | Harita ve operasyon merkezi; Faz 6’da tanımlanan canlı yönetim paneli. |
| **Tenant** | İl veya kurum bazlı izole müşteri ortamı. |
| **PMTiles** | Harita verilerini çevrimdışı saklamayı sağlayan paket formatı. |
| **GeoJSON BBOX** | Harita API’lerinde kullanılan dikdörtgen koordinat filtresi. |
| **Dry-run** | Kural motorunda aksiyon üretmeden tetikleri test etme modu. |
| **Idempotency-Key** | Offline/edge senaryolarında mükerrer yazımı engelleyen istek kimliği. |
| **ICS** | Incident Command System standart form seti (201/202/204/205/206). |

## Belge Versiyon Geçmişi

| Sürüm | Tarih | Kapsam | Onaylayan |
| --- | --- | --- | --- |
| v0.1 | 2024-06-15 | İlk faz dokümantasyonu ve temel teknolojik kararların aktarılması | Ürün Sahibi |
| v0.2 | 2024-06-28 | Yönetişim çerçevesi, RFC süreci ve operasyon runbooklarının eklenmesi | Teknik Lider |
| v0.3 | 2024-07-02 | SLA/SLO hedefleri, denetim takvimleri ve kriz iletişim planının genişletilmesi | Operasyon Direktörü |
| v0.4 | 2024-07-05 | Güvenlik, uyum, veri yönetişimi ve saha süreçlerinin kapsamlı olarak entegrasyonu | Güvenlik Komitesi |
| v0.5 | 2024-07-06 | Dokümantasyonu destekleyen dizin iskeletleri ve kayıt şablonlarının oluşturulması | Teknik Operasyon Ekibi |
| v0.6 | 2024-07-07 | Knowledge base, veri paylaşım, uyum ve güvenlik kayıtları için tam klasör yapısının ve örnek dosyaların yayımlanması | Yönetişim Kurulu |
| v0.7 | 2024-07-08 | Operasyonel runbook’ların faz referanslı, ölçülebilir prosedürlerle genişletilmesi | Operasyon Direktörü |
| v0.8 | 2024-07-09 | Dijital ikiz veri seti/ senaryo şablonları ve alarm odaklı runbook genişletmeleri | Güvenlik & Ops Kurulu |
| v0.9 | 2024-07-10 | Test yönetişimi, kapsama matrisi ve OpsCenter benchmark raporlarının yayımlanması | QA & Performans Ekibi |
| v0.10 | 2024-07-11 | Etik kurul ajandası, personel refah artefaktları ve topluluk katılım kayıtlarının eklenmesi | İnsan & Topluluk Konseyi |
| v0.11 | 2024-07-12 | Gözlemlenebilirlik programı, alarm kuralları ve servis sağlık checklist’lerinin yayımlanması | Observability Ekibi |
| v0.12 | 2024-07-13 | Metrik kataloğu, SLO kayıt defteri ve inceleme döngüsü artefaktlarının eklenmesi | Observability Ekibi |
| v0.13 | 2024-07-14 | On-call vardiya yönetişimi ile kaos mühendisliği dayanıklılık programının yayımlanması | Operasyon & Dayanıklılık Kurulu |
| v0.13.1 | 2024-07-15 | ICS yayın takvimi dosyalarının metin olarak işlenmesi ve PR otomasyonundaki ikili dosya hatasının giderilmesi | Yapılandırma Sahibi |
| v0.14 | 2024-07-16 | “Devam et” talimatlarıyla oluşturulan klasör yapılarını açıklayan rehberin yayımlanması | Yönetişim Kurulu |
| v0.15 | 2024-07-17 | Siber sigorta program rehberi ve delil zinciri işletim kılavuzunun eklenmesi, yönetişim rehberinin güncellenmesi | Risk & Güvenlik Ekibi |
| v0.16 | 2024-07-18 | Mühendislik uygulama rehberlerinin yayımlanması ve yönetişim bölümlerinin güncellenmesi | Teknik Liderlik |
| v0.17 | 2024-07-19 | Kodlama standartları, statik analiz ve bağımlılık yönetimi politikalarının dokümante edilmesi | Teknik Liderlik |
| v0.18 | 2024-07-20 | Kod kalite araç konfigürasyonlarının eklenmesi ve ilgili rehber bağlantılarının güncellenmesi | Teknik Liderlik |
| v0.18.1 | 2024-07-21 | İkili placeholder dosyaların CSV/Markdown formatına taşınması ve yönetişim kayıtlarının güncellenmesi | Teknik Liderlik |
| v0.19 | 2024-07-22 | PR öncesi kontrol listesi ve ikili dosya tarama script’inin eklenmesi, ilgili rehber bağlantılarının güncellenmesi | Teknik Liderlik |
| v0.20 | 2024-07-23 | Toplu kalite suite script’inin eklenmesi, README/Devam Et rehberi ve mühendislik belgelerinin güncellenmesi | Teknik Liderlik |
| v0.21 | 2024-07-24 | PHP kalite araçları için Composer bağımlılıklarının eklenmesi ve konfigürasyon kapsamının dinamikleştirilmesi | Teknik Liderlik |
| v0.22 | 2024-07-25 | Kalite suite’in Composer bağımlılıklarını otomatik kuracak şekilde güncellenmesi ve ilgili rehberlerin revizyonu | Teknik Liderlik |
| v0.23 | 2024-07-26 | Yerel geliştirme rehberinin yayımlanması ve mühendislik/Devam Et kayıtlarının güncellenmesi | Teknik Liderlik |

> _Not: Yeni bir sürüm yayımlandığında bu tabloya satır eklenmeli ve ilgili bölümlerde revizyon tarihi güncellenmelidir._

## Referanslar & Kaynaklar

- `docs/changelog/` dizinindeki sürüm notları (her sprint sonrası güncellenir).
- `docs/rfc/` altında tutulan karar kayıtları ve onaylanmış mimari öneriler.
- Operasyonel runbook’lar için `runbooks/` dizini ve OpsCenter iç rehberleri.
- Güvenlik ve uyum takipleri için Sentry, Prometheus/Grafana ve denetim rapor depoları.
- Harici standartlar: AFAD yönetmelikleri, INSARAG kılavuzları, KVKK rehberleri.

