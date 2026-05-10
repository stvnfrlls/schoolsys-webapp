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

# Generate app key if not set
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

# Run migrations (add --force so it runs non-interactively in production)
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