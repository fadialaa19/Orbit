#!/bin/sh
set -e

php artisan package:discover --ansi
php artisan storage:link || true
php artisan migrate --force
php artisan db:seed --force
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Process queued jobs (verification/reset emails) in the background so slow
# SMTP calls never block an HTTP request and trigger a gateway timeout.
php artisan queue:work --tries=3 --timeout=90 --sleep=3 &

exec php artisan serve --host=0.0.0.0 --port="${PORT:-10000}"
