# OpsCenter Benchmark — 2024-07-10

## Bağlam
- **Faz**: Faz 6 — OpsCenter (Faz 4 canlı takip entegrasyonları dahil)
- **Ortam**: Staging (v0.9 aday build)
- **Veri**: 250 aktif olay, 1.200 görev, 45k ping kaydı, 30 harici katman

## Senaryolar
| Senaryo | Açıklama | Concurrency | Süre |
| --- | --- | --- | --- |
| MAP-REFRESH-01 | BBOX filtreli olay + görev katmanı yükleme | 200 eş zamanlı istek | 10 dk |
| LIVE-PING-02 | WebSocket alarm konsolu + ping yayını | 1.2k WS istemci | 15 dk |
| SNAPSHOT-03 | Harita snapshot üretimi (PMTiles çevrimdışı) | 50 eş zamanlı istek | 5 dk |

## Sonuçlar
- Ortalama REST yanıt süresi: **480 ms** (hedef ≤ 500 ms)
- 95p REST yanıt süresi: **820 ms** (hedef ≤ 900 ms)
- WebSocket ping işleme gecikmesi: **240 ms** (hedef ≤ 300 ms)
- Harita snapshot üretim süresi: **14 sn** (hedef ≤ 20 sn)
- Hata oranı: **%0.6** (hedef ≤ %1)

## Karşılaştırma
- 2024-06-20 benchmark’ına göre REST 95p’de %8 iyileşme.
- WS gecikmesinde %3 artış (sebep: yeni alarm doğrulama penceresi); `observability/capacity-journal.md` güncellendi.

## Riskler & Bulgu Listesi
- `tech-debt/backlog.csv` → `OPS-128` maddesi: WS gecikmesini azaltmak için alarm doğrulama throttle ayarı.
- `runbooks/opscenter/alarm-console-escalation.md` → Adım 3.2’de yeni latency metriği ile uyum doğrulandı.

## İyileştirme Planı
1. Kural motoru WebSocket outbox flush intervalini 250 ms’den 200 ms’ye düşür (RFC-2024-07-12).
2. PMTiles cache ön yüklemesini `infra/rate-limit.tf` profilinde `opscenter_snapshot` anahtarı ile etiketle.
3. Sonraki benchmark: 2024-07-24 (OpsCenter + Faz 20 dashboard bileşik testi).

## Onay
- **Sorumlu**: Performans Ekibi Lideri
- **Gözden Geçirme Tarihi**: 2024-07-11 (OpsCenter Çekirdek Ekibi)
