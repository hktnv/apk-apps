# Architecture

APK Apps uses context-oriented layering:

- `IdentityAccess`: admin users, login, logout, and admin creation command.
- `ApplicationCatalog`: managed Android applications and package names.
- `ReleaseDistribution`: APK validation, artifact storage, release publishing, rollback, update checks, and downloads.
- `SharedKernel`: small cross-context contracts such as identifiers, clocks, transactions, and operation results.

## Dependency Direction

Presentation calls application use cases. Application code depends on domain objects and ports. Infrastructure implements ports and talks to Laravel persistence, storage, and framework facilities.

```text
Presentation -> Application -> Domain
Infrastructure -> Application / Domain
SharedKernel is reusable support code.
```

Deptrac enforces these boundaries.

## Persistence

PostgreSQL stores:

- `admin_users`
- `managed_applications`
- `apk_releases`
- `release_publications`
- Laravel `sessions` and `cache`

APK files are stored on the configured `apks` filesystem disk. The database stores metadata, path, size, SHA-256 hash, and publication history.

## Release Lifecycle

1. Admin creates a managed application.
2. Admin uploads an APK. The release is draft only.
3. Admin publishes a release to `stable`, `beta`, or `internal`.
4. Android client checks for updates by package name, channel, and current version code.
5. Client downloads only the currently published artifact.
6. Admin can rollback a channel to an older uploaded release.
