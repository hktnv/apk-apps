# Operations

## Health

- `GET /health/live`: process is alive.
- `GET /health/ready`: database and storage are reachable.

## Backup

Back up PostgreSQL and the APK artifact volume together. Database metadata without artifacts is incomplete.

Example scripts are in `ops/deployment/`.

## Restore

1. Stop web traffic.
2. Restore database.
3. Restore `storage/app/apks`.
4. Run `php artisan migrate --force`.
5. Check `/health/ready`.

## Incident Notes

- If an APK file is missing, public download returns `ARTIFACT_UNAVAILABLE`.
- If a draft release is requested, public download returns not found.
- Rollback creates a new publication record; it does not mutate old history.
