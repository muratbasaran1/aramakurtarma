# Envanter Transferi Runbook

| Alan | Bilgi |
| --- | --- |
| İlgili Fazlar | Faz 3 (Envanter Yönetimi), Faz 5 (QR & Yoklama), Faz 18 (Lojistik) |
| Son Gözden Geçirme | 2024-07-08 |
| Sorumlu Takım | Lojistik Operasyon Ekibi |

## Amaç ve Kapsam
Depolar arası kritik ekipman transferinin (araç, jeneratör, ilk yardım seti)
standartlara uygun şekilde planlanması, QR tabanlı zimmetlenmesi ve teslim zincirinin
tamamlanmasını sağlar. İşlem boyunca chain-of-custody kayıtlarının güncel tutulması zorunludur.

## Tetikleyiciler
- OpsCenter’dan yeni görev için ek ekipman talebi.
- Depo stok seviyesinin kritik eşiğin altına düşmesi (`reports/performance/...`).
- Lojistik tatbikatı (Faz 18) kapsamında planlı transfer.

## Ön Koşullar
1. Transfer talep formu `open-data/requests/REQUEST_TEMPLATE.md` referans alınarak dolduruldu.
2. Kaynak ve hedef depo sorumluları `docs/stakeholders/contacts.csv` üzerinde teyitli.
3. Envanter kalemleri `governance/license-inventory.csv` ve `chain-of-custody.csv`de kayıtlı.

## Adımlar
### 1. Planlama (0–30 dk)
1. OpsCenter talebi `ops/weekly-ops-briefing.md` üzerinden doğrular.
2. Transfer edilecek kalemler `inventory.status=active` koşuluyla listelenir.
3. Ulaşım rotası `forms/incident.json` alanlarıyla uyumlu şekilde belirlenir.

### 2. Hazırlık (30–60 dk)
1. Her ekipman için geçici QR kodu `Faz 5` yönergelerine göre üretilir.
2. `kvkk-requests/README.md` kontrol edilerek PII içeren belgelerin maskelemesi yapılır.
3. Araç ve personel listesi `training/attendance.csv` ile eşleştirilerek sertifikasyon
   tarihleri kontrol edilir.

### 3. Çıkış İşlemi (60–90 dk)
1. Kaynak depoda fiziksel sayım yapılır ve `chain-of-custody.csv` dosyasında "dispatch" kaydı açılır.
2. QR kodları okutularak zimmet `mobile app` üzerinden teyit edilir; kopyası `archive/sms/`
   ve `archive/mail/` dizinlerine kaydedilir.
3. Araç çıkışı `observability/capacity-journal.md` üzerinde yakıt ve kilometre bilgisiyle not edilir.

### 4. Transfer Takibi (90–150 dk)
1. Canlı konum takibi `Faz 4` pingleriyle izlenir, geofence ihlali olup olmadığı kontrol edilir.
2. Gecikme durumunda OpsCenter’a haber verilir; alternatif rota değerlendirilir.
3. Kritik stok alarmı devam ediyorsa `docs/threat-program/README.md` tetikleri
   devreye alınır.

### 5. Varış ve Teslim (150–210 dk)
1. Hedef depoda QR kodlar tekrar okutulur, `chain-of-custody.csv`de "receive" kaydı tamamlanır.
2. Envanter durumu `service` veya `active` olarak güncellenir.
3. Transfer raporu `reports/performance/2024-06-performance.md` formatına göre hazırlanır
   ve `reports/archive/` dizinine eklenir.

## İletişim Planı
| Rol | Kanal | Not |
| --- | --- | --- |
| Lojistik Lider | Slack #logistics | Plan onayı |
| OpsCenter | PagerDuty | Görev güncellemesi |
| Saha Ekibi | Mobil Uygulama | QR zimmet teyidi |
| Finans | E-posta | Maliyet raporu |

## Kontrol Listesi
- [ ] Kaynak depoda zimmet formu imzalandı.
- [ ] Araç takip cihazı aktif.
- [ ] Hedef depoda teslim tutanağı sisteme yüklendi.
- [ ] Transfer sonrası stok seviyeleri güncellendi.

## Başarı Kriterleri
- Transfer süresi planlanan rota süresinin %120’sini aşmamalı.
- QR zimmet hatası 0.
- Finansal kayıt 24 saat içinde güncellendi.

## Referanslar
- `chain-of-custody.csv`
- `docs/field/ppe-checklist.md`
- `reports/performance/README.md`
- `docs/governance/README.md`
