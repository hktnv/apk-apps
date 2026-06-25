# ADR 0002: PostgreSQL and Docker Runtime

## Status

Accepted

## Decision

PostgreSQL 17 is the only supported database. Production runtime uses Nginx, PHP-FPM, and PostgreSQL through Docker Compose.

## Consequences

The local and production database behavior stays consistent. The app avoids SQLite-only behavior in feature tests where release metadata matters.
