# Dağıtım

## Üretim Compose

`.env.example` dosyasından `.env` oluşturun; gerçek `APP_KEY`, veritabanı parolası, trusted proxy ve genel erişimli URL değerlerini ayarlayın.

```powershell
copy .env.example .env
docker run --rm php:8.4.7-cli-bookworm php -r "echo 'base64:'.base64_encode(random_bytes(32)).PHP_EOL;"
```

Compose yapılandırmasını doğrulayın:

```powershell
$env:DB_PASSWORD='change-this-secret'
docker compose -f compose.prod.yaml config
```

Build edip çalıştırın:

```powershell
docker compose -f compose.prod.yaml up -d --build
docker compose -f compose.prod.yaml exec app php artisan migrate --force
docker compose -f compose.prod.yaml exec app php artisan admin:create
```

## Ters Proxy

Örnek Nginx vhost dosyası `ops/nginx/apk.habersoft.com.conf` altında bulunur.

TLS'i edge proxy üzerinde sonlandırın ve trafiği compose web servisine yönlendirin. Üretim ortamında `SESSION_SECURE_COOKIE=true` değerini koruyun.
