# 1. Etapa de Node para compilar assets (Vite / Inertia)
FROM node:20-slim AS node_builder
WORKDIR /app
COPY package*.json ./
RUN npm install
COPY . .
RUN npm run build

# 2. Etapa principal PHP + Nginx
FROM richarvey/nginx-php-fpm:php82

WORKDIR /var/www/html

# Copiar código y assets compilados
COPY . .
COPY --from=node_builder /app/public/build ./public/build

# Configurar variables de entorno para Nginx
ENV WEBROOT=/var/www/html/public
ENV PHP_ERRORS_STDERR=1

# Instalar dependencias de PHP vía Composer
RUN composer install --no-dev --optimize-autoloader

# Ajustar permisos para storage y cache
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

EXPOSE 80