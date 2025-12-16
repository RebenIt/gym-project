<?php
/**
 * Database Configuration
 * FitZone Gym Management System
 */

// Database credentials
define('DB_HOST', 'localhost');
define('DB_NAME', 'gym_website');
define('DB_USER', 'root');
define('DB_PASS', ''); // Change this in production!
define('DB_CHARSET', 'utf8mb4');

// Site configuration
define('SITE_URL', 'http://localhost/gym-project'); // Change to your domain
define('SITE_NAME', 'FitZone');
define('ADMIN_EMAIL', 'admin@fitzone.com');

// File paths
define('ROOT_PATH', dirname(__DIR__) . '/');
define('INCLUDES_PATH', ROOT_PATH . 'includes/');
define('UPLOADS_PATH', ROOT_PATH . 'assets/uploads/');
define('UPLOADS_URL', SITE_URL . '/assets/uploads/');

// Session configuration
define('SESSION_NAME', 'fitzone_session');
define('SESSION_LIFETIME', 86400); // 24 hours

// Security
define('HASH_COST', 12); // For password_hash

// Timezone
date_default_timezone_set('Asia/Baghdad');

// Error reporting (disable in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_name(SESSION_NAME);
    session_start();
}

/**
 * Database Connection Class
 */
class Database {
    private static $instance = null;
    private $connection;
    
    private function __construct() {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
            ];
            $this->connection = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            die("Database connection failed: " . $e->getMessage());
        }
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function getConnection() {
        return $this->connection;
    }
    
    // Prevent cloning
    private function __clone() {}
    
    // Prevent unserialization
    public function __wakeup() {
        throw new Exception("Cannot unserialize singleton");
    }
}

/**
 * Get database connection
 */
function db() {
    return Database::getInstance()->getConnection();
}

/**
 * Quick query helper
 */
function query($sql, $params = []) {
    $stmt = db()->prepare($sql);
    $stmt->execute($params);
    return $stmt;
}

/**
 * Fetch single row
 */
function fetchOne($sql, $params = []) {
    return query($sql, $params)->fetch();
}

/**
 * Fetch all rows
 */
function fetchAll($sql, $params = []) {
    return query($sql, $params)->fetchAll();
}

/**
 * Get last insert ID
 */
function lastInsertId() {
    return db()->lastInsertId();
}
