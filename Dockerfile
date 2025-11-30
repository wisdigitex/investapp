FROM php:8.2-cli

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    libonig-dev \
    libxml2-dev \
    && docker-php-ext-install pdo_mysql mbstring zip

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy composer files and install dependencies
COPY composer.json composer.lock* ./
RUN composer install --no-dev --optimize-autoloader

# Copy the rest of the app
COPY . .

# Laravel storage link (won't fail app if it errors)
# This will run at container start instead of build so env is ready
CMD sh -c "php artisan storage:link || true && php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=${PORT:-8000}"
