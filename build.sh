#!/bin/bash
set -e

echo "Installing PHP dependencies..."
composer install --no-dev --optimize-autoloader

echo "Installing Node.js dependencies..."
npm ci

echo "Building assets..."
npm run build

echo "Caching Laravel configuration..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "Creating storage link..."
php artisan storage:link

echo "Build completed successfully!"