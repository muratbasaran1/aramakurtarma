# Tehdit İstihbarat Akışları

Bu klasör, Faz 22 siber güvenlik ve Faz 7 kural motoru süreçleri için tüketilen harici tehdit istihbaratı kaynaklarının kayıtlarını içerir.

## Akış Kategorileri
| Kategori | Kaynak | Güncelleme Sıklığı | Tetiklenen Aksiyon |
| --- | --- | --- | --- |
| Açık Kaynak İstihbarat (OSINT) | CERT-Bund, US-CERT RSS | 30 dk | `security/threat-intel-register.md` güncelle, kritik kayıtları SIEM'e gönder |
| Ticari Beslemeler | FireEye Helix, CrowdStrike | 15 dk | IOC eşleştirme, kural motoru `security.high_risk` tetikleyicisi |
| Devlet Uyarıları | USOM, ENISA | 60 dk | Hukuk & uyum değerlendirmesi, tenant bilgilendirmesi |
| Sosyal Medya İzleme | Twitter listeleri, AFAD duyuruları | 10 dk | OpsCenter bilgilendirmesi, olay oluşturma önerisi |

## İş Akışı
1. `threat_feeds/README.md` üzerindeki cron takvimi ile beslemeler çekilir.
2. IOC ve taktikler `security/threat-intel-register.md` dosyasına yazılır.
3. Kritik IOC'ler için `docs/threat-program/playbooks/` altında ilgili playbook tetiklenir.
4. Haftalık özetler `docs/threat-program/reports/` klasörüne eklenir.

## Kalite Kontrolleri
- Besleme doğruluğu aylık purple team tatbikatında ölçülür.
- Yanlış pozitifler `feedback/inbox/` yoluyla raporlanır.
- Kaynak güvenilirliği her çeyrekte `docs/vendors/README.md` ile yeniden değerlendirilir.
