# Runbook Dizinleri

Runbook kaynakları `runbooks/` dizini ile senkronize tutulur. Bu klasör, inceleme notları ve runbook güncelleme geçmişini saklamak için kullanılır.

## Klasör Yapısı
- `runbooks/incident-response/` — Güvenlik ve acil durum runbook’ları (örn. kimlik bilgisi ihlali)
- `runbooks/maintenance/` — Planlı bakım ve yama süreçleri
- `runbooks/opscenter/` — OpsCenter alarm ve görselleştirme işlemleri
- `runbooks/tracking/` — Canlı takip ve hareketsizlik müdahale akışları

Her runbook güncellemesi sonrasında:
1. Değişiklik `docs/changelog/` altına özetlenir.
2. README’deki [Operasyonel Runbooklar](../../README.md#operasyonel-runbooklar) tablosu güncellenir.
3. Tatbikat notları `docs/tatbikat/` dizinine eklenir.
