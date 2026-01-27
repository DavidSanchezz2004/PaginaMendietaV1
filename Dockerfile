FROM php:8.2-cli

WORKDIR /app

RUN apt-get update && apt-get install -y \
    git unzip libzip-dev \
    libonig-dev pkg-config \
  && docker-php-ext-install pdo_mysql mbstring zip \
  && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

COPY . .

RUN composer install --no-dev --optimize-autoloader --no-interaction

RUN chmod -R 775 storage bootstrap/cache || true

EXPOSE 8080
CMD sh -lc "php artisan serve --host=0.0.0.0 --port=${PORT:-8080}"
