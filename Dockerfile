FROM php:8.4-fpm

ARG USER
ARG USER_ID
ARG GROUP_ID

WORKDIR /var/www

RUN apt-get update && apt-get install -y \
    git \
    zip \
    unzip \
    curl \
    vim

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Copy composer files first so composer can install dependencies
COPY composer.json composer.lock /var/www/

# Install PHP dependencies (no dev deps in production build)
RUN composer install --no-dev --no-interaction --optimize-autoloader --ansi --no-progress || true

# Copy application source (after deps to leverage layer cache)
COPY . /var/www

# Ensure optimized autoloader after copying app (in case of PSR changes)
RUN composer dump-autoload --optimize --no-dev --classmap-authoritative --no-interaction || true
