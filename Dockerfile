FROM php:8.2-fpm

# Instalar dependencias del sistema requeridas para GD y Zip
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libfreetype6-dev \
    libjpeg66-turbo-dev \
    zip \
    unzip \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo_mysql gd bcmath \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Copiar ejecutable de Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copiar el proyecto
COPY . .

# Instalar dependencias de Laravel optimizadas para producción
RUN composer install --no-dev --optimize-autoloader --no-interaction

EXPOSE 9000
CMD ["php-fpm"]