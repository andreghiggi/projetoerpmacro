#!/bin/bash
set -e
cd /app
composer install --no-interaction --prefer-dist --no-dev
composer dump-autoload
php artisan migrate
php artisan key:generate
php artisan db:seed

chown -R www-data:www-data /app/storage
chmod -R 775 /app/storage