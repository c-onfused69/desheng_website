<?php
require_once '../../config/config.php';

header('Content-Type: application/json');

// Check if user is logged in
if (!isLoggedIn()) {
    jsonResponse(['success' => false, 'message' => 'Please login to add items to cart'], 401);
}

// Check request method
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['success' => false, 'message' => 'Method not allowed'], 405);
}

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);

$product_id = intval($input['product_id'] ?? 0);
$quantity = intval($input['quantity'] ?? 1);
$user_id = $_SESSION['user_id'];

// Validation
if ($product_id <= 0) {
    jsonResponse(['success' => false, 'message' => 'Invalid product ID']);
}

if ($quantity <= 0 || $quantity > 10) {
    jsonResponse(['success' => false, 'message' => 'Quantity must be between 1 and 10']);
}

try {
    $db = getDB();
    
    // Check if product exists and is active
    $stmt = $db->prepare("SELECT id, title, price, sale_price FROM products WHERE id = ? AND is_active = 1");
    $stmt->execute([$product_id]);
    $product = $stmt->fetch();
    
    if (!$product) {
        jsonResponse(['success' => false, 'message' => 'Product not found or unavailable']);
    }
    
    // Check if user already owns this product (for digital products, typically one purchase)
    $stmt = $db->prepare("
        SELECT COUNT(*) as count 
        FROM order_items oi 
        JOIN orders o ON oi.order_id = o.id 
        WHERE o.user_id = ? AND oi.product_id = ? AND o.payment_status = 'paid'
    ");
    $stmt->execute([$user_id, $product_id]);
    $already_owned = $stmt->fetch()['count'] > 0;
    
    if ($already_owned) {
        jsonResponse(['success' => false, 'message' => 'You already own this product']);
    }
    
    // Check if item already in cart
    $stmt = $db->prepare("SELECT id, quantity FROM cart WHERE user_id = ? AND product_id = ?");
    $stmt->execute([$user_id, $product_id]);
    $existing_item = $stmt->fetch();
    
    if ($existing_item) {
        // Update quantity (for digital products, usually just 1)
        $new_quantity = min($existing_item['quantity'] + $quantity, 1); // Digital products typically quantity 1
        $stmt = $db->prepare("UPDATE cart SET quantity = ?, updated_at = NOW() WHERE id = ?");
        $stmt->execute([$new_quantity, $existing_item['id']]);
        
        jsonResponse([
            'success' => true, 
            'message' => 'Cart updated successfully',
            'action' => 'updated'
        ]);
    } else {
        // Add new item to cart
        $stmt = $db->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?)");
        $stmt->execute([$user_id, $product_id, min($quantity, 1)]); // Digital products typically quantity 1
        
        jsonResponse([
            'success' => true, 
            'message' => 'Item added to cart successfully',
            'action' => 'added'
        ]);
    }
    
} catch (Exception $e) {
    error_log("Add to cart error: " . $e->getMessage());
    jsonResponse(['success' => false, 'message' => 'An error occurred while adding item to cart'], 500);
}
?>
