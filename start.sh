#!/bin/bash

php -r "
file_put_contents('/var/www/html/config.php', '<?php
define(\"BASE_URL\", \"/\");
define(\"DB_HOST\", \"' . getenv('MYSQLHOST') . '\");
define(\"DB_PORT\", \"' . getenv('MYSQLPORT') . '\");
define(\"DB_NAME\", \"' . getenv('MYSQLDATABASE') . '\");
define(\"DB_USER\", \"' . getenv('MYSQLUSER') . '\");
define(\"DB_PASSWORD\", \"' . getenv('MYSQLPASSWORD') . '\");
define(\"MONGODB_URI\", \"' . getenv('MONGODB_URI') . '\");
define(\"MAIL_USERNAME\", \"' . getenv('MAIL_USERNAME') . '\");
define(\"MAIL_PASSWORD\", \"' . getenv('MAIL_PASSWORD') . '\");
');
"

echo "Listen ${PORT:-80}" > /etc/apache2/ports.conf
sed -i "s/<VirtualHost \*:.*>/<VirtualHost *:${PORT:-80}>/" /etc/apache2/sites-available/000-default.conf

a2dismod mpm_event 2>/dev/null
a2enmod mpm_prefork 2>/dev/null

apache2-foreground