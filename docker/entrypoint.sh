#!/bin/bash
set -e

# ─────────────────────────────────────────────
# 1. Render injects $PORT; default to 80 locally
# ─────────────────────────────────────────────
export PORT="${PORT:-80}"

echo "==> Starting on port $PORT"

# ─────────────────────────────────────────────
# 2. Inject $PORT into Nginx config via envsubst
# ─────────────────────────────────────────────
envsubst '${PORT}' \
    < /etc/nginx/conf.d/default.conf.template \
    > /etc/nginx/conf.d/default.conf

echo "==> Nginx config written"

# ─────────────────────────────────────────────
# 3. Laravel bootstrap
# ─────────────────────────────────────────────
cd /var/www/html

# Render has no .env file — env vars are injected at runtime.
# Laravel's artisan needs a .env to exist, so we generate one.
if [ ! -f .env ]; then
    echo "==> Creating .env from runtime environment..."
    printenv \
        | grep -E '^(APP_|DB_|REDIS_|CACHE_|SESSION_|QUEUE_|MAIL_|AWS_|LOG_|BROADCAST_)' \
        | sed "s/=\(.*\)/='\1'/" \
        > .env
    echo "==> .env written"
fi

# Generate app key only if APP_KEY is missing
if [ -z "$APP_KEY" ]; then
    echo "==> Generating APP_KEY..."
    php artisan key:generate --force
fi

# Cache config/routes/views for production
if [ "$APP_ENV" = "production" ]; then
    echo "==> Caching Laravel config..."
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
fi

# Run migrations (--force skips interactive confirmation)
echo "==> Running migrations..."
php artisan migrate --force

# Ensure storage is linked
php artisan storage:link 2>/dev/null || true

# Fix permissions after any artisan commands
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

echo "==> Bootstrap complete. Starting services..."

# ─────────────────────────────────────────────
# 4. Hand off to Supervisord (Nginx + PHP-FPM)
# ─────────────────────────────────────────────
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf