# Use the official PHP image with Apache
FROM php:8.2-apache

# Enable mod_rewrite
RUN a2enmod rewrite

# Install Composer
RUN apt-get update && apt-get install -y unzip git curl \
 && curl -sS https://getcomposer.org/installer | php \
 && mv composer.phar /usr/local/bin/composer

# Set working directory to project root
WORKDIR /var/www

# Copy all files into container
COPY . .

# Run composer install in the project root
RUN composer install --no-dev --optimize-autoloader

# Set working directory for Apache to look for public files
WORKDIR /var/www/public

# Set permissions
RUN chown -R www-data:www-data /var/www

# Expose port
EXPOSE 80
