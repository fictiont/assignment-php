FROM php:8.0.2-fpm-buster

RUN apt-get clean
RUN apt-get update
RUN apt install -y zip libzip-dev openssl acl libyaml-dev

RUN pecl channel-update pecl.php.net
RUN pecl install yaml && echo "extension=yaml.so" > /usr/local/etc/php/conf.d/ext-yaml.ini && docker-php-ext-enable yaml

RUN docker-php-ext-install pdo pdo_mysql zip

WORKDIR /var/www/html/app

COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer