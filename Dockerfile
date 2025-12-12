FROM php:8.4-apache

LABEL container="battleship"

COPY . /var/www/html

WORKDIR /var/www/html

RUN chown -R www-data:www-data /var/www/html \ 
    && chmod -R 755 /var/www/html

EXPOSE 80