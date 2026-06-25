#!/usr/bin/env bash
set -euo pipefail

if [[ $# -ne 1 ]]; then
  echo "Usage: restore.sh <backup-directory>" >&2
  exit 1
fi

backup_dir="$1"
compose_file="${COMPOSE_FILE:-compose.prod.yaml}"

test -f "${backup_dir}/database.sql"
test -f "${backup_dir}/apks.tar.gz"

docker compose -f "${compose_file}" exec -T db psql -U "${DB_USERNAME:-apk_apps}" "${DB_DATABASE:-apk_apps}" < "${backup_dir}/database.sql"
cat "${backup_dir}/apks.tar.gz" | docker compose -f "${compose_file}" exec -T app tar -C /var/www/html/storage/app -xzf -
docker compose -f "${compose_file}" exec -T app php artisan migrate --force
