FROM ghcr.io/gotoeveryone/composer-php:7.4

ENV APP_ROOT /var/app

WORKDIR ${APP_ROOT}
COPY . ${APP_ROOT}

RUN composer install
