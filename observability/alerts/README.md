# Alarm Politikaları

Bu klasör, TUDAK Afet Yönetim Sistemi için Prometheus, Grafana Mimir veya PagerDuty benzeri platformlarda çalıştırılan alarm kurallarını içerir. Tüm kurallar Faz 4 (Canlı Takip), Faz 6 (OpsCenter), Faz 7 (Kural Motoru) ve Faz 11 (DevOps & İzlenebilirlik) gereksinimlerine referans verir.

## Dosya Yapısı

- `opscenter.yml` — OpsCenter ve canlı izleme bileşenleri için metrik tabanlı alarmlar.
- `README.md` — Bu dosya.

## Değişiklik Süreci

1. Yeni bir alarm önerisi RFC sürecinden (`docs/rfc/`) geçirilmelidir.
2. Alarm mantığı test ortamında 72 saat boyunca gözlemlenir.
3. Yanlış pozitif/negatif analizi `observability/service-health-checklist.md` üzerinden kayıt altına alınır.
4. Onaylanan alarmlar için changelog ve `README.md` güncellenir, runbook bağlantıları doğrulanır.

## Eskalasyon

- Kritik (P1) alarmlar → 5 dk içinde OpsCenter vardiya liderine, 10 dk içinde Faz 7 kural motoru sorumlusuna.
- Yüksek (P2) alarmlar → 15 dk içinde SRE on-call.
- Orta (P3) alarmlar → Günlük rapor; haftalık değerlendirme toplantısında aksiyon planı belirlenir.
- Düşük (P4) alarmlar → Trend analizi için backlog kaydı.

## Referanslar

- `runbook/incident-response.md`
- `runbook/opscenter-degradation.md`
- `runbooks/opscenter/alarm-console-escalation.md`
- `docs/tests/README.md`
- `observability/metrics-catalog.md`
- `observability/slo-register.md`
- `observability/reviews/`
