FROM php:7.4-apache
RUN docker-php-ext-install \
    pdo_mysql \
    && a2enmod \
    rewrite
RUN set -eux; apt-get update; apt-get install -y libzip-dev zlib1g-dev; docker-php-ext-install zip
RUN set -eux; apt-get update; apt-get install -y libxml2-dev
RUN docker-php-ext-install xml
