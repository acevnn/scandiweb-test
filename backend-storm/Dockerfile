# Use the official PHP image with Apache
FROM php:8.2-apache

# Enable mod_rewrite (required for many frameworks)
RUN a2enmod rewrite

# Install dependencies (composer, unzip)
RUN apt-get update && apt-get install -y unzip git && \
    curl -sS https://getcomposer.org/installer | php && \
    mv composer.phar /usr/local/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy everything to the container
COPY . .

# Install PHP dependencies
RUN composer install --no-interaction --no-dev --optimize-autoloader

# Set proper permissions
RUN chown -R www-data:www-data /var/www/html

# Expose port 80
EXPOSE 80
