#!/bin/sh
set -e

echo "======================================"
echo " Laravel Production Startup"
echo "======================================"

cd /var/www/html

echo "[1/5] Limpiando cache antigua..."
php artisan optimize:clear --quiet 2>/dev/null || true

echo "[2/5] Generando cache de config/rutas/vistas..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

echo "[3/5] Ejecutando migraciones centrales..."
php artisan migrate --force

echo "[4/5] Storage symlink..."
php artisan storage:link 2>/dev/null || true

echo "[5/5] Todo listo. Iniciando supervisord..."
echo "======================================"

exec /usr/bin/supervisord -n -c /etc/supervisord.conf