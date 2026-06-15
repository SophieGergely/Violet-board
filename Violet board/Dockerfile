FROM php:8.2-fpm

RUN apt-get update && apt-get install -y \
    git curl libpng-dev libpq-dev libzip-dev zip unzip \
    && docker-php-ext-install pdo pdo_pgsql pgsql gd zip \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

COPY composer.json composer.lock ./
RUN composer install --no-scripts --no-autoloader --prefer-dist

COPY . .

RUN composer dump-autoload --optimize

# Fix storage permissions
RUN mkdir -p storage/logs storage/framework/sessions storage/framework/views storage/framework/cache \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

EXPOSE 9000
CMD ["php-fpm"]
