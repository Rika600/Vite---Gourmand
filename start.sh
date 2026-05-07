#!/bin/bash

# Générer config.php avec PHP
php << 'EOFPHP'
<?php
$config = "<?php\n";
$config .= "define('BASE_URL', '/');\n";
$config .= "define('DB_HOST', '" . getenv('MYSQLHOST') . "');\n";
$config .= "define('DB_PORT', '" . getenv('MYSQLPORT') . "');\n";
$config .= "define('DB_NAME', '" . getenv('MYSQLDATABASE') . "');\n";
$config .= "define('DB_USER', '" . getenv('MYSQLUSER') . "');\n";
$config .= "define('DB_PASSWORD', '" . getenv('MYSQLPASSWORD') . "');\n";
$config .= "define('MONGODB_URI', '" . getenv('MONGODB_URI') . "');\n";
$config .= "define('MAIL_USERNAME', '" . getenv('MAIL_USERNAME') . "');\n";
$config .= "define('MAIL_PASSWORD', '" . getenv('MAIL_PASSWORD') . "');\n";
file_put_contents('/var/www/html/config.php', $config);
echo "config.php généré\n";
?>
EOFPHP

echo "Listen ${PORT:-80}" > /etc/apache2/ports.conf
sed -i "s/<VirtualHost \*:.*>/<VirtualHost *:${PORT:-80}>/" /etc/apache2/sites-available/000-default.conf

a2dismod mpm_event 2>/dev/null
a2enmod mpm_prefork 2>/dev/null

apache2-foreground