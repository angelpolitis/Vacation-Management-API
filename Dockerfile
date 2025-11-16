FROM php:8.4-apache

# Enable the Apache rewrite module.
RUN a2enmod rewrite

# Define the system dependencies.
RUN apt-get update && apt-get install -y \
    unzip \
    git \
    curl \
    libpq-dev \
    libzip-dev \
    libonig-dev \
    mariadb-client \
    && docker-php-ext-install pdo pdo_mysql zip

# Set document root to public/
ENV APACHE_DOCUMENT_ROOT /var/www/html/public

# Update the Apache configuration to point to the public directory.
RUN sed -ri -e 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/000-default.conf

# Set the working directory.
WORKDIR /var/www/html