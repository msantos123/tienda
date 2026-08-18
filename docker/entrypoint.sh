#!/bin/bash
set -e

echo "======================================"
echo " Laravel Production Startup"
echo "======================================"

cd /var/www/html

# Limpiar cache anterior (puede tener valores del build)
echo "[1/5] Limpiando cache antigua..."
php artisan optimize:clear --quiet 2>/dev/null || true

# Generar cache con env vars reales de Coolify
echo "[2/5] Generando cache de config/rutas/vistas..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# Migraciones centrales (crea sessions, tenants, domains, etc.)
echo "[3/5] Ejecutando migraciones centrales..."
php artisan migrate --force

# Storage symlink
echo "[4/5] Storage symlink..."
php artisan storage:link 2>/dev/null || true

echo "[5/5] Todo listo. Iniciando supervisord..."
echo "======================================"

# Iniciar supervisord (nginx + php-fpm)
exec /usr/bin/supervisord -n -c /etc/supervisord.conf
