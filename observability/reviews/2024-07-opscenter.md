# 2024-07 OpsCenter İzleme İncelemesi

- **Tarih:** 2024-07-12
- **Katılımcılar:** Observability Ekibi, OpsCenter Vardiya Liderleri, SRE On-call
- **Kapsam:** OpsCenter Web uygulaması, WebSocket canlı takip, kural motoru teslimleri, alarm hijyeni

## Öne Çıkan Bulgular

1. `api_latency_p95` değeri benchmark sırasında 1180 ms’ye yükseldi fakat SLO sınırının altında kaldı.
2. `ws_delivery_success` metriğinde kısa süreli %0.8’lik düşüş gözlendi; edge node tamponu genişletildi.
3. `alert_fatigue_index` %14’e çıktı; hareketsizlik alarmı yanlış pozitiflerine yönelik manuel analiz yapıldı.

## Alınan Aksiyonlar

- `observability/alerts/opscenter.yml` dosyasında hareketsizlik alarmı için 3 dakikalık doğrulama penceresi eklendi.
- `runbooks/tracking/no-motion-alert.md` adımları 2FA doğrulama çağrısı ile güncellenecek (RFC-2024-017 taslağı açıldı).
- `observability/capacity-journal.md` dosyasına WS bağlantı pik değeri işlendi.

## Takip Öğeleri

| Öğe | Sorumlu | Termin | Not |
| --- | --- | --- | --- |
| `alert_fatigue_index` değerini %8’in altına çekmek | Observability Ekibi | 2024-08-01 | Yanlış pozitif analizi tamamlanıp eşik revizyonu yapılacak. |
| WebSocket tampon optimizasyonunun load test tekrarı | Performans Ekibi | 2024-07-20 | `docs/tests/matrix.md` senaryosu `LOAD-WS-02` güncellenecek. |
| Alarm eskalasyonunda mobil push fallback testi | Mobil Takım | 2024-07-18 | Tatbikat planına eklenmeli. |

## Ekler

- `analytics/benchmark/2024-07-10-opscenter.md`
- `observability/slo-register.md`
- Grafana panel: `opscenter-latency`
