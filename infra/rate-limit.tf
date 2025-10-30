# Placeholder Terraform module for API rate limit configuration

# Bu dosya, üretim ortamındaki API Gateway veya WAF yapılandırmalarında
# kullanılacak rate limit politikalarını tanımlamak için taslaktır.
# Gerçek değerleri eklemeden önce ilgili RFC onayını aldığınızdan emin olun.

variable "tenant_profiles" {
  description = "Tenant bazlı rate limit profilleri"
  type        = map(object({
    burst = number
    rate  = number
  }))
}

# Not: Uygulama sırasında provider ve kaynak tanımlarını ekleyin.
