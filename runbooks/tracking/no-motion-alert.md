# Hareketsizlik Alarmı Müdahale Runbook'u

## Amaç
Faz 4 canlı takip modülünde 120 sn üzeri hareketsizlik tespit edilen personel için doğrulama, iletişim ve yardım süreçlerini standardize etmek.

## Kapsam
- Mobil uygulama pingleri (GPS, hız, ivme)
- OpsCenter alarm konsolu
- SOS ve kural motoru tetikleri

## Tetikleyiciler
- Kural motoru `tracking.no_motion` olayı
- OpsCenter manuel alarm kaydı
- Saha liderinden gelen telefon veya telsiz uyarısı

## Hazırlık Kontrol Listesi
1. Alarmın tenant bilgisi ve görev ID'sini doğrulayın.
2. Personelin görev statüsünü `tasks` tablosundan `IN_PROGRESS` olduğunu teyit edin.
3. Personelin belge geçerliliğini `docs/training/README.md` üzerinden kontrol edin (süre aşımı varsa not alın).
4. Hareketsizlik tolerans modu (kısa mola) aktif mi `tracking_flags` tablosundan bakın.

## Müdahale Adımları
1. **İlk Doğrulama (≤ 1 dk)**
   - Son ping zaman damgasını ve cihaz batarya seviyesini OpsCenter’dan görüntüleyin.
   - Harita katmanında (Faz 6) son konumu ve geofence durumunu kontrol edin.
2. **Personel ile Temas (≤ 2 dk)**
   - Mobil uygulamaya push bildirimi gönderin; 15 sn cevap penceresinde onay bekleyin.
   - Cevap yoksa kayıtlı telefon numarasını arayın.
3. **Ekip Eskalasyonu (≤ 5 dk)**
   - Görev liderini bilgilendirin; en yakın ekip üyesini bölgeye yönlendirin.
   - Sağlık riski varsa 112 ve kurum sağlık sorumlusuna haber verin.
4. **Konum Doğrulama (≤ 7 dk)**
   - En yakın ekip pinglerini inceleyin; hareketsiz personelin bulunduğu konuma yaklaşımı doğrulayın.
   - Drone veya kamera desteği gerekiyorsa `digital-twin/scenarios/earthquake-ops.md` göre plan yapın.
5. **Alarm Kapatma (≤ 10 dk)**
   - Personel iyi durumda ise OpsCenter’da alarmı "Çözüldü" olarak işaretleyin, nedeni yazın.
   - Tıbbi müdahale gerekiyorsa `postmortem/initial.md` kaydı açın ve olay şiddetini Seviye 4’e yükseltin.

## Başarı Kriterleri
- İlk insan teması ≤ 2 dk içinde gerçekleşti.
- Alarm durumuna uygun aksiyon planı ve kayıt oluşturuldu.
- Personel durumu doğrulandı ve audit log güncellendi.

## Kayıt ve Raporlama
- `docs/threat-program/reports/2024-07-summary.md` içine olay özeti ekleyin.
- KPI takibi için `Saha Operasyon Performans Puan Kartı` bölümündeki yanıt süresi metriklerini güncelleyin.
- Yanlış pozitif ise `feedback/inbox/README.md` prosedürüne göre kullanıcıdan geri bildirim alın.

## Tatbikat
- Hareketsizlik alarm tatbikatları haftalık mobil eğitim oturumlarında (Çarşamba 14:00) canlandırılır.
- Tatbikat sonuçları `training/attendance.csv` dosyasına işlenir.
