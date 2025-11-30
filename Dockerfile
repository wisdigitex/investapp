# ---------------------------
# Stage 1: Build dependencies
# ---------------------------
FROM composer:2 AS vendor

WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-progress

# ---------------------------
# Stage 2: Main PHP runtime
# ---------------------------
FROM php:8.2-fpm

# Install extensions
RUN apt-get update && apt-get install -y \
    zip unzip curl libpng-dev libonig-dev libxml2-dev gnupg gosu \
    && docker-php-ext-install pdo pdo_mysql mbstring exif pcntl bcmath gd

# Copy application
WORKDIR /var/www/html
COPY . .

# Copy vendor files from stage 1
COPY --from=vendor /app/vendor ./vendor

# Set permissions
RUN chmod -R 775 storage bootstrap/cache

# Expose port
EXPOSE 8080

# Start Laravel using PHP built-in server
CMD php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=8080
