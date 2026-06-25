# Android İstemci Entegrasyonu

Android istemcileri uygulama başlangıcında veya kontrollü bir güncelleme ekranında update-check endpoint'ini çağırmalıdır.

Örnek:

```http
GET https://apk.habersoft.com/api/v1/applications/com.habersoft.player/channels/stable/update-check?current_version_code=12
```

Durumlar:

- `UPDATE_AVAILABLE`: release notes ve download URL gösterilir.
- `UP_TO_DATE`: işlem gerekmez.
- `CLIENT_AHEAD`: kurulu build yayınlanmış kanaldan daha yenidir.
- `NO_RELEASE`: kanalda yayınlanmış sürüm yoktur.

`required` true ise istemci güncelleme kurulana kadar normal kullanımı engellemelidir.

İndirilen APK dosyası kurulum akışına verilmeden önce `sha256` alanı veya `X-APK-SHA256` response header değeriyle doğrulanmalıdır.
