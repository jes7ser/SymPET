FROM php:8.3-fpm

# Installer les dépendances système
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libicu-dev \
    libzip-dev \
    libpng-dev \
    libonig-dev \
    && rm -rf /var/lib/apt/lists/*

# Installer les extensions PHP requises par Symfony
RUN docker-php-ext-install \
    pdo_mysql \
    intl \
    zip \
    opcache

# Installer Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Définir le répertoire de travail
WORKDIR /var/www/html
