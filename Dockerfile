FROM ubuntu:latest

ENV DEBIAN_FRONTEND=noninteractive \
    COMPOSER_ALLOW_SUPERUSER=1 \
    DB_CONNECTION=sqlite \
    DB_DATABASE=/app/storage/database.sqlite

RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        ca-certificates \
        composer \
        nodejs \
        npm \
        php-cli \
        php-curl \
        php-mbstring \
        php-sqlite3 \
        php-xml \
        php-zip \
        unzip \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /app

COPY . .

RUN composer install --no-dev --prefer-dist --no-interaction --no-progress --optimize-autoloader \
    && npm ci \
    && npm run build \
    && rm -rf node_modules /root/.npm /root/.cache/composer \
    && mkdir -p storage/app/public storage/framework/cache/data storage/framework/sessions storage/framework/views storage/logs bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache

USER www-data

EXPOSE 8000

CMD ["sh", "-c", "touch storage/database.sqlite && php artisan migrate --force && exec php artisan serve --host=0.0.0.0 --port=8000"]
