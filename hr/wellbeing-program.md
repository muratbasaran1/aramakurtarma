# Personel Refah Programı

Bu belge, saha ve OpsCenter ekiplerinin psikososyal destek sürecini standardize eder ve Faz 14 mobil uygulama ile Faz 26 stratejik hedefleri arasındaki bağı ortaya koyar.

## Protokoller
- **Ön Görev Kontrolü:** `hr/wellbeing-checklist.csv` listesindeki maddeler tamamlanmadan göreve çıkılamaz.
- **24 Saat İçinde Değerlendirme:** Hareketsizlik alarmı, ağır yaralanma veya yüksek şiddetli olay sonrası ilgili personel için psikolojik ilk yardım randevusu atanır.
- **72 Saat İzlemesi:** İlk yardım sonrası takip görüşmesi planlanır; sonuç HR gizli klasöründe saklanır.

## Roller
| Rol | Sorumluluk | Artefakt |
| --- | --- | --- |
| İnsan Kaynakları Lideri | Program koordinasyonu, kaynak planlama | Bu belge |
| Saha Psikoloğu | Görüşmeleri yürütür, raporlar | Gizli rapor deposu |
| OpsCenter Süpervizörü | Görev planlarını refah durumuna göre ayarlar | OpsCenter vardiya planı |

## Raporlama
- Aylık refah skorları `surveys/opscenter-nps.json` ve saha anketlerinden toplanır.
- Özet raporlar `docs/wellbeing/README.md` referansıyla yönetim kurullarına sunulur.

## Hata Önleme
- Raporlanmayan vakalar için otomatik hatırlatıcı e-posta tetiklenir.
- Personel verileri maskeleme kurallarına (`open-data/releases/v2024.07/masking-checklist.md`) uygun olarak paylaşılır.
