# Veri Geri Yükleme Runbook

| Alan | Bilgi |
| --- | --- |
| İlgili Fazlar | Faz 1 (Güvenlik & Altyapı), Faz 3 (Çekirdek Modüller), Faz 9 (Offline & Edge), Faz 11 (DevOps) |
| Son Gözden Geçirme | 2024-07-08 |
| Sorumlu Takım | Veri Platformu & Altyapı Ekibi |

## Amaç ve Kapsam
Plansız veri kaybı veya bozulması durumunda MySQL 8 üretim kümesinin
tüm tenant verileriyle birlikte güvenli şekilde geri yüklenmesini sağlar.
Prosedür yedekleme politikaları (Faz 1) ve edge kuyruk eşitleme gereksinimleri (Faz 9)
ile uyumlu olmak zorundadır.

## Tetikleyiciler
- Günlük yedek bütünlük kontrolünün başarısız olması.
- `audit/findings-tracker.csv` üzerinde veri tutarsızlığı kaydı.
- Saha/merkez operasyonlarından kritik veri eksikliği ihbarı.

## Ön Koşullar
1. `dr/backlog.csv` üzerinde olay referans numarası açılmış olmalı.
2. Son başarılı yedek dosyası `backup/YYYY-MM-DD.sql.enc` formatında erişilebilir durumda.
3. Edge kuyruklarında bekleyen yazma işlemleri `offline-edge-recovery.md` ile uyumlu
   şekilde dondurulmuş olmalı.

## Adımlar
### 1. Durum Doğrulama (0–20 dk)
1. `docs/data-catalog/template.yaml` üzerinden etkilenen şema ve tablo listesi çıkarılır.
2. `governance/data-quality-dashboard/README.md` metrikleri kontrol edilerek veri kaybının
   kapsamı ölçülür.
3. OpsCenter’a degrade bildirim için `runbook/opscenter-degradation.md` çalıştırılır.

### 2. Geri Yükleme Hazırlığı (20–50 dk)
1. Şifreli yedek `backup/` konumundan çekilir, `kms decrypt` işlemi gerçekleştirilir.
2. Bozuk veri barındıran tenantlar `maintenance` moduna alınır; API oran limitleri
   `infra/rate-limit.tf` ile daraltılır.
3. Yeniden oynatılacak migration listesi `docs/dr/alternatives.md` üzerinden seçilir.

### 3. Yükleme (50–140 dk)
1. MySQL sunucusunda `RESET MASTER` uygulanmaz; `DROP` yerine `TRUNCATE + IMPORT` tercih edilir.
2. `mysql --binary-mode` ile yedek yüklenir; spatial indexler için `ANALYZE TABLE`
   çalıştırılır.
3. Faz 3 kısıtları (FK, CHECK) devre dışı bırakılmaz; hata veren kayıtlar `data-quality/daily-report.json`
   dosyasına eklenerek kuyruğa alınır.

### 4. Edge Kuyruk Senkronizasyonu (140–200 dk)
1. Edge node’larda bekleyen `POST/PATCH` istekleri `offline-edge-recovery.md`
   senaryosuna göre sıralı yeniden gönderilir.
2. Her yazım için `Idempotency-Key` doğrulaması yapılır; çakışan kayıtlar `tech-debt/backlog.csv`
   dosyasına "data-reconcile" etiketiyle girilir.

### 5. Doğrulama ve Açılış (200–260 dk)
1. `docs/tests/latest` referans alınarak çekirdek feature testleri çalıştırılır.
2. `reports/performance/2024-06-performance.md` eşiğinde belirtilen SLA metrikleri karşılaştırılır.
3. Tenantlar bakım modundan çıkarılır ve kullanıcılar bilgilendirilir.

## İletişim Planı
| Rol | Kanal | Zorunlu Bildirim |
| --- | --- | --- |
| Veri Platformu Lideri | Slack #incident-data | Başlangıç ve tamamlanış |
| OpsCenter | PagerDuty | Bakım moduna geçiş |
| Hukuk & KVKK | E-posta + ticket | PII etkisi varsa 60 dk içinde |
| Müşteri Temsilcileri | CRM notu | Tenant bazlı açılış duyurusu |

## Kontrol Listesi
- [ ] Şifre çözme anahtarları `config/environment/` içinde güncel.
- [ ] `audit_logs` tablosu toplam satır sayısı yedekle uyumlu.
- [ ] Spatial indexler (`polygon`, `LINESTRING`) yeniden oluşturuldu.
- [ ] `open-data/releases/` için anonimleştirme kontrolleri yeniden doğrulandı.

## Başarı Kriterleri
- Yedek geri yüklendikten sonra veri bütünlüğü hatasız (`0` failed constraint).
- Edge kuyruk işlem süresi ≤ 30 dk.
- Etkilenen kullanıcı biletleri 24 saat içinde kapatılmış.

## Referanslar
- `docs/dr/README.md`
- `docs/tests/README.md`
- `runbook/offline-edge-recovery.md`
- `docs/changelog/README.md`
