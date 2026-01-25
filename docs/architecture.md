# VisitGökçeada - Mimari ve Tenant Tasarımı

Bu doküman, uygulamanın çok-kiracılı (multi-tenant) mimarisini, alan adı (subdomain/custom domain) akışını, medya ve harita entegrasyon stratejisini özetler.

## Hedefler
- Her işletme (`Tenant`) kendi vitrini ve içeriklerini yönetebilsin.
- Admin isterse sınırsız işletme ekleyebilsin; işletmeler subdomain veya özel domain ile yayınlansın.
- Veriler izole kalsın; kod karmaşası en aza indirgenecek.
- Google Places/Maps verileri (puan, yorum, fotoğraflar) isteğe bağlı içeriğe import edilebilsin.

## Tenant Resolution
- Host-based çözüm uygulanır.
  - Öncelik 1: `tenants.domain` alanı ile birebir eşleşme (custom domain).
  - Öncelik 2: `subdomain.main_domain` ise subdomain `business_profiles.slug` ile eşleştirilir.
- `IdentifyTenantByHost` middleware request başında tenant'ı belirler ve `Tenant::setCurrent($tenant)` ile global olarak bağlar.
- `TenantScope` önce `Tenant::getCurrent()` kontrol eder; varsa sorgular otomatik tenant_id filtresi uygular.

## Domain / DNS / SSL
- Prod için wildcard DNS (örn. `*.visitgokceada.com`) ve wildcard A/CNAME kaydı gerekir.
- Custom domain desteği için tenant'ın `domain` alanı kontrol edilir. Custom domain SSL için Let's Encrypt + Traefik/Caddy önerilir.
- Local test için hosts dosyasına `127.0.0.1 isletme1.visitgokceada.com` eklenebilir.

## Veri Modeli Özet (yeni alanlar)
- `business_profiles` içine eklendi:
  - `short_description`, `cover_image_path`, `gallery` (JSON), `google_place_id`, `avg_rating`, `last_synced_at`, `latitude`, `longitude`

## Media Yönetimi
- Her tenant için `storage/app/public/tenants/{tenant_id}/` dizini.
  - Kapak: `cover.jpg` (veya webp)
  - Galeri: `gallery/1.jpg`, `gallery/2.jpg` ...
- `php artisan storage:link` ile `public/storage` bağlanmalı.
- Görüntü optimizasyonu için `spatie/image-optimizer` veya `intervention/image` kullanılabilir.
- Eğer ortam yoğunluğu artarsa, S3 veya CDN entegrasyonu önerilir.

## Google Places Entegrasyonu (import stratejisi)
- Admin panelde `google_place_id` girildiğinde veya "Import from Google" butonuna basıldığında:
  - `Place Details` endpoint çağrılır: `fields=name,rating,reviews,geometry,photos,formatted_address`
  - `avg_rating`, `short_description` (snippet), `gallery` (photo references) ve `latitude/longitude` business profile'a kaydedilir.
  - Fotoğrafları indirmek istenirse, geçici olarak indirip optimize edip `storage` içine koy.
- API anahtarı: `.env` içine `GOOGLE_PLACES_API_KEY` eklenmeli.
- Senkronizasyon için `php artisan google:sync-places` adında scheduled job eklenebilir.

## Frontend & Harita
- Public vitrinde haritalar için varsayılan `Leaflet + OpenStreetMap` kullanılacak (ücretsiz).
- Google istenirse sadece ek veri kaynağı olarak kullanılacak (rating/reviews/photos).

## Güvenlik ve İzolasyon
- Tüm tenant-specific modeller `tenant_id` ile ayrılır.
- Global `TenantScope` her modelde uygulanmalı (Category, Product, vb.).
- Filament admin: `super_admin` tüm tenantları görür; `business_owner` sadece kendi tenant'larını.
- Dosya yolları tenant id ile ayrılmalı; signed URLs veya temporary access kullanılabilir.

## Geliştirme / Test Notları
- `.env` ayarları: `APP_MAIN_DOMAIN`, `GOOGLE_PLACES_API_KEY` (opsiyonel).
- Local subdomain test: hosts dosyası + `php artisan serve` kullanımı.

## Sonraki Adımlar
1. Google Places import komutu + admin buton (yapılıyor).
2. Domain yönetimi UI (tenant -> domain mapping).
3. Media optimizasyon + background jobs.
4. Public tenant routing + blade şablonları.

