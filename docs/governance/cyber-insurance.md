# Siber Sigorta Programı

Bu belge, TUDAK Afet Yönetim Sistemi için siber sigorta ve risk transferi süreçlerinin nasıl yürütüleceğini adım adım açıklar. Amaç; finansal etkisi yüksek olaylarda önceden tanımlı teminatlara ve karşı taraf yükümlülüklerine hızlıca erişebilmektir.

## 1. Program Bileşenleri

| Bileşen | Açıklama | Sorumlu | Frekans |
| --- | --- | --- | --- |
| Teminat Matrisi | Risk kayıt defterindeki her senaryonun sigorta/rezerv eşleşmesi. | Risk & Uyum | Yıllık güncelleme |
| Poliçe Kütüphanesi | Aktif poliçeler ve ek klozların PDF/özet kayıtları (`compliance/finance/`). | Finans | Sürekli |
| Tazminat Kanıt Seti | Audit log, chain-of-custody kayıtları ve olay raporlarının paketlenmesi. | Güvenlik & Operasyon | Olay sonrası 48 saat |
| Sağlayıcı Performans Notları | Sigorta şirketi hizmet seviyesi, ödeme hızları ve uyum talepleri. | Finans | Yenileme öncesi |

## 2. Süreç Akışı

1. **Risk Analizi Girdisi**  
   - `governance/risk-register.csv` içinde etki ≥ 4 olan tüm kayıtlar gözden geçirilir.  
   - Yeni riskler için RFC açılır ve teminat gereksinimi belirlenir.
2. **Teminat Tasarımı**  
   - Sigorta, sözleşmesel transfer veya özkaynak rezervi kararı alınır.  
   - Seçilen model `compliance/finance/` altındaki ilgili çeyrek raporuna işlenir.
3. **Uyum Kontrolü**  
   - Sağlayıcının teknik gereksinimleri `Faz 1`, `Faz 9` ve `Faz 22` çıktılarıyla karşılaştırılır.  
   - Eksikler için sorumlu birimler ve kapanış tarihleri `audit/findings-tracker.csv` dosyasına eklenir.
4. **Poliçe Onayı**  
   - Finans direktörü, hukuk ve güvenlik lideri elektronik onay verir.  
   - Onay kaydı `docs/governance/exec-minutes-template.md` formatında saklanır.
5. **Yenileme & Tatbikat**  
   - Yenileme öncesinde teminat kullanım raporu hazırlanır (bkz. [OpsCenter KPI’ları](../../README.md#operasyonel-kpilar--alarm-eşikleri)).  
   - En az yılda bir sigorta hasar bildirim tatbikatı `docs/tatbikat/` planına eklenir.

## 3. Olay Müdahalesi Sırasında

- Olay seviyelendikten sonra (bkz. `runbook/incident-response.md`) finans sorumlusu 2 saat içinde sigorta sağlayıcısıyla irtibata geçer.
- `chain-of-custody.csv` ve `security/threat-intel-register.md` dosyalarından alınan kayıtlar tek paket halinde `docs/audit/` deposuna konur.
- Hasar talebi gönderildiğinde `communications/public/STATEMENT_TEMPLATE.md` güncellenmiş metinle paydaşlara gönderilmek üzere hazırlanır.

## 4. Performans ve İyileştirme

- **Metrikler:** Teminat kullanım oranı, kapsam dışı kalan olay sayısı, ödeme süresi (gün).  
- **İzleme:** Metrikler aylık olarak `analytics/benchmark/` raporlarına eklenir.  
- **İyileştirme:** Kapsam dışı kalan olaylar için 14 gün içinde RFC açılması zorunludur.

## 5. Belgeler ve Referanslar

- `README.md` → [Siber Sigorta & Risk Transfer Stratejisi](../../README.md#siber-sigorta--risk-transfer-stratejisi)  
- `compliance/finance/` çeyreksel raporları  
- `docs/threat-program/reports/` güvenlik özetleri  
- `docs/governance/devam-et-yapi-rehberi.md`

## 6. Versiyon Geçmişi

| Tarih | Sürüm | Değişiklik | Hazırlayan |
| --- | --- | --- | --- |
| 2024-07-17 | v1.0 | İlk yayın; program bileşenleri ve süreç akışı belirlendi. | Risk & Uyum |
