FROM richarvey/nginx-php-fpm:3.1.6

WORKDIR /var/www/html

# 1. Instalar Node.js 20 en la misma imagen
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs

# 2. Copiar todo el proyecto
COPY . .

# 3. Configurar variables básicas para Nginx
ENV WEBROOT=/var/www/html/public
ENV PHP_ERRORS_STDERR=1

# 4. Instalar dependencias PHP (genera la carpeta /vendor necesaria para Ziggy)
RUN composer install --no-dev --optimize-autoloader

# 5. Instalar dependencias Node y compilar assets
RUN npm install && npm run build

# 6. Permisos de directorios de almacenamiento
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

EXPOSE 80