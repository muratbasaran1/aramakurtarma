# Rollback Kontrol Listesi

Bu şablon, üretim ortamında geri alma işlemi gerektiğinde uygulanacak adımları sıralar.

1. **Olay Bilgisi**
   - İlgili yayın sürümü:
   - Olay referansı / tiket:
   - Karar veren kişi:
2. **Etkilenen Bileşenler**
   - Servisler:
   - Veri tabanı değişiklikleri:
3. **Ön Koşullar**
   - Yedekleme doğrulandı mı? (bağlantı)
   - İlgili ekip bilgilendirildi mi?
4. **Geri Alma Adımları**
   - [ ] Kod rollback (branch/tag)
   - [ ] Migration geri alma
   - [ ] Cache/queue temizliği
5. **Doğrulama**
   - [ ] Health check /metrics
   - [ ] OpsCenter görünürlüğü
   - [ ] Kullanıcı geri bildirim kontrolü
6. **İletişim**
   - İç iletişim kanalları (Slack/Teams):
   - Paydaş bilgilendirmesi (e-posta/SMS):
7. **Kapanış**
   - Sonuç:
   - Ek takip aksiyonları:
   - `communications/public/` açıklaması gerekli mi?
