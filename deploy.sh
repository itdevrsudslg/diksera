#!/usr/bin/env bash
# Deploy Diksera di server aaPanel: tarik kode terbaru dari GitHub lalu refresh Laravel.
# Pemakaian (di server): bash /www/wwwroot/103.139.47.173/diksera/deploy.sh
set -euo pipefail

APP_DIR="/www/wwwroot/103.139.47.173/diksera"
cd "$APP_DIR"

git config --global --get-all safe.directory | grep -qx "$APP_DIR" \
  || git config --global --add safe.directory "$APP_DIR"

echo ">> git pull"
git pull --ff-only origin main

echo ">> composer install"
composer install --no-dev --optimize-autoloader --no-interaction

echo ">> migrate"
php artisan migrate --force

echo ">> clear & rebuild cache"
php artisan optimize:clear
php artisan config:cache
php artisan view:cache

echo ">> selesai: $(git log --oneline -1)"
