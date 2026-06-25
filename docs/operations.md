# Operasyon

## Sağlık

- `GET /health/live`: süreç çalışıyor.
- `GET /health/ready`: veritabanı ve storage erişilebilir.

## Yedekleme

PostgreSQL ve APK çıktı volume'unu birlikte yedekleyin. Çıktı dosyaları olmadan veritabanı metadata tek başına eksiktir.

Örnek scriptler `ops/deployment/` altında bulunur.

## Geri Yükleme

1. Web trafiğini durdurun.
2. Veritabanını geri yükleyin.
3. `storage/app/apks` içeriğini geri yükleyin.
4. `php artisan migrate --force` çalıştırın.
5. `/health/ready` kontrolünü yapın.

## Olay Notları

- APK dosyası eksikse genel erişimli indirme `ARTIFACT_UNAVAILABLE` döner.
- Taslak bir sürüm istenirse genel erişimli indirme bulunamadı döner.
- Rollback yeni bir publication kaydı oluşturur; eski geçmişi değiştirmez.
