# Alternatif DR Senaryoları

| Senaryo | Açıklama | Aktivasyon Süresi | Ön Koşullar | İlgili Runbook |
| --- | --- | --- | --- | --- |
| regional-edge | Bölgesel veri merkezi kaybı | 30 dk | Edge node senkronizasyonu | `runbook/offline-edge-recovery.md` |
| db-failover | MySQL ana veritabanı kaybı | 15 dk | Replika hazır, yedek doğrulandı | `runbook/data-restore.md` |
