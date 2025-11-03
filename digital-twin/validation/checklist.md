# Dijital İkiz Veri Doğrulama Kontrol Listesi

Bu kontrol listesi, dijital ikiz veri setlerinin Faz 24 veri gölü ve Faz 16 simülasyon senaryolarına aktarılmadan önce doğrulanması için kullanılır.

## Ön Koşullar
- Veri seti `digital-twin/datasets/catalog.yaml` içinde tanımlı.
- Maskelenmiş veriler KVKK kurallarına uygun (`open-data/releases/v2024.07/masking-checklist.md`).
- Veri aktarımı için gerekli API anahtarları güncel.

## Kontrol Adımları
1. **Şema Doğrulaması**
   - JSON/YAML şeması `docs/data-catalog/template.yaml` ile uyumlu mu?
   - Zorunlu alanlarda boş değer var mı?
2. **Uzamsal Kalite**
   - Poligon self-intersection testi (`ST_IsValid`).
   - Koordinatlar tenant sınırları içinde mi (`Faz 25` gereği zaman dilimi uyumu kontrolü).
3. **Zaman Serisi Tutarlılığı**
   - Timestamp'ler ISO8601 formatında mı?
   - Sıralı kayıtlar arasında negatif zaman farkı yok.
4. **Performans Testi**
   - Playback modülü ile 10 dk simülasyon koş, P95 render süresi ≤ 2 sn.
   - OpsCenter BBOX sorgusunda cevap süresi ≤ 1.5 sn.
5. **Güvenlik & Erişim**
   - Veri paylaşımı `docs/data-sharing/policy.md` ile uyumlu.
   - Yetkisiz erişim denemesi için audit log kontrolü.

## Onay
| Aşama | Sorumlu | Tarih | Not |
| --- | --- | --- | --- |
| Veri Kalitesi | Veri Yönetim Ekibi | | |
| Güvenlik | Güvenlik Ekibi | | |
| Operasyon | OpsCenter Ekibi | | |

Tamamlanan kontrol listeleri `docs/threat-program/reports/` klasörüne eklenir ve sürüm numarasıyla arşivlenir.
