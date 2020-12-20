FROM php:7.3

RUN apt-get -y update && \
  apt-get install -y libicu-dev libzip-dev && \
  docker-php-ext-install zip pdo_mysql intl

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
