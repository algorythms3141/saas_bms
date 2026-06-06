<?php
/**
 * Application Configuration
 * 
 * Main configuration file for the SaaS Business Management System
 */

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Error Reporting (Set to 0 in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Timezone
date_default_timezone_set('Asia/Calcutta');

// Application Settings
define('APP_NAME', 'SaaS Business Management System');
define('APP_VERSION', '1.0.0');
define('APP_URL', 'https://bms.algorythms.in');

// Directory Paths
define('ROOT_PATH', dirname(__DIR__));
define('CONFIG_PATH', ROOT_PATH . '/config');
define('CONTROLLERS_PATH', ROOT_PATH . '/controllers');
define('MODELS_PATH', ROOT_PATH . '/models');
define('VIEWS_PATH', ROOT_PATH . '/views');
define('ASSETS_PATH', ROOT_PATH . '/assets');
define('PUBLIC_PATH', ROOT_PATH . '/public');

// URL Paths
define('BASE_URL', APP_URL);
define('ASSETS_URL', BASE_URL . '/assets');
define('CSS_URL', ASSETS_URL . '/css');
define('JS_URL', ASSETS_URL . '/js');
define('IMG_URL', ASSETS_URL . '/images');

// Session Configuration
define('SESSION_TIMEOUT', 3600); // 1 hour in seconds

// Pagination
define('RECORDS_PER_PAGE', 10);

// Date Format
define('DATE_FORMAT', 'Y-m-d');
define('DATETIME_FORMAT', 'Y-m-d H:i:s');
define('DISPLAY_DATE_FORMAT', 'd M Y');
define('DISPLAY_DATETIME_FORMAT', 'd M Y h:i A');

// Renewal Alert Days
define('ALERT_DAYS_URGENT', 7);    // Red alert
define('ALERT_DAYS_WARNING', 15);  // Yellow alert
define('ALERT_DAYS_INFO', 30);     // Green/Info alert

// Currency
if (!defined('CURRENCY_SYMBOL')) {
    define('CURRENCY_SYMBOL', '$');
}
define('CURRENCY_CODE', 'USD');

// Email Configuration
define('SMTP_HOST', 'server.ayiarak.in');
define('SMTP_PORT', 587);
define('SMTP_USERNAME', 'noreply@algorythms.in');
define('SMTP_PASSWORD', 'Makebillions@26');
define('SMTP_FROM_EMAIL', 'noreply@algorythms.in');
define('SMTP_FROM_NAME', 'ALGORYTHMS');

// Email Recipients for Notifications
define('NOTIFICATION_EMAILS', [
    'algorythms3141@gmail.com',
    'joshuajeberson@gmail.com',
    'sadiqsariq55@gmail.com'
]);

// Security
define('PASSWORD_MIN_LENGTH', 6);
define('HASH_ALGORITHM', PASSWORD_DEFAULT);

// Include database configuration
require_once CONFIG_PATH . '/database.php';
require_once CONFIG_PATH . '/Mailer.php';

/**
 * Autoloader for classes
 */
spl_autoload_register(function ($class) {
    // Convert namespace to file path
    $file = str_replace('\\', '/', $class) . '.php';
    
    // Check in models directory
    if (file_exists(MODELS_PATH . '/' . $file)) {
        require_once MODELS_PATH . '/' . $file;
        return;
    }
    
    // Check in controllers directory
    if (file_exists(CONTROLLERS_PATH . '/' . $file)) {
        require_once CONTROLLERS_PATH . '/' . $file;
        return;
    }
});

/**
 * Helper Functions
 */

/**
 * Redirect to a URL
 */
function redirect($url) {
    header("Location: " . BASE_URL . $url);
    exit;
}

/**
 * Check if user is logged in
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Get current user ID
 */
function getCurrentUserId() {
    return $_SESSION['user_id'] ?? null;
}

/**
 * Get current user data
 */
function getCurrentUser() {
    return $_SESSION['user'] ?? null;
}

/**
 * Check if user is admin
 */
function isAdmin() {
    return isset($_SESSION['user']['role']) && $_SESSION['user']['role'] === 'admin';
}

/**
 * Require login
 */
function requireLogin() {
    if (!isLoggedIn()) {
        redirect('/index.php?page=login');
    }
}

/**
 * Require admin role
 */
function requireAdmin() {
    requireLogin();
    if (!isAdmin()) {
        redirect('/index.php?page=dashboard');
    }
}

/**
 * Sanitize input
 */
function sanitize($data) {
    if (is_array($data)) {
        return array_map('sanitize', $data);
    }
    return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES, 'UTF-8');
}

