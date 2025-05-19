# Use PHP with Apache base image version 8.1
FROM php:8.1-apache

# Set timezone
ENV TZ=Asia/Jakarta

# Install required dependencies
RUN apt-get update && apt-get install -y --no-install-recommends \
    libssl-dev \
    libcurl4-openssl-dev \
    pkg-config \
    libicu-dev \
    libxml2-dev \
    libonig-dev \
    unzip \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    libzip-dev \
    libwebp-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install gd curl intl mbstring mysqli xml zip \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Install PHP Opcache
RUN docker-php-ext-install opcache

# Enable mod_rewrite and vhost_alias_module for Apache
RUN a2enmod rewrite vhost_alias

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy application code into the container
COPY . /var/www/html

# Set permissions for the working directory
WORKDIR /var/www/html

# Install PHP dependencies using Composer
RUN composer install --no-dev --optimize-autoloader --verbose || { echo "Composer install failed"; exit 1; }

# Set PHP upload file size and max post size
RUN echo "upload_max_filesize = 20M\npost_max_size = 20M" > /usr/local/etc/php/conf.d/uploads.ini

# Install OpenSwoole for CI4
# RUN pecl install openswoole \
#     && docker-php-ext-enable openswoole

# Set writable permissions for CI4 directories
RUN chown -R www-data:www-data /var/www/html/writable \
    && chmod -R 755 /var/www/html/writable

# Update Apache configuration
RUN echo '<VirtualHost *:80>\n    ServerName lazisnu.deviscode.com\n    ServerAdmin webmaster@localhost\n    DocumentRoot /var/www/html/public\n    ErrorLog /var/log/apache2/error.log\n    CustomLog /var/log/apache2/access.log combined\n    <Directory "/var/www/html/public">\n        Options Indexes FollowSymLinks\n        AllowOverride All\n        Require all granted\n    </Directory>\n</VirtualHost>' > /etc/apache2/sites-available/000-default.conf

# Expose port 80 for Apache
EXPOSE 80
