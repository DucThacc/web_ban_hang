<?php
/**
 * Database Configuration and Connection
 */

// Database credentials from environment variables or defaults
define('DB_HOST', getenv('DB_HOST') ?: 'db');
define('DB_NAME', getenv('DB_NAME') ?: 'pc_shop');
define('DB_USER', getenv('DB_USER') ?: 'pc_shop_user');
define('DB_PASSWORD', getenv('DB_PASSWORD') ?: 'secure_password_123');
define('DB_CHARSET', 'utf8mb4');

// Application settings
define('SITE_NAME', 'PC Shop - Máy Tính & Linh Kiện');
define('SITE_URL', getenv('APP_URL') ?: 'http://localhost:8080');
define('ADMIN_EMAIL', 'admin@pcshop.com');

// Upload settings
define('UPLOAD_DIR', __DIR__ . '/../public/uploads/');
define('PRODUCT_UPLOAD_DIR', UPLOAD_DIR . 'products/');
define('BANNER_UPLOAD_DIR', UPLOAD_DIR . 'banners/');
define('MAX_UPLOAD_SIZE', 5242880); // 5MB
define('ALLOWED_IMAGE_TYPES', ['jpg', 'jpeg', 'png', 'gif', 'webp']);

// Session settings
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_secure', 0); // Set to 1 if using HTTPS
session_save_path('/tmp');

// Error reporting (disable in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

/**
 * Get database connection using PDO
 * @return PDO
 */
function getDBConnection() {
    static $pdo = null;
    
    if ($pdo === null) {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];
            
            $pdo = new PDO($dsn, DB_USER, DB_PASSWORD, $options);
        } catch (PDOException $e) {
            die("Database connection failed: " . $e->getMessage());
        }
    }
    
    return $pdo;
}

/**
 * Close database connection
 */
function closeDBConnection() {
    global $pdo;
    $pdo = null;
}
