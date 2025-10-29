# Deprem Operasyonu Dijital İkiz Senaryosu

## Amaç
Faz 16 simülasyon tatbikatlarında, Faz 4 canlı takip ve Faz 6 OpsCenter modüllerinin deprem senaryosunda birlikte çalışmasını doğrulamak.

## Senaryo Özeti
- **Olay Türü:** Mw 6.8 deprem
- **Tenant:** İzmir
- **Zaman Çizelgesi:** 0–72 saat
- **Katmanlar:** Olay poligonu, artçı sarsıntı heatmap, lojistik depo konumları, gönüllü toplama alanları

## Veri Gereksinimleri
- `digital-twin/datasets/catalog.yaml` > `dt-incident-polygons`
- Tracking simülasyon verisi (`runbooks/tracking/no-motion-alert.md` test dataset)
- Envanter hareket logları (`runbook/inventory-transfer.md` tatbikat sürümü)

## Tatbikat Adımları
1. **Başlangıç (T0)**
   - Incident oluşturulur (`status=ACTIVE`), poligon OpsCenter'a işlenir.
   - Görevler planlanır, hareketsizlik toleransı 60 sn'ye düşürülür.
2. **İlerleme (T+6 saat)**
   - Artçı sarsıntı overlay eklenir, risk puanları kural motoruna gönderilir.
   - 2 ekip için hareketsizlik alarmı canlandırılır.
3. **Lojistik (T+12 saat)**
   - Depo stokları transfer edilir, QR yoklaması tetiklenir.
   - Edge node kesintisi simüle edilerek offline kuyruk runbook'u test edilir.
4. **Raporlama (T+24 saat)**
   - OpsCenter snapshot alınır, ICS-201 formu `reports/ics/` altına eklenir.
   - Tehdit istihbaratı ile güvenlik riski kontrol edilir.
5. **Kapanış (T+72 saat)**
   - Playback modülüyle ekip hareketleri incelenir.
   - Öğrenilen dersler `docs/threat-program/reports/2024-07-summary.md` dosyasına not düşülür.

## Başarı Kriterleri
- OpsCenter degrade moduna geçmeden 500 eş zamanlı ping işlenir.
- Hareketsizlik alarmı yanıt süresi ortalaması ≤ 3 dk.
- Edge kuyruk senkronizasyonu yeniden bağlantıdan sonra ≤ 10 dk içinde tamamlanır.

## Notlar
- Senaryoda kullanılan veriler gerçek tenant verilerinden maskelenmiş olmalıdır (`open-data/releases/v2024.07/masking-checklist.md`).
- Tatbikat sonuçları `docs/retrospective/README.md` altındaki çerçeveye göre değerlendirilir.
