<?php
/**
 * Main Configuration File
 * Desh Engineering Ecommerce
 */

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Define constants
define('SITE_URL', 'http://localhost/desh-eshop');
define('SITE_NAME', 'Desh Engineering');
define('SITE_EMAIL', 'info@deshengineering.com');

// File upload settings
define('UPLOAD_PATH', __DIR__ . '/../uploads/');
define('UPLOAD_URL', SITE_URL . '/uploads/');
define('MAX_FILE_SIZE', 50 * 1024 * 1024); // 50MB

// Security settings
define('ENCRYPTION_KEY', 'desh_engineering_2024_secure_key');
define('JWT_SECRET', 'jwt_secret_key_desh_engineering');

// Pagination settings
define('PRODUCTS_PER_PAGE', 12);
define('ORDERS_PER_PAGE', 10);

// Email settings (will be loaded from database)
$email_settings = [
    'smtp_host' => '',
    'smtp_port' => 587,
    'smtp_username' => '',
    'smtp_password' => '',
    'from_email' => 'noreply@deshengineering.com',
    'from_name' => 'Desh Engineering'
];

// Payment gateway settings (will be loaded from database)
$payment_settings = [
    'default_gateway' => 'stripe',
    'currency' => 'BDT',
    'tax_rate' => 0
];

// Include required files
require_once __DIR__ . '/database.php';
require_once __DIR__ . '/../includes/functions.php';

// Error reporting (disable in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set timezone
date_default_timezone_set('Asia/Dhaka');

// CORS headers for API requests
if (isset($_SERVER['HTTP_ORIGIN'])) {
    header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
    header('Access-Control-Allow-Credentials: true');
    header('Access-Control-Max-Age: 86400');
}

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD'])) {
        header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
    }
    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS'])) {
        header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
    }
    exit(0);
}

// Load settings from database
function loadSettings() {
    global $email_settings, $payment_settings;
    
    try {
        $db = getDB();
        $stmt = $db->query("SELECT setting_key, setting_value FROM settings");
        $settings = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
        
        // Update email settings
        if (isset($settings['smtp_host'])) $email_settings['smtp_host'] = $settings['smtp_host'];
        if (isset($settings['smtp_port'])) $email_settings['smtp_port'] = $settings['smtp_port'];
        if (isset($settings['smtp_username'])) $email_settings['smtp_username'] = $settings['smtp_username'];
        if (isset($settings['smtp_password'])) $email_settings['smtp_password'] = $settings['smtp_password'];
        if (isset($settings['from_email'])) $email_settings['from_email'] = $settings['from_email'];
        if (isset($settings['from_name'])) $email_settings['from_name'] = $settings['from_name'];
        
        // Update payment settings
        if (isset($settings['payment_gateway'])) $payment_settings['default_gateway'] = $settings['payment_gateway'];
        if (isset($settings['currency'])) $payment_settings['currency'] = $settings['currency'];
        if (isset($settings['tax_rate'])) $payment_settings['tax_rate'] = floatval($settings['tax_rate']);
        
        return $settings;
    } catch (Exception $e) {
        error_log("Error loading settings: " . $e->getMessage());
        return [];
    }
}

// Load settings on initialization
$site_settings = loadSettings();
?>
