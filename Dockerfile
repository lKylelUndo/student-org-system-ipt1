# syntax=docker/dockerfile:1

ARG PHP_VERSION=8.3

# ---------------------------------------------------------------------------
# Stage 1: build the Vite frontend assets (CSS + JS production bundles)
# ---------------------------------------------------------------------------
FROM node:22-alpine AS frontend

WORKDIR /app

# Install JS dependencies first (cached unless lockfile changes)
COPY package.json package-lock.json ./
RUN npm ci

# Copy only what Vite needs to build
COPY vite.config.js ./vite.config.js
COPY public ./public
COPY resources/css ./resources/css
COPY resources/js ./resources/js

RUN npm run build

# ---------------------------------------------------------------------------
# Stage 2: PHP production runtime
# ---------------------------------------------------------------------------
FROM php:${PHP_VERSION}-cli-alpine

# System packages + required PHP extensions for Neon Postgres
RUN apk add --no-cache \
        libpq-dev \
        icu-dev \
        libzip-dev \
        zip \
        unzip \
        git \
    && docker-php-ext-install \
        pdo_pgsql \
        bcmath \
        intl \
        zip \
        opcache

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app

# PHP dependencies first (cached unless composer.lock changes)
COPY composer.json composer.lock ./
RUN composer install \
        --no-dev \
        --prefer-dist \
        --no-interaction \
        --no-progress \
        --optimize-autoloader \
        --no-scripts

# Application source
COPY . .

# Remove local dev/build artifacts the host copy shipped
# (e.g. bootstrap/cache/packages.php references dev-only providers)
RUN rm -f bootstrap/cache/*.php \
    && rm -rf storage/framework/views/* \
             storage/framework/cache/* \
             storage/framework/sessions/* \
             storage/framework/testing/* \
             storage/logs/*

# discover packages now that artisan exists (skipped during composer install)
RUN php artisan package:discover --ansi

# Built frontend assets from stage 1 (avoids stale local artifacts)
COPY --from=frontend /app/public/build ./public/build

# Create the public/storage symlink at image-build time
# (running container is non-root, so symlink creation must happen here)
RUN php artisan storage:link --force

# Writable runtime directories (app runs as www-data)
RUN chown -R www-data:www-data storage bootstrap/cache

USER www-data

ENV PORT=10000

EXPOSE 10000

ENTRYPOINT ["/bin/sh", "/app/docker/entrypoint.sh"]
