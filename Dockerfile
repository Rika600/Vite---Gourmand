FROM php:8.2-apache-bookworm

RUN apt-get update \
    && apt-get install -y libssl-dev pkg-config unzip \
    && docker-php-ext-install pdo_mysql \
    && pecl install mongodb \
    && docker-php-ext-enable mongodb

RUN a2enmod rewrite

COPY . /var/www/html/

# Script de démarrage qui génère config.php et lance Apache sur le bon port
RUN echo '#!/bin/bash\n\
# Générer config.php à partir des variables d environnement Railway\n\
cat > /var/www/html/config.php << PHPEOF\n\
<?php\n\
define("BASE_URL", "/");\n\
define("DB_HOST", "${MYSQLHOST:-127.0.0.1}");\n\
define("DB_PORT", "${MYSQLPORT:-3306}");\n\
define("DB_NAME", "${MYSQLDATABASE:-vite_gourmand}");\n\
define("DB_USER", "${MYSQLUSER:-root}");\n\
define("DB_PASSWORD", "${MYSQLPASSWORD:-}");\n\
define("MONGODB_URI", "${MONGODB_URI:-}");\n\
define("MAIL_USERNAME", "${MAIL_USERNAME:-}");\n\
define("MAIL_PASSWORD", "${MAIL_PASSWORD:-}");\n\
PHPEOF\n\
\n\
# Changer le port Apache pour Railway\n\
sed -i "s/80/${PORT:-80}/g" /etc/apache2/sites-available/000-default.conf\n\
sed -i "s/Listen 80/Listen ${PORT:-80}/g" /etc/apache2/ports.conf\n\
\n\
apache2-foreground' > /start.sh && chmod +x /start.sh

CMD ["/start.sh"]
