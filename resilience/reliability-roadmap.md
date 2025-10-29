# Dayanıklılık Yol Haritası (2024 Q3)

| Hedef | Hizmet | Ölçüm | Durum | Faz Referansı | Not |
| --- | --- | --- | --- | --- | --- |
| OpsCenter degrade modunun otomasyonu | OpsCenter (Web + WS) | P95 yanıt süresi, alarm süreleri | Aktif | Faz 6, Faz 20 | Network partition deneyi sonrası SMS fallback güncellemesi bekliyor. |
| Offline kuyruk dayanıklılığı | Offline Edge & Mobil | Kuyruk boşaltma süresi, başarısız retry oranı | Planlandı | Faz 4, Faz 9 | Retry katsayısı artışı sonrası load test koşulacak. |
| Kural motoru teslim güvenilirliği | Kural Motoru | Başarısız aksiyon yüzdesi, retry sayısı | Aktif | Faz 7 | Rate limit tuning ve deneme notları `docs/threat-program/` ile entegre edilecek. |
| Veri tabanı failover tatbikatı | MySQL & Migration Guard | Failover süresi, veri bütünlüğü kontrolleri | Planlandı | Faz 2, Faz 11 | Ağustos tatbikatı için RFC hazırlanıyor. |

## Takvim
- **Temmuz 2024:** Network partition kaos deneyi, sonuç değerlendirmesi, SMS fallback güncellemesi.
- **Ağustos 2024:** MySQL failover tatbikatı, OpsCenter snapshot doğrulama, offline kuyruk load testi.
- **Eylül 2024:** Tenant bazlı felaket senaryosu (edge node kaybı) ve veri kataloğu bütünlük kontrolü.

## İzleme & Raporlama
- Haftalık durum güncellemeleri `ops/weekly-ops-briefing.md` içinde paylaşılır.
- Kritik riskler `governance/risk-register.csv` ve `tech-debt/backlog.csv` dosyalarına işlenir.
- Program sonuçları README “Kaos Mühendisliği & Dayanıklılık Testleri” bölümü ve `CHANGELOG.md` üzerinde duyurulur.
