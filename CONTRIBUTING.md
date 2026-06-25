# Contributing

## Development Rules

- Keep business rules inside context-specific `Domain` and `Application` namespaces.
- Keep controllers thin; they should validate requests and call use cases.
- Do not introduce `app/Services`, `app/Repositories`, or global helper folders.
- Do not introduce hidden observers, queues, scheduler jobs, mail, Redis, or provider scraping.
- Every upload must remain draft until explicitly published.
- Draft artifacts must not be downloadable through the public API.

## Before Opening a PR

Run:

```powershell
docker compose exec app composer validate --strict
docker compose exec app php artisan test
docker compose exec app vendor/bin/pint --test
docker compose exec app vendor/bin/phpstan analyse
docker compose exec app vendor/bin/deptrac analyse
docker compose exec app npm run build
```

Document database, API, or operational changes under `docs/`.
