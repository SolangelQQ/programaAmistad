# Usar PHP 8.2 con Apache
FROM php:8.2-apache

# Instalar dependencias del sistema
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    libpq-dev \
    default-mysql-client \
    zip \
    unzip \
    ca-certificates \
    gnupg

# Instalar Node.js 18
RUN curl -fsSL https://deb.nodesource.com/setup_18.x | bash - \
    && apt-get install -y nodejs

# Limpiar cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Instalar extensiones PHP
RUN docker-php-ext-install pdo_pgsql pgsql pdo_mysql mbstring exif pcntl bcmath gd zip

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Configurar Apache
RUN a2enmod rewrite
COPY ./apache-config.conf /etc/apache2/sites-available/000-default.conf

# Establecer directorio de trabajo
WORKDIR /var/www/html

# Copiar archivos del proyecto
COPY . .

# Limpiar problemas de PSR-4
RUN rm -f database/factories/BuddyFactory.php database/factories/FriendshipFactory.php 2>/dev/null || true

# Instalar dependencias PHP
RUN composer install --no-dev --optimize-autoloader

# Crear directorios necesarios
RUN mkdir -p storage/app/public storage/framework/cache storage/framework/sessions storage/framework/views storage/logs
RUN mkdir -p public/storage public/build

# Crear enlace simbólico para storage
RUN ln -sf /var/www/html/storage/app/public /var/www/html/public/storage

# Configurar permisos
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/public
RUN chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/public

# Crear archivo .env desde variables de entorno
RUN touch /var/www/html/.env

# Crear script de inicio
COPY start.sh /usr/local/bin/start.sh
RUN chmod +x /usr/local/bin/start.sh

# Exponer puerto
EXPOSE 80

# Comando para iniciar
CMD ["/usr/local/bin/start.sh"]