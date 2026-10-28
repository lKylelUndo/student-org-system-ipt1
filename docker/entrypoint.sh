#!/bin/sh
# Laravel production entrypoint for Render (Docker runtime).
# Boot sequence: caches -> migrations -> seed -> serve on $PORT.

set -e

if [ -z "${PHP_CLI_SERVER_WORKERS-}" ]; then
    export PHP_CLI_SERVER_WORKERS=4
fi

echo "==> Optimizing framework caches..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "==> Running migrations..."
php artisan migrate --force

echo "==> Seeding (idempotent)..."
php artisan db:seed --force || echo "[entrypoint] seed skipped/failed, continuing"

echo "==> Starting server on port ${PORT:-10000}..."
exec php artisan serve --host=0.0.0.0 --port="${PORT:-10000}" --no-reload
