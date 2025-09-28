<?php
require_once '../../config/config.php';

header('Content-Type: application/json');

// Check if user is logged in
if (!isLoggedIn()) {
    jsonResponse(['count' => 0]);
}

try {
    $db = getDB();
    $stmt = $db->prepare("
        SELECT SUM(quantity) as count 
        FROM cart c
        JOIN products p ON c.product_id = p.id
        WHERE c.user_id = ? AND p.is_active = 1
    ");
    $stmt->execute([$_SESSION['user_id']]);
    $result = $stmt->fetch();
    
    jsonResponse(['count' => intval($result['count'] ?? 0)]);
    
} catch (Exception $e) {
    error_log("Cart count error: " . $e->getMessage());
    jsonResponse(['count' => 0]);
}
?>
