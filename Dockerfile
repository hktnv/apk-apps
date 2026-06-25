ARG PHP_IMAGE=php:8.4.7-fpm-bookworm
ARG COMPOSER_IMAGE=composer:2.8.8
ARG NODE_IMAGE=node:24.4.1-bookworm-slim
ARG NGINX_IMAGE=nginx:1.27.5-bookworm

FROM ${PHP_IMAGE} AS php-base

RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        git \
        libicu-dev \
        libpq-dev \
        libzip-dev \
        unzip \
    && docker-php-ext-install intl opcache pdo_pgsql zip \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /var/www/html
COPY docker/php/php.ini /usr/local/etc/php/conf.d/apk-apps.ini

FROM ${COMPOSER_IMAGE} AS composer-bin

FROM php-base AS composer-deps
COPY --from=composer-bin /usr/bin/composer /usr/bin/composer
COPY composer.json composer.lock ./
RUN composer install \
    --no-dev \
    --no-interaction \
    --prefer-dist \
    --no-progress \
    --no-scripts \
    --no-autoloader

FROM ${NODE_IMAGE} AS frontend-build
WORKDIR /var/www/html
COPY package.json package-lock.json ./
RUN npm ci
COPY resources ./resources
COPY vite.config.js ./
RUN npm run build

FROM ${NODE_IMAGE} AS node-bin

FROM php-base AS development
COPY --from=composer-bin /usr/bin/composer /usr/bin/composer
COPY --from=node-bin /usr/local/bin/node /usr/local/bin/node
COPY --from=node-bin /usr/local/lib/node_modules /usr/local/lib/node_modules
RUN ln -s /usr/local/lib/node_modules/npm/bin/npm-cli.js /usr/local/bin/npm \
    && ln -s /usr/local/lib/node_modules/npm/bin/npx-cli.js /usr/local/bin/npx
CMD ["php-fpm"]

FROM php-base AS production
ENV APP_ENV=production \
    APP_DEBUG=false
COPY --from=composer-bin /usr/bin/composer /usr/bin/composer
COPY --chown=www-data:www-data . .
COPY --from=composer-deps --chown=www-data:www-data /var/www/html/vendor ./vendor
COPY --from=frontend-build --chown=www-data:www-data /var/www/html/public/build ./public/build
RUN composer dump-autoload --no-dev --classmap-authoritative --no-interaction \
    && mkdir -p storage/app/apks storage/framework/cache storage/framework/sessions storage/framework/views bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache \
    && rm -f /usr/bin/composer
USER www-data
CMD ["php-fpm"]

FROM ${NGINX_IMAGE} AS nginx-production
COPY docker/nginx/default.conf /etc/nginx/conf.d/default.conf
COPY --from=production /var/www/html/public /var/www/html/public
