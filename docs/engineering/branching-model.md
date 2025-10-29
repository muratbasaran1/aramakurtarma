# Git Dal Stratejisi

Bu belge, TUDAK Afet Yönetim Sistemi’nin Git tabanlı geliştirme akışını tanımlar. Amaç; paralel faz geliştirmelerini güvenli şekilde yönetmek, release planlarını şeffaflaştırmak ve geri dönüş (rollback) süreçlerini hızlandırmaktır.

## Dal Tipleri

| Dal | Açıklama | Sürümleme İlişkisi |
| --- | --- | --- |
| `main` | Üretimde çalışan doğrulanmış kod. Her commit release notu ve sürüm etiketiyle ilişkilidir. | `vMAJOR.MINOR.PATCH` etiketleri |
| `develop` | Aktif sprint çalışmalarının birleştirildiği dal. Otomatik test ve kalite kapıları bu dalda zorunludur. | Haftalık build etiketi (`build-YYYYMMDD`) |
| `feature/<faz>-<konu>` | Faz bazlı özellik geliştirmeleri. Kısa ömürlü tutulur, tamamlandığında `develop` dalına merge edilir. | İş kaydı ID’si ile etiket | 
| `hotfix/<versiyon>` | Üretim ortamındaki kritik hataları düzeltmek için açılır. Doğrudan `main` ve `develop` dallarına uygulanır. | `vMAJOR.MINOR.PATCH` artışı |
| `release/<versiyon>` | Go-live hazırlığı sırasında oluşturulur. Son testler, dokümantasyon ve çeviri kontrolleri bu dalda yapılır. | Sürüm notu hazırlığı |

## Akış Kuralları

1. `develop` dalı her sprint başında `main` ile senkronize edilir.
2. Özellik dalları, ilgili faz runbook’larında tanımlı veri şeması ve güvenlik kontrollerine uymak zorundadır.
3. `release/` dalı açıldığında yeni özellik dalları sadece bir sonraki sürüm için kabul edilir.
4. `hotfix/` dalı tamamlandığında ilgili runbook, değişiklik günlüğü ve README sürüm geçmişi güncellenmelidir.
5. Tüm merge işlemleri “squash and merge” değil, faz bazlı commit geçmişini koruyacak şekilde “merge commit” ile yapılır.

## Rollback Stratejisi

- Üretimde kritik hata tespit edilirse `main` dalındaki son stabil etiket (`vX.Y.Z`) referans alınarak hızlı rollback uygulanır.
- Rollback sonrasında `hotfix/` dalı açılarak kalıcı çözüm uygulanır ve `CHANGELOG.md`ye kayıt düşülür.
- Offline/edge veya OpsCenter konfigürasyonları etkilenmişse ilgili runbook’larda geri dönüş adımları izlenir.

## Denetim ve İzleme

- Dal koruma kuralları (branch protection) Git platformunda aktif tutulur: kod inceleme, test ve kalite kapıları olmadan merge engellenir.
- Dal temizliği aylık olarak yapılır; kapatılan `feature/` dalları silinir ve kayıtları `sprint-goals.md` dosyasında tutulur.
- Strateji revizyonları `docs/rfc/` süreci ile yapılır ve bu belge güncellenir.
