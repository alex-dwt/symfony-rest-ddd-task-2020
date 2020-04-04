FROM php:7.4.3-fpm

RUN apt-get update && apt-get install -y vim wget zip git libicu-dev \
    && docker-php-ext-install pdo_mysql \
    && docker-php-ext-configure intl \
    && docker-php-ext-install intl

WORKDIR /app
CMD ["bash", "/scripts/boot.bash"]

COPY ./php/install_composer.sh /tmp/install_composer.sh
RUN chmod +x /tmp/install_composer.sh \
    && /tmp/install_composer.sh \
    && rm /tmp/install_composer.sh

COPY php/php-cli.ini /usr/local/etc/php
COPY php/www.conf /usr/local/etc/php-fpm.d/www.conf
COPY php/boot.bash /scripts/boot.bash

#RUN pecl install xdebug && docker-php-ext-enable xdebug

RUN docker-php-ext-install bcmath

RUN apt-get update && apt-get install -y apache2-utils

COPY ./concurrency_test.bash /concurrency_test.bash