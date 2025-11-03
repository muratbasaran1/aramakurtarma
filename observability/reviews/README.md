# Gözlemlenebilirlik İncelemeleri

Bu dizin, metrik ve alarm gözden geçirme oturumlarının çıktılarını saklar. İncelemeler, README’de tanımlanan haftalık ve çeyreklik döngülerle uyumlu yürütülür.

## Şablon

Her inceleme kaydı aşağıdaki başlıklara sahip olmalıdır:

1. **Kapsam & Tarih** — Ele alınan servisler ve gözden geçirme periyodu.
2. **Öne Çıkan Bulgular** — Ölçülen metrikler, trendler ve dikkat edilmesi gereken sapmalar.
3. **Alınan Aksiyonlar** — Runbook tetikleri, RFC ihtiyaçları veya konfigürasyon değişiklikleri.
4. **Takip Öğeleri** — Açık kalan konular ve sorumlu ekipler.
5. **Ekler** — Grafana panel bağlantıları, SLO kayıtları veya tatbikat raporları.

Yeni dosyalar `YYYY-MM-<konu>.md` formatıyla adlandırılmalı ve changelog’da referanslanmalıdır.
