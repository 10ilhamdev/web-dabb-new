# Stage 1: Composer dependencies
FROM composer:2.9 AS vendor

WORKDIR /app

# Copy composer files terlebih dahulu agar cache lebih efisien
COPY composer.json composer.lock ./

# Install dependencies (vendor)
RUN composer install --no-interaction --prefer-dist --optimize-autoloader --no-scripts

# Stage 2: PHP runtime
FROM php:8.3-fpm-bookworm

WORKDIR /var/www

# 1. Install system dependencies & libraries
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    libonig-dev \
    libxml2-dev \
    libicu-dev \
    git \
    unzip \
    zip \
    curl \
    && docker-php-ext-install -j$(nproc) bcmath intl mbstring xml

# 2. Install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) pdo pdo_mysql gd zip

# 3. Copy source code
COPY . .

# 4. Copy vendor dari stage Composer
COPY --from=vendor /app/vendor ./vendor

# 5. Buat folder Laravel yang wajib ada
RUN mkdir -p /var/www/storage /var/www/bootstrap/cache

# 6. Set permission untuk Laravel
RUN chown -R www-data:www-data /var/www \
    && chmod -R 775 /var/www/storage /var/www/bootstrap/cache

CMD ["php-fpm"]
