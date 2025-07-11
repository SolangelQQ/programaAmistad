#!/bin/bash

# Crear archivo .env desde variables de entorno
# cat > /var/www/html/.env << EOF
# APP_NAME="${APP_NAME}"
# APP_ENV=production
# APP_KEY="${APP_KEY}"
# APP_DEBUG=false
# APP_URL="${APP_URL}"
# APP_LOCALE=es
# APP_FALLBACK_LOCALE=es
# APP_FAKER_LOCALE=es
# APP_MAINTENANCE_DRIVER=file
# PHP_CLI_SERVER_WORKERS=4
# BCRYPT_ROUNDS=12
# LOG_CHANNEL=stack
# LOG_STACK=single
# LOG_DEPRECATIONS_CHANNEL=null
# LOG_LEVEL=error

# DB_CONNECTION=pgsql
# DB_HOST="${DB_HOST}"
# DB_PORT="${DB_PORT}"
# DB_DATABASE="${DB_DATABASE}"
# DB_USERNAME="${DB_USERNAME}"
# DB_PASSWORD="${DB_PASSWORD}"

# SESSION_DRIVER=database
# SESSION_LIFETIME=120
# SESSION_ENCRYPT=false
# SESSION_PATH=/
# SESSION_DOMAIN=null

# GOOGLE_CLIENT_ID="${GOOGLE_CLIENT_ID}"
# GOOGLE_CLIENT_SECRET="${GOOGLE_CLIENT_SECRET}"
# GOOGLE_REDIRECT_URI="${GOOGLE_REDIRECT_URI}"
# GOOGLE_MAPS_API_KEY="${GOOGLE_MAPS_API_KEY}"

# MAIL_MAILER=smtp
# MAIL_HOST=smtp.gmail.com
# MAIL_PORT=587
# MAIL_USERNAME="${MAIL_USERNAME}"
# MAIL_PASSWORD="${MAIL_PASSWORD}"
# MAIL_ENCRYPTION=tls
# MAIL_FROM_ADDRESS="${MAIL_FROM_ADDRESS}"
# MAIL_FROM_NAME="${MAIL_FROM_NAME}"

# BROADCAST_CONNECTION=log
# FILESYSTEM_DISK=local
# QUEUE_CONNECTION=database
# CACHE_STORE=database
# MEMCACHED_HOST=127.0.0.1
# REDIS_CLIENT=phpredis
# REDIS_HOST=127.0.0.1
# REDIS_PASSWORD=null
# REDIS_PORT=6379

# AWS_ACCESS_KEY_ID=
# AWS_SECRET_ACCESS_KEY=
# AWS_DEFAULT_REGION=us-east-1
# AWS_BUCKET=
# AWS_USE_PATH_STYLE_ENDPOINT=false

# VITE_APP_NAME="${APP_NAME}"
# EOF

# # Esperar a que la base de datos esté disponible
# echo "Esperando conexión a la base de datos..."
# until php artisan migrate:status 2>/dev/null; do
#     echo "Base de datos no disponible, esperando..."
#     sleep 2
# done

# # Ejecutar migraciones
# echo "Ejecutando migraciones..."
# php artisan migrate --force

# # Ejecutar migración de datos (solo si es el primer deploy)
# if [ "$MIGRATE_DATA" = "true" ]; then
#     echo "Migrando datos desde MySQL..."
#     php artisan migrate:to-postgresql
# fi

# # Limpiar y cachear configuración
# echo "Limpiando cache..."
# php artisan config:clear
# php artisan route:clear
# php artisan view:clear
# php artisan cache:clear

# echo "Creando cache..."
# php artisan config:cache
# php artisan route:cache
# php artisan view:cache

# # Crear enlace simbólico para storage (si no existe)
# if [ ! -L /var/www/html/public/storage ]; then
#     php artisan storage:link
# fi

# # Iniciar Apache
# echo "Iniciando Apache..."
# apache2-foreground

#!/bin/bash
set -e

echo "Starting Laravel application..."

# Generar APP_KEY si no existe
if [ -z "$APP_KEY" ]; then
    echo "Generating APP_KEY..."
    php artisan key:generate --force
fi

# Limpiar cache
echo "Clearing cache..."
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear

# Crear cache
echo "Creating cache..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Crear enlace de storage si no existe
if [ ! -L public/storage ]; then
    echo "Creating storage link..."
    php artisan storage:link
fi

# Verificar conexión a BD
echo "Checking database connection..."
php artisan migrate:status || echo "Database connection failed"

# Iniciar servidor
echo "Starting server on port $PORT..."
php artisan serve --host=0.0.0.0 --port=$PORT