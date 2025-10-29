# Hareketsizlik Yüksek Risk Playbook'u

## Amaç
Faz 4 hareketsizlik alarmının yüksek riskli ekip üyelerinde tetiklenmesi durumunda, tehdit istihbaratı ile korelasyon yaparak olası sabotaj veya güvenlik ihlali senaryolarını değerlendirmek.

## Tetikleyiciler
- `tracking.no_motion` alarmı + personel sağlık durumu "riskli" etiketi
- Aynı bölgede son 30 dk içinde raporlanan güvenlik tehdidi (threat intel kaydı)
- SOS alarmına cevap alınamaması

## Adımlar
1. **Alarm Doğrulaması**
   - `runbooks/tracking/no-motion-alert.md` adımlarını eşzamanlı yürütün.
   - Personel bilgilerini `docs/training/README.md` üzerinden kontrol edin.
2. **Tehdit Korelasyonu**
   - Son 2 saat içinde gelen IOC ve tehdit raporlarını `security/threat-intel-register.md` ile karşılaştırın.
   - Bölgeye ait fiziksel tehdit veya sabotaj uyarısı var mı `docs/threat-program/reports/` altında arayın.
3. **Eskalasyon**
   - Risk seviyesini Seviye 4 olarak işaretleyin, Güvenlik Direktörü'ne PagerDuty.
   - Yerel kolluk kuvvetleri ve saha güvenlik ekibini bilgilendirin.
4. **Delil Toplama**
   - `chain-of-custody.csv` üzerinde ekipman kayıtlarını güncelleyin.
   - OpsCenter harita snapshot'ını alın, `reports/archive/` altında saklayın.
5. **Raporlama**
   - Olay notlarını `security/incidents/README.md` prosedürüne göre kaydedin.
   - Faz 7 kural motoru tetik geçmişini `docs/threat-program/lessons-learned.md` içine ekleyin.

## Başarı Kriterleri
- Tehdit korelasyonu 10 dk içinde tamamlandı.
- Gereken güvenlik ve kolluk bildirimleri yapıldı.
- Delil zinciri tam ve audit log ile eşleşti.

## Tatbikat
- Aylık purple team tatbikatında bu playbook test edilir, sonuçlar `docs/threat-program/reports/2024-07-summary.md` dosyasına yazılır.
