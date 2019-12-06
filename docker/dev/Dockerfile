FROM php:7.3-apache
#
#ENV DEBIAN_FRONTEND noninteractive
#
RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        wget \
        curl \
        ca-certificates \
        build-essential \
        apt-transport-https \
        gnupg\
        imagemagick \
        jpegoptim \
        libjpeg-turbo-progs \
        pngcrush \
        optipng \
        libpng16-16 \
        libpng-dev \
        libmagick++-dev \
        bash-completion \
    && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-install \
    mysqli \
    pdo_mysql \
    opcache \
    gd
    
    # Install composer
ADD install_composer.sh /install_composer.sh
RUN chmod +x /install_composer.sh \
 && sh /install_composer.sh \
 && mv composer.phar /usr/bin/composer \
 && chmod +x /usr/bin/composer

# set recommended PHP.ini settings
# see https://secure.php.net/manual/en/opcache.installation.php
#ADD php/opcache.ini /etc/php/7.2/mods-available/opcache.ini

RUN mkdir -p /openhanditour \
 && usermod -u 1000 -d /openhanditour -s /bin/bash www-data \
 && chown www-data:www-data /openhanditour

WORKDIR /openhanditour

RUN rm -f /etc/apache2/sites-enabled/000-default.conf

EXPOSE 80

RUN sed -i "s/AllowOverride None/AllowOverride All/g" /etc/apache2/apache2.conf
RUN a2enmod headers
RUN a2enmod rewrite

#HTTP 2
RUN a2enmod http2
RUN a2enmod ssl
ADD http2.conf /etc/apache2/conf-available/http2.conf
RUN a2enconf http2

#Apache conf
ADD apache.vhost.conf /etc/apache2/sites-available/docker-app.conf
RUN a2ensite docker-app

# Change conf
#RUN sed -i "s/;date.timezone =/date.timezone = UTC/" /etc/php/7.2/cli/php.ini \
# && sed -i "s/;date.timezone =/date.timezone = UTC/" /etc/php/7.2/apache2/php.ini \
# && sed -i "s/;realpath_cache_size = 4096k/realpath_cache_size = 4096k/" /etc/php/7.2/cli/php.ini \
# && sed -i "s/;realpath_cache_size = 4096k/realpath_cache_size = 4096k/" /etc/php/7.2/apache2/php.ini \
# && sed -i "s/;realpath_cache_ttl = 120/realpath_cache_ttl = 600/" /etc/php/7.2/cli/php.ini \
# && sed -i "s/;realpath_cache_ttl = 120/realpath_cache_ttl = 600/" /etc/php/7.2/apache2/php.ini \
# && sed -i -e "s/^display_errors = Off/display_errors = On/" /etc/php/7.2/apache2/php.ini \
# && sed -i -e "s/^display_errors = Off/display_errors = On/" /etc/php/7.2/cli/php.ini \
# && rm -f /etc/php/7.2/*/conf.d/20-opcache.ini

# Run Apache
ADD run.sh /run.sh

CMD ["/bin/bash", "/run.sh"]
