# Usa una imagen oficial de PHP con Apache
FROM php:8.2-fpm

# Instala dependencias del sistema
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    nginx \
    libzip-dev \
    default-mysql-client \
    nodejs \
    npm

# Limpia caché de apt
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Instala extensiones de PHP
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

# Instala Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Establece el directorio de trabajo
WORKDIR /app

# Copia los archivos del proyecto
COPY . .

# Instala dependencias de PHP
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Instala dependencias de Node y construye assets
RUN npm ci && npm run build

# Configura permisos
RUN chown -R www-data:www-data /app/storage /app/bootstrap/cache

# Expone el puerto
EXPOSE 8000

# Comando para iniciar la aplicación
CMD php artisan serve --host=0.0.0.0 --port=8000
