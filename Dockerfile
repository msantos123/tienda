# 1. Etapa PHP: Instala dependencias
FROM richarvey/nginx-php-fpm:3.1.6 AS php_base
WORKDIR /app
COPY . .
RUN composer install --no-dev --optimize-autoloader

# 2. Etapa Node: Compila assets
FROM node:20-alpine AS node_builder
WORKDIR /app
COPY package*.json ./
RUN npm install
COPY . .
COPY --from=php_base /app/vendor ./vendor
RUN npm run build

# 3. Etapa Final
FROM richarvey/nginx-php-fpm:3.1.6
WORKDIR /var/www/html

# Configuración explícita de servidor web para Laravel
ENV WEBROOT=/var/www/html/public
ENV LARA_APP=true
ENV SKIP_COMPOSER=1
ENV PHP_ERRORS_STDERR=1
# Fallback seguro: usar sesiones en archivo para evitar error "relation sessions does not exist"
# Coolify puede sobreescribir esto con SESSION_DRIVER=database una vez que existan las tablas.
ENV SESSION_DRIVER=file

# Copiar archivos de la aplicación
COPY --from=php_base /app /var/www/html
COPY --from=node_builder /app/public/build /var/www/html/public/build

# Copiar script de arranque (richarvey/nginx-php-fpm lo ejecuta automáticamente con LARA_APP=true)
# Este script corre en runtime con las env vars reales de Coolify ya inyectadas.
COPY scripts/run.sh /var/www/html/scripts/run.sh
RUN chmod +x /var/www/html/scripts/run.sh

# Configuración de permisos
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

EXPOSE 80
