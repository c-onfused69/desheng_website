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
$is_active = intval($input['is_active'] ?? 0);

if ($item_id <= 0 || empty($item_type)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid parameters']);
    exit;
}

try {
    $db = getDB();
    
    switch ($item_type) {
        case 'product':
            $stmt = $db->prepare("UPDATE products SET is_active = ? WHERE id = ?");
            break;
            
        case 'product-featured':
            $stmt = $db->prepare("UPDATE products SET is_featured = ? WHERE id = ?");
            break;
            
        case 'category':
            $stmt = $db->prepare("UPDATE categories SET is_active = ? WHERE id = ?");
            break;
            
        default:
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Invalid item type']);
            exit;
    }
    
    $stmt->execute([$is_active, $item_id]);
    
    if ($stmt->rowCount() > 0) {
        echo json_encode(['success' => true, 'message' => 'Status updated successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Item not found or no changes made']);
    }
    
} catch (Exception $e) {
    error_log("Toggle status error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Internal server error']);
}
?>
