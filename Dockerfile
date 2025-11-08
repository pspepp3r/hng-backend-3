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
    vim \
    libicu-dev

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Install PHP dependencies (no dev deps in production build)
RUN composer install --no-dev --no-interaction --optimize-autoloader --ansi --no-progress

# Ensure optimized autoloader after copying app (in case of PSR changes)
RUN composer dump-autoload --optimize --no-dev --classmap-authoritative --no-interaction

RUN docker-php-ext-configure intl

RUN groupadd --force -g $GROUP_ID $USER
RUN useradd -ms /bin/bash --no-user-group -g $GROUP_ID -u 1337 $USER
RUN usermod -u $USER_ID $USER

USER $USER
