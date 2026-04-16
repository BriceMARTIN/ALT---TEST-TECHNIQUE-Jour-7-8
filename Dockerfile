FROM php:8.4-cli

RUN apt-get update \
    && apt-get install -y --no-install-recommends git unzip zip libzip-dev libicu-dev libonig-dev libxml2-dev \
    && docker-php-ext-install pdo_mysql intl opcache \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
WORKDIR /var/www/html
COPY . /var/www/html
RUN git config --global --add safe.directory /var/www/html \
    && composer install --no-interaction --prefer-dist --optimize-autoloader
EXPOSE 8000
CMD ["php", "-S", "0.0.0.0:8000", "-t", "public", "public/index.php"]
