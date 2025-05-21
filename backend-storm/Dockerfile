FROM php:8.2-apache

RUN a2enmod rewrite

# Install Composer
RUN apt-get update && apt-get install -y unzip git curl \
 && curl -sS https://getcomposer.org/installer | php \
 && mv composer.phar /usr/local/bin/composer

WORKDIR /var/www/html

# Copy only composer files first
COPY composer.json composer.lock ./

# Install dependencies
RUN composer install --no-dev --optimize-autoloader

# Then copy the rest of the app
COPY . .

# Set correct permissions
RUN chown -R www-data:www-data /var/www/html

EXPOSE 80
