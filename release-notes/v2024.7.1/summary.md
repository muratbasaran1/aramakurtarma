# v2024.7.1 — 1 Temmuz 2024 Üretim Dağıtımı

## Dağıtım Özeti
- **Kapsam:** Faz 6 OpsCenter iyileştirmeleri (alarm konsolu yanıt süresi, harita önbellekleme optimizasyonu, tenant bazlı görselleştirme netleştirmeleri).
- **Dağıtım Penceresi:** 1 Temmuz 2024, 12:00-13:00 TRT; staging → production ardışık geçiş.
- **Durum:** Başarılı; müşteri bildirimi yapılmadan kesinti gözlenmedi. Operasyon merkezi 10 dakikalık gözlem süresinden sonra tamamen açıldı.
- **Bağımlılıklar:** Faz 4 canlı takip veri akışı ve Faz 7 kural tetikleyicileriyle geriye dönük uyumluluk sağlandı.

## Rollback Planı
1. **Uygulama Sürümü Geri Alma:** OpsCenter web ve websocket katmanını `v2024.6.3` etiketiyle yeniden dağıt; CDN önbelleğini temizle.
2. **Veri Katmanı:** Yeni eklenen alarm önceliklendirme tablosunu `ALTER TABLE ... RENAME TO alarm_priority_v20240701_backup` ile yedekle; eski görünümleri tekrar etkinleştir.
3. **Konfigürasyon:** Feature flag `opscenter.alarm_p95_tuning` kapatılır, `opscenter.map_cache_level` değeri eski `medium` profiline çekilir.
4. **Doğrulama:** Alarm kuyruğu süresi, harita P95 yanıt süresi, kural motoru tetik gecikmesi için gözlemlenebilirlik panelleri kontrol edilir; PagerDuty uyarıları kapatılır.
5. **İletişim:** Ops komutanı, saha koordinatörleri ve devops kanallarına rollback duyurusu gönderir; tenant durum banner’ı güncellenir.

## İzleme Sonrası Notlar
- 30 dakikalık üretim gözleminde p95 alarm kuyruğu süresi 1.4s → 0.9s’e indi; harita cache isabet oranı %12 arttı.
- Hareketsizlik alarmı eskalasyonlarında beklenen oran yakalandı; API hata oranı < %0.2.
- Ek aksiyon: Ağustos (hareketsizlik alarmı analizi) ve Eylül (dijital ikiz etik doğrulaması) fazları onaylandığında aynı klasör yapısıyla sürüm notları açılacak.
