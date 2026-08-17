# 1. Etapa PHP: Instala dependencias de Composer
FROM richarvey/nginx-php-fpm:3.1.6 AS php_base
WORKDIR /app
COPY . .
RUN composer install --no-dev --optimize-autoloader

# 2. Etapa Node: Compila los assets de Vite/Inertia
FROM node:20-alpine AS node_builder
WORKDIR /app
COPY package*.json ./
RUN npm install
COPY . .
COPY --from=php_base /app/vendor ./vendor
RUN npm run build

# 3. Etapa Final: Servidor PHP + Nginx listo para producción
FROM richarvey/nginx-php-fpm:3.1.6
WORKDIR /var/www/html

ENV WEBROOT=/var/www/html/public
ENV PHP_ERRORS_STDERR=1
# ESTA LÍNEA EVITA EL ERROR DE PRESTISSIMO AL ARRANCAR
ENV SKIP_COMPOSER=1 

# Copiar el proyecto con vendor y los assets ya compilados
COPY --from=php_base /app /var/www/html
COPY --from=node_builder /app/public/build /var/www/html/public/build

# Permisos de almacenamiento
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

EXPOSE 80