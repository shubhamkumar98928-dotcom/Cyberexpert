FROM php:8.3-apache

COPY . /var/www/html/

RUN a2enmod rewrite

CMD ["apache2-foreground"]
