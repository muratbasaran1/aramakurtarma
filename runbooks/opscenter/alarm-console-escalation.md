# OpsCenter Alarm Konsolu Eskalasyon Runbook'u

## Amaç
OpsCenter alarm konsoluna düşen kritik bildirimlerin (hareketsizlik, geofence ihlali, SOS, ping kaybı) hızlıca değerlendirilip doğru ekiplere yönlendirilmesini sağlamak.

## Kapsam
- Faz 4 canlı takip pingleri
- Faz 6 OpsCenter alarm konsolu
- Faz 7 kural motoru tetikleri

## Alarm Kategorileri
| Kategori | Örnek Olay | Varsayılan Şiddet |
| --- | --- | --- |
| Hareketsizlik | 120 sn hareket yok, görev durumu IN_PROGRESS | Seviye 3 |
| Geofence İhlali | Görevli ekip poligon dışı | Seviye 2 |
| SOS | Personel manuel SOS butonu | Seviye 4 |
| Ping Kaybı | 3 ardışık ping eksik, cihaz batarya uyarısı | Seviye 2 |

## Hazırlık Kontrolleri
1. OpsCenter dashboard P95 yanıt süresi ≤ 3 sn (aksi halde degrade runbook'una geçin).
2. Alarm operatörünün 2FA oturumu aktif ve audit log kaydı açık.
3. İlgili tenant için iletişim listesi `docs/stakeholders/contacts.csv` ile güncel.
4. Ops ekip telsiz kanalı ve mobil push sistemi hazır.

## İşleyiş Adımları
1. **Alarmı Açma (≤ 1 dk)**
   - Alarm detayı kartını açın, olay ID ve görev ID not alın.
   - Şiddet seviyesini doğrulayın; anormallik varsa yükseltin/düşürün.
2. **Durum Doğrulama (≤ 2 dk)**
   - Hareketsizlik için son ping ve hız/ivme değerini görüntüleyin.
   - Harita üzerinde son bilinen konumu ve rota sapmasını inceleyin.
   - Gerekirse `runbooks/tracking/no-motion-alert.md` adımlarını tetikleyin.
3. **İletişim (≤ 3 dk)**
   - Tenant saha liderini arayın; cevap alınamazsa yedek kişiye geçin.
   - Ekip cihazına push bildirimi gönderin, 15 sn cevap penceresini başlatın.
   - SOS durumunda derhal 112 ve kurum güvenlik hattını arayın.
4. **Aksiyon Kaydı (≤ 5 dk)**
   - OpsCenter konsolunda alarm notunu güncelleyin, kimlerle konuştuğunuzu yazın.
   - `chain-of-custody.csv` üzerinde fiziksel ekipman (ör. drone) gönderildiyse kayıt altına alın.
   - Gerekirse envanter transferi için `runbook/inventory-transfer.md` runbook’unu tetikleyin.
5. **Kapatma & Raporlama (≤ 10 dk)**
   - Alarm nedeni çözüldüğünde OpsCenter kartını kapatın.
   - `reports/performance/2024-06-performance.md` benzeri rapora KPI sapmasını ekleyin.
   - Şiddet 3+ alarmı için post-incident notu `postmortem/initial.md` dosyasına girin.

## İletişim Matrisi
| Paydaş | Kanal | SLA |
| --- | --- | --- |
| Saha Lideri | Telefon + OpsCenter push | ≤ 2 dk ilk yanıt |
| Operasyon Direktörü | PagerDuty | ≤ 5 dk |
| Güvenlik Ekibi | Şifreli e-posta + Slack | Seviye 4 olaylarda derhal |

## Başarı Kriterleri
- Alarm yanıt süresi hedefleri sağlandı.
- Sahadan doğrulama alındı ve yanlış pozitifler kayıt altına alındı.
- İlgili runbook'lar tetiklendi ve sonuçları `docs/threat-program/reports/` altında raporlandı.

## Tatbikat
- Haftalık masa başı tatbikatında (Cuma 10:00) 2 örnek alarm senaryosu canlandırılır.
- Tatbikat çıktıları `docs/tatbikat/2024-06-report.md` formatında güncellenir.
