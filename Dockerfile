# Imagen base con PHP 8.2 CLI
FROM php:8.2-cli

WORKDIR /app

# Instalar dependencias del sistema + Node.js
RUN apt-get update && apt-get install -y \
    git unzip libzip-dev \
    libonig-dev pkg-config \
    curl \
  && docker-php-ext-install pdo_mysql mbstring zip \
  && curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
  && apt-get install -y nodejs \
  && rm -rf /var/lib/apt/lists/*

# Copiar Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copiar archivos del proyecto
COPY . .

# Instalar dependencias de PHP
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Instalar dependencias de Node y compilar assets
RUN npm ci && npm run build

# Configurar permisos
RUN chmod -R 775 storage bootstrap/cache || true

EXPOSE 8080
CMD sh -lc "php artisan serve --host=0.0.0.0 --port=${PORT:-8080}"

