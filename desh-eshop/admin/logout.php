<?php
require_once '../config/config.php';

// Log admin logout
if (isAdminLoggedIn()) {
    $admin = getCurrentAdmin();
    error_log("Admin logout: " . $admin['email'] . " from " . ($_SERVER['REMOTE_ADDR'] ?? 'unknown'));
}

// Clear all admin session data
session_unset();
session_destroy();

// Clear admin remember me cookie if it exists
if (isset($_COOKIE['admin_remember_token'])) {
    setcookie('admin_remember_token', '', time() - 3600, '/admin/');
}

// Start a new session for the success message
session_start();
$_SESSION['admin_success_message'] = 'You have been logged out successfully.';

// Redirect to admin login
redirect(SITE_URL . '/admin/login.php');
?>
