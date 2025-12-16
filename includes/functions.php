<?php
/**
 * Helper Functions
 * FitZone Gym Management System
 */

require_once __DIR__ . '/config.php';

/**
 * Sanitize input
 */
function sanitize($input) {
    if (is_array($input)) {
        return array_map('sanitize', $input);
    }
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

/**
 * Escape output for HTML
 */
function e($string) {
    return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
}

/**
 * Generate CSRF token
 */
function generateCSRFToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verify CSRF token
 */
function verifyCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Get CSRF input field
 */
function csrfField() {
    return '<input type="hidden" name="csrf_token" value="' . generateCSRFToken() . '">';
}

/**
 * Redirect to URL
 */
function redirect($url) {
    header("Location: $url");
    exit;
}

/**
 * Check if user is logged in
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Check if admin is logged in
 */
function isAdminLoggedIn() {
    return isset($_SESSION['admin_id']) && !empty($_SESSION['admin_id']);
}

/**
 * Require user login
 */
function requireLogin() {
    if (!isLoggedIn()) {
        $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
        redirect(SITE_URL . '/login.php');
    }
}

/**
 * Require admin login
 */
function requireAdminLogin() {
    if (!isAdminLoggedIn()) {
        redirect(SITE_URL . '/admin/login.php');
    }
}

/**
 * Get current user data
 */
function getCurrentUser() {
    if (!isLoggedIn()) return null;
    return fetchOne("SELECT * FROM users WHERE id = ?", [$_SESSION['user_id']]);
}

/**
 * Get current admin data
 */
function getCurrentAdmin() {
    if (!isAdminLoggedIn()) return null;
    return fetchOne("SELECT * FROM admins WHERE id = ?", [$_SESSION['admin_id']]);
}

/**
 * Get setting value
 */
function getSetting($key, $lang = 'en') {
    $setting = fetchOne("SELECT * FROM settings WHERE setting_key = ?", [$key]);
    if (!$setting) return null;
    
    if ($lang === 'ku' && !empty($setting['setting_value_ku'])) {
        return $setting['setting_value_ku'];
    }
    return $setting['setting_value'];
}

/**
 * Get all settings by category
 */
function getSettingsByCategory($category) {
    return fetchAll("SELECT * FROM settings WHERE category = ? ORDER BY sort_order", [$category]);
}

/**
 * Update setting
 */
function updateSetting($key, $value, $valueKu = null) {
    $sql = "UPDATE settings SET setting_value = ?, setting_value_ku = ? WHERE setting_key = ?";
    return query($sql, [$value, $valueKu, $key]);
}

/**
 * Get current language
 */
function getCurrentLang() {
    if (isset($_GET['lang']) && in_array($_GET['lang'], ['en', 'ku'])) {
        $_SESSION['lang'] = $_GET['lang'];
    }
    return $_SESSION['lang'] ?? 'en';
}

/**
 * Get text based on language
 */
function __($textEn, $textKu = null) {
    $lang = getCurrentLang();
    if ($lang === 'ku' && $textKu !== null) {
        return $textKu;
    }
    return $textEn;
}

/**
 * Get localized field from database row
 */
function getLocalized($row, $field) {
    $lang = getCurrentLang();
    $kuField = $field . '_ku';
    
    if ($lang === 'ku' && isset($row[$kuField]) && !empty($row[$kuField])) {
        return $row[$kuField];
    }
    return $row[$field] ?? '';
}

/**
 * Format date
 */
function formatDate($date, $format = 'M d, Y') {
    if (empty($date)) return '';
    return date($format, strtotime($date));
}

/**
 * Format price
 */
function formatPrice($amount, $currency = '$') {
    return $currency . number_format($amount, 2);
}

/**
 * Generate slug from string
 */
function generateSlug($string) {
    $string = preg_replace('/[^a-zA-Z0-9\s]/', '', $string);
    $string = strtolower(trim($string));
    $string = preg_replace('/\s+/', '-', $string);
    return $string;
}

/**
 * Upload file
 */
function uploadFile($file, $allowedTypes = ['jpg', 'jpeg', 'png', 'gif', 'webp'], $maxSize = 5242880) {
    if (!isset($file['error']) || $file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'message' => 'Upload error'];
    }
    
    $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($extension, $allowedTypes)) {
        return ['success' => false, 'message' => 'Invalid file type'];
    }
    
    if ($file['size'] > $maxSize) {
        return ['success' => false, 'message' => 'File too large'];
    }
    
    $filename = uniqid() . '_' . time() . '.' . $extension;
    $destination = UPLOADS_PATH . $filename;
    
    if (!is_dir(UPLOADS_PATH)) {
        mkdir(UPLOADS_PATH, 0755, true);
    }
    
    if (move_uploaded_file($file['tmp_name'], $destination)) {
        return ['success' => true, 'filename' => $filename];
    }
    
    return ['success' => false, 'message' => 'Failed to save file'];
}

