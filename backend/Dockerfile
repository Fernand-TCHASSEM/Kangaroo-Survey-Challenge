# syntax=docker/dockerfile:1

FROM php:8.4-apache

ENV APP_HOME=/var/www/html
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
    && rm -rf /var/lib/apt/lists/*

# ---- PHP extensions ----
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j"$(nproc)" \
        mbstring \
        exif \
        pcntl \
        bcmath \
        gd \
        zip \
        intl \
        opcache

# ---- Apache config ----
RUN a2enmod rewrite headers

ENV APACHE_DOCUMENT_ROOT=${APP_HOME}/public
RUN sed -ri -e "s!/var/www/html!${APACHE_DOCUMENT_ROOT}!g" /etc/apache2/sites-available/*.conf \
    && sed -ri -e "s!/var/www/!${APACHE_DOCUMENT_ROOT}!g" /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

RUN printf '<Directory %s>\n\tOptions Indexes FollowSymLinks\n\tAllowOverride All\n\tRequire all granted\n</Directory>\n' "${APACHE_DOCUMENT_ROOT}" \
    > /etc/apache2/conf-available/laravel.conf \
    && a2enconf laravel

RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone

# ---- Composer ----
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR $APP_HOME

# ---- Application code + dependencies (ACTIVÉ pour Render) ----
# 1. Copier d'abord les fichiers composer pour profiter du cache de couche
COPY composer.json composer.lock* ./

# 2. Installer les dépendances de production (sans les scripts qui ont besoin du code complet)
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist --no-interaction

# 3. Copier tout le reste du code
COPY . .

# 4. Générer l'autoloader optimisé maintenant que tout le code est là
RUN composer dump-autoload --optimize --no-dev

# ---- Permissions (Render) ----
# storage/ et bootstrap/cache doivent être accessibles en écriture par Apache (www-data)
RUN chown -R ${USERNAME}:${USERNAME} ${APP_HOME} \
    && chmod -R 755 ${APP_HOME} \
    && chmod -R 775 ${APP_HOME}/storage ${APP_HOME}/bootstrap/cache

USER ${USERNAME}

# Apache écoute sur 80 dans le conteneur ; Render mappe automatiquement
EXPOSE 80

CMD ["apache2-foreground"]