FROM php:8.2-cli

RUN apt-get update && apt-get install -y --no-install-recommends \
        git curl zip unzip ca-certificates gnupg \
        libpng-dev libjpeg62-turbo-dev libfreetype6-dev libwebp-dev \
        libonig-dev libxml2-dev libzip-dev libpq-dev \
    && docker-php-ext-configure gd --with-jpeg --with-freetype --with-webp \
    && docker-php-ext-install pdo pdo_mysql pdo_pgsql pdo_sqlite mbstring exif pcntl bcmath gd zip \
    && curl -fsSL https://deb.nodesource.com/setup_22.x | bash - \
    && apt-get install -y --no-install-recommends nodejs \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
COPY . .

RUN composer install --no-dev --optimize-autoloader --no-interaction
RUN npm ci && npm run build && rm -rf node_modules

RUN mkdir -p storage/framework/sessions storage/framework/views storage/framework/cache storage/logs bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache \
    && chmod +x docker-entrypoint.sh

EXPOSE 10000
CMD ["./docker-entrypoint.sh"]
