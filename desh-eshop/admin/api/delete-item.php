<?php
require_once '../../config/config.php';

// Check if admin is logged in
if (!isAdminLoggedIn()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid JSON input']);
    exit;
}

$item_id = intval($input['item_id'] ?? 0);
$item_type = sanitize($input['item_type'] ?? '');

if ($item_id <= 0 || empty($item_type)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid parameters']);
    exit;
}

try {
    $db = getDB();
    
    switch ($item_type) {
        case 'product':
            // Check if product has orders
            $stmt = $db->prepare("SELECT COUNT(*) FROM order_items WHERE product_id = ?");
            $stmt->execute([$item_id]);
            if ($stmt->fetchColumn() > 0) {
                echo json_encode(['success' => false, 'message' => 'Cannot delete product with existing orders']);
                exit;
            }
            
            // Delete product images first
            $stmt = $db->prepare("DELETE FROM product_images WHERE product_id = ?");
            $stmt->execute([$item_id]);
            
            // Delete product
            $stmt = $db->prepare("DELETE FROM products WHERE id = ?");
            break;
            
        case 'category':
            // Check if category has products
            $stmt = $db->prepare("SELECT COUNT(*) FROM products WHERE category_id = ?");
            $stmt->execute([$item_id]);
            if ($stmt->fetchColumn() > 0) {
                echo json_encode(['success' => false, 'message' => 'Cannot delete category with existing products']);
                exit;
            }
            
            // Delete category
            $stmt = $db->prepare("DELETE FROM categories WHERE id = ?");
            break;
            
        default:
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Invalid item type']);
            exit;
    }
    
    $stmt->execute([$item_id]);
    
    if ($stmt->rowCount() > 0) {
        echo json_encode(['success' => true, 'message' => ucfirst($item_type) . ' deleted successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => ucfirst($item_type) . ' not found']);
    }
    
} catch (Exception $e) {
    error_log("Delete item error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Internal server error']);
}
?>
