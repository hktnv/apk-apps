# apk-apps

APK Apps, Android uygulamaları için özel bir APK sürüm dağıtım servisidir. Taslak APK sürümü yükleme, sürümleri `stable`, `beta` veya `internal` kanallarına yayınlama, kanal geri alma, güncelleme kontrolü ve APK indirme akışlarını sağlar.

Çalışma zamanı yığını Laravel 13, PHP 8.4, PostgreSQL 17, Nginx, PHP-FPM, Blade ve Vite üzerine kuruludur.

## Amaç

`apk-apps`, Play Store dışından dağıtılan Android/Kotlin APK projeleri için merkezi sürüm kontrol ve dağıtım servisidir. Android uygulaması API'ye kendi `package_name` ve `versionCode` bilgisiyle istek atar; servis son yayınlanan sürümü, güncellemenin zorunlu olup olmadığını ve APK indirme URL'sini döner.

## Yerel Geliştirme

Projenin veri diskinde çalışması beklenir:

```bash
cd /e/Codex/apk-apps
```

Windows PowerShell:

```powershell
cd E:\Codex\apk-apps
docker compose up -d --build
docker compose exec app composer install --no-interaction
docker compose exec app php artisan key:generate --force
docker compose exec app npm ci
docker compose exec app npm run build
docker compose exec app php artisan migrate --force
docker compose exec app php artisan admin:create
```

Admin paneli:

```text
http://localhost:8080
```

Sağlık kontrolleri:

```text
http://localhost:8080/health/live
http://localhost:8080/health/ready
```

## Docker Desktop

Compose proje adı `apk-apps` olarak görünür. Docker Desktop containerları göstermiyorsa Docker Desktop'ın `desktop-linux` context'ini kullandığını doğrulayın ve arayüz filtrelerini temizleyin.

CLI doğrulaması:

```powershell
docker context ls
docker compose ls
docker ps
```

Beklenen yerel servisler:

```text
apk-apps-web-1  127.0.0.1:8080->80/tcp
apk-apps-app-1  9000/tcp
apk-apps-db-1   127.0.0.1:54329->5432/tcp
```

## Kalite

```powershell
docker compose exec app composer validate --strict
docker compose exec app php artisan test
docker compose exec app vendor/bin/pint --test
docker compose exec app vendor/bin/phpstan analyse
docker compose exec app vendor/bin/deptrac analyse
docker compose exec app npm run build
```

Veya:

```powershell
make quality
```

## API

Güncelleme kontrolü:

```http
GET /api/v1/applications/{packageName}/channels/{channel}/update-check?current_version_code=12
```

APK indirme:

```http
GET /api/v1/artifacts/{releaseId}/download
```

Yalnızca yayınlanmış sürümler indirilebilir. Taslak yüklemeler bir kanala yayınlanana kadar erişilemez.
