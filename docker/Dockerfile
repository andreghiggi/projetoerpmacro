FROM public.ecr.aws/dwchiang/nginx-php-fpm:8.2.26-fpm-bookworm-nginx-1.27.1

COPY --from=composer:latest  /usr/bin/composer /usr/bin/composer
COPY docker/default.conf /etc/nginx/conf.d/default.conf
COPY docker/www.conf /usr/local/etc/php-fpm.d/www.conf
COPY docker/php.ini /usr/local/etc/php/php.ini

RUN apt-get update && apt-get install -y \
    libmemcached-dev \
    zlib1g-dev \
    libssl-dev \
    libzip-dev \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zlib1g-dev

RUN docker-php-ext-install mbstring && docker-php-ext-enable mbstring
RUN yes '' | pecl install memcached && docker-php-ext-enable memcached
RUN docker-php-ext-install mysqli && docker-php-ext-enable mysqli
RUN apt install libmagickwand-dev --no-install-recommends -y
RUN yes '' | pecl install imagick && docker-php-ext-enable imagick
RUN docker-php-ext-configure gd --with-freetype --with-jpeg && docker-php-ext-install gd
RUN docker-php-ext-install exif
RUN docker-php-ext-install zip
RUN docker-php-ext-install soap
RUN docker-php-ext-install intl
RUN docker-php-ext-install bcmath
RUN docker-php-ext-install pdo 
RUN docker-php-ext-install pdo_mysql
RUN docker-php-ext-install fileinfo
ENV dev=1