# Topluluk ve Geri Bildirim Platformu Yönetişimi

Bu rehber, vatandaş, gönüllü ve paydaş kurumlarla yürütülen iletişim döngülerinin nasıl standartlaştırıldığını ve README’deki topluluk KPI’larının hangi kaynaktan beslendiğini açıklar.

## Kanallar
- **Geri Bildirim Portalı:** Vatandaş & gönüllü bildirimleri, `feedback/inbox/README.md` ile uyumlu şekilde sınıflandırılır.
- **Bülten & Sosyal Medya:** Haftalık paydaş bülteni ve kriz iletişimi içerikleri `communications/public/` dizinindeki şablonlar üzerinden hazırlanır.
- **Topluluk Buluşmaları:** Çeyreklik saha buluşmaları ve eğitim oturumları `community/engagement-log.csv` dosyasında tarih bazlı olarak kaydedilir.

## Süreç Adımları
1. Gelen geri bildirimler `feedback-tracker.md` üzerinde kimlik bilgileri maskeleme kurallarına göre işlenir.
2. Etik değerlendirme gerektiren konular Etik Kurul gündemine (`docs/ethics/review-schedule.md`) taşınır.
3. Topluluk memnuniyet skorları `surveys/` dizinindeki NPS ve memnuniyet anketlerinden çekilir, README KPI bölümünde raporlanır.
4. Kritik bulgular OpsCenter runbook’larına (örn. alarm eskalasyonu) geri beslenir.

## Artefaktlar
- **Katılım Logu:** `community/engagement-log.csv`
- **İletişim Şablonları:** `communications/public/STATEMENT_TEMPLATE.md`
- **Eylem Planları:** Topluluk kaynaklı aksiyonlar `governance/risk-register.csv` içinde topluluk etiketiyle izlenir.

## Hata Önleme
- Doğrulanmamış bilgiler yayınlanmaz; kaynak teyidi `docs/templates/communications/README.md` sürecinin parçasıdır.
- Geri bildirim yanıt SLA’sı (72 saat) aşılırsa otomatik eskalasyon krizi iletişim planına göre tetiklenir.
- Portal bakımı yapılmadığında (uptime <%98) olay kaydı `security/incidents/README.md` prosedürüne göre açılır.
