# Etik Kurul Yönetişimi

Bu belge, TUDAK Afet Yönetim Sistemi içinde etik ilkelerin nasıl denetlendiğini, karar alma süreçlerinin hangi artefaktlarla desteklendiğini ve etik kurul çıktılarını hangi fazlara bağladığımızı açıklar.

## Amaçlar
- Yapay zekâ ve otomasyon kararlarının şeffaflığını korumak (Faz 15, Faz 7).
- Vatandaş, gönüllü ve personel verilerinin sorumlu kullanımını doğrulamak (Faz 17, Faz 21).
- Operasyonel süreçlerde ortaya çıkan etik riskleri erken tespit etmek ve kayıt altına almak.

## Kurul Yapısı
| Rol | Sorumluluklar | Temsil Ettiği Fazlar |
| --- | --- | --- |
| Ürün Sahibi | Etik gereksinimleri backlog’a taşır, kabul kriterlerini onaylar | Faz 3, Faz 6 |
| Güvenlik Ekibi | Veri koruma ve tehdit modeli değerlendirmelerini sunar | Faz 1, Faz 22 |
| Hukuk Danışmanı | Mevzuat uyumu ve vatandaş hakları değerlendirmesini yapar | Faz 21, Faz 17 |
| Yapay Zekâ Lideri | Model kararlarını ve açıklanabilirlik raporlarını sunar | Faz 15 |
| Topluluk Temsilcisi | Geri bildirim platformundan gelen etik vakaları paylaşır | Faz 17, Topluluk bölümü |

## Gündem Hazırlığı
1. İlgili faz ekipleri, etik etkisi olan yeni özellikleri RFC sürecinde işaretler.
2. Kurul sekreteri, `docs/ethics/review-schedule.md` dosyasında yer alan ajandayı günceller.
3. İncelenecek kararlar için gerekli destekleyici belgeler (model kartları, DPIA, veri maskeleme checklistleri) toplantıdan 48 saat önce paylaşılır.

## Çıktılar
- Onaylanan kararlar `architecture/rfc-decisions.csv` dosyasında etik sütunu doldurularak kaydedilir.
- İyileştirme gerektiren aksiyonlar `governance/risk-register.csv` içinde etik risk etiketiyle takip edilir.
- Vatandaş veri haklarıyla ilgili sonuçlar `docs/community/README.md` bölümündeki iletişim planına göre duyurulur.

## Ölçüm & İzleme
- Kurul kararlarının uygulanma oranı aylık olarak `docs/ethics/review-schedule.md` içinde raporlanır.
- Etik ihlal bildirimleri `security/incidents/README.md` sürecine bağlı olarak kapatılır.
- Yapay zekâ karar destek bileşenlerinin etik uyum skorları `docs/ml/models.yaml` dosyasında güncellenir.

## Hata Önleme
- Onaysız etik karar içeren özellikler üretim pipeline’ında otomatik olarak bloklanır.
- Kurul toplantısı yapılmadan riskli özellik yayına alınamaz; acil durumlarda olay sonrası 24 saat içinde geriye dönük inceleme zorunludur.
- Etik kayıtları eksik olan runbook’lar (örn. hareketsizlik alarmı) yayınlanmadan önce `docs/tests/matrix.md` denetiminden geçer.
