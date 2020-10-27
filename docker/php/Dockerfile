FROM php:7.4-fpm

RUN docker-php-ext-install pdo_mysql

RUN pecl install apcu

RUN apt-get update && \
apt-get install -y \
libzip-dev

RUN docker-php-ext-install zip
RUN docker-php-ext-enable apcu

#Install CURL
RUN apt-get update && \
	apt-get install -y \
		curl \
		apt-transport-https

RUN apt-get update && apt-get install -y gnupg2
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
RUN php composer-setup.php
RUN php -r "unlink('composer-setup.php');"
RUN mv composer.phar /usr/local/bin/composer
RUN chmod +x /usr/local/bin/composer
RUN ln -snf /usr/share/zoneinfo/Europe/Lisbon /etc/localtime

#Install Yarn
RUN curl -sS https://dl.yarnpkg.com/debian/pubkey.gpg | apt-key add -
RUN echo "deb https://dl.yarnpkg.com/debian/ stable main" | tee /etc/apt/sources.list.d/yarn.list
RUN apt-get update && apt-get install -y yarn

COPY ./docker/php/conf/php.ini /usr/local/etc/php/php.ini

WORKDIR /usr/src/app

COPY --chown=1000:1000 src /usr/src/app

RUN PATH=$PATH:/usr/src/apps/vendor/bin:bin
