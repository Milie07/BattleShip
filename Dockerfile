FROM php:8.4-apache

LABEL container="battleship"

WORKDIR /var/www/html

# Copier les fichiers de l'application
COPY . /var/www/html/

# Configuration Apache + permissions
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf \
    && chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html \
    && sed -i 's/80/8080/g' /etc/apache2/sites-available/000-default.conf /etc/apache2/ports.conf

EXPOSE 8080

CMD ["apache2-foreground"]