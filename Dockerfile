FROM composer:2 AS composer

FROM php:8.4-apache

ENV APP_ENV=prod
ENV APP_DEBUG=0
ENV COMPOSER_ALLOW_SUPERUSER=1

WORKDIR /var/www/html

RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        git \
        libicu-dev \
        libpq-dev \
        unzip \
    && docker-php-ext-install \
        intl \
        opcache \
        pdo_mysql \
        pdo_pgsql \
    && a2enmod rewrite headers \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

COPY docker/apache/000-default.conf /etc/apache2/sites-available/000-default.conf
COPY --from=composer /usr/bin/composer /usr/bin/composer
COPY composer.json composer.lock symfony.lock ./

RUN composer install \
    --no-dev \
    --no-interaction \
    --no-progress \
    --no-scripts \
    --optimize-autoloader \
    --prefer-dist

COPY . .

RUN composer dump-autoload --classmap-authoritative --no-dev \
    && mkdir -p var/cache var/log \
    && chown -R www-data:www-data var

EXPOSE 80
