FROM composer/composer:2-bin AS composer

FROM php:8.1-fpm-buster
ARG TIMEZONE

COPY php/php.ini /usr/local/etc/php/conf.d/docker-php-config.ini

COPY . ./

RUN apt-get update && apt-get install -y \
    gnupg \
    g++ \
    procps \
    openssl \
    git \
    unzip \
    zlib1g-dev \
    libzip-dev \
    libfreetype6-dev \
    libpng-dev \
    libjpeg-dev \
    libicu-dev  \
    libonig-dev \
    libxslt1-dev \
    acl \
    libpq-dev \
    && echo 'alias sf="php bin/console"' >> ~/.bashrc

COPY --from=composer /composer /usr/bin/composer
RUN composer install --no-dev --no-scripts --no-progress --optimize-autoloader

RUN docker-php-ext-configure gd --with-jpeg --with-freetype

RUN docker-php-ext-install \
    pdo pdo_mysql pdo_pgsql zip xsl gd intl opcache exif mbstring

RUN pecl install redis && docker-php-ext-enable redis

RUN if [ -n "$JWT_SECRET" ]; then echo "$JWT_SECRET" > /var/www/html/config/jwt/private.pem; fi

RUN if [ -n "$JWT_PUBLIC" ]; then echo "$JWT_PUBLIC" > /var/www/html/config/jwt/public.pem; fi

# Set timezone
RUN ln -snf /usr/share/zoneinfo/${TIMEZONE} /etc/localtime && echo ${TIMEZONE} > /etc/timezone \
    && printf '[PHP]\ndate.timezone = "%s"\n', ${TIMEZONE} > /usr/local/etc/php/conf.d/tzone.ini \
    && "date"

WORKDIR /var/www/html