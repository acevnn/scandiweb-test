# Use the official PHP image with Apache
FROM php:8.2-apache

# Enable mod_rewrite
RUN a2enmod rewrite

# Install Composer
RUN apt-get update && apt-get install -y unzip git curl \
 && curl -sS https://getcomposer.org/installer | php \
 && mv composer.phar /usr/local/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy all files
COPY . .

# Run composer install
RUN composer install --no-dev --optimize-autoloader

# Set permissions
RUN chown -R www-data:www-data /var/www/html

# Expose port
EXPOSE 80
