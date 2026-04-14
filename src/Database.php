<?php 


    class Database {
    private static ?PDO $instance = null;

  // Méthode statique qui retourne toujours la MÊME connexion PDO (pattern Singleton)
  public static function getConnection(): PDO {
    if (self::$instance ===null) {
        $host     = '127.0.0.1';
            $port     = '3307';
            $dbname   = 'vite_gourmand';
            $username = 'root';
            $password = '';
            $charset  = 'utf8mb4';

          $dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=$charset";
          
        
           $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
          ];

          try {
            self::$instance = new PDO ($dsn, $username, $password, $options);
          } catch (PDOException $e) {
            die("Erreur de connexion : " . $e->getMessage());
          }
    }

    return self::$instance;
  }
}