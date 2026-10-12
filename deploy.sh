#!/usr/bin/env bash
# Deploy Diksera di server aaPanel: tarik kode terbaru dari GitHub lalu refresh cache Laravel.
#
# Pemakaian (Terminal aaPanel):
#   bash /www/wwwroot/103.139.47.173/diksera/deploy.sh            -> update kode (tanpa migrate)
#   bash /www/wwwroot/103.139.47.173/diksera/deploy.sh --migrate  -> update kode + jalankan migration
#   --from <commit>  : basis pembanding (dipakai update.ps1 yang sudah pull duluan)
#
# Catatan:
# - Skrip berjalan sebagai user www (pemilik folder) agar kepemilikan file tidak berubah.
# - JANGAN pakai `php artisan config:cache`: kode memanggil env() langsung
#   (Telegram, API_KEY_DIKSERA) dan nilainya akan jadi null kalau config di-cache.
set -euo pipefail

APP_DIR="/www/wwwroot/103.139.47.173/diksera"
RUN_USER="www"

if [ "$(id -un)" != "$RUN_USER" ]; then
  exec sudo -u "$RUN_USER" bash "$0" "$@"
fi

cd "$APP_DIR"
export HOME="${HOME:-/tmp}" COMPOSER_HOME="/tmp/composer-$RUN_USER"
git() { command git -c safe.directory="$APP_DIR" "$@"; }

if [ -n "$(git status --porcelain --untracked-files=no -- . ':!database/migrations')" ]; then
  echo "!! Ada file yang diedit langsung di server. Batalkan dulu atau commit lewat lokal:"
  git status --short --untracked-files=no
  exit 1
fi

MIGRATE=0; FROM=""
while [ $# -gt 0 ]; do
  case "$1" in
    --migrate) MIGRATE=1 ;;
    --from) FROM="$2"; shift ;;
  esac
  shift
done

OLD=${FROM:-$(git rev-parse HEAD)}
echo ">> git pull"
git pull --ff-only origin main
NEW=$(git rev-parse HEAD)

if [ "$OLD" = "$NEW" ]; then
  echo ">> Sudah versi terbaru ($(git log --oneline -1))"
else
  echo ">> Perubahan $OLD..$NEW:"
  git log --oneline "$OLD..$NEW"
fi

if git diff --name-only "$OLD" "$NEW" | grep -qx 'composer.lock'; then
  echo ">> composer.lock berubah -> composer install"
  composer install --no-dev --optimize-autoloader --no-interaction
fi

NEW_MIGRATIONS=$(git diff --name-only --diff-filter=A "$OLD" "$NEW" -- database/migrations || true)
if [ "$MIGRATE" = 1 ]; then
  echo ">> migrate (pastikan database sudah di-backup)"
  php artisan migrate --force
elif [ -n "$NEW_MIGRATIONS" ]; then
  echo "!! Ada migration baru, BELUM dijalankan:"
  echo "$NEW_MIGRATIONS"
  echo "   Backup database dulu, lalu jalankan: bash $0 --migrate"
fi

echo ">> bersihkan cache"
php artisan route:clear
php artisan view:clear
php artisan cache:clear
php artisan config:clear

echo ">> selesai: $(git log --oneline -1)"
