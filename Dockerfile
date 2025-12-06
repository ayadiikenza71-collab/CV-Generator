
FROM php:8.2-apache

# Enable Apache mod_rewrite (often needed for PHP apps)
RUN a2enmod rewrite

# Install mysqli extension
RUN docker-php-ext-install mysqli

# Set working directory
WORKDIR /var/www/html

# Copy application source
COPY . /var/www/html

# Ensure correct permissions for Apache user
RUN chown -R www-data:www-data /var/www/html \
    && find /var/www/html -type d -exec chmod 755 {} \; \
    && find /var/www/html -type f -exec chmod 644 {} \;