/**
 * Format date for display
 */
function formatDate($date, $format = DISPLAY_DATE_FORMAT) {
    if (empty($date) || $date === '0000-00-00') {
        return 'N/A';
    }
    return date($format, strtotime($date));
}

/**
 * Format currency
 */
function formatCurrency($amount) {
    return CURRENCY_SYMBOL . number_format($amount, 2);
}

/**
 * Calculate days left until expiry
 */
function daysLeft($expiryDate) {
    $now = new DateTime();
    $expiry = new DateTime($expiryDate);
    $diff = $now->diff($expiry);
    
    if ($expiry < $now) {
        return -$diff->days; // Negative for expired
    }
    return $diff->days;
}

/**
 * Get alert class based on days left
 */
function getAlertClass($daysLeft) {
    if ($daysLeft < 0) {
        return 'danger'; // Expired
    } elseif ($daysLeft <= ALERT_DAYS_URGENT) {
        return 'danger'; // Urgent
    } elseif ($daysLeft <= ALERT_DAYS_WARNING) {
        return 'warning'; // Warning
    } elseif ($daysLeft <= ALERT_DAYS_INFO) {
        return 'info'; // Info
    }
    return 'success'; // Safe
}

/**
 * Get status badge class
 */
function getStatusBadge($status) {
    $badges = [
        'active' => 'success',
        'inactive' => 'secondary',
        'expired' => 'danger',
        'pending' => 'warning',
        'suspended' => 'danger',
        'paid' => 'success',
        'failed' => 'danger',
        'refunded' => 'info'
    ];
    return $badges[$status] ?? 'secondary';
}

/**
 * Flash message functions
 */
function setFlashMessage($type, $message) {
    $_SESSION['flash_message'] = [
        'type' => $type,
        'message' => $message
    ];
}

function getFlashMessage() {
    if (isset($_SESSION['flash_message'])) {
        $flash = $_SESSION['flash_message'];
        unset($_SESSION['flash_message']);
        return $flash;
    }
    return null;
}

/**
 * Generate CSRF Token
 */
function generateCSRFToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verify CSRF Token
 */
function verifyCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Get pagination data
 */
function getPagination($totalRecords, $currentPage = 1, $recordsPerPage = RECORDS_PER_PAGE) {
    $totalPages = ceil($totalRecords / $recordsPerPage);
    $currentPage = max(1, min($currentPage, $totalPages));
    $offset = ($currentPage - 1) * $recordsPerPage;
    
    return [
        'total_records' => $totalRecords,
        'total_pages' => $totalPages,
        'current_page' => $currentPage,
        'records_per_page' => $recordsPerPage,
        'offset' => $offset,
        'has_prev' => $currentPage > 1,
        'has_next' => $currentPage < $totalPages
    ];
}

/**
 * Convert timestamp to human-readable time ago format
 */
function timeAgo($datetime) {
    $timestamp = strtotime($datetime);
    $difference = time() - $timestamp;
    
    if ($difference < 60) {
        return 'Just now';
    } elseif ($difference < 3600) {
        $minutes = floor($difference / 60);
        return $minutes . ' minute' . ($minutes > 1 ? 's' : '') . ' ago';
    } elseif ($difference < 86400) {
        $hours = floor($difference / 3600);
        return $hours . ' hour' . ($hours > 1 ? 's' : '') . ' ago';
    } elseif ($difference < 604800) {
        $days = floor($difference / 86400);
        return $days . ' day' . ($days > 1 ? 's' : '') . ' ago';
    } elseif ($difference < 2592000) {
        $weeks = floor($difference / 604800);
        return $weeks . ' week' . ($weeks > 1 ? 's' : '') . ' ago';
    } elseif ($difference < 31536000) {
        $months = floor($difference / 2592000);
        return $months . ' month' . ($months > 1 ? 's' : '') . ' ago';
    } else {
        $years = floor($difference / 31536000);
        return $years . ' year' . ($years > 1 ? 's' : '') . ' ago';
    }
}

/**
 * Log activity
 */
function logActivity($action, $module, $recordId = null, $description = '') {
    try {
        $db = getDBConnection();
        $stmt = $db->prepare("
            INSERT INTO activity_logs (user_id, action, module, record_id, description, ip_address, user_agent)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            getCurrentUserId(),
            $action,
            $module,
            $recordId,
            $description,
            $_SERVER['REMOTE_ADDR'] ?? null,
            $_SERVER['HTTP_USER_AGENT'] ?? null
        ]);
    } catch (Exception $e) {
        error_log("Activity Log Error: " . $e->getMessage());
    }
}

// Made with Bob
