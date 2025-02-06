FROM composer:lts

WORKDIR /app

COPY . /app

RUN composer global require "laravel/installer=~1.1"
RUN composer install
RUN composer dump-autoload
RUN docker-php-ext-install pdo pdo_mysql && docker-php-ext-enable pdo_mysql
COPY --chmod=0755 ./entrypoint.sh /entrypoint.sh
RUN chmod +x entrypoint.sh

EXPOSE 8000
ENTRYPOINT ["/entrypoint.sh"]
