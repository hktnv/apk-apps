# Project Agent Notes

This repository is `apk-apps`, a private APK release management service.

## Product Direction

- Laravel 13, PHP 8.4, PostgreSQL 17, Blade, Vite, Nginx, and PHP-FPM are the default stack.
- Keep UI -> application -> domain -> infrastructure dependencies one-way.
- Do not add public app discovery, scraping, DRM bypass, or unauthorized distribution features.
- Treat APK artifacts, admin accounts, update policies, and production secrets as sensitive.
- Work from `E:\Codex\apk-apps` on this machine. Do not move or delete files from `C:` without explicit user approval.

## Local Commands

- `docker compose up -d --build`
- `docker compose exec app php artisan migrate --force`
- `docker compose exec app php artisan test`
- `docker compose exec app vendor/bin/pint --test`
- `docker compose exec app vendor/bin/phpstan analyse`
- `docker compose exec app vendor/bin/deptrac analyse`
- `docker compose exec app npm run build`
- `make quality`
