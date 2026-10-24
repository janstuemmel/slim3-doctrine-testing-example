FROM php:8.2.35-apache

ENV APACHE_RUN_USER=app

RUN mkdir /app

RUN useradd -u 1000 -ms /bin/bash ${APACHE_RUN_USER} && \
    chown -R "${APACHE_RUN_USER}:${APACHE_RUN_USER}" /var/lock/apache2 /var/run/apache2 && \
    chown -R "${APACHE_RUN_USER}:${APACHE_RUN_USER}" /app

# install deps
RUN apt-get update && \
    apt-get install -y libjpeg62-turbo-dev libfreetype6-dev libpng-dev git zip sqlite3 libcap2-bin && \
    rm -rf /var/lib/apt/lists/*

# allow non-root to bind on port 80
RUN setcap 'cap_net_bind_service=+ep' /usr/sbin/apache2

# install php extension
RUN docker-php-ext-configure gd --with-freetype --with-jpeg && \
    docker-php-ext-install -j$(nproc) gd pdo_mysql exif

# enforce patched PHP and disable upload progress sessions
RUN printf '%s\n' 'session.upload_progress.enabled=0' > "$PHP_INI_DIR/conf.d/zz-security.ini" && \
    php -r 'if (PHP_VERSION_ID < 80235) { fwrite(STDERR, "PHP 8.2.35 or newer is required\n"); exit(1); } if (ini_get("session.upload_progress.enabled") !== "0") { fwrite(STDERR, "session.upload_progress.enabled must be 0\n"); exit(1); }'

# install composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin --filename=composer

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