/**
 * Delete file
 */
function deleteFile($filename) {
    $filepath = UPLOADS_PATH . $filename;
    if (file_exists($filepath) && is_file($filepath)) {
        return unlink($filepath);
    }
    return false;
}

/**
 * Get upload URL
 */
function uploadUrl($filename) {
    if (empty($filename)) return SITE_URL . '/assets/images/placeholder.jpg';
    return UPLOADS_URL . $filename;
}

/**
 * Flash message
 */
function setFlash($type, $message) {
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

/**
 * Get and clear flash message
 */
function getFlash() {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

/**
 * Display flash message
 */
function displayFlash() {
    $flash = getFlash();
    if ($flash) {
        $type = $flash['type'];
        $message = e($flash['message']);
        echo "<div class='alert alert-{$type}'>{$message}</div>";
    }
}

/**
 * Pagination helper
 */
function paginate($totalItems, $perPage = 10, $currentPage = 1) {
    $totalPages = ceil($totalItems / $perPage);
    $currentPage = max(1, min($currentPage, $totalPages));
    $offset = ($currentPage - 1) * $perPage;
    
    return [
        'total_items' => $totalItems,
        'per_page' => $perPage,
        'current_page' => $currentPage,
        'total_pages' => $totalPages,
        'offset' => $offset,
        'has_prev' => $currentPage > 1,
        'has_next' => $currentPage < $totalPages
    ];
}

/**
 * JSON response
 */
function jsonResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

/**
 * Is AJAX request
 */
function isAjax() {
    return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
           strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
}

/**
 * Get YouTube embed URL
 */
function getYouTubeEmbed($url) {
    $videoId = '';
    
    if (preg_match('/youtube\.com\/watch\?v=([^\&\?\/]+)/', $url, $matches)) {
        $videoId = $matches[1];
    } elseif (preg_match('/youtube\.com\/embed\/([^\&\?\/]+)/', $url, $matches)) {
        $videoId = $matches[1];
    } elseif (preg_match('/youtu\.be\/([^\&\?\/]+)/', $url, $matches)) {
        $videoId = $matches[1];
    }
    
    if ($videoId) {
        return "https://www.youtube.com/embed/{$videoId}";
    }
    
    return '';
}

/**
 * Truncate text
 */
function truncate($text, $length = 100, $suffix = '...') {
    if (mb_strlen($text) <= $length) return $text;
    return mb_substr($text, 0, $length) . $suffix;
}

/**
 * Log activity
 */
function logActivity($action, $details = '', $userId = null, $adminId = null) {
    // Optional: Implement activity logging if needed
    // For now, we'll just return true
    return true;
}

/**
 * Validate email
 */
function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Validate phone (basic)
 */
function isValidPhone($phone) {
    return preg_match('/^[\+]?[0-9\s\-]{8,20}$/', $phone);
}

/**
 * Get time ago
 */
function timeAgo($datetime) {
    $timestamp = strtotime($datetime);
    $diff = time() - $timestamp;
    
    if ($diff < 60) return 'just now';
    if ($diff < 3600) return floor($diff / 60) . ' minutes ago';
    if ($diff < 86400) return floor($diff / 3600) . ' hours ago';
    if ($diff < 604800) return floor($diff / 86400) . ' days ago';
    if ($diff < 2592000) return floor($diff / 604800) . ' weeks ago';
    
    return formatDate($datetime);
}
