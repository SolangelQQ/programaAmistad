#!/bin/bash

# Ejecutar migraciones
php artisan migrate --force

# Limpiar cache
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Iniciar Apache
apache2-foreground