# 2024-07 Tehdit Programı Özeti

## Genel Bakış
- Toplam IOC kaydı: 124
- Kritik (Seviye 4) olay sayısı: 3
- Yanlış pozitif oranı: %4

## Öne Çıkan Olaylar
1. **2024-07-04 Hareketsizlik + Tehdit Korelasyonu**
   - Tetikleyici: `tracking.no_motion` + bölgesel güvenlik uyarısı
   - Uygulanan Playbook: `docs/threat-program/playbooks/no-motion-high-risk.md`
   - Sonuç: Personel sağlıklı, yanlış pozitif. Feedback `feedback/inbox/README.md` üzerinden toplandı.
2. **2024-07-11 API Kimlik Bilgisi Sızıntısı**
   - Tetikleyici: Ticari feed IOC eşleşmesi
  - Uygulanan Runbook: `runbooks/incident-response/credential-compromise.md`
   - Sonuç: Anahtarlar döndürüldü, tenant bilgilendirildi.
3. **2024-07-18 Sosyal Medya Yanlış Bilgilendirme**
   - Tetikleyici: Sosyal medya izleme uyarısı
   - Aksiyon: `communications/public/STATEMENT_TEMPLATE.md` ile düzeltme yapıldı.

## Metrikler
| Gösterge | Hedef | Gerçekleşen | Açıklama |
| --- | --- | --- | --- |
| IOC İşleme Süresi | ≤ 30 dk | 22 dk | Otomasyon pipeline'ı başarılı |
| Playbook Tamamlama | ≥ %95 | %97 | 1 senaryo manuel müdahale gerektirdi |
| Yanlış Pozitif İzleme | ≤ %5 | %4 | Hareketsizlik alarmı kaynaklı |

## Tatbikat ve Öğrenilen Dersler
- Purple team tatbikatında Hareketsizlik Yüksek Risk senaryosu test edildi, runbook güncellemesi gerektirmedi.
- Threat intel kaynak ağırlıkları yeniden değerlendirildi; USOM kaynak ağırlığı +10%. 

## Aksiyon Maddeleri
- `docs/threat-program/intel-feeds/README.md` kaynak listesine AFAD API eklenecek (sorumlu: Güvenlik Ekibi, T+7 gün).
- OpsCenter alarm konsolu için otomatik playbook öneri banner'ı geliştirmeye alınacak (sorumlu: Ops Ekibi, T+14 gün).
