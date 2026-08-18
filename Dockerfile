# 1. Etapa PHP: Instala dependencias de Composer
FROM richarvey/nginx-php-fpm:3.1.6 AS php_base
WORKDIR /app
COPY . .
RUN composer install --no-dev --optimize-autoloader

# 2. Etapa Node: Compila assets con Vite
FROM node:20-alpine AS node_builder
WORKDIR /app
COPY package*.json ./
RUN npm install
COPY . .
COPY --from=php_base /app/vendor ./vendor
RUN npm run build

# 3. Etapa Final de producción
FROM richarvey/nginx-php-fpm:3.1.6
WORKDIR /var/www/html

ENV WEBROOT=/var/www/html/public
ENV SKIP_COMPOSER=1
ENV PHP_ERRORS_STDERR=1
ENV PHP_MEM_LIMIT=256M
# Sesiones en archivo para evitar error "relation sessions does not exist"
# en el primer arranque (antes de que migrate cree la tabla sessions)
ENV SESSION_DRIVER=file

# Copiar código de la aplicación
COPY --from=php_base /app /var/www/html
COPY --from=node_builder /app/public/build /var/www/html/public/build

# ── Nginx ──────────────────────────────────────────────────────────────────
# Reemplazamos nginx.conf completo: la imagen incluye sites-enabled FUERA del
# bloque http{}, por lo que server{} era inválido. Con un nginx.conf propio
# controlamos el contexto correcto y garantizamos try_files para Laravel.
COPY docker/nginx.conf /etc/nginx/nginx.conf
RUN sed -i 's/\r//' /etc/nginx/nginx.conf

# ── Entrypoint ─────────────────────────────────────────────────────────────
# Corre artisan (config:cache, route:cache, migrate) con las env vars reales
# de Coolify ya inyectadas, y luego lanza supervisord (nginx + php-fpm).
COPY docker/entrypoint.sh /entrypoint.sh
# Convertir CRLF → LF (archivos creados en Windows) y dar permisos
RUN sed -i 's/\r//' /entrypoint.sh && chmod +x /entrypoint.sh

# Permisos de escritura para Laravel
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

EXPOSE 80

# Usar 'sh' explícito: Alpine no tiene /bin/bash instalado por defecto
CMD ["sh", "/entrypoint.sh"]
