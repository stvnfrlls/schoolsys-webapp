#!/bin/sh
set -e

echo "Linking storage..."
php artisan storage:link --force

echo "Optimizing..."
php artisan optimize

echo "Starting services..."
exec /usr/bin/supervisord -c /etc/supervisord.conf