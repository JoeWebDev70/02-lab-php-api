FROM php:8.1.23-apache
#replace name of file : php.ini-development in php.ini 
RUN mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini" 
RUN docker-php-ext-install pdo_mysql
RUN a2enmod rewrite