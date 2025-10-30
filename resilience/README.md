# Dayanıklılık ve Kaos Mühendisliği Programı

Bu program, TUDAK Afet Yönetim Sistemi’nin kritik bileşenlerinin beklenmedik aksaklıklar karşısında hedeflenen seviyede hizmet vermesini garanti altına almak amacıyla yürütülür. Program Faz 4 (canlı takip), Faz 6 (OpsCenter), Faz 9 (offline/edge), Faz 11 (devops) ve Faz 22 (siber dayanıklılık) çıktılarıyla doğrudan ilişkilidir.

## Yönetim Yapısı
- **Sponsor:** Operasyon & Dayanıklılık Kurulu
- **Teknik Sorumlu:** DevOps Lideri
- **Destek Ekipleri:** Güvenlik, OpsCenter, Mobil, Veri
- **Onay Süreci:** Her deney öncesi RFC değerlendirmesi, risk analizi ve rollback planı yönetim kurulunca onaylanır.

## Program Döngüsü
1. **Planla:** Hedef hizmet, metrik ve bağımlılıkları belirle. (Referans: `resilience/reliability-roadmap.md`)
2. **Tasarla:** Deney şablonunu doldur, güvenlik ve iletişim planlarını hazırla. (`experiments/chaos-template.md`)
3. **Uygula:** Deneyi izole ortamda veya onaylı üretim penceresinde uygula.
4. **Gözlemle:** Sonuçları `observability/` artefaktları ve `runbook/` güncellemeleriyle karşılaştır.
5. **İyileştir:** Bulguları backlog ve risk kayıtlarına taşı, takip aksiyonları aç.

## Ölçüm
- SLA/SLO etkileri `observability/slo-register.md` ve OpsCenter dashboardlarında izlenir.
- Kapasite trendleri `observability/capacity-journal.md` üzerinde güncellenir.
- Deney sonrası raporlar `experiments/` klasörüne eklenir ve README’ye bağlanır.

## İletişim ve Paydaş Yönetimi
- Deney öncesi ve sonrası bilgilendirmeler `ops/weekly-ops-briefing.md` ve `communications/public/` kanallarında paylaşılır.
- Vatandaş ve paydaş etkileri için tenant temsilcileri ile koordinasyon kurulur.

## Dokümantasyon
- Kaos deneyleri, `CHANGELOG.md` ve README versiyon geçmişinde yer alır.
- Runbook ve politika güncellemeleri ilgili klasörlerde sürümlenir.
