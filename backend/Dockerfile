# syntax=docker/dockerfile:1

FROM php:8.4-apache

# set main params
ENV APP_HOME=/var/www/html
ARG HOST_UID=1000
ARG HOST_GID=1000
ENV USERNAME=www-data
ENV TZ='America/Toronto'

# ---- System dependencies ----
RUN apt-get update && apt-get install -y --no-install-recommends \
    git \
    unzip \
    zip \
    curl \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    libicu-dev \
    default-mysql-client \
    && rm -rf /var/lib/apt/lists/*

# ---- PHP extensions Laravel typically needs ----
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j"$(nproc)" \
        pdo_mysql \
        mbstring \
        exif \
        pcntl \
        bcmath \
        gd \
        zip \
        intl \
        opcache

# Redis extension (optional but common with Laravel queues/cache)
# RUN pecl install redis && docker-php-ext-enable redis

# ---- Apache config ----
RUN a2enmod rewrite headers

# Point Apache's document root at Laravel's public/ directory
ENV APACHE_DOCUMENT_ROOT=${APP_HOME}/public
RUN sed -ri -e "s!/var/www/html!${APACHE_DOCUMENT_ROOT}!g" /etc/apache2/sites-available/*.conf \
    && sed -ri -e "s!/var/www/!${APACHE_DOCUMENT_ROOT}!g" /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Allow .htaccess overrides (needed for Laravel's front-controller routing)
RUN printf '<Directory %s>\n\tOptions Indexes FollowSymLinks\n\tAllowOverride All\n\tRequire all granted\n</Directory>\n' "${APACHE_DOCUMENT_ROOT}" \
    > /etc/apache2/conf-available/laravel.conf \
    && a2enconf laravel

# ---- Composer ----
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR $APP_HOME

# ---- Application code ----
# Copy composer files first for better layer caching
# COPY composer.json composer.lock* ./
# RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist

# COPY . .

# RUN composer dump-autoload --optimize \
#     && php artisan config:clear || true

RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone

# Fix permissions for www-data user and change owner to www-data
RUN mkdir -p /home/$USERNAME && chown $USERNAME:$USERNAME /home/$USERNAME \
  && usermod -o -u $HOST_UID $USERNAME -d /home/$USERNAME \
  && groupmod -o -g $HOST_GID $USERNAME \
  && chown -R ${USERNAME}:${USERNAME} $APP_HOME \
  && chmod -R 755 $APP_HOME

USER ${USERNAME}

# EXPOSE 80

CMD ["apache2-foreground"]
