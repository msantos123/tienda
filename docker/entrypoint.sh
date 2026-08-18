#!/bin/sh
set -e

echo "======================================"
echo " Laravel Production Startup"
echo "======================================"

cd /var/www/html

echo "[1/6] Limpiando cache antigua..."
php artisan optimize:clear --quiet 2>/dev/null || true

echo "[2/6] Generando cache de config/rutas/vistas..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

echo "[3/6] Ejecutando migraciones centrales..."
php artisan migrate --force

echo "[4/6] Storage symlink..."
php artisan storage:link 2>/dev/null || true

echo "[5/6] Fijando permisos de storage para www-data..."
# Los comandos artisan corren como root y crean archivos en storage/.
# PHP-FPM corre como www-data -> necesita ser propietario de esos archivos.
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

echo "[6/6] Todo listo. Iniciando supervisord..."
echo "======================================"

exec /usr/bin/supervisord -n -c /etc/supervisord.conf