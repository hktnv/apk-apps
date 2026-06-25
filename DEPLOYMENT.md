# Dağıtım

Üretim sunucusu hedef dizini:

```bash
/opt/habersoft-apk
```

## İlk Kurulum

```bash
git clone https://github.com/hktnv/apk-apps.git /opt/habersoft-apk
cd /opt/habersoft-apk
cp .env.example .env
```

Üretim `APP_KEY` değeri üretin:

```bash
docker run --rm php:8.4.7-cli-bookworm php -r "echo 'base64:'.base64_encode(random_bytes(32)).PHP_EOL;"
```

Üretilen anahtarı `.env` içine yazın; üretim veritabanı bilgilerini, `APP_ENV=production`, `APP_DEBUG=false` ve genel erişimli `APP_URL` değerlerini ayarlayın.

Üretim containerlarını başlatın:

```bash
docker compose -f compose.prod.yaml up -d --build
docker compose -f compose.prod.yaml exec app php artisan migrate --force
docker compose -f compose.prod.yaml exec app php artisan admin:create
```

## Güncelleme

```bash
cd /opt/habersoft-apk
git pull --ff-only origin main
docker compose -f compose.prod.yaml up -d --build
docker compose -f compose.prod.yaml exec app php artisan migrate --force
docker compose -f compose.prod.yaml exec app php artisan optimize
```

## Sağlık Kontrolü

```bash
curl -f http://127.0.0.1:8088/health/live
curl -f http://127.0.0.1:8088/health/ready
```

## Ters Proxy

`apk.habersoft.com`, OpenLiteSpeed vhost/ters proxy ile Docker web servisine yönlendirilecek. TLS ve güvenli cookie ayarlarını üretim kenarında açık tutun.

## Gizli Bilgiler

`.env` asla commitlenmez. Parola, token, GitHub credential, üretim `APP_KEY` değeri veya veritabanı dump dosyalarını commit etmeyin.

## APK Depolama

Yüklenen gerçek APK dosyaları Git içinde tutulmaz. Dosyalar çalışma zamanı depolamasında yaşar ve PostgreSQL metadata ile birlikte yedeklenmelidir.
