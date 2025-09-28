<?php
require_once 'config/config.php';

// Check if user is logged in
if (!isLoggedIn()) {
    redirect(SITE_URL . '/login?redirect=' . urlencode($_SERVER['REQUEST_URI']));
}

$page_title = 'My Orders';
$page_description = 'View and manage your order history.';
$body_class = 'orders-page';

$current_user = getCurrentUser();

// Pagination
$page = intval($_GET['page'] ?? 1);
$per_page = ORDERS_PER_PAGE;
$offset = ($page - 1) * $per_page;

// Get orders
try {
    $db = getDB();
    
    // Get total count
    $stmt = $db->prepare("SELECT COUNT(*) as total FROM orders WHERE user_id = ?");
    $stmt->execute([$current_user['id']]);
    $total_orders = $stmt->fetch()['total'];
    $total_pages = ceil($total_orders / $per_page);
    
    // Get orders with item count
    $stmt = $db->prepare("
        SELECT o.*, COUNT(oi.id) as item_count
        FROM orders o
        LEFT JOIN order_items oi ON o.id = oi.order_id
        WHERE o.user_id = ?
        GROUP BY o.id
        ORDER BY o.created_at DESC
        LIMIT {$per_page} OFFSET {$offset}
    ");
    $stmt->execute([$current_user['id']]);
    $orders = $stmt->fetchAll();
    
} catch (Exception $e) {
    error_log("Orders page error: " . $e->getMessage());
    $orders = [];
    $total_orders = 0;
    $total_pages = 0;
}

include 'includes/header.php';
?>

<div class="container py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo SITE_URL; ?>">Home</a></li>
            <li class="breadcrumb-item"><a href="<?php echo SITE_URL; ?>/profile">Profile</a></li>
            <li class="breadcrumb-item active">Orders</li>
        </ol>
    </nav>
    
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="display-6 fw-bold mb-0">My Orders</h1>
            <p class="text-muted mb-0"><?php echo number_format($total_orders); ?> orders found</p>
        </div>
        <a href="<?php echo SITE_URL; ?>/downloads" class="btn btn-outline-primary">
            <i class="bi bi-download me-2"></i>
            My Downloads
        </a>
    </div>
    
    <?php if (!empty($orders)): ?>
        <!-- Orders List -->
        <div class="row g-4">
            <?php foreach ($orders as $order): ?>
                <div class="col-12">
                    <div class="card order-card">
                        <div class="card-header">
                            <div class="row align-items-center">
                                <div class="col-md-3">
                                    <div class="order-number">
                                        <strong>Order #<?php echo htmlspecialchars($order['order_number']); ?></strong>
                                    </div>
                                    <small class="text-muted"><?php echo date('M j, Y g:i A', strtotime($order['created_at'])); ?></small>
                                </div>
                                <div class="col-md-2">
                                    <div class="order-total">
                                        <div class="fw-bold"><?php echo formatCurrency($order['total_amount']); ?></div>
                                        <small class="text-muted"><?php echo $order['item_count']; ?> item(s)</small>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <span class="badge bg-<?php echo getOrderStatusColor($order['status']); ?> fs-6">
                                        <?php echo ucfirst($order['status']); ?>
                                    </span>
                                </div>
                                <div class="col-md-2">
                                    <span class="badge bg-<?php echo getPaymentStatusColor($order['payment_status']); ?> fs-6">
                                        <?php echo ucfirst(str_replace('_', ' ', $order['payment_status'])); ?>
                                    </span>
                                </div>
                                <div class="col-md-3 text-end">
                                    <div class="btn-group">
                                        <a href="<?php echo SITE_URL; ?>/order/<?php echo $order['order_number']; ?>" 
                                           class="btn btn-outline-primary btn-sm">
                                            <i class="bi bi-eye me-1"></i>
                                            View Details
                                        </a>
                                        <?php if ($order['payment_status'] === 'paid'): ?>
                                            <a href="<?php echo SITE_URL; ?>/downloads?order=<?php echo $order['order_number']; ?>" 
                                               class="btn btn-primary btn-sm">
                                                <i class="bi bi-download me-1"></i>
                                                Download
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <?php if ($order['payment_status'] === 'pending'): ?>
                            <div class="card-body">
                                <div class="alert alert-warning mb-0">
                                    <i class="bi bi-exclamation-triangle me-2"></i>
                                    <strong>Payment Pending:</strong> 
                                    Complete your payment to access your digital products.
                                    <a href="<?php echo SITE_URL; ?>/process-payment?order=<?php echo $order['order_number']; ?>" 
                                       class="btn btn-warning btn-sm ms-2">
                                        Complete Payment
                                    </a>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        
        <!-- Pagination -->
        <?php if ($total_pages > 1): ?>
            <nav aria-label="Orders pagination" class="mt-5">
                <ul class="pagination justify-content-center">
                    <?php if ($page > 1): ?>
                        <li class="page-item">
                            <a class="page-link" href="<?php echo SITE_URL; ?>/orders?page=<?php echo $page - 1; ?>">
                                <i class="bi bi-chevron-left"></i>
                            </a>
                        </li>
                    <?php endif; ?>
                    
                    <?php
                    $start_page = max(1, $page - 2);
                    $end_page = min($total_pages, $page + 2);
                    
                    if ($start_page > 1): ?>
                        <li class="page-item">
                            <a class="page-link" href="<?php echo SITE_URL; ?>/orders?page=1">1</a>
                        </li>
                        <?php if ($start_page > 2): ?>
                            <li class="page-item disabled">
                                <span class="page-link">...</span>
                            </li>
                        <?php endif; ?>
                    <?php endif; ?>
                    
                    <?php for ($i = $start_page; $i <= $end_page; $i++): ?>
                        <li class="page-item <?php echo $i === $page ? 'active' : ''; ?>">
                            <a class="page-link" href="<?php echo SITE_URL; ?>/orders?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                        </li>
                    <?php endfor; ?>
                    
                    <?php if ($end_page < $total_pages): ?>
                        <?php if ($end_page < $total_pages - 1): ?>
                            <li class="page-item disabled">
                                <span class="page-link">...</span>
                            </li>
                        <?php endif; ?>
                        <li class="page-item">
                            <a class="page-link" href="<?php echo SITE_URL; ?>/orders?page=<?php echo $total_pages; ?>"><?php echo $total_pages; ?></a>
                        </li>
                    <?php endif; ?>
                    
                    <?php if ($page < $total_pages): ?>
                        <li class="page-item">
                            <a class="page-link" href="<?php echo SITE_URL; ?>/orders?page=<?php echo $page + 1; ?>">
                                <i class="bi bi-chevron-right"></i>
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </nav>
        <?php endif; ?>
        
    <?php else: ?>
        <!-- No Orders -->
        <div class="text-center py-5">
            <i class="bi bi-bag display-1 text-muted mb-4"></i>
            <h3 class="text-muted mb-3">No orders yet</h3>
            <p class="text-muted mb-4">
                You haven't placed any orders yet. Start shopping to see your orders here.
            </p>
            <div class="d-flex gap-2 justify-content-center">
                <a href="<?php echo SITE_URL; ?>/products" class="btn btn-primary btn-lg">
                    <i class="bi bi-grid me-2"></i>
                    Browse Products
                </a>
                <a href="<?php echo SITE_URL; ?>" class="btn btn-outline-primary btn-lg">
                    <i class="bi bi-house me-2"></i>
                    Go Home
                </a>
            </div>
        </div>
    <?php endif; ?>
</div>

<style>
.order-card {
    transition: all 0.3s ease;
}

.order-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 16px var(--shadow-lg);
}

.order-number {
    font-size: 1.1rem;
}

.order-total {
    text-align: center;
}

@media (max-width: 767.98px) {
    .card-header .row > div {
        margin-bottom: 0.5rem;
    }
    
    .order-total {
        text-align: left;
    }
    
    .btn-group {
        width: 100%;
    }
    
    .btn-group .btn {
        flex: 1;
    }
}
</style>

<?php
// Helper functions for status colors
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
