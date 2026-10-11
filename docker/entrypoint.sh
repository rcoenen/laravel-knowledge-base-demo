#!/usr/bin/env bash
# Prepares the app on first start (dependencies, .env, key, database with the
# demo content), then runs the given command. Later starts skip what's done.
set -euo pipefail

if [ ! -f vendor/autoload.php ]; then
  composer install --no-interaction --prefer-dist
fi

if [ ! -f .env ]; then
  cp .env.example .env
fi

# Point the app at the Postgres container. These go into .env, not into the
# container's environment, so the tests keep using their own in-memory SQLite
# database and never touch the demo data.
set_env() {
  sed -i -E "/^#? ?$1=/d" .env
  echo "$1=$2" >> .env
}
set_env DB_CONNECTION pgsql
set_env DB_HOST postgres
set_env DB_PORT 5432
set_env DB_DATABASE fernleaf
set_env DB_USERNAME fernleaf
set_env DB_PASSWORD fernleaf
set_env APP_URL "http://localhost:${DEMO_PORT:-8000}"

if ! grep -q '^APP_KEY=base64:' .env; then
  php artisan key:generate --force
fi

# Serve files on the public disk (images in articles) under /storage, like
# `php artisan storage:link`, but relative so the link also works outside
# the container.
if [ ! -e public/storage ]; then
  ln -s ../storage/app/public public/storage
fi

if ! php artisan migrate:status >/dev/null 2>&1; then
  php artisan migrate --force --seed
fi

exec "$@"
