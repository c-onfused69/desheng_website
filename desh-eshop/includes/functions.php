<?php
/**
 * Common Functions
 * Desh Engineering Ecommerce
 */

/**
 * Sanitize input data
 */
function sanitize($data) {
    return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES, 'UTF-8');
}

/**
 * Validate email
 */
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

/**
 * Hash password
 */
function hashPassword($password) {
    return password_hash($password, PASSWORD_DEFAULT);
}

/**
 * Verify password
 */
function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}

/**
 * Generate random token
 */
function generateToken($length = 32) {
    return bin2hex(random_bytes($length));
}

/**
 * Generate order number
 */
function generateOrderNumber() {
    return 'DE' . date('Ymd') . strtoupper(substr(uniqid(), -6));
}

/**
 * Format currency
 */
function formatCurrency($amount, $currency = 'BDT') {
    $symbols = [
        'USD' => '$',
        'EUR' => '€',
        'GBP' => '£',
        'INR' => '₹',
        'BDT' => '৳'
    ];
    
    $symbol = isset($symbols[$currency]) ? $symbols[$currency] : $currency . ' ';
    return $symbol . number_format($amount, 2);
}

/**
 * Generate URL slug from text
 */
function generateSlug($text) {
    // Convert to lowercase
    $slug = strtolower($text);
    
    // Remove special characters and replace with hyphens
    $slug = preg_replace('/[^a-z0-9\s-]/', '', $slug);
    
    // Replace spaces and multiple hyphens with single hyphen
    $slug = preg_replace('/[\s-]+/', '-', $slug);
    
    // Trim hyphens from beginning and end
    $slug = trim($slug, '-');
    
    return $slug;
}

/**
 * Time ago function
 */
function timeAgo($datetime) {
    $time = time() - strtotime($datetime);
    
    if ($time < 60) return 'just now';
    if ($time < 3600) return floor($time/60) . ' minutes ago';
    if ($time < 86400) return floor($time/3600) . ' hours ago';
    if ($time < 2592000) return floor($time/86400) . ' days ago';
    if ($time < 31536000) return floor($time/2592000) . ' months ago';
    
    return floor($time/31536000) . ' years ago';
}

/**
 * Redirect function
 */
