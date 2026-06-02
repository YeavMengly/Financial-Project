FROM php:8.2-fpm

WORKDIR /var/www

# =========================
# System dependencies
# =========================
RUN apt-get update && apt-get install -y \
    git \
    curl \
    zip \
    unzip \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    libcurl4-openssl-dev \
    && rm -rf /var/lib/apt/lists/*

# =========================
# GD CONFIG (IMPORTANT FIX)
# =========================
# =========================
# Install system dependencies (IMPORTANT)
# =========================
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    libzip-dev \
    zip \
    unzip \
    git \
    curl

# =========================
# Configure GD BEFORE install
# =========================
RUN docker-php-ext-configure gd \
    --with-freetype \
    --with-jpeg

# =========================
# PHP core extensions
# =========================
RUN docker-php-ext-install \
    pdo \
    pdo_mysql \
    mbstring \
    exif \
    pcntl \
    bcmath \
    gd \
    zip

# =========================
# OPcache (IMPORTANT for performance)
# =========================
RUN docker-php-ext-install opcache

# Enable OPcache properly
RUN docker-php-ext-enable opcache

# =========================
# Redis extension
# =========================
RUN pecl install redis \
    && docker-php-ext-enable redis

# =========================
# Composer
# =========================
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# =========================
# Copy full project FIRST (IMPORTANT FIX)
# =========================
COPY . .

# =========================
# Install dependencies
# =========================
ENV COMPOSER_MEMORY_LIMIT=-1

# RUN composer install \
#     --no-dev \
#     --prefer-dist \
#     --no-interaction \
#     --no-progress

# =========================
# Permissions
# =========================
RUN chown -R www-data:www-data /var/www \
    && chmod -R 775 storage bootstrap/cache

CMD ["php-fpm"]