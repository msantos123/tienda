FROM php:8.2-fpm

# 1. Instalar dependencias del sistema y Node.js/NPM
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    curl \
    && curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs

# 2. Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# 3. Copiar el código del proyecto
COPY . .

# 4. Instalar dependencias de PHP y Node.js
RUN composer install --no-dev --optimize-autoloader
RUN npm install

# 5. Compilar los assets con Vite (crea public/build/manifest.json)
RUN npm run build

# Permisos de almacenamiento
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

EXPOSE 8000

CMD ["sh", "-c", "php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=8000"]