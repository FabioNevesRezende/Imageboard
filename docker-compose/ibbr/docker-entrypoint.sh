#!/bin/sh

echo "Cleaning vendor"
rm -rf vendor composer.lock

echo "Installing composer dependencies"
composer install

echo "Generating app key"
php artisan key:generate

echo "Cleaning cache"
php artisan cache:clear

# Wait for the database to be accessible
echo "Waiting for the database..."
while ! nc -z db 3306; do
  echo "Waiting for the database..."
  sleep 1
done
echo "Database is up!"

# Run Laravel migration and seeding commands

echo "Migrating database"
php artisan migrate

echo "Seeding database"
php artisan db:seed --force 

#echo "Starting to serve at port 8000"
#php artisan serve --port=8000 &

# Start the PHP-FPM process
#"$@"
