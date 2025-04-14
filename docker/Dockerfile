# Multi-stage build to optimize for production
FROM composer:latest as composer
WORKDIR /app

# Copy only the files needed to install dependencies
COPY composer.json composer.lock ./
RUN composer install --no-scripts --no-autoloader --prefer-dist --no-dev

# Main PHP image
FROM php:8.2-apache

# Args to handle environment
ARG APP_ENV=development

# Set environment
ENV APP_ENV=$APP_ENV

# Install required packages and PHP extensions
RUN apt-get update && apt-get install -y \
    apache2 \
    libzip-dev \
    zip \
    unzip \
    git \
    curl \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libwebp-dev \
    libonig-dev \
    libxml2-dev \
    default-mysql-client \
    cron \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install -j$(nproc) \
    zip \
    pdo \
    pdo_mysql \
    mysqli \
    gd \
    exif \
    opcache

# Enable Apache modules
RUN a2enmod rewrite headers expires mime deflate

# Configure Apache
COPY docker/apache.conf /etc/apache2/sites-available/000-default.conf

# Set working directory
WORKDIR /var/www/html

# Copy PHP configuration
COPY docker/php.ini /usr/local/etc/php/php.ini

# Create log files and set permissions
RUN touch /var/log/php_errors.log \
    && chown www-data:www-data /var/log/php_errors.log

# Copy database connection check script
COPY docker/wait-for-db.sh /usr/local/bin/wait-for-db.sh
RUN chmod +x /usr/local/bin/wait-for-db.sh

# Copy the vendor directory from the composer stage
COPY --from=composer /app/vendor /var/www/html/vendor

# Copy the entire application
COPY . .

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Conditional package installation and optimization based on environment
RUN if [ "$APP_ENV" = "production" ]; then \
    # Optimize for production
    composer dump-autoload --optimize --no-dev && \
    # Configure OPcache for production
    { \
        echo 'opcache.memory_consumption=128'; \
        echo 'opcache.interned_strings_buffer=8'; \
        echo 'opcache.max_accelerated_files=4000'; \
        echo 'opcache.revalidate_freq=60'; \
        echo 'opcache.fast_shutdown=1'; \
        echo 'opcache.enable_cli=1'; \
        echo 'opcache.jit=1255'; \
        echo 'opcache.jit_buffer_size=100M'; \
    } > /usr/local/etc/php/conf.d/opcache-recommended.ini; \
    else \
    # Install all dependencies for development
    composer install --optimize-autoloader; \
    fi

# Set permissions
RUN chown -R www-data:www-data /var/www && \
    chmod -R 755 /var/www/html && \
    chmod -R 775 /var/www/html/storage && \
    chmod -R 775 /var/www/html/public/assets && \
    find /var/www/html/public/assets -type f -exec chmod 644 {} \;

EXPOSE 80

ENTRYPOINT ["/usr/local/bin/wait-for-db.sh"]