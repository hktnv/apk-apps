#!/usr/bin/env bash
set -euo pipefail

timestamp="$(date -u +%Y%m%dT%H%M%SZ)"
backup_dir="${BACKUP_DIR:-./backups/${timestamp}}"
compose_file="${COMPOSE_FILE:-compose.prod.yaml}"

mkdir -p "${backup_dir}"

docker compose -f "${compose_file}" exec -T db pg_dump -U "${DB_USERNAME:-apk_apps}" "${DB_DATABASE:-apk_apps}" > "${backup_dir}/database.sql"
docker compose -f "${compose_file}" exec -T app tar -C /var/www/html/storage/app -czf - apks > "${backup_dir}/apks.tar.gz"

echo "Backup written to ${backup_dir}"
