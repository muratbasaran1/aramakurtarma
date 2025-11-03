# Kod İnceleme Standartları

Kod inceleme süreci, TUDAK Afet Yönetim Sistemi’nin güvenlik, bütünlük ve sürdürülebilirlik hedeflerinin sağlanması için zorunludur. Bu belge, tüm modüller için minimum gereksinimleri ve gözden geçirme sırasında takip edilmesi gereken kontrol listesini sunar.

## İnceleme Ön Koşulları

- **Testler:** İlgili birim/özellik testleri çalıştırılmış ve sonuçlar PR açıklamasında paylaşılmış olmalıdır.
- **Dokümantasyon:** README veya modül dokümanlarında değişiklik gerektiren noktalar güncellenmelidir.
- **Güvenlik Etkisi:** Kimlik doğrulama, yetkilendirme, veri saklama veya şifreleme değişiklikleri risk değerlendirme notu içermelidir.
- **Bağımlılıklar:** Yeni paket veya servis kullanımı için RFC onayı ve lisans kontrolü yapılmalıdır.

## İnceleme Kontrol Listesi

1. **İş Gereksinimi Doğrulaması**  
   - Kullanıcı hikâyesi ve kabul kriterleri sağlanıyor mu?  
   - Faz hedefleriyle çelişen bir durum var mı?
2. **Güvenlik Kontrolleri**  
   - Girdi doğrulama, yetki kontrolleri ve audit log kayıtları mevcut mu?  
   - Hareketsizlik veya kural motoru tetikleri etkileniyorsa ilgili runbook bağlantıları güncellendi mi?
3. **Kod Kalitesi**
   - Kod stil rehberine uyum, temiz modüler yapı ve hata yönetimi uygun mu? (`docs/engineering/coding-standards.md`)
   - Statik analiz uyarıları ve lint hataları giderildi mi? (`docs/engineering/static-analysis.md`)
   - Gereksiz karmaşıklık veya tekrar eden kod var mı?
4. **Test Kapsamı**  
   - Kritik akışlar için hem pozitif hem negatif test senaryoları mevcut mu?  
   - Offline/edge ve çoklu tenant senaryoları değerlendirildi mi?
5. **Performans & Gözlemlenebilirlik**  
   - Yeni metrik, alarm veya log gereksinimi varsa eklendi mi?  
   - BBOX, rate limit veya caching stratejileri etkileniyor mu?

## Onay Kriterleri

- En az iki farklı rol (ör. modül geliştiricisi + güvenlik gözlemcisi) tarafından onay alınmalıdır.
- Açıkta kalan kritik bulgular `blocking` etiketiyle çözülmeden merge edilemez.
- İnceleme tamamlandığında `CHANGELOG.md` kaydı ve ilgili runbook/policy güncellemeleri doğrulanır.

## Reddedilme Senaryoları

- Testlerin başarısız olması veya raporlanmaması
- Yetki kontrollerinde eksik/yanlış konfigürasyon
- Veri şeması değişikliklerinde migrasyon planının bulunmaması
- OpsCenter, kural motoru veya offline kuyruk üzerinde performans riski yaratması

## Sürekli İyileştirme

Kod inceleme metrikleri (örn. inceleme süresi, tespit edilen kritik hata sayısı) `observability/metrics-catalog.md` üzerinde takip edilir. Aylık retrospektiflerde süreçteki darboğazlar değerlendirilir ve gerektiğinde bu belge güncellenir.
