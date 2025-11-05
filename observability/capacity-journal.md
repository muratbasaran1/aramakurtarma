# Kapasite Günlüğü

| Tarih | Kaynak | Ölçüm | Limit | Not |
| --- | --- | --- | --- | --- |
| 2024-06-30 | WebSocket | 8.5k eşzamanlı | 10k | Limitin %85'i kullanıldı |
| 2024-07-10 | WebSocket | 9.2k eşzamanlı | 10k | OpsCenter benchmark’ı sırasında %70 CPU, alarm doğrulama throttle gözden geçirilecek |
| 2024-07-12 | WebSocket | 9.6k eşzamanlı | 10k | İnceleme toplantısı sonrası edge tamponu +10% arttırıldı, `observability/reviews/2024-07-opscenter.md` |
| 2024-07-14 | Offline Kuyruk | 1.8k bekleyen mesaj | 2.5k | Kaos deneyi sonrası retry `max_retries=8`, SMS fallback devrede |
