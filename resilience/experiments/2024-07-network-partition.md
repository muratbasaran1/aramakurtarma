# Deney: Network Partition — WebSocket & Offline Kuyruk

- **Tarih:** 2024-07-14
- **Ortam:** Staging + izole tenant replika
- **Sorumlu Ekip:** DevOps + OpsCenter + Mobil
- **Bağlantılı Fazlar:** 4, 6, 7, 9, 11

## 1. Hipotez
Network partition senaryosunda OpsCenter alarm konsolu degrade moda ≤2 dakikada geçecek, hareketsizlik alarmı kuyruğa alınıp retry politikası sayesinde 10 dakika içinde teslim edilecektir.

## 2. Deney Adımları
1. Staging ortamındaki WebSocket katmanında yapay gecikme ve paket kaybı enjekte edildi.
2. Edge node bağlantısı kesilerek offline kuyruk 5 dakikalık pencerede dolduruldu.
3. Hareketsizlik tetikleyicisi simüle edilerek OpsCenter alarm konsolu izlenmeye başlandı.

## 3. Metrik Sonuçları
| Metrik | Hedef | Sonuç | Not |
| --- | --- | --- | --- |
| OpsCenter degrade moda geçiş süresi | ≤ 120 sn | 98 sn | Başarılı |
| Hareketsizlik alarm teslim süresi | ≤ 10 dk | 12 dk | Retry sayısı yetersiz, manuel SMS tetiklendi |
| Offline kuyruk boşaltma süresi | ≤ 15 dk | 14 dk | Sınırda başarı |

## 4. Risk Azaltma & Rollback
- Geri döndürme planı başarıyla uygulandı; network normal durumuna 3 dk içinde döndürüldü.
- Kullanıcıya duyuru yapılmadı (staging). Tenant temsilcileri bilgilendirildi.

## 5. Öğrenilen Dersler
- Retry sayısı 5’ten 8’e çıkarılmalı (`config/limit-profile.yml`).
- OpsCenter alarm konsoluna manuel SMS fallback adımı eklenmeli (`runbooks/opscenter/alarm-console-escalation.md`).
- Mobil uygulama banner mesajı degrade durumunda otomatik açılmalı (backlog maddesi açıldı).

## 6. Aksiyonlar
| Aksiyon | Sorumlu | Hedef Tarih | Referans |
| --- | --- | --- | --- |
| Retry parametre güncellemesi | DevOps | 2024-07-16 | `config/limit-profile.yml` |
| SMS fallback eklemesi | OpsCenter Komutanı | 2024-07-18 | `runbooks/opscenter/alarm-console-escalation.md` |
| Mobil banner otomasyonu | Mobil Takım | 2024-07-31 | `tech-debt/backlog.csv` |

## 7. Dokümantasyon Güncellemeleri
- README “Kaos Mühendisliği & Dayanıklılık Testleri” tablosuna sonuçlar işlendi.
- `CHANGELOG.md` ve versiyon geçmişi güncellendi.
- OpsCenter degrade runbook’u ve offline edge recovery planı gözden geçirildi.
