FROM php:8.4-apache
RUN apt update
RUN apt dist-upgrade -y
RUN apt install -y libicu-dev git vim
RUN docker-php-ext-install intl
RUN docker-php-ext-install mysqli
RUN docker-php-ext-install pdo_mysql
RUN docker-php-ext-install opcache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*
RUN a2enmod headers
RUN a2enmod rewrite
RUN docker-php-ext-enable opcache
WORKDIR /var/www