# Kimlik Bilgisi İhlali Müdahale Runbook'u

## Amaç
Tenant bazlı hesaplarda yetkisiz erişim veya kimlik bilgisi sızıntısı tespit edildiğinde hızlıca izolasyon, doğrulama ve geri dönüş adımlarını standartlaştırmak.

## Kapsam
- Laravel Fortify kullanıcı hesapları
- OpsCenter oturumları ve yönetim paneli girişleri
- NETGSM ve SMTP kimlik bilgileri

## Tetikleyiciler
- Güvenlik bilgi sisteminden (SIEM) "çok faktörlü kimlik doğrulama başarısızlığı" alarmı (Seviye 3+)
- Dark web taramalarında tenant hesaplarına ait şüpheli parola sızıntısı raporu
- KVKK kanalına gelen kullanıcı ihbarı (hesaba izinsiz erişim) — 24 saat içinde doğrulama zorunlu

## Hazırlık Kontrol Listesi
1. Son 30 dakikaya ait Fortify audit log'larını indirin (`security/incidents/` klasöründe olay ID açın).
2. Etkilenen hesabın rol ve yetki seviyesini Spatie Permissions tablosundan doğrulayın.
3. Tenant izolasyonu kapsamında aynı e-posta farklı tenantlarda mı kontrol edin.
4. MFA yedek kodlarının güvenli kasada bulunduğunu doğrulayın.

## Müdahale Adımları
1. **İzolasyon (≤ 5 dk)**
   - Etkilenen hesabın oturumlarını `php artisan fortify:logout --user={id}` komutuyla sonlandırın.
   - Tenant bazlı feature flag üzerinden kritik işlemleri (envanter transferi, görev onayı) geçici olarak kapatın.
2. **Doğrulama (≤ 15 dk)**
   - Audit log'larda IP ve cihaz parmak izini çıkarın, şüpheli oturumları listeleyin.
   - OpsCenter oturum geçmişi ile çakışan hareket olup olmadığını `reports/security-dashboard.md` ile karşılaştırın.
3. **Parola ve MFA Yenileme (≤ 30 dk)**
   - Kullanıcıya tek kullanımlık bağlantı gönderin; MFA'yı zorunlu olarak sıfırlayın.
   - NETGSM veya SMTP API anahtarı etkilenmişse `config/limit-profile.yml` uyarınca yeni anahtar üretin ve sıraya alın.
4. **Etkilenen İşlemleri İnceleme (≤ 2 saat)**
   - Kullanıcı tarafından açılmış görev/olay kayıtlarını `audit/findings-tracker.csv` üzerinden gözden geçirin.
   - Yetkisiz değişiklik varsa `runbook/data-restore.md` kapsamında rollback planı hazırlayın.
5. **Hukuki ve Paydaş Bildirimi (≤ 4 saat)**
   - Hukuk ekibini `security/breach-reports/README.md` protokolüne göre bilgilendirin.
   - Tenant liderine ve ilgili saha sorumlularına `communications/public/STATEMENT_TEMPLATE.md` formatında özet gönderin.

## İletişim ve Eskalasyon
- **Sorumlu Takım:** Güvenlik Ekibi (On-call)
- **Eskalasyon:** 15 dk içinde yanıt alınamazsa Güvenlik Direktörü + Operasyon Direktörü bilgilendirilir.
- **Bildirim Kanalları:** PagerDuty, şifreli e-posta, OpsCenter broadcast banner (gerekirse).

## Başarı Kriterleri
- Şüpheli oturumlar sonlandırıldı ve tekrar giriş denemeleri engellendi.
- Parola & MFA yenileme tamamlandı, kullanıcı doğrulandı.
- Yetkisiz değişiklik bulunmadı veya rollback başarıyla uygulandı.
- KVKK bildirim süreleri (maks. 72 saat) içerisinde gerekli paydaş iletişimi tamamlandı.

## Takip Aksiyonları
- `security/vuln-register.csv` dosyasında olay ID referansı ile kayıt açın.
- Root cause için RFC gerektiriyorsa `docs/rfc/` altında taslak başlatın.
- 7 gün içinde masa başı tatbikatı ile prosedürü doğrulayın, sonuçları `docs/threat-program/reports/` klasörüne ekleyin.
