FROM php:8.0.2-fpm-alpine

# required packages
RUN apk update && \
    apk add --no-cache freetype libpng libjpeg-turbo freetype-dev libpng-dev libjpeg-turbo-dev && \
    docker-php-ext-configure gd -with-jpeg=/usr/include/ --with-freetype=/usr/include/ && \
    NPROC=$(grep -c ^processor /proc/cpuinfo 2>/dev/null || 1) && \
    docker-php-ext-install -j${NPROC} gd pdo_mysql && \
    apk del --no-cache freetype-dev libpng-dev libjpeg-turbo-dev

# multi stage build (install composer)
COPY --from=composer:2.1 /usr/bin/composer /usr/bin/composer

RUN { \
  echo 'access.format = "%R - %u %t \"%m %{REQUEST_URI}e\" %s"'; \
} >> /usr/local/etc/php-fpm.d/www.conf