# Mimari

APK Apps context odaklı katmanlama kullanır:

- `IdentityAccess`: admin kullanıcıları, giriş, çıkış ve admin oluşturma komutu.
- `ApplicationCatalog`: yönetilen Android uygulamaları ve paket adları.
- `ReleaseDistribution`: APK doğrulama, çıktı depolama, sürüm yayınlama, rollback, güncelleme kontrolleri ve indirme akışları.
- `SharedKernel`: identifier, clock, transaction ve operation result gibi context'ler arası küçük sözleşmeler.

## Bağımlılık Yönü

Presentation katmanı application use case'lerini çağırır. Application kodu domain nesnelerine ve port sözleşmelerine bağlıdır. Infrastructure katmanı portları uygular ve Laravel kalıcılık, depolama ve framework imkanlarıyla konuşur.

```text
Presentation -> Application -> Domain
Infrastructure -> Application / Domain
SharedKernel yeniden kullanılabilir destek kodudur.
```

Bu sınırlar Deptrac ile denetlenir.

## Kalıcılık

PostgreSQL şu verileri saklar:

- `admin_users`
- `managed_applications`
- `apk_releases`
- `release_publications`
- Laravel `sessions` ve `cache`

APK dosyaları yapılandırılmış `apks` filesystem diskinde saklanır. Veritabanı metadata, path, size, SHA-256 hash ve yayın geçmişini tutar.

## Sürüm Yaşam Döngüsü

1. Admin yönetilen uygulama kaydı oluşturur.
2. Admin APK yükler. Sürüm yalnızca taslak olarak kalır.
3. Admin sürümü `stable`, `beta` veya `internal` kanalına yayınlar.
4. Android istemcisi paket adı, kanal ve mevcut version code ile güncelleme kontrolü yapar.
5. İstemci yalnızca o anda yayınlanmış çıktı dosyasını indirir.
6. Admin bir kanalı daha eski bir yüklü sürüme rollback edebilir.
