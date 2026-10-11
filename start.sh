#!/usr/bin/env bash
# Starts the demo with Docker. The first start installs everything and fills
# the database with the demo content (a few minutes); later starts are quick.
# Another port: DEMO_PORT=8080 ./start.sh
set -euo pipefail
cd "$(dirname "$0")"

port="${DEMO_PORT:-8000}"

docker compose up -d --build

printf 'Waiting for the app'
until curl -fsS -o /dev/null "http://localhost:$port/up" 2>/dev/null; do
  if [ -z "$(docker compose ps --status running -q app)" ]; then
    echo; echo "The app stopped. See: docker compose logs app" >&2
    exit 1
  fi
  printf '.'; sleep 2
done

echo
echo "Fernleaf help center: http://localhost:$port"
echo "Staff admin:          http://localhost:$port/admin   (admin@fernleaf.test / Pl3nts&P0ts!)"
echo "Reset the content:    docker compose exec app php artisan migrate:fresh --seed"
echo "Stop:                 docker compose down   (add -v to also delete the database)"
