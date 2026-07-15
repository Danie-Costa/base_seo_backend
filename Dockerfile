FROM php:8.2-fpm

# System packages
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    libicu-dev \
    zip \
    unzip \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libwebp-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install -j$(nproc) \
        pdo_mysql \
        mbstring \
        exif \
        pcntl \
        bcmath \
        gd \
        zip \
        xml \
        fileinfo \
        intl \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Create system user
RUN groupadd -g 1000 app && useradd -u 1000 -g app -s /bin/bash -m app

# Working dir
WORKDIR /var/www

# Copy composer files first for caching
COPY composer.json composer.lock* /var/www/

# Install dependencies
RUN composer install --no-interaction --no-autoloader --no-scripts

# Copy app
COPY . /var/www

# Generate autoloader
RUN composer dump-autoload --no-scripts

# Permissions
RUN chown -R app:app /var/www \
    && chmod -R 775 /var/www/storage /var/www/bootstrap/cache

USER app

EXPOSE 9000
CMD ["php-fpm"]
