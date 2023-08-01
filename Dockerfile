# Use the official PHP 8.2 image as the base image
FROM php:8.2-fpm

# Arguments defined in docker-compose.yml
ARG user
ARG uid

# Install system dependencies
RUN apt-get update && \
    apt-get install -y \
        libzip-dev \
        unzip \
        libonig-dev \
        openssl \
        ncat \
        libpng-dev \
        && docker-php-ext-install \
        pdo_mysql \
        zip \
        mbstring \
        exif \
        bcmath \
        gd \
        mysqli


# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Create system user to run Composer and Artisan Commands
RUN useradd -G www-data,root -u $uid -d /home/$user $user
RUN mkdir -p /home/$user/.composer && \
    chown -R $user:$user /home/$user

#COPY docker-compose/ibbr/docker-entrypoint.sh /usr/local/bin/
#RUN chmod +x /usr/local/bin/docker-entrypoint.sh

# Set working directory
WORKDIR /var/www

EXPOSE 80

USER $user

#ENTRYPOINT ["/usr/local/bin/docker-entrypoint.sh"]

# setup: 
# docker compose build app
# docker compose up -d 
# docker compose exec app rm -rf vendor composer.lock
# docker compose exec app composer install
# docker compose exec app php artisan key:generate
# docker compose exec app php artisan cache:clear
# docker compose exec app php artisan serve --port=8000