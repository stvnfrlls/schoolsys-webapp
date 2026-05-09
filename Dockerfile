FROM php:8.4-fpm

# ─────────────────────────────────────────────
# System dependencies
# ─────────────────────────────────────────────
RUN apt-get update && apt-get install -y \
    git curl libpng-dev libonig-dev libxml2-dev \
    libpq-dev libzip-dev zip unzip \
    && docker-php-ext-install \
    pdo pdo_mysql pdo_pgsql mbstring exif pcntl bcmath gd zip \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# ─────────────────────────────────────────────
# Redis extension via PECL
# ─────────────────────────────────────────────
RUN pecl install redis && docker-php-ext-enable redis

# ─────────────────────────────────────────────
# Composer
# ─────────────────────────────────────────────
COPY --from=composer:2.7 /usr/bin/composer /usr/bin/composer

# ─────────────────────────────────────────────
# PHP config overrides
# ─────────────────────────────────────────────
COPY docker/php/local.ini /usr/local/etc/php/conf.d/local.ini

# ─────────────────────────────────────────────
# Install Composer dependencies at build time
# ─────────────────────────────────────────────
WORKDIR /var/www/html

# Copy only composer files first — maximizes Docker layer cache
# (vendor only rebuilds when composer.json/lock actually changes)
COPY --chown=www-data:www-data composer.json composer.lock ./

RUN composer install --no-interaction --no-scripts --no-autoloader --prefer-dist

# Copy the rest of the application
COPY --chown=www-data:www-data . .

# Generate the optimized autoloader now that all files are present
RUN composer dump-autoload --optimize

RUN chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache