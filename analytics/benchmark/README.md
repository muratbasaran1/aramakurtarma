# Performans Benchmark Programı

Bu dizin, Faz 6 OpsCenter, Faz 4 Canlı Takip ve Faz 20 Dashboard hedeflerine yönelik performans kıyas çalışmalarını içerir.

## İçerik Şablonu

Her benchmark raporu aşağıdaki bölümleri içermelidir:

1. **Bağlam** — Ölçülen bileşen, ortam ve kullanılan veri seti.
2. **Senaryolar** — Kullanıcı akışı, concurrency seviyesi, ping frekansı gibi metrikler.
3. **Sonuçlar** — Ortalama/95p yanıt süresi, kaynak kullanımı, hata oranı.
4. **Karşılaştırma** — Bir önceki benchmark ile delta ve hedef SLA/SLO kıyaslaması.
5. **İyileştirme Planı** — Ops veya geliştirme backlog’una gönderilecek aksiyonlar.

## Rapor Adlandırma

```
analytics/benchmark/YYYY-MM-DD-<bilesen>.md
```

Örnek: `analytics/benchmark/2024-07-10-opscenter.md`

## Veri Kaynakları

- `observability/capacity-journal.md`
- `runbooks/opscenter/alarm-console-escalation.md`
- `docs/tests/matrix.md`

## Güncelleme Döngüsü

- Major release öncesi (minimum ayda bir) yeni benchmark raporu eklenmelidir.
- Benchmark sonuçları `release-notes/` ve `CHANGELOG.md` dosyalarına özetlenir.
