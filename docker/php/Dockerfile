FROM php:8.2-fpm

# !!! you should change uid to match your host OS LINUX uid, command id, first user default uid 1000
ARG uid=1000
ARG user=phpuser

RUN useradd -m -s /bin/bash -u $uid $user
COPY docker/php/.bashrc /home/$user/

COPY --from=composer:2.7.9 /usr/bin/composer /usr/local/bin/composer

RUN apt-get update -y

RUN curl -sL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get update && apt-get install nodejs --no-install-recommends --no-install-suggests -y \
    && rm -r /var/lib/apt/lists/*

# If you got invalid for another bla bla time. Run this command in your ubuntu host
# sudo date -s "$(wget -qSO- --max-redirect=0 google.com 2>&1 | grep Date: | cut -d' ' -f5-8)Z"
# https://askubuntu.com/questions/1059217/getting-release-is-not-valid-yet-while-updating-ubuntu-docker-container
RUN apt-get update && apt-get install --no-install-recommends --no-install-suggests -y \
    #build-essential \
    git \
    curl \
    less \
    libzip-dev \
    unzip \
    libfreetype6-dev \
    libwebp-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    libgmp-dev \
    ssh \
    && rm -r /var/lib/apt/lists/*


RUN docker-php-ext-configure gd --with-freetype --with-webp --with-jpeg

RUN docker-php-ext-install \
    pdo_mysql \
    bcmath \
    zip \
    exif \
    gd

COPY docker/php/custom.ini $PHP_INI_DIR/conf.d/

USER $user
