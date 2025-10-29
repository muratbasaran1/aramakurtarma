# Mühendislik Uygulama Yönetişimi

Bu klasör, TUDAK Afet Yönetim Sistemi geliştirme ekiplerinin kod kalitesi, sürümleme ve dağıtım disiplinlerini tutarlı şekilde uygulayabilmesi için hazırlanan rehberleri içerir. Dokümanlar, Faz 0 (teknoloji kararları), Faz 11 (DevOps & İzlenebilirlik) ve Faz 12 (Testler) çıktıları ile doğrudan ilişkilidir.

## İçerik Haritası

| Doküman | Amaç | İlgili Fazlar |
| --- | --- | --- |
| `code-review.md` | Kod inceleme süreçlerini, onay kriterlerini ve güvenlik kontrollerini standardize eder. | Faz 0, Faz 1, Faz 11 |
| `coding-standards.md` | PHP, Vue/Blade ve genel kodlama standartlarını tanımlar; çoklu tenant ve güvenlik gereksinimlerini pekiştirir. | Faz 0, Faz 3, Faz 11 |
| `branching-model.md` | Git dal stratejisini, sürüm etiketleme ve geri dönüş (rollback) senaryolarını tanımlar. | Faz 0, Faz 11, Faz 13 |
| `static-analysis.md` | Statik analiz, lint ve güvenlik tarama araçlarının çalışma noktalarını ve raporlama yükümlülüklerini açıklar. | Faz 11, Faz 12 |
| `tooling-configuration.md` | Kod kalite araçlarının (`phpstan`, `phpcs`, ESLint, Stylelint) proje konfigürasyonlarını ve komut eşleşmelerini özetler. | Faz 0, Faz 11, Faz 12 |
| `deployment-gates.md` | CI/CD pipeline’ında uygulanacak kalite kapılarını ve ölçütleri listeler. | Faz 11, Faz 12, Faz 13 |
| `dependency-management.md` | Composer, npm ve sistem paketlerinin güncelleme ve güvenlik politikalarını standartlaştırır. | Faz 0, Faz 10, Faz 22 |

## Kullanım Notları

1. Yeni bir geliştirme öncesinde ilgili rehberleri gözden geçirerek sprint planlarına gerekli kalite adımlarını ekleyin.
2. Kod incelemesi veya release sürecinde güncellenen standartlar varsa `CHANGELOG.md` ve README sürüm tablosunu revize edin.
3. Statik analiz ve bağımlılık taramalarının çıktılarının ilgili kayıt defterlerine işlendiğini doğrulayın.
4. Takım içi eğitimlerde bu klasörü referans göstererek yeni geliştiricilerin yönetişim beklentilerine uyumunu hızlandırın.

## İletişim

Rehberlerden sorumlu teknik liderlik ekibi için iletişim bilgileri `docs/governance/2024-06-strategy-board.md` dosyasında yer alır. Standartlarda iyileştirme ihtiyacı tespit edildiğinde RFC sürecini kullanarak öneride bulunun.
