FROM php:7.2-apache

ENV APACHE_RUN_USER app

RUN mkdir /app

RUN useradd -u 1000 -ms /bin/bash ${APACHE_RUN_USER} && \
    chown -R "${APACHE_RUN_USER}:${APACHE_RUN_USER}" /var/lock/apache2 /var/run/apache2 && \
    chown -R "${APACHE_RUN_USER}:${APACHE_RUN_USER}" /app

# install deps
RUN apt-get update && \
    apt-get install -y libjpeg62-turbo-dev libfreetype6-dev libpng-dev git zip sqlite libcap2-bin gnupg

# allow non-root to bind on port 80
RUN setcap 'cap_net_bind_service=+ep' /usr/sbin/apache2

# install php extension
RUN docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ && \
    docker-php-ext-install -j$(nproc) gd pdo_mysql exif

# install composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin --filename=composer

# install node
RUN curl -sL https://deb.nodesource.com/setup_9.x | bash - && apt-get install -y nodejs

# cleanup
RUN apt-get clean && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

# enable php mods
RUN a2enmod rewrite

# configure apache
RUN sed -ri -e 's!/var/www/html!/app/public!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!/app!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

ADD . /app

WORKDIR /app

USER ${APACHE_RUN_USER}

RUN composer install