function redirect($url) {
    header("Location: " . $url);
    exit();
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
 * Get current user
 */
function getCurrentUser() {
    if (!isLoggedIn()) return null;
    
    try {
        $db = getDB();
        $stmt = $db->prepare("SELECT * FROM users WHERE id = ? AND is_active = 1");
        $stmt->execute([$_SESSION['user_id']]);
        return $stmt->fetch();
    } catch (Exception $e) {
        error_log("Error getting current user: " . $e->getMessage());
        return null;
    }
}

/**
 * Get current admin
 */
function getCurrentAdmin() {
    if (!isAdminLoggedIn()) return null;
    
    try {
        $db = getDB();
        $stmt = $db->prepare("SELECT * FROM admin_users WHERE id = ? AND is_active = 1");
        $stmt->execute([$_SESSION['admin_id']]);
        return $stmt->fetch();
    } catch (Exception $e) {
        error_log("Error getting current admin: " . $e->getMessage());
        return null;
    }
}

/**
 * Upload file
 */
function uploadFile($file, $directory = 'general') {
    if (!isset($file['tmp_name']) || empty($file['tmp_name'])) {
        return ['success' => false, 'message' => 'No file uploaded'];
    }
    
    $upload_dir = UPLOAD_PATH . $directory . '/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }
    
    $file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'zip', 'rar', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx'];
    
    if (!in_array($file_extension, $allowed_extensions)) {
        return ['success' => false, 'message' => 'File type not allowed'];
    }
    
    if ($file['size'] > MAX_FILE_SIZE) {
        return ['success' => false, 'message' => 'File size too large'];
    }
    
    $filename = uniqid() . '_' . time() . '.' . $file_extension;
    $filepath = $upload_dir . $filename;
    
    if (move_uploaded_file($file['tmp_name'], $filepath)) {
        return [
            'success' => true,
            'filename' => $filename,
            'filepath' => $filepath,
            'url' => UPLOAD_URL . $directory . '/' . $filename
        ];
    }
    
    return ['success' => false, 'message' => 'Failed to upload file'];
}

/**
 * Send email
 */
function sendEmail($to, $subject, $body, $isHTML = true) {
    global $email_settings;
    
    // For now, we'll use PHP's mail function
    // In production, you should use PHPMailer or similar
    $headers = "From: {$email_settings['from_name']} <{$email_settings['from_email']}>\r\n";
    $headers .= "Reply-To: {$email_settings['from_email']}\r\n";
    
    if ($isHTML) {
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
    }
    
    return mail($to, $subject, $body, $headers);
}

/**
 * Get cart count for user
 */
function getCartCount($user_id = null) {
    if (!$user_id && !isLoggedIn()) return 0;
    
    $user_id = $user_id ?: $_SESSION['user_id'];
    
    try {
        $db = getDB();
        $stmt = $db->prepare("SELECT SUM(quantity) as count FROM cart WHERE user_id = ?");
        $stmt->execute([$user_id]);
        $result = $stmt->fetch();
        return $result['count'] ?: 0;
    } catch (Exception $e) {
        error_log("Error getting cart count: " . $e->getMessage());
        return 0;
    }
}

/**
 * Add to cart
 */
function addToCart($user_id, $product_id, $quantity = 1) {
    try {
        $db = getDB();
        
        // Check if product exists and is active
        $stmt = $db->prepare("SELECT id FROM products WHERE id = ? AND is_active = 1");
        $stmt->execute([$product_id]);
        if (!$stmt->fetch()) {
            return ['success' => false, 'message' => 'Product not found'];
        }
        
        // Check if item already in cart
        $stmt = $db->prepare("SELECT id, quantity FROM cart WHERE user_id = ? AND product_id = ?");
        $stmt->execute([$user_id, $product_id]);
        $existing = $stmt->fetch();
        
        if ($existing) {
            // Update quantity
            $new_quantity = $existing['quantity'] + $quantity;
            $stmt = $db->prepare("UPDATE cart SET quantity = ?, updated_at = NOW() WHERE id = ?");
            $stmt->execute([$new_quantity, $existing['id']]);
        } else {
            // Insert new item
            $stmt = $db->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?)");
            $stmt->execute([$user_id, $product_id, $quantity]);
        }
        
        return ['success' => true, 'message' => 'Item added to cart'];
    } catch (Exception $e) {
        error_log("Error adding to cart: " . $e->getMessage());
        return ['success' => false, 'message' => 'Failed to add item to cart'];
    }
}

/**
 * Get setting value
 */
function getSetting($key, $default = '') {
    global $site_settings;
    return isset($site_settings[$key]) ? $site_settings[$key] : $default;
}

/**
 * Create slug from string
 */
function createSlug($string) {
    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $string)));
    return trim($slug, '-');
}

/**
 * Truncate text
 */
function truncateText($text, $length = 100, $suffix = '...') {
    if (strlen($text) <= $length) return $text;
    return substr($text, 0, $length) . $suffix;
}

/**
 * Generate breadcrumb
 */
function generateBreadcrumb($items) {
    $breadcrumb = '<nav aria-label="breadcrumb"><ol class="breadcrumb">';
    
    foreach ($items as $index => $item) {
        if ($index === count($items) - 1) {
            $breadcrumb .= '<li class="breadcrumb-item active" aria-current="page">' . $item['title'] . '</li>';
        } else {
            $breadcrumb .= '<li class="breadcrumb-item"><a href="' . $item['url'] . '">' . $item['title'] . '</a></li>';
        }
    }
    
    $breadcrumb .= '</ol></nav>';
    return $breadcrumb;
}

/**
 * JSON response
 */
function jsonResponse($data, $status_code = 200) {
    http_response_code($status_code);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit();
}
?>
