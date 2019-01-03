FROM php:7.2-fpm

RUN apt-get update
RUN apt-get dist-upgrade -y
RUN apt-get install -y curl git subversion openssl openssh-client mercurial bash zlib1g-dev

RUN docker-php-ext-install pdo_mysql
RUN docker-php-ext-install zip

ENV COMPOSER_ALLOW_SUPERUSER 1
ENV COMPOSER_HOME /tmp

RUN curl -sS https://getcomposer.org/installer | php && mv composer.phar /usr/local/bin/composer && composer global require hirak/prestissimo --no-plugins --no-scripts \
 && composer --ansi --version --no-interaction \
 && rm -rf /tmp/* /tmp/.htaccess

EXPOSE 1337

RUN usermod -u 1000 www-data
