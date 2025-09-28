<?php
require_once '../../config/config.php';

header('Content-Type: application/json');

// Check if user is logged in
if (!isLoggedIn()) {
    jsonResponse(['success' => false, 'message' => 'Please login to view cart totals'], 401);
}

try {
    $db = getDB();
    
    // Get cart items with prices
    $stmt = $db->prepare("
        SELECT c.quantity, p.price, p.sale_price
        FROM cart c
        JOIN products p ON c.product_id = p.id
        WHERE c.user_id = ? AND p.is_active = 1
    ");
    $stmt->execute([$_SESSION['user_id']]);
    $cart_items = $stmt->fetchAll();
    
    // Calculate totals
    $subtotal = 0;
    foreach ($cart_items as $item) {
        $price = $item['sale_price'] && $item['sale_price'] < $item['price'] ? $item['sale_price'] : $item['price'];
        $subtotal += $price * $item['quantity'];
    }
    
    $tax_rate = floatval(getSetting('tax_rate', 0)) / 100;
    $tax_amount = $subtotal * $tax_rate;
    $total = $subtotal + $tax_amount;
    
    $currency = getSetting('currency', 'BDT');
    
    jsonResponse([
        'success' => true,
        'subtotal' => $subtotal,
        'tax' => $tax_amount,
        'total' => $total,
        'subtotal_formatted' => formatCurrency($subtotal, $currency),
        'tax_formatted' => formatCurrency($tax_amount, $currency),
        'total_formatted' => formatCurrency($total, $currency),
        'item_count' => count($cart_items)
    ]);
    
} catch (Exception $e) {
    error_log("Cart totals error: " . $e->getMessage());
    jsonResponse(['success' => false, 'message' => 'Failed to calculate cart totals'], 500);
}
?>
