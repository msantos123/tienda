#!/usr/bin/env bash
set -e

echo "==> [Laravel] Iniciando configuracion de produccion..."

# Limpiar cache anterior (por si quedo cache de build incorrecta)
php /var/www/html/artisan optimize:clear --quiet 2>/dev/null || true

# Generar cache con las env vars reales inyectadas por Coolify en runtime
echo "==> [Laravel] Generando cache de configuracion, rutas y vistas..."
php /var/www/html/artisan config:cache
php /var/www/html/artisan route:cache
php /var/www/html/artisan view:cache
php /var/www/html/artisan event:cache

# Ejecutar migraciones centrales (crea sessions, tenants, domains, etc.)
echo "==> [Laravel] Ejecutando migraciones centrales..."
php /var/www/html/artisan migrate --force

# Crear symlink de storage si no existe
echo "==> [Laravel] Verificando storage:link..."
php /var/www/html/artisan storage:link 2>/dev/null || true

echo "==> [Laravel] Configuracion completada!"
