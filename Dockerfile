# ─────────────────────────────────────────────
# Stage 1: Composer dependencies
# ─────────────────────────────────────────────
FROM composer:2 AS vendor

WORKDIR /app

COPY composer.json composer.lock ./
RUN composer install \
    --no-dev \
    --no-scripts \
    --no-autoloader \
    --prefer-dist \
    --ignore-platform-reqs

COPY . .
RUN composer dump-autoload --optimize --classmap-authoritative

# ─────────────────────────────────────────────
# Stage 2: Final image
# ─────────────────────────────────────────────
FROM php:8.4-fpm

# Install system dependencies + Nginx + Supervisor
RUN apt-get update && apt-get install -y \
    nginx \
    supervisor \
    git \
    curl \
    zip \
    unzip \
    gettext-base \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libpq-dev \
    libzip-dev \
    && docker-php-ext-install \
    pdo \
    pdo_mysql \
    pdo_pgsql \
    mbstring \
    exif \
    pcntl \
    bcmath \
    gd \
    zip \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /var/www/html

# Copy app + vendor from build stage
COPY --from=vendor /app /var/www/html

# Set correct permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage \
    && chmod -R 755 /var/www/html/bootstrap/cache

# ── Nginx ──────────────────────────────────────
# Remove default nginx site; copy ours as a template
#  (envsubst will replace $PORT at runtime)
RUN rm -f /etc/nginx/sites-enabled/default
COPY docker/nginx/default.conf.template /etc/nginx/conf.d/default.conf.template

# ── Supervisor ─────────────────────────────────
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# ── Entrypoint ─────────────────────────────────
COPY docker/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

EXPOSE 80

ENTRYPOINT ["/entrypoint.sh"]