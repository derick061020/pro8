FROM php:8.3-cli

RUN apt-get update && apt-get install -y \
    git \
    curl \
    unzip \
    zlib1g-dev \
    libzip-dev \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev

# configurar gd
RUN docker-php-ext-configure gd --with-freetype --with-jpeg

# instalar extensiones necesarias
RUN docker-php-ext-install \
    gd \
    zip \
    pdo \
    pdo_mysql

# instalar composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app
