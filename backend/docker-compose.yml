FROM php:8.2-fpm

# ---------- System Dependencies ----------
RUN apt-get update && apt-get install -y \
    git unzip curl libzip-dev zip nginx \
    && docker-php-ext-install pdo pdo_mysql zip

# ---------- Composer ----------
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# ---------- Working Directory ----------
WORKDIR /var/www

# Copy composer files first (build cache optimization)
COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Copy application
COPY . .

# Laravel setup
RUN cp .env.example .env || true
RUN php artisan key:generate --force
RUN php artisan config:clear
RUN php artisan route:clear
RUN php artisan view:clear

# Permissions
RUN chown -R www-data:www-data storage bootstrap/cache

# ---------- NGINX CONFIG ----------
COPY nginx.conf /etc/nginx/sites-available/default

# ---------- Startup Script ----------
COPY start.sh /start.sh
RUN chmod +x /start.sh

EXPOSE 10000

ENTRYPOINT ["/start.sh"]

