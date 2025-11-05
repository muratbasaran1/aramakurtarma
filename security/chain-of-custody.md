# Chain-of-Custody İşletim Rehberi

Bu rehber, TUDAK Afet Yönetim Sistemi içerisinde toplanan dijital delillerin delil zinciri (`chain-of-custody.csv`) üzerinden nasıl yönetileceğini ve doğrulanacağını açıklar.

## 1. Roller

| Rol | Sorumluluk |
| --- | --- |
| Olay Yöneticisi | Delil toplama görevini atar, kayıtların tamlığını kontrol eder. |
| Adli Bilişim Uzmanı | Delili toplar, hash doğrulamasını yapar ve kayıt altına alır. |
| Güvenlik Gözden Geçireni | Zinciri periyodik olarak denetler, eksik/hatalı kayıtları raporlar. |
| Hukuk Temsilcisi | Delil paylaşım taleplerini onaylar ve saklama süresi takibini yapar. |

## 2. Kayıt Şablonu

`chain-of-custody.csv` dosyasında aşağıdaki kolonlar bulunur:

1. `evidence_id`
2. `source_system`
3. `collector`
4. `collection_timestamp`
5. `hash`
6. `hash_algorithm`
7. `storage_location`
8. `transfer_to`
9. `transfer_timestamp`
10. `verification_by`
11. `notes`

Her kayıt yeni bir satıra eklenir ve CSV dosyası `git` ile versiyonlanır. Hassas veri içermediğinden düz metin tutulur.

## 3. İş Akışı

1. **Tetikleme:** `runbook/incident-response.md` veya `security/breach-reports/` üzerinden açılan olay.
2. **Toplama:** Delili toplayan uzman, hash değerini hesaplar ve aynı gün içinde CSV’ye işler.
3. **Doğrulama:** Farklı bir uzman SHA-256 hash’ini doğrular ve `verification_by` kolonuna imzasını ekler.
4. **Transfer:** Delil başka bir kasaya veya kuruma aktarıldığında `transfer_to` ve `transfer_timestamp` alanları güncellenir.
5. **Arşivleme:** Saklama süresi bitmeden en az 30 gün önce hukuk birimi bilgilendirilir.

## 4. Denetim

- Aylık denetimlerde rastgele seçilen 3 kayıt kontrol edilir.  
- Eksik alan tespit edilirse olay seviyelendirilir ve `audit/findings-tracker.csv` dosyasına işlenir.  
- Denetim raporları `docs/audit/operational/` klasörüne eklenir.

## 5. Otomasyon & Entegrasyon

- Zincirin Git commit geçmişi `docs/threat-program/lessons-learned.md` ile eşleştirilerek olay sonrası değerlendirmeye dahil edilir.  
- OpsCenter alarm konsolu ile entegrasyon planlanıyorsa RFC açılması ve veri minimizasyonunun belgelenmesi zorunludur.

## 6. Referanslar

- `README.md` → [Delil Zinciri & Adli Bilişim Protokolleri](../README.md#delil-zinciri--adli-bilişim-protokolleri)  
- `docs/threat-program/playbooks/no-motion-high-risk.md`  
- `docs/governance/devam-et-yapi-rehberi.md`

## 7. Versiyon Geçmişi

| Tarih | Sürüm | Değişiklik | Hazırlayan |
| --- | --- | --- | --- |
| 2024-07-17 | v1.0 | İlk sürüm; iş akışı ve denetim adımları tanımlandı. | Güvenlik |
