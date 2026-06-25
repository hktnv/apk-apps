# Katkı Rehberi

## Geliştirme Kuralları

- İş kurallarını ilgili context altındaki `Domain` ve `Application` namespace'lerinde tutun.
- Controller sınıfları ince kalmalı; yalnızca istek doğrulamalı ve use case çağırmalıdır.
- `app/Services`, `app/Repositories` veya global yardımcı klasörleri eklemeyin.
- Gizli observer, queue, scheduler job, mail, Redis veya sağlayıcı kazıma akışları eklemeyin.
- Her APK yüklemesi açıkça yayınlanana kadar taslak kalmalıdır.
- Taslak çıktı dosyaları genel erişimli API üzerinden indirilemez olmalıdır.

## PR Açmadan Önce

Çalıştırın:

```powershell
docker compose exec app composer validate --strict
docker compose exec app php artisan test
docker compose exec app vendor/bin/pint --test
docker compose exec app vendor/bin/phpstan analyse
docker compose exec app vendor/bin/deptrac analyse
docker compose exec app npm run build
```

Veritabanı, API veya operasyonel değişiklikleri `docs/` altında belgeleyin.
