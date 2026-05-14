#!/bin/sh
set -e

# Fix CRLF line endings and generate APP_KEY if missing (local only)
if [ "$APP_ENV" = "local" ]; then
    sed -i 's/\r//' .env
    if [ -z "$(grep '^APP_KEY=.\+' .env)" ]; then
        echo "Generating APP_KEY..."
        php artisan key:generate --force
    fi
fi

# Wait for PostgreSQL to be ready
echo "Waiting for database connection..."
until php artisan db:monitor > /dev/null 2>&1; do
    echo "Database not ready, retrying in 2s..."
    sleep 2
done
echo "Database is ready."

echo "Linking storage..."
php artisan storage:link --force

echo "Optimizing..."
php artisan optimize

# Run migrations/seeds based on APP_DEPLOY_TASK env var
case "$APP_DEPLOY_TASK" in
  migrate)
    echo "Running migrations..."
    php artisan migrate --force
    ;;
  migrate:fresh)
    echo "Running fresh migrations..."
    php artisan migrate:fresh --force
    ;;
  seed)
    echo "Running seeders..."
    php artisan db:seed --force
    ;;
  migrate:seed)
    echo "Running migrations and seeders..."
    php artisan migrate --force
    php artisan db:seed --force
    ;;
  migrate:fresh:seed)
    echo "Running fresh migrations with seeders..."
    php artisan migrate:fresh --seed --force
    ;;
  *)
    if [ -n "$APP_DEPLOY_TASK" ]; then
      echo "Unknown APP_DEPLOY_TASK value: '$APP_DEPLOY_TASK', skipping."
    else
      echo "No APP_DEPLOY_TASK set, skipping."
    fi
    ;;
esac

echo "Starting services..."
exec /usr/bin/supervisord -c /etc/supervisord.conf