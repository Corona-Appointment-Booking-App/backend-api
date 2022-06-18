ARG PHP_VERSION

FROM php:${PHP_VERSION}-fpm

ENV TZ=UTC
RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone

COPY ./default-overrides.ini /usr/local/etc/php/conf.d/docker-php-default-overrides.ini

RUN apt update && apt upgrade -y \
    && apt install -y  \
    git \
    vim \
    nano \
    curl \
    wget \
    unzip \
    zip \
    default-mysql-client \
    gnupg \
    gettext \
    cron \
    libzip-dev \
    libfreetype6-dev \
    libmcrypt-dev \
    libgmp-dev \
    libbz2-dev \
    libpng-dev \
    libjpeg62-turbo-dev \
    libicu-dev \
    libyaml-dev \
    libssh-dev \
    && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-install -j$(nproc) \
    iconv \
    pdo \
    mysqli \
    pdo_mysql \
    intl \
    bcmath \
    gmp \
    bz2 \
    zip \
    && apt-get clean

RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd \
    && pecl install -o -f xdebug-3.0.3 \
    && docker-php-ext-enable xdebug

COPY ./xdebug.ini /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

COPY --from=composer:2.2.2 /usr/bin/composer /usr/local/bin/composer

RUN curl -fsSL https://deb.nodesource.com/setup_16.x | bash - \
    && apt-get install -y nodejs

RUN npm install -g yarn

WORKDIR /var/www

RUN usermod -g www-data root