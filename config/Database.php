<?php
// Database Singleton using PDO
require_once __DIR__ . '/config.php';

class Database {
    private static ?PDO $instance = null;

    public static function getConnection(): PDO {
        if (self::$instance === null) {
            $dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=utf8mb4";
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];
            try {
                self::$instance = new PDO($dsn, DB_USER, DB_PASS, $options);
            } catch (PDOException $e) {
                // In case port 3306 is used in some environments, attempt fallback
                try {
                    $dsnFallback = "mysql:host=" . DB_HOST . ";port=3306;dbname=" . DB_NAME . ";charset=utf8mb4";
                    self::$instance = new PDO($dsnFallback, DB_USER, DB_PASS, $options);
                } catch (PDOException $e2) {
                    die("Koneksi Database Gagal: " . $e->getMessage());
                }
            }
        }
        return self::$instance;
    }
}
