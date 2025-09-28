<?php
require_once 'config/config.php';

// Check if user is logged in
if (!isLoggedIn()) {
    redirect(SITE_URL . '/login?redirect=' . urlencode($_SERVER['REQUEST_URI']));
}

// Get order number from URL
$order_number = $_GET['order'] ?? '';
if (empty($order_number)) {
    // Try to get from path info
    $path_info = $_SERVER['PATH_INFO'] ?? '';
    if (!empty($path_info)) {
        $order_number = trim($path_info, '/');
    }
}

if (empty($order_number)) {
    redirect(SITE_URL . '/orders');
}

$current_user = getCurrentUser();

try {
    $db = getDB();
    
    // Get order details
    $stmt = $db->prepare("
        SELECT * FROM orders 
        WHERE order_number = ? AND user_id = ?
    ");
    $stmt->execute([$order_number, $current_user['id']]);
    $order = $stmt->fetch();
    
    if (!$order) {
        redirect(SITE_URL . '/orders');
    }
    
    // Get order items
    $stmt = $db->prepare("
        SELECT oi.*, p.slug,
               (SELECT image_path FROM product_images WHERE product_id = p.id AND is_primary = 1 LIMIT 1) as primary_image
        FROM order_items oi
        JOIN products p ON oi.product_id = p.id
        WHERE oi.order_id = ?
        ORDER BY oi.id
    ");
    $stmt->execute([$order['id']]);
    $order_items = $stmt->fetchAll();
    
    // Get downloads if order is paid
    $downloads = [];
    if ($order['payment_status'] === 'paid') {
        $stmt = $db->prepare("
            SELECT d.*, p.title, p.slug
            FROM downloads d
            JOIN products p ON d.product_id = p.id
            WHERE d.order_id = ?
        ");
        $stmt->execute([$order['id']]);
        $downloads = $stmt->fetchAll();
    }
    
} catch (Exception $e) {
    error_log("Order detail error: " . $e->getMessage());
    redirect(SITE_URL . '/orders');
}

$page_title = 'Order #' . $order['order_number'];
$page_description = 'View order details and download your products.';
$body_class = 'order-detail-page';

include 'includes/header.php';
?>

<div class="container py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo SITE_URL; ?>">Home</a></li>
            <li class="breadcrumb-item"><a href="<?php echo SITE_URL; ?>/profile">Profile</a></li>
            <li class="breadcrumb-item"><a href="<?php echo SITE_URL; ?>/orders">Orders</a></li>
            <li class="breadcrumb-item active">Order #<?php echo htmlspecialchars($order['order_number']); ?></li>
        </ol>
    </nav>
    
    <!-- Order Header -->
    <div class="row mb-4">
        <div class="col-lg-8">
            <h1 class="display-6 fw-bold mb-2">Order #<?php echo htmlspecialchars($order['order_number']); ?></h1>
            <p class="text-muted mb-0">
                Placed on <?php echo date('F j, Y \a\t g:i A', strtotime($order['created_at'])); ?>
            </p>
        </div>
        <div class="col-lg-4 text-lg-end">
            <div class="order-status-badges">
                <span class="badge bg-<?php echo getOrderStatusColor($order['status']); ?> fs-6 me-2">
                    <?php echo ucfirst($order['status']); ?>
                </span>
                <span class="badge bg-<?php echo getPaymentStatusColor($order['payment_status']); ?> fs-6">
                    <?php echo ucfirst(str_replace('_', ' ', $order['payment_status'])); ?>
                </span>
            </div>
            
            <?php if ($order['payment_status'] === 'pending'): ?>
                <div class="mt-3">
                    <a href="<?php echo SITE_URL; ?>/process-payment?order=<?php echo $order['order_number']; ?>" 
                       class="btn btn-warning">
                        <i class="bi bi-credit-card me-2"></i>
                        Complete Payment
                    </a>
                </div>
            <?php elseif ($order['payment_status'] === 'paid'): ?>
                <div class="mt-3">
                    <a href="<?php echo SITE_URL; ?>/downloads?order=<?php echo $order['order_number']; ?>" 
                       class="btn btn-primary">
                        <i class="bi bi-download me-2"></i>
                        Download Products
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <div class="row">
        <!-- Order Items -->
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-bag me-2"></i>
                        Order Items (<?php echo count($order_items); ?>)
                    </h5>
                </div>
                <div class="card-body p-0">
                    <?php foreach ($order_items as $index => $item): ?>
                        <div class="order-item p-4 <?php echo $index < count($order_items) - 1 ? 'border-bottom' : ''; ?>">
                            <div class="row align-items-center">
                                <div class="col-md-2">
                                    <img src="<?php echo $item['primary_image'] ? SITE_URL . '/uploads/products/' . $item['primary_image'] : SITE_URL . '/assets/images/placeholder-product.jpg'; ?>" 
                                         alt="<?php echo htmlspecialchars($item['product_title']); ?>"
                                         class="order-item-image img-fluid rounded">
                                </div>
                                
                                <div class="col-md-6">
                                    <h6 class="fw-bold mb-1">
                                        <a href="<?php echo SITE_URL; ?>/product/<?php echo $item['slug']; ?>" 
                                           class="text-decoration-none text-dark">
                                            <?php echo htmlspecialchars($item['product_title']); ?>
                                        </a>
                                    </h6>
                                    <div class="product-meta">
                                        <small class="text-muted">
                                            Quantity: <?php echo $item['quantity']; ?> × <?php echo formatCurrency($item['product_price']); ?>
                                        </small>
                                    </div>
                                    
                                    <?php if ($order['payment_status'] === 'paid'): ?>
                                        <!-- Find corresponding download -->
                                        <?php 
                                        $item_download = null;
                                        foreach ($downloads as $download) {
                                            if ($download['product_id'] == $item['product_id']) {
                                                $item_download = $download;
                                                break;
                                            }
                                        }
                                        ?>
                                        
                                        <?php if ($item_download): ?>
                                            <div class="download-status mt-2">
                                                <?php
                                                $is_expired = $item_download['expires_at'] && strtotime($item_download['expires_at']) < time();
                                                $downloads_remaining = $item_download['max_downloads'] - $item_download['download_count'];
                                                $can_download = !$is_expired && $downloads_remaining > 0;
                                                ?>
                                                
                                                <?php if ($can_download): ?>
                                                    <span class="badge bg-success me-2">
                                                        <i class="bi bi-download me-1"></i>
                                                        Available for Download
                                                    </span>
                                                    <small class="text-muted">
                                                        <?php echo $downloads_remaining; ?> downloads remaining
                                                    </small>
                                                <?php elseif ($is_expired): ?>
                                                    <span class="badge bg-danger">
                                                        <i class="bi bi-x-circle me-1"></i>
                                                        Download Expired
                                                    </span>
                                                <?php else: ?>
                                                    <span class="badge bg-warning">
                                                        <i class="bi bi-exclamation-triangle me-1"></i>
                                                        No Downloads Remaining
                                                    </span>
                                                <?php endif; ?>
                                            </div>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="col-md-2 text-center">
                                    <div class="item-price">
                                        <div class="fw-bold"><?php echo formatCurrency($item['total_price']); ?></div>
                                    </div>
                                </div>
                                
                                <div class="col-md-2 text-end">
                                    <?php if ($order['payment_status'] === 'paid' && $item_download && $can_download): ?>
                                        <form method="POST" action="<?php echo SITE_URL; ?>/downloads" class="d-inline">
                                            <input type="hidden" name="action" value="download">
                                            <input type="hidden" name="download_token" value="<?php echo $item_download['download_token']; ?>">
                                            <button type="submit" class="btn btn-primary btn-sm">
                                                <i class="bi bi-download me-1"></i>
                                                Download
                                            </button>
                                        </form>
                                    <?php else: ?>
                                        <a href="<?php echo SITE_URL; ?>/product/<?php echo $item['slug']; ?>" 
                                           class="btn btn-outline-primary btn-sm">
                                            <i class="bi bi-eye me-1"></i>
                                            View
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <!-- Order Timeline -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-clock-history me-2"></i>
                        Order Timeline
                    </h5>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        <div class="timeline-item">
                            <div class="timeline-marker bg-primary"></div>
                            <div class="timeline-content">
                                <h6 class="fw-bold mb-1">Order Placed</h6>
                                <p class="text-muted mb-0">
                                    <?php echo date('F j, Y \a\t g:i A', strtotime($order['created_at'])); ?>
                                </p>
                            </div>
                        </div>
                        
                        <?php if ($order['payment_status'] === 'paid'): ?>
                            <div class="timeline-item">
                                <div class="timeline-marker bg-success"></div>
                                <div class="timeline-content">
                                    <h6 class="fw-bold mb-1">Payment Confirmed</h6>
                                    <p class="text-muted mb-0">
                                        <?php echo date('F j, Y \a\t g:i A', strtotime($order['updated_at'])); ?>
                                    </p>
                                    <?php if ($order['transaction_id']): ?>
                                        <small class="text-muted">
                                            Transaction ID: <?php echo htmlspecialchars($order['transaction_id']); ?>
                                        </small>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <div class="timeline-item">
                                <div class="timeline-marker bg-success"></div>
                                <div class="timeline-content">
                                    <h6 class="fw-bold mb-1">Products Available for Download</h6>
                                    <p class="text-muted mb-0">
                                        Digital products are now available in your downloads section.
                                    </p>
                                </div>
                            </div>
                        <?php elseif ($order['payment_status'] === 'pending'): ?>
                            <div class="timeline-item">
                                <div class="timeline-marker bg-warning"></div>
                                <div class="timeline-content">
                                    <h6 class="fw-bold mb-1">Awaiting Payment</h6>
                                    <p class="text-muted mb-0">
                                        Complete your payment to access your digital products.
                                    </p>
                                </div>
                            </div>
                        <?php elseif ($order['payment_status'] === 'failed'): ?>
                            <div class="timeline-item">
                                <div class="timeline-marker bg-danger"></div>
                                <div class="timeline-content">
                                    <h6 class="fw-bold mb-1">Payment Failed</h6>
                                    <p class="text-muted mb-0">
                                        Payment could not be processed. Please try again.
                                    </p>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Order Summary -->
        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-receipt me-2"></i>
                        Order Summary
                    </h5>
                </div>
                <div class="card-body">
                    <div class="summary-item">
                        <span>Subtotal:</span>
                        <span><?php echo formatCurrency($order['subtotal']); ?></span>
                    </div>
                    
                    <?php if ($order['discount_amount'] > 0): ?>
                        <div class="summary-item text-success">
                            <span>Discount:</span>
                            <span>-<?php echo formatCurrency($order['discount_amount']); ?></span>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($order['tax_amount'] > 0): ?>
                        <div class="summary-item">
                            <span>Tax:</span>
                            <span><?php echo formatCurrency($order['tax_amount']); ?></span>
                        </div>
                    <?php endif; ?>
                    
                    <div class="summary-total">
                        <span>Total:</span>
                        <span><?php echo formatCurrency($order['total_amount']); ?></span>
                    </div>
                    
                    <div class="payment-method mt-3">
                        <small class="text-muted">
                            <strong>Payment Method:</strong> <?php echo ucfirst($order['payment_method']); ?>
                        </small>
                    </div>
                </div>
            </div>
            
            <!-- Billing Information -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-person me-2"></i>
                        Billing Information
                    </h5>
                </div>
                <div class="card-body">
                    <address class="mb-0">
                        <strong><?php echo htmlspecialchars($order['billing_name']); ?></strong><br>
                        <?php if ($order['billing_address']): ?>
                            <?php echo nl2br(htmlspecialchars($order['billing_address'])); ?><br>
                        <?php endif; ?>
                        <?php if ($order['billing_city'] || $order['billing_state'] || $order['billing_postal_code']): ?>
                            <?php echo htmlspecialchars($order['billing_city']); ?>
                            <?php echo $order['billing_state'] ? ', ' . htmlspecialchars($order['billing_state']) : ''; ?>
                            <?php echo $order['billing_postal_code'] ? ' ' . htmlspecialchars($order['billing_postal_code']) : ''; ?><br>
                        <?php endif; ?>
                        <?php if ($order['billing_country']): ?>
                            <?php echo htmlspecialchars($order['billing_country']); ?><br>
                        <?php endif; ?>
                        <?php if ($order['billing_phone']): ?>
                            <abbr title="Phone">P:</abbr> <?php echo htmlspecialchars($order['billing_phone']); ?><br>
                        <?php endif; ?>
                        <abbr title="Email">E:</abbr> <?php echo htmlspecialchars($order['billing_email']); ?>
                    </address>
                </div>
            </div>
            
            <!-- Actions -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-gear me-2"></i>
                        Actions
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <?php if ($order['payment_status'] === 'paid'): ?>
                            <a href="<?php echo SITE_URL; ?>/downloads?order=<?php echo $order['order_number']; ?>" 
                               class="btn btn-primary">
                                <i class="bi bi-download me-2"></i>
                                View Downloads
                            </a>
                        <?php endif; ?>
                        
                        <button class="btn btn-outline-primary" onclick="window.print()">
                            <i class="bi bi-printer me-2"></i>
                            Print Order
                        </button>
                        
                        <a href="<?php echo SITE_URL; ?>/support?order=<?php echo $order['order_number']; ?>" 
                           class="btn btn-outline-secondary">
                            <i class="bi bi-headset me-2"></i>
                            Get Support
                        </a>
                        
                        <a href="<?php echo SITE_URL; ?>/orders" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left me-2"></i>
                            Back to Orders
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.order-item-image {
    width: 80px;
    height: 80px;
    object-fit: cover;
}

.summary-item {
    display: flex;
    justify-content: space-between;
    margin-bottom: 8px;
}

.summary-total {
    display: flex;
    justify-content: space-between;
    font-size: 1.25rem;
    font-weight: 700;
    padding-top: 1rem;
    border-top: 2px solid var(--border-color);
    margin-top: 1rem;
}

.timeline {
    position: relative;
    padding-left: 2rem;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 15px;
    top: 0;
    bottom: 0;
    width: 2px;
    background-color: var(--border-color);
}

.timeline-item {
    position: relative;
    margin-bottom: 2rem;
}

.timeline-marker {
    position: absolute;
    left: -2rem;
    top: 0;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    border: 2px solid white;
}

.timeline-content {
    padding-left: 1rem;
}

@media (max-width: 767.98px) {
    .order-status-badges {
        margin-top: 1rem;
    }
    
    .timeline {
        padding-left: 1.5rem;
    }
    
    .timeline-marker {
        left: -1.5rem;
    }
}

@media print {
    .btn, .card-header, .timeline::before, .timeline-marker {
        display: none !important;
    }
    
    .card {
        border: none !important;
        box-shadow: none !important;
    }
    
    .container {
        max-width: none !important;
    }
}
</style>

<?php
// Helper functions for status colors (reuse from orders.php)
function getOrderStatusColor($status) {
    switch ($status) {
        case 'completed':
            return 'success';
        case 'processing':
            return 'primary';
        case 'pending':
            return 'warning';
        case 'cancelled':
        case 'refunded':
            return 'danger';
        default:
            return 'secondary';
    }
}

function getPaymentStatusColor($status) {
    switch ($status) {
        case 'paid':
            return 'success';
        case 'pending':
            return 'warning';
        case 'failed':
        case 'refunded':
            return 'danger';
        default:
            return 'secondary';
    }
}

include 'includes/footer.php';
?>
