# Personel Refahı & Psikolojik Destek Çerçevesi

Bu rehber, Faz 14 mobil saha deneyimleri ve Faz 26 stratejik yönetim hedefleriyle uyumlu şekilde personel refahının nasıl izleneceğini açıklar.

## Program Bileşenleri
- **Ön Görev Kontrolü:** Göreve çıkmadan önce ekip lideri `hr/wellbeing-checklist.csv` listesini doldurur.
- **Olay Sonrası Değerlendirme:** Hareketsizlik alarmı veya yüksek stresli görev sonrası 24 saat içinde psikolojik ilk yardım görüşmesi planlanır.
- **Aylık Nabız Anketi:** OpsCenter, saha ve gönüllü ekipleri için ayrı anketler `surveys/` dizininde saklanır; skorlar README’deki KPI tablosuna aktarılır.

## Süreç Akışı
1. Görev ataması sırasında sistem personel belge geçerliliği ile birlikte refah durumu bayraklarını kontrol eder.
2. “Riskli” işareti taşıyan personel için görev ataması yapılmadan önce İnsan Kaynakları onayı istenir.
3. Psikososyal destek görüşmeleri `hr/wellbeing-program.md` dosyasında tanımlanan protokole göre yürütülür.
4. Sonuçlar anonimleştirilerek `docs/community/README.md` ile paylaşılan paydaş raporlarına girdi sağlar.

## İzlence
- Aylık raporlar `hr/wellbeing-program.md` dosyasında özetlenir.
- Refah skorları `observability/capacity-journal.md` ile çapraz kontrol edilerek yoğunluk–refah korelasyonu takip edilir.
- Kritik bulgular Etik Kurul gündemine `docs/ethics/review-schedule.md` üzerinden taşınır.

## Hata Önleme
- Refah verileri KVKK gereksinimlerine uygun şekilde maskelenmeden paylaşılmaz.
- Destek görüşmesi planlanmadığı tespit edilen görevler otomatik olarak personel havuzuna geri alınır.
- Üç ardışık ay düşük skor bildiren ekipler için zorunlu saha molası planlanır ve Strateji Kurulu’na raporlanır.
