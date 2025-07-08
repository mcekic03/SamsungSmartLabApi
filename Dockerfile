# Laravel API Dockerfile - Ubuntu version
FROM php:8.2-fpm

# Instaliraj sistemske zavisnosti
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libxml2-dev \
    zip \
    unzip \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libwebp-dev \
    libonig-dev \
    libzip-dev \
    && rm -rf /var/lib/apt/lists/*

# Instaliraj PHP ekstenzije
RUN docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install -j$(nproc) pdo pdo_mysql mbstring exif pcntl bcmath gd zip

# Instaliraj Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Postavi radni direktorijum
WORKDIR /var/www/html

# Kopiraj sve fajlove aplikacije
COPY . .

# Instaliraj PHP zavisnosti
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Kopiraj .env.example kao .env ako .env ne postoji
RUN cp .env.example .env

# Generiši aplikacioni ključ
RUN php artisan key:generate

# Postavi prava pristupa
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage \
    && chmod -R 755 /var/www/html/bootstrap/cache

# Optimizuj Laravel
RUN php artisan config:cache \
    && php artisan route:cache

# Osiguraj da je radni direktorijum postavljen
WORKDIR /var/www/html

# Expose port
EXPOSE 3500

# Pokreni Laravel aplikaciju
CMD ["php", "artisan", "start"] 