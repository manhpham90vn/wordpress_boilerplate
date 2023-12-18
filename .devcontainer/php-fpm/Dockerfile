FROM php:8.2-fpm

RUN apt-get update && apt-get install -y \
    libzip-dev \
    zlib1g-dev \
    libjpeg-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    libpng-dev \
    zip \
    git

RUN docker-php-ext-install mysqli

RUN docker-php-ext-configure gd --with-freetype=/usr/include/ --with-jpeg=/usr/include/

RUN docker-php-ext-install -j$(nproc) gd zip