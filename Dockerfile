# 1. Copiar Node.js y NPM directamente desde la imagen oficial
FROM node:20-alpine AS node_base

# 2. Imagen principal PHP
FROM php:8.2-fpm

# Copiar Node y NPM desde la etapa anterior
COPY --from=node_base /usr/local/bin /usr/local/bin
COPY --from=node_base /usr/local/lib/node_modules /usr/local/lib/node_modules

# Instalar dependencias del sistema necesarias
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    curl \
    && rm -rf /var/lib/apt/lists/*

# Instalar Composer desde la imagen oficial
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copiar archivos del proyecto
COPY . .

# Instalar dependencias de PHP y Frontend
RUN composer install --no-dev --optimize-autoloader
RUN npm install
RUN npm run build

# Ajustar permisos de directorios
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

EXPOSE 8000

CMD ["sh", "-c", "php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=8000"]