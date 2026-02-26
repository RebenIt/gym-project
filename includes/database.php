<?php
require_once 'config.php';

// ============================================
// DATABASE CONNECTION CLASS
// ============================================
class Database {
    private static $instance = null;
    private $conn;

    private function __construct() {
        try {
            $this->conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

            if ($this->conn->connect_error) {
                throw new Exception("Connection failed: " . $this->conn->connect_error);
            }

            $this->conn->set_charset("utf8mb4");
        } catch (Exception $e) {
            die("Database connection error: " . $e->getMessage());
        }
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    public function getConnection() {
        return $this->conn;
    }

    // Prevent cloning
    private function __clone() {}

    // Prevent unserialization
    public function __wakeup() {
        throw new Exception("Cannot unserialize singleton");
    }
}

// ============================================
// GET DATABASE CONNECTION
// ============================================
function getDB() {
    return Database::getInstance()->getConnection();
}

// ============================================
// EXECUTE QUERY (Only if not already defined by config.php)
// ============================================
if (!function_exists('query')) {
    function query($sql, $params = []) {
        $conn = getDB();
        $stmt = $conn->prepare($sql);

        if (!$stmt) {
            return false;
        }

        if (!empty($params)) {
            $types = '';
            foreach ($params as $param) {
                if (is_int($param)) {
                    $types .= 'i';
                } elseif (is_float($param)) {
                    $types .= 'd';
                } else {
                    $types .= 's';
                }
            }
            $stmt->bind_param($types, ...$params);
        }

        $stmt->execute();
        return $stmt;
    }
}

// ============================================
// FETCH ALL RESULTS (Only if not already defined)
// ============================================
if (!function_exists('fetchAll')) {
    function fetchAll($sql, $params = []) {
        $stmt = query($sql, $params);
        if (!$stmt) return [];

        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }
}

// ============================================
// FETCH SINGLE RESULT (Only if not already defined)
// ============================================
if (!function_exists('fetchOne')) {
    function fetchOne($sql, $params = []) {
        $stmt = query($sql, $params);
        if (!$stmt) return null;

        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }
}

// ============================================
// INSERT AND GET ID
// ============================================
if (!function_exists('insert')) {
    function insert($sql, $params = []) {
        $stmt = query($sql, $params);
        if (!$stmt) return false;

        return getDB()->insert_id;
    }
}

// ============================================
// UPDATE/DELETE
// ============================================
if (!function_exists('execute')) {
    function execute($sql, $params = []) {
        $stmt = query($sql, $params);
        return $stmt ? $stmt->affected_rows : false;
    }
}

// ============================================
// ESCAPE STRING
// ============================================
function escape($string) {
    return getDB()->real_escape_string($string);
}
?>
