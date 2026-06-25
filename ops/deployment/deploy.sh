#!/usr/bin/env bash
set -euo pipefail

compose_file="${COMPOSE_FILE:-compose.prod.yaml}"

docker compose -f "${compose_file}" pull --ignore-pull-failures
docker compose -f "${compose_file}" up -d --build
docker compose -f "${compose_file}" exec -T app php artisan migrate --force
docker compose -f "${compose_file}" exec -T app php artisan optimize
docker compose -f "${compose_file}" ps
