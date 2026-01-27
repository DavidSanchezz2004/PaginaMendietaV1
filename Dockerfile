FROM php:8.2-cli

WORKDIR /app

# Dependencias + extensiones típicas Laravel
RUN apt-get update && apt-get install -y \
    git unzip libzip-dev \
  && docker-php-ext-install pdo_mysql mbstring zip \
  && rm -rf /var/lib/apt/lists/*

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Código
COPY . .

# Dependencias PHP
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Permisos
RUN chmod -R 775 storage bootstrap/cache || true

# EasyPanel suele inyectar $PORT
EXPOSE 8080
CMD sh -lc "php artisan serve --host=0.0.0.0 --port=${PORT:-8080}"
