FROM php:8.1-apache

RUN \
    apt-get update && \
    docker-php-ext-install pdo_mysql && \
    ln -s /etc/apache2/mods-available/rewrite.load /etc/apache2/mods-enabled/rewrite.load