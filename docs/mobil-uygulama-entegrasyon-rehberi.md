# Mobil Uygulama Entegrasyon Rehberi

Bu rehber, Android/Kotlin uygulamalarının `apk-apps` servisi ile güncelleme kontrolü, APK indirme ve kurulum akışını nasıl kullanacağını anlatır.

## Mobil Uygulama Bu Servisi Neden Kullanır?

`apk-apps`, Play Store dışında dağıtılan APK'lar için kontrollü güncelleme sağlar.

Mobil uygulama bu servisle:

- Kendi kurulu sürümünün güncel olup olmadığını öğrenir.
- Yeni APK varsa kullanıcıya güncelleme gösterir.
- Zorunlu güncellemede normal kullanımı durdurabilir.
- APK dosyasını doğrulanabilir indirme URL'siyle alır.

## VersionCode ve VersionName Nasıl Düşünülmeli?

Android tarafında asıl karşılaştırma `versionCode` ile yapılır.

- `versionCode`: Her yeni APK'da artmalıdır. Örnek: `41`, sonra `42`.
- `versionName`: Kullanıcıya görünen sürüm adıdır. Örnek: `1.4.2`.

Servis, istemciden gelen `current_version_code` değerini kanaldaki yayınlanmış release `version_code` değeriyle karşılaştırır.

## Update Check Ne Zaman Yapılmalı?

Önerilen zamanlar:

- Uygulama açıldıktan sonra ana ekran hazır olduğunda.
- Kullanıcı ayarlardan “Güncellemeyi kontrol et” dediğinde.
- TV Box uygulamalarında oynatma başlamadan önce, kullanıcı deneyimini kilitlemeden.

Her ekrana geçişte veya çok sık aralıklarla update-check yapılmamalıdır. Ağ hatası varsa uygulama normal açılmalı, hata sade şekilde geçilmelidir.

## Update Check API İsteği

Endpoint:

```http
GET /api/v1/applications/{packageName}/channels/{channel}/update-check?current_version_code={versionCode}
```

Canlı örnek:

```bash
curl "https://apk.habersoft.com/api/v1/applications/com.habersoft.player/channels/stable/update-check?current_version_code=41" \
  -H "Accept: application/json"
```

Kanallar:

- `stable`
- `beta`
- `internal`

## Başarılı Cevap Nasıl Yorumlanır?

Güncelleme varsa örnek cevap:

```json
{
  "data": {
    "status": "UPDATE_AVAILABLE",
    "package_name": "com.habersoft.player",
    "channel": "stable",
    "current_version_code": 41,
    "published_version_code": 42,
    "required": false,
    "release": {
      "id": "01J...",
      "version_code": 42,
      "version_name": "1.4.2",
      "release_notes": "Performans iyileştirmeleri",
      "sha256": "abcdef...",
      "size_bytes": 25000000,
      "download_url": "https://apk.habersoft.com/api/v1/artifacts/01J.../download"
    }
  },
  "meta": {
    "request_id": "01J..."
  }
}
```

Durumlar:

- `UPDATE_AVAILABLE`: Yeni APK var. `release` alanı gelir.
- `UP_TO_DATE`: Kurulu sürüm güncel.
- `CLIENT_AHEAD`: Cihazdaki sürüm kanaldaki yayından daha yeni.
- `NO_PUBLISHED_RELEASE`: Bu kanalda yayınlanmış release yok.

`required` sadece `UPDATE_AVAILABLE` durumunda gelir.

## Zorunlu Güncelleme Davranışı

`required: true` ise uygulama:

- Güncelleme ekranını kapatılamaz veya tekrar gösterilir hale getirmeli.
- Kullanıcıya kısa ve açık bir mesaj göstermeli.
- Normal kullanımı güncelleme kurulana kadar engellemelidir.

`required: false` ise:

- Kullanıcıya “Şimdi güncelle” ve “Daha sonra” seçenekleri sunulabilir.
- TV Box tarafında kumandayla kolay seçilen iki net aksiyon yeterlidir.

## APK İndirme ve Kurulum Akışı

1. `download_url` ile APK indirilir.
2. İndirilen dosyanın SHA-256 değeri, response içindeki `release.sha256` ile karşılaştırılır.
3. Hash doğruysa Android kurulum ekranı açılır.
4. Kullanıcı onay verirse APK kurulur.

İndirme endpoint'i APK binary döner:

```http
GET /api/v1/artifacts/{releaseId}/download
```

Response header alanları:

```http
Content-Type: application/vnd.android.package-archive
X-APK-SHA256: ...
X-APK-Size: ...
```

## Android 8+ Bilinmeyen Kaynak İzni

Android 8 ve üzeri sürümlerde uygulamanın APK kurulum ekranını açabilmesi için “bu kaynaktan yüklemeye izin ver” izni gerekebilir.

Uygulama:

- İzin yoksa kullanıcıyı sistem ayarındaki ilgili ekrana yönlendirmeli.
- İzin verildikten sonra kurulumu tekrar başlatmalıdır.
- Bu izni otomatik aşmaya çalışmamalıdır.

## Hata Durumlarında Ne Yapılmalı?

Hatalı cevap formatı:

```json
{
  "error": {
    "code": "APPLICATION_NOT_FOUND",
    "message": "Uygulama bulunamadı."
  },
  "meta": {
    "request_id": "01J..."
  }
}
```

Önerilen davranış:

- Ağ yoksa: Uygulamayı aç, güncelleme kontrolünü daha sonra tekrar dene.
- `APPLICATION_NOT_FOUND`: Package name veya kanal ayarını kontrol et.
- `INVALID_CHANNEL`: Uygulamanın kanal değerini düzelt.
- `ARTIFACT_NOT_FOUND`: Güncelleme ekranını göstermeyi bırak, daha sonra tekrar dene.
- `ARTIFACT_UNAVAILABLE`: Kullanıcıya “Güncelleme şu anda indirilemiyor” mesajı göster.

Kullanıcıya teknik hata kodlarını doğrudan göstermek yerine kısa mesaj gösterin; hata kodunu log'a yazabilirsiniz.

## Kotlin Tarafı İçin Sade Akış

```kotlin
data class UpdateResponse(
    val data: UpdateData?,
    val error: ApiError?
)

suspend fun checkUpdate() {
    val packageName = context.packageName
    val versionCode = BuildConfig.VERSION_CODE
    val channel = "stable"

    val response = api.updateCheck(packageName, channel, versionCode)

    when (response.data?.status) {
        "UPDATE_AVAILABLE" -> {
            val release = response.data.release ?: return
            if (response.data.required == true) {
                showRequiredUpdateScreen(release)
            } else {
                showOptionalUpdateDialog(release)
            }
        }
        "UP_TO_DATE", "CLIENT_AHEAD", "NO_PUBLISHED_RELEASE" -> {
            continueApp()
        }
        else -> {
            continueApp()
        }
    }
}

suspend fun downloadAndInstall(release: ReleaseInfo) {
    val apkFile = downloader.download(release.downloadUrl)
    val actualSha256 = sha256(apkFile)

    if (actualSha256 != release.sha256) {
        showMessage("Güncelleme dosyası doğrulanamadı.")
        return
    }

    if (!canInstallUnknownApps()) {
        openUnknownAppsPermissionSettings()
        return
    }

    openAndroidInstallScreen(apkFile)
}
```

## İstatistikler Ne Anlama Gelir?

Servis, uygulama bazında toplam update-check ve APK indirme sayılarını tutar. Bu sayılar admin panelde ve agent API'de görünür.

- `update_check_count`: Mobil uygulamalardan gelen başarılı update-check istekleri.
- `update_available_count`: Yeni sürüm var cevabı dönen istekler.
- `up_to_date_count`: Kurulu sürüm güncel cevabı dönen istekler.
- `apk_download_count`: Başarılı APK indirme istekleri.

Mobil uygulama bu sayaçlar için ek veri göndermek zorunda değildir. IP, cihaz kimliği, user-agent analitiği veya kullanıcı takibi bu kapsamda tutulmaz.

## Güvenlik Notları

- Her zaman HTTPS kullanın.
- APK kurulmadan önce SHA-256 hash kontrolü yapın.
- Kurulum için kullanıcı onayı gerekir; bunu atlamaya çalışmayın.
- Mobil uygulamaya agent secret, admin token, `.env` değeri veya servis iç anahtarı gömmeyin.
- Agent API mobil uygulama içinde kullanılmamalıdır; agent erişimi CI/CD veya güvenli backend otomasyonları içindir.

## TV Box Notları

- Update kontrolünü uygulama açılır açılmaz ekranı kilitleyecek şekilde yapmayın; ana ekranı gösterip arka planda kontrol etmek daha iyi hissettirir.
- Zorunlu güncelleme ekranında kumanda odağı net olmalıdır.
- “Güncelle” ve “Çıkış” gibi az sayıda, büyük ve anlaşılır aksiyon kullanın.
- İndirme sırasında ilerleme gösterin; TV Box kullanıcıları uzun beklemede uygulamanın donduğunu düşünebilir.
