# Deployment

Production server target directory:

```bash
/opt/habersoft-apk
```

## First Install

```bash
git clone https://github.com/hktnv/apk-apps.git /opt/habersoft-apk
cd /opt/habersoft-apk
cp .env.example .env
```

Generate a production `APP_KEY`:

```bash
docker run --rm php:8.4.7-cli-bookworm php -r "echo 'base64:'.base64_encode(random_bytes(32)).PHP_EOL;"
```

Write the generated key into `.env`, set production database credentials, set `APP_ENV=production`, `APP_DEBUG=false`, and set the public `APP_URL`.

Start production containers:

```bash
docker compose -f compose.prod.yaml up -d --build
docker compose -f compose.prod.yaml exec app php artisan migrate --force
docker compose -f compose.prod.yaml exec app php artisan admin:create
```

## Update

```bash
cd /opt/habersoft-apk
git pull --ff-only origin main
docker compose -f compose.prod.yaml up -d --build
docker compose -f compose.prod.yaml exec app php artisan migrate --force
docker compose -f compose.prod.yaml exec app php artisan optimize
```

## Health Check

```bash
curl -f http://127.0.0.1:8088/health/live
curl -f http://127.0.0.1:8088/health/ready
```

## Reverse Proxy

`apk.habersoft.com` will be routed through an OpenLiteSpeed vhost/reverse proxy to the Docker web service. Keep TLS and secure cookies enabled at production edge.

## Secrets

`.env` is never committed. Do not commit passwords, tokens, GitHub credentials, production `APP_KEY`, or database dumps.

## APK Storage

Uploaded real APK files are not stored in Git. They live in runtime storage and must be backed up together with PostgreSQL metadata.
