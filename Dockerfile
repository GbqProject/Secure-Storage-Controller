# Dockerfile - builds PHP-FPM image for Laravel app
FROM php:8.2-fpm

# Arguments (optional)
ARG USER=www-data
ARG UID=1000
ARG GID=1000

# install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    zip \
    unzip \
    libpq-dev \
    libzip-dev \
    zip \
    supervisor \
    nginx \
    && docker-php-ext-install pdo_pgsql zip

# Install Composer
COPY --from=composer:2.6 /usr/bin/composer /usr/bin/composer

# Create app directory
WORKDIR /var/www

# Copy composer files first (for caching)
COPY composer.json composer.lock ./

# Install PHP dependencies
RUN composer install --no-interaction --prefer-dist --no-dev --optimize-autoloader || true

# Copy application code
COPY . .

# Ensure storage/logs writable
RUN chown -R ${USER}:${USER} /var/www/storage /var/www/bootstrap/cache || true
RUN chmod -R 775 /var/www/storage /var/www/bootstrap/cache || true

# Expose php-fpm port (used by nginx)
EXPOSE 9000

# Use the default php-fpm command
CMD ["php-fpm"]
