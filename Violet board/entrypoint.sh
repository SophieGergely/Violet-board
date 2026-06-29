#!/bin/sh
set -e

echo "Running database migrations..."
php artisan migrate --force

if [ ! -f /var/www/.seed-marker/.seeded ]; then
  echo "Seeding database..."
  php artisan db:seed --force
  touch /var/www/.seed-marker/.seeded
  echo "Seeding complete."
else
  echo "Database already seeded, skipping."
fi

echo "Caching config, routes and views..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "Creating storage link..."
php artisan storage:link || true

echo "Starting PHP-FPM..."
exec php-fpm
