FROM mcr.microsoft.com/devcontainers/base:ubuntu-22.04

RUN sudo apt update && sudo apt install -y \
    software-properties-common

RUN sudo add-apt-repository ppa:ondrej/php -y && sudo apt update && sudo apt install -y \
    nginx \
    php8.2-fpm \ 
    php8.2-common \ 
    php8.2-mysql \
    php8.2-xml \ 
    php8.2-xmlrpc \ 
    php8.2-curl \ 
    php8.2-gd \
    php8.2-imagick \ 
    php8.2-cli \ 
    php8.2-dev \ 
    php8.2-imap \
    php8.2-mbstring \
    php8.2-opcache \
    php8.2-redis \
    php8.2-soap \
    php8.2-zip