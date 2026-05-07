#!/bin/bash

cat > /var/www/html/config.php << PHPEOF
<?php
define('BASE_URL', '/');
define('DB_HOST', '${MYSQLHOST:-127.0.0.1}');
define('DB_PORT', '${MYSQLPORT:-3306}');
define('DB_NAME', '${MYSQLDATABASE:-vite_gourmand}');
define('DB_USER', '${MYSQLUSER:-root}');
define('DB_PASSWORD', '${MYSQLPASSWORD:-}');
define('MONGODB_URI', '${MONGODB_URI:-}');
define('MAIL_USERNAME', '${MAIL_USERNAME:-}');
define('MAIL_PASSWORD', '${MAIL_PASSWORD:-}');
PHPEOF

echo "Listen ${PORT:-80}" > /etc/apache2/ports.conf
sed -i "s/<VirtualHost \*:.*>/<VirtualHost *:${PORT:-80}>/" /etc/apache2/sites-available/000-default.conf

a2dismod mpm_event 2>/dev/null
a2enmod mpm_prefork 2>/dev/null

apache2-foreground