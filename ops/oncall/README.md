# On-call & Vardiya Programı

TUDAK Afet Yönetim Sistemi 7/24 kesintisiz çalışmak zorundadır. Bu nedenle on-call süreçleri Faz 1 (güvenlik), Faz 4 (canlı takip), Faz 6 (OpsCenter) ve Faz 11 (DevOps) çıktılarıyla uyumlu biçimde planlanmıştır.

## Amaçlar
- Kritik alarm ve olaylara hedef süreler içinde yanıt vermek.
- Vardiya devri sırasında bilgi kaybını önlemek ve audit trail sağlamak.
- Yorgunluk ve kapasite risklerini ölçerek sürdürülebilir rotasyon kurmak.

## Roller ve Kapsam
| Rol | Birincil Sorumluluklar | İkincil Destek | Onay Gereksinimi |
| --- | --- | --- | --- |
| OpsCenter Komutanı | Alarm konsolu, saha koordinasyonu, tenant bilgilendirmesi | Mobil uygulama desteği | Olay şiddeti 3+ durumlarında yönetim onayı |
| DevOps Gözcüsü | CI/CD, altyapı, veri tabanı, kuyruk izleme | Kural motoru incelemesi | Planlı bakımda CAB onayı |
| Güvenlik Gözcüsü | WAF, 2FA, tehdit istihbaratı, audit log takibi | Hukuk iletişimi | KVKK ihlali bildirimi öncesi hukuk onayı |
| Tenant İletişim Temsilcisi | Kamu & paydaş iletişimi, portal eskalasyonu | OpsCenter komutanı | Kamusal duyurularda iletişim direktörü onayı |

Her rol haftalık rotasyonda en fazla 2 gece vardiyası üstlenebilir. Arka arkaya 7/24 kapsama için minimum 4 kişilik çekirdek ekip gereklidir.

## Vardiya Öncesi Checklist
1. `observability/service-health-checklist.md` maddeleri uygulanır.
2. Açık olaylar `runbooks/` referansıyla birlikte gözden geçirilir.
3. `ops/oncall/rotation-schedule.csv` üzerinden bir sonraki üç vardiya teyit edilir.
4. Planlı bakım, test ve kaos deneyleri `ops/weekly-ops-briefing.md` ve `resilience/reliability-roadmap.md` ile çapraz kontrol edilir.

## Vardiya Devir Şablonu
- Handover şablonu (`handoff-template.md`) doldurularak hem Slack/Matrix kanallarında hem de arşiv klasöründe saklanır.
- Devralınan kritik aksiyonlar için kapanış zamanı ve sorumlusu not edilir.
- Varsa saha ekiplerine gönderilen bildirimler `archive/sms`, `archive/mail`, `archive/push` klasörlerine işlenir.

## Raporlama & Ölçüm
- Yanıt süreleri `observability/slo-register.md` içinde raporlanır.
- Dönem sonu değerlendirmeleri `ops/weekly-ops-briefing.md` üzerinde paylaşılır ve README “On-call & Vardiya Yönetimi” bölümü güncellenir.
- Aşırı yük veya kapasite açığı tespit edilirse `tech-debt/backlog.csv` ve `governance/risk-register.csv` kayıtlarına yansıtılır.

## Dokümantasyon
- Politika revizyonları RFC süreciyle (`docs/rfc/`) onaylanır.
- Vardiya değişiklikleri CHANGELOG’a eklenir ve README versiyon geçmişinde yeni sürüm olarak not edilir.
