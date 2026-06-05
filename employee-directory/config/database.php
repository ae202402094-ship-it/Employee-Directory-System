<?php
// ============================================================
// config/database.php — PDO Singleton Connection
// ============================================================

class Database {
    private static ?PDO $instance = null;

    private function __construct() {}
    private function __clone() {}

    public static function getInstance(): PDO {
        if (self::$instance === null) {
            $host   = getenv('DB_HOST')     ?: 'localhost';
            $dbname = getenv('DB_NAME')     ?: 'employee_directory';
            $user   = getenv('DB_USER')     ?: 'root';
            $pass   = getenv('DB_PASS')     ?: '';
            $port   = getenv('DB_PORT')     ?: '3306';
            $charset = 'utf8mb4';

            $dsn = "mysql:host={$host};port={$port};dbname={$dbname};charset={$charset}";

            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];

            try {
                self::$instance = new PDO($dsn, $user, $pass, $options);
            } catch (PDOException $e) {
                // In production, log this and show a friendly error
                if (APP_ENV === 'development') {
                    throw new RuntimeException('Database connection failed: ' . $e->getMessage());
                }
                http_response_code(503);
                die('Service temporarily unavailable. Please try again later.');
            }
        }

        return self::$instance;
    }
}
