# Deployment

## Production Compose

Create `.env` from `.env.example`, set a real `APP_KEY`, database password, trusted proxy, and public URL.

```powershell
copy .env.example .env
docker run --rm php:8.4.7-cli-bookworm php -r "echo 'base64:'.base64_encode(random_bytes(32)).PHP_EOL;"
```

Validate compose:

```powershell
$env:DB_PASSWORD='change-this-secret'
docker compose -f compose.prod.yaml config
```

Build and run:

```powershell
docker compose -f compose.prod.yaml up -d --build
docker compose -f compose.prod.yaml exec app php artisan migrate --force
docker compose -f compose.prod.yaml exec app php artisan admin:create
```

## Reverse Proxy

An example Nginx vhost is provided at `ops/nginx/apk.habersoft.com.conf`.

Terminate TLS at the edge proxy and forward to the compose web service. Keep `SESSION_SECURE_COOKIE=true` in production.
