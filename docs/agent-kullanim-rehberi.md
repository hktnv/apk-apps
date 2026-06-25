# Agent Kullanım Rehberi

Bu rehber, `apk-apps` servisini API üzerinden kullanacak geliştiriciler ve otomasyon agentları için hazırlanmıştır.

## Servis Ne İşe Yarar?

`apk-apps`, Play Store dışından dağıtılan Android APK dosyaları için sürüm ve yayın yönetimi sağlar.

Bu servis ile:

- Android uygulama kaydı oluşturulur.
- İmzalı APK dosyası release olarak yüklenir.
- Release `stable`, `beta` veya `internal` kanalına yayınlanır.
- Gerekirse önceki bir release için rollback kaydı oluşturulur.
- Android istemcisi kendi `package_name` ve `versionCode` bilgisiyle güncelleme kontrolü yapar.

## Agent Kimlik Doğrulaması

Agent API uçları şu iki header ile korunur:

```http
X-Agent-Id: agt_PLACEHOLDER
X-Agent-Secret: sec_PLACEHOLDER
```

Base URL:

```text
https://apk.habersoft.com
```

Agent secret sadece oluşturma veya yenileme anında gösterilir. Veritabanında düz metin olarak tutulmaz; hashlenmiş hali saklanır.

## Ortak Cevap Formatı

Başarılı cevaplar:

```json
{
  "data": {},
  "meta": {
    "request_id": "01J..."
  }
}
```

Hatalı cevaplar:

```json
{
  "error": {
    "code": "AGENT_AUTH_FAILED",
    "message": "Agent bilgileri geçerli değil."
  },
  "meta": {
    "request_id": "01J..."
  }
}
```

Otomasyonlar hata okurken HTTP status code yanında `error.code` alanını da kontrol etmelidir.

## Uygulama Oluşturma Akışı

Slug göndermeyin. Slug sistem tarafından uygulama adına göre otomatik üretilir ve çakışma varsa benzersiz hale getirilir.

```bash
curl -X POST "https://apk.habersoft.com/api/v1/agent/applications" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -H "X-Agent-Id: agt_PLACEHOLDER" \
  -H "X-Agent-Secret: sec_PLACEHOLDER" \
  -d '{
    "name": "HaberSoft Player",
    "package_name": "com.habersoft.player",
    "description": "Android TV oynatıcı"
  }'
```

Önemli alanlar:

- `id`: Sonraki release ve publish işlemlerinde kullanılır.
- `package_name`: Android istemcisinin update-check isteğinde kullandığı değerdir.
- `slug`: Sistem tarafından üretilir.

## APK / Release Yükleme Akışı

Release yükleme multipart form-data ile yapılır.

```bash
curl -X POST "https://apk.habersoft.com/api/v1/agent/applications/{applicationId}/releases" \
  -H "Accept: application/json" \
  -H "X-Agent-Id: agt_PLACEHOLDER" \
  -H "X-Agent-Secret: sec_PLACEHOLDER" \
  -F "version_code=42" \
  -F "version_name=1.4.2" \
  -F "release_notes=Performans iyileştirmeleri" \
  -F "apk=@/path/to/app-release.apk"
```

Kurallar:

- `version_code` aynı uygulamadaki en yüksek değerden büyük olmalıdır.
- Dosya `.apk` uzantılı ve geçerli ZIP/APK içeriğine sahip olmalıdır.
- Yüklenen release hemen dağıtıma çıkmaz; ayrıca publish yapılmalıdır.

## Yayına Alma / Publish Akışı

Bir release seçilen kanala publish edilir.

```bash
curl -X POST "https://apk.habersoft.com/api/v1/agent/applications/{applicationId}/releases/{releaseId}/publish" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -H "X-Agent-Id: agt_PLACEHOLDER" \
  -H "X-Agent-Secret: sec_PLACEHOLDER" \
  -d '{
    "channel": "stable",
    "force_update": false,
    "minimum_supported_version_code": 0,
    "comment": "Stable yayın"
  }'
```

Kanallar:

- `stable`: Genel yayın
- `beta`: Beta test
- `internal`: İç test

`force_update` true ise Android istemci güncellemeyi zorunlu kabul etmelidir.

## Rollback Akışı

Rollback, sorunlu bir yayından sonra seçilen release'i tekrar aktif kanala bağlar.

```bash
curl -X POST "https://apk.habersoft.com/api/v1/agent/applications/{applicationId}/rollback" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -H "X-Agent-Id: agt_PLACEHOLDER" \
  -H "X-Agent-Secret: sec_PLACEHOLDER" \
  -d '{
    "release_id": "{releaseId}",
    "channel": "stable",
    "force_update": false,
    "minimum_supported_version_code": 0,
    "comment": "Sorunlu yayın geri alındı"
  }'
```

Android, cihazda daha yüksek `versionCode` yüklüyse otomatik downgrade yapmaz. Rollback yeni kurulumlar veya uygun cihazlar için güvenli bir dönüş noktası sağlar.

## Android Update Kontrol Mantığı

Android uygulama kendi `package_name`, kanal ve mevcut `versionCode` değeriyle update-check çağırır.

```bash
curl "https://apk.habersoft.com/api/v1/applications/com.habersoft.player/channels/stable/update-check?current_version_code=41" \
  -H "Accept: application/json"
```

Olası durumlar:

- `UPDATE_AVAILABLE`: Yeni sürüm var.
- `UP_TO_DATE`: Kurulu sürüm güncel.
- `CLIENT_AHEAD`: Kurulu sürüm kanaldaki yayından daha yeni.
- `NO_RELEASE`: Kanalda yayınlanmış release yok.

`UPDATE_AVAILABLE` cevabında `release.download_url`, `release.sha256`, `release.size_bytes`, `required` ve release notları bulunur. İstemci indirdiği APK'yı kurulumdan önce SHA-256 ile doğrulamalıdır.

## Secret Güvenliği

- Gerçek `agent_secret` hiçbir repoya, belgeye, log'a veya test fixture'a yazılmamalıdır.
- Secret kaybolursa admin panelden “Secret yenile” kullanılır.
- Secret yenilendiğinde eski secret hemen geçersiz olur.
- Agent pasifleştirilirse doğru secret gönderilse bile API isteği reddedilir.
- Agent bilgilerini CI/CD ortamında secret store veya güvenli environment variable olarak saklayın.

## Sık Hata Kodları

- `AGENT_CREDENTIALS_REQUIRED`: Header eksik.
- `AGENT_AUTH_FAILED`: Agent ID veya secret yanlış.
- `AGENT_INACTIVE`: Agent pasif.
- `APPLICATION_NOT_FOUND`: Uygulama bulunamadı.
- `DUPLICATE_PACKAGE_NAME`: Aynı package name daha önce kaydedilmiş.
- `VERSION_CODE_NOT_GREATER`: Yeni release version code mevcut en yüksek değerden büyük değil.
- `INVALID_CHANNEL`: Kanal adı geçerli değil.
- `RELEASE_NOT_FOUND`: Release bulunamadı veya ilgili uygulamaya ait değil.

## Minimum Mutlu Yol

1. Admin panelden agent oluşturun.
2. `agent_id` ve `agent_secret` değerlerini güvenli şekilde saklayın.
3. API ile uygulama oluşturun.
4. APK release yükleyin.
5. Release'i `stable` kanalına publish edin.
6. Android istemciden update-check çağırın.
7. Cevaptaki download URL ve SHA-256 bilgisiyle APK kurulum akışını başlatın.
