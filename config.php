<?php
// ===== XAMPP local =====
define('BASE_URL', '/vite-gourmand/');
define('DB_HOST', '127.0.0.1');
define('DB_PORT', '3307');
define('DB_NAME', 'vite_gourmand');
define('DB_USER', 'root');
define('DB_PASSWORD', '');

// ===== Docker =====
// define('BASE_URL', '/');
// define('DB_HOST', 'db');
// define('DB_PORT', '3306');
// define('DB_PASSWORD', 'root');

// Configuration MongoDB
define('MONGODB_URI', 'mongodb+srv://karima740_db_user:ZAcS7gb11NvQ1m0x@cluster0.hsa6bee.mongodb.net/?retryWrites=true&w=majority&tls=true&tlsAllowInvalidCertificates=true');

// Configuration PHPMailer
define('MAIL_USERNAME', 'vite.gourmand.contact@gmail.com');
define('MAIL_PASSWORD', getenv('MAIL_PASSWORD') ?: 'bmgm fuhg ehpb drqu');

