FROM php:8.0.2-fpm-buster

RUN apt-get clean
RUN apt-get update
RUN apt install -y zip libzip-dev

RUN docker-php-ext-install pdo pdo_mysql zip

WORKDIR /var/www/html/app

COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer