# Android Client Integration

Android clients should call the update-check endpoint at app start or from a controlled update screen.

Example:

```http
GET https://apk.habersoft.com/api/v1/applications/com.habersoft.player/channels/stable/update-check?current_version_code=12
```

Statuses:

- `UPDATE_AVAILABLE`: show release notes and download URL.
- `UP_TO_DATE`: no action.
- `CLIENT_AHEAD`: installed build is newer than published channel.
- `NO_RELEASE`: channel has no published release.

If `required` is true, the client should block normal use until the update is installed.

Downloaded APKs must be verified against the `sha256` field or `X-APK-SHA256` response header before install handoff.
