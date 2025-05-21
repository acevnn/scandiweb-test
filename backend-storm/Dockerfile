FROM php:8.2-apache

# Enable mod_rewrite
RUN a2enmod rewrite

RUN apt-get update && apt-get install -y unzip git curl libpng-dev libonig-dev libxml2-dev \
  && docker-php-ext-install pdo pdo_mysql

# Set working directory
WORKDIR /var/www/html

# Copy composer files first
COPY composer.json composer.lock ./

# Install dependencies
RUN curl -sS https://getcomposer.org/installer | php && \
    mv composer.phar /usr/local/bin/composer && \
    composer install --no-dev --optimize-autoloader

# Copy the rest of the app
COPY . .

# Set permissions
RUN chown -R www-data:www-data /var/www/html

EXPOSE 80
