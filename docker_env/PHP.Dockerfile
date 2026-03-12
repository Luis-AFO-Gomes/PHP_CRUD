FROM php:fpm

RUN docker-php-ext-install pdo pdo_mysql mysqli
RUN apt-get update && apt-get install -y zip unzip git

# Install Xdebug
RUN pecl install xdebug && docker-php-ext-enable xdebug

# Copy Xdebug config
COPY xdebug.ini /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

WORKDIR /app
