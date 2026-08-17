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

# Copiar archivos
COPY --from=php_base /app /var/www/html
COPY --from=node_builder /app/public/build /var/www/html/public/build

# Configuración de permisos
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

EXPOSE 80