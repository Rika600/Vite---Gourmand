FROM php:8.2-apache-bookworm

RUN apt-get update \
    && apt-get install -y libssl-dev pkg-config unzip \
    && docker-php-ext-install pdo_mysql \
    && pecl install mongodb \
    && docker-php-ext-enable mongodb

RUN a2enmod rewrite

ENV LANG=C.UTF-8

COPY . /var/www/html/
COPY start.sh /start.sh
RUN chmod +x /start.sh

CMD ["/start.sh"]