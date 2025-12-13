FROM php:8.4-apache

LABEL container="battleship"

WORKDIR /var/www/html

RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf \
    && chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

EXPOSE 80