# apk-apps

APK Apps is a private APK release distribution service for Android applications. It provides an admin panel for uploading draft APK releases, publishing releases to `stable`, `beta`, or `internal` channels, rolling back channels, and a small Android client API for update checks and artifact downloads.

The runtime stack is Laravel 13, PHP 8.4, PostgreSQL 17, Nginx, PHP-FPM, Blade, and Vite.

## Purpose

`apk-apps` is a central version control and distribution service for Android/Kotlin APK projects distributed outside Play Store. Android apps call the API with their `package_name` and `versionCode`; the service returns the latest published release, whether the update is required, and the APK download URL.

## Local Development

The project is expected to live on the data drive:

```bash
cd /e/Codex/apk-apps
```

On Windows PowerShell:

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

Admin panel:

```text
http://localhost:8080
```

Health checks:

```text
http://localhost:8080/health/live
http://localhost:8080/health/ready
```

## Docker Desktop

The compose project name is `apk-apps`. If Docker Desktop does not show the containers, verify that Docker Desktop is using the `desktop-linux` context and clear any UI filters.

CLI verification:

```powershell
docker context ls
docker compose ls
docker ps
```

Expected local services:

```text
apk-apps-web-1  127.0.0.1:8080->80/tcp
apk-apps-app-1  9000/tcp
apk-apps-db-1   127.0.0.1:54329->5432/tcp
```

## Quality

```powershell
docker compose exec app composer validate --strict
docker compose exec app php artisan test
docker compose exec app vendor/bin/pint --test
docker compose exec app vendor/bin/phpstan analyse
docker compose exec app vendor/bin/deptrac analyse
docker compose exec app npm run build
```

Or:

```powershell
make quality
```

## API

Update check:

```http
GET /api/v1/applications/{packageName}/channels/{channel}/update-check?current_version_code=12
```

Artifact download:

```http
GET /api/v1/artifacts/{releaseId}/download
```

Only published releases can be downloaded. Draft uploads remain inaccessible until published to a channel.
