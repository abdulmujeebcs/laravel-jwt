FROM php:8.2-fpm-alpine

WORKDIR /var/www/html

RUN docker-php-ext-install pdo pdo_mysql

RUN mkdir -p /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod 777 -R storage /var/www/html/bootstrap/cache