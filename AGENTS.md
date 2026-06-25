# Proje Ajan Notları

Bu repository `apk-apps` projesidir; özel APK sürüm yönetimi servisi olarak geliştirilir.

## Ürün Yönü

- Varsayılan teknoloji yığını Laravel 13, PHP 8.4, PostgreSQL 17, Blade, Vite, Nginx ve PHP-FPM'dir.
- Bağımlılık yönü UI -> application -> domain -> infrastructure şeklinde tek yönlü kalmalıdır.
- Herkese açık uygulama keşfi, kaynak kazıma, DRM bypass veya yetkisiz dağıtım özelliği eklemeyin.
- APK çıktı dosyalarını, admin hesaplarını, güncelleme politikalarını ve üretim gizli bilgilerini hassas kabul edin.
- Bu makinede `E:\Codex\apk-apps` üzerinden çalışın. Kullanıcı açık onay vermeden `C:` altındaki dosyaları taşımayın veya silmeyin.

## Yerel Komutlar

- `docker compose up -d --build`
- `docker compose exec app php artisan migrate --force`
- `docker compose exec app php artisan test`
- `docker compose exec app vendor/bin/pint --test`
- `docker compose exec app vendor/bin/phpstan analyse`
- `docker compose exec app vendor/bin/deptrac analyse`
- `docker compose exec app npm run build`
- `make quality`
