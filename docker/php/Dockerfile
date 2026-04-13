##################################
# Base stage
##################################
FROM php:8.2-fpm-alpine AS base

# Install system dependencies
RUN apk add --no-cache \
    acl \
    fcgi \
    file \
    gettext \
    git \
    gnu-libiconv \
    linux-headers \
    $PHPIZE_DEPS

# Install PHP extensions
RUN set -eux; \
    docker-php-ext-install -j$(nproc) \
        intl \
        opcache \
        pdo_pgsql \
        sysvsem \
    ; \
    pecl install \
        apcu \
        redis \
    ; \
    pecl clear-cache; \
    docker-php-ext-enable \
        apcu \
        redis \
    ;

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY docker/php/php.ini /usr/local/etc/php/php.ini
COPY docker/php/php-fpm.conf /usr/local/etc/php-fpm.d/zz-docker.conf

COPY composer.json composer.lock* symfony.lock* ./

RUN composer install --prefer-dist --no-dev --no-scripts --no-progress --optimize-autoloader \
    && composer clear-cache

COPY . .
RUN composer dump-autoload --optimize

RUN chown -R www-data:www-data var

##################################
# Dev stage
##################################
FROM base AS dev

RUN pecl install xdebug \
    && docker-php-ext-enable xdebug \
    && pecl clear-cache

COPY docker/php/xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini

RUN composer install --prefer-dist --no-scripts --no-progress

USER www-data

##################################
# Production stage
##################################
FROM base AS prod

ENV APP_ENV=prod
ENV APP_DEBUG=0

RUN php bin/console cache:warmup --no-debug

USER www-data
