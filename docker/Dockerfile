FROM php:8.3-fpm-alpine

# Systemowe pakiety + PHP extensions
RUN apk add --no-cache \
    bash nginx curl git unzip tzdata supervisor \
    libzip-dev libpng-dev libjpeg-turbo-dev freetype-dev icu-dev \
    zlib-dev oniguruma-dev libxml2-dev \
    && docker-php-ext-install pdo pdo_mysql zip intl gd bcmath opcache

# Composer
COPY --from=composer:2.7 /usr/bin/composer /usr/bin/composer

# Ustawienia pracy
WORKDIR /var/www

# Skopiuj cały kod aplikacji
COPY . .

# Instalacja zależności
RUN composer install --optimize-autoloader --no-dev --no-scripts

# Ustaw uprawnienia do storage/cache
RUN chown -R www-data:www-data /var/www \
    && chmod -R 755 /var/www/storage /var/www/bootstrap/cache

# Nginx config
COPY docker/nginx.conf /etc/nginx/nginx.conf

# Supervisord (do uruchamiania PHP-FPM i NGINX w jednym kontenerze)
COPY docker/supervisord.conf /etc/supervisord.conf

EXPOSE 80

CMD ["/usr/bin/supervisord", "-c", "/etc/supervisord.conf"]
