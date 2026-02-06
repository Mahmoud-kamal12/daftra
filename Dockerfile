FROM php:8.2-apache

WORKDIR /var/www/html

RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    curl \
    unzip \
    git \
    && docker-php-ext-configure gd \
    && docker-php-ext-install pdo pdo_mysql mbstring exif pcntl bcmath gd \
    && pecl install redis  \
    && docker-php-ext-enable redis

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

COPY ./composer.json ./composer.lock ./

RUN composer install --no-interaction --optimize-autoloader --no-scripts \
    && composer clear-cache

COPY . /var/www/html

RUN chmod -R 777 /var/www/html/storage /var/www/html/bootstrap/cache

RUN php artisan migrate

RUN a2enmod rewrite

ENV APACHE_DOCUMENT_ROOT /var/www/html/public

# Update Apache configuration with the correct document root
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

USER www-data

EXPOSE 80

CMD ["apache2-foreground"]
