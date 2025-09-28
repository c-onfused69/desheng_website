<?php
require_once 'config/config.php';

// Check if user is logged in
if (!isLoggedIn()) {
    redirect(SITE_URL . '/login?redirect=' . urlencode($_SERVER['REQUEST_URI']));
}

$page_title = 'My Downloads';
$page_description = 'Access and download your purchased digital products.';
$body_class = 'downloads-page';

$current_user = getCurrentUser();
$order_filter = sanitize($_GET['order'] ?? '');

// Get downloads
try {
    $db = getDB();
    
    $where_clause = "WHERE d.user_id = ?";
    $params = [$current_user['id']];
    
    if (!empty($order_filter)) {
        $where_clause .= " AND o.order_number = ?";
        $params[] = $order_filter;
    }
    
    $stmt = $db->prepare("
        SELECT d.*, p.title, p.slug, p.short_description, p.digital_file_path,
               o.order_number, o.created_at as order_date,
               (SELECT image_path FROM product_images WHERE product_id = p.id AND is_primary = 1 LIMIT 1) as primary_image
        FROM downloads d
        JOIN products p ON d.product_id = p.id
        JOIN orders o ON d.order_id = o.id
        {$where_clause}
        ORDER BY d.created_at DESC
    ");
    $stmt->execute($params);
    $downloads = $stmt->fetchAll();
    
} catch (Exception $e) {
    error_log("Downloads page error: " . $e->getMessage());
    $downloads = [];
}

// Handle download request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'download' && isset($_POST['download_token'])) {
        $download_token = sanitize($_POST['download_token']);
        
        try {
            $stmt = $db->prepare("
                SELECT d.*, p.title, p.digital_file_path
                FROM downloads d
                JOIN products p ON d.product_id = p.id
                WHERE d.download_token = ? AND d.user_id = ? 
                AND (d.expires_at IS NULL OR d.expires_at > NOW())
                AND d.download_count < d.max_downloads
            ");
            $stmt->execute([$download_token, $current_user['id']]);
            $download = $stmt->fetch();
            
            if ($download) {
                // Update download count
                $stmt = $db->prepare("
                    UPDATE downloads 
                    SET download_count = download_count + 1, last_downloaded_at = NOW()
                    WHERE download_token = ?
                ");
                $stmt->execute([$download_token]);
                
                // Log download
                $ip_address = $_SERVER['REMOTE_ADDR'] ?? '';
                $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
                
                $stmt = $db->prepare("
                    UPDATE downloads 
                    SET ip_address = ?, user_agent = ?
                    WHERE download_token = ?
                ");
                $stmt->execute([$ip_address, $user_agent, $download_token]);
                
                // For demo purposes, we'll simulate file download
                $_SESSION['success_message'] = 'Download started for: ' . $download['title'];
                redirect(SITE_URL . '/downloads');
            } else {
                $_SESSION['error_message'] = 'Download not available or expired.';
            }
        } catch (Exception $e) {
            error_log("Download error: " . $e->getMessage());
            $_SESSION['error_message'] = 'Download failed. Please try again.';
        }
        
        redirect(SITE_URL . '/downloads');
    }
}

include 'includes/header.php';
?>

<div class="container py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo SITE_URL; ?>">Home</a></li>
            <li class="breadcrumb-item"><a href="<?php echo SITE_URL; ?>/profile">Profile</a></li>
            <li class="breadcrumb-item active">Downloads</li>
        </ol>
    </nav>
    
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="display-6 fw-bold mb-0">My Downloads</h1>
            <p class="text-muted mb-0">Access your purchased digital products</p>
        </div>
        <div class="d-flex gap-2">
            <a href="<?php echo SITE_URL; ?>/orders" class="btn btn-outline-primary">
                <i class="bi bi-bag me-2"></i>
                My Orders
            </a>
            <?php if (!empty($order_filter)): ?>
                <a href="<?php echo SITE_URL; ?>/downloads" class="btn btn-outline-secondary">
                    <i class="bi bi-x-circle me-2"></i>
                    Clear Filter
                </a>
            <?php endif; ?>
        </div>
    </div>
    
    <?php if (!empty($downloads)): ?>
        <!-- Downloads Grid -->
        <div class="row g-4">
            <?php foreach ($downloads as $download): ?>
                <?php
                $is_expired = $download['expires_at'] && strtotime($download['expires_at']) < time();
                $downloads_remaining = $download['max_downloads'] - $download['download_count'];
                $can_download = !$is_expired && $downloads_remaining > 0;
                ?>
                <div class="col-lg-6 col-xl-4">
                    <div class="card download-card h-100">
                        <div class="position-relative">
                            <img src="<?php echo $download['primary_image'] ? SITE_URL . '/uploads/products/' . $download['primary_image'] : SITE_URL . '/assets/images/placeholder-product.jpg'; ?>" 
                                 class="card-img-top" 
                                 alt="<?php echo htmlspecialchars($download['title']); ?>"
                                 style="height: 200px; object-fit: cover;">
                            
                            <!-- Status Badge -->
                            <?php if ($is_expired): ?>
                                <span class="position-absolute top-0 end-0 m-2 badge bg-danger">Expired</span>
                            <?php elseif ($downloads_remaining <= 0): ?>
                                <span class="position-absolute top-0 end-0 m-2 badge bg-warning">No Downloads Left</span>
                            <?php else: ?>
                                <span class="position-absolute top-0 end-0 m-2 badge bg-success">Available</span>
                            <?php endif; ?>
                        </div>
                        
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title"><?php echo htmlspecialchars($download['title']); ?></h5>
                            <p class="card-text text-muted small flex-grow-1">
                                <?php echo truncateText($download['short_description'], 100); ?>
                            </p>
                            
                            <!-- Download Info -->
                            <div class="download-info mb-3">
                                <div class="row text-center">
                                    <div class="col-4">
                                        <div class="info-item">
                                            <div class="fw-bold text-primary"><?php echo $download['download_count']; ?></div>
                                            <small class="text-muted">Downloaded</small>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="info-item">
                                            <div class="fw-bold text-primary"><?php echo $downloads_remaining; ?></div>
                                            <small class="text-muted">Remaining</small>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="info-item">
                                            <div class="fw-bold text-primary">
                                                <?php 
                                                if ($download['expires_at']) {
                                                    $days_left = max(0, ceil((strtotime($download['expires_at']) - time()) / (24 * 60 * 60)));
                                                    echo $days_left;
                                                } else {
                                                    echo '∞';
                                                }
                                                ?>
                                            </div>
                                            <small class="text-muted">Days Left</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Order Info -->
                            <div class="order-info mb-3">
                                <small class="text-muted">
                                    <i class="bi bi-receipt me-1"></i>
                                    Order #<?php echo htmlspecialchars($download['order_number']); ?>
                                    <br>
                                    <i class="bi bi-calendar me-1"></i>
                                    Purchased <?php echo date('M j, Y', strtotime($download['order_date'])); ?>
                                </small>
                                <?php if ($download['last_downloaded_at']): ?>
                                    <br>
                                    <small class="text-muted">
                                        <i class="bi bi-download me-1"></i>
                                        Last downloaded <?php echo timeAgo($download['last_downloaded_at']); ?>
                                    </small>
                                <?php endif; ?>
                            </div>
                            
                            <!-- Action Buttons -->
                            <div class="mt-auto">
                                <?php if ($can_download): ?>
                                    <form method="POST" class="d-inline w-100">
                                        <input type="hidden" name="action" value="download">
                                        <input type="hidden" name="download_token" value="<?php echo $download['download_token']; ?>">
                                        <button type="submit" class="btn btn-primary w-100 mb-2">
                                            <i class="bi bi-download me-2"></i>
                                            Download Now
                                        </button>
                                    </form>
                                <?php else: ?>
                                    <button class="btn btn-secondary w-100 mb-2" disabled>
                                        <i class="bi bi-x-circle me-2"></i>
                                        <?php echo $is_expired ? 'Expired' : 'No Downloads Left'; ?>
                                    </button>
                                <?php endif; ?>
                                
                                <div class="d-flex gap-2">
                                    <a href="<?php echo SITE_URL; ?>/product/<?php echo $download['slug']; ?>" 
                                       class="btn btn-outline-primary btn-sm flex-fill">
                                        <i class="bi bi-eye me-1"></i>
                                        View Product
                                    </a>
                                    <a href="<?php echo SITE_URL; ?>/order/<?php echo $download['order_number']; ?>" 
                                       class="btn btn-outline-secondary btn-sm flex-fill">
                                        <i class="bi bi-receipt me-1"></i>
                                        View Order
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        
        <!-- Download Guidelines -->
        <div class="row mt-5">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="bi bi-info-circle me-2"></i>
                            Download Guidelines
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h6 class="fw-bold">Download Limits</h6>
                                <ul class="list-unstyled">
                                    <li><i class="bi bi-check-circle text-success me-2"></i>Each product has a download limit (usually 5 downloads)</li>
                                    <li><i class="bi bi-check-circle text-success me-2"></i>Downloads expire after 30 days from purchase</li>
                                    <li><i class="bi bi-check-circle text-success me-2"></i>You can re-download within the limit and timeframe</li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <h6 class="fw-bold">Need Help?</h6>
                                <ul class="list-unstyled">
                                    <li><i class="bi bi-question-circle text-info me-2"></i>Download not working? <a href="<?php echo SITE_URL; ?>/support">Contact Support</a></li>
                                    <li><i class="bi bi-question-circle text-info me-2"></i>Need more downloads? <a href="<?php echo SITE_URL; ?>/support">Request Extension</a></li>
                                    <li><i class="bi bi-question-circle text-info me-2"></i>File corrupted? <a href="<?php echo SITE_URL; ?>/support">Report Issue</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
    <?php else: ?>
        <!-- No Downloads -->
        <div class="text-center py-5">
            <i class="bi bi-download display-1 text-muted mb-4"></i>
            <h3 class="text-muted mb-3">No downloads available</h3>
            <p class="text-muted mb-4">
                <?php if (!empty($order_filter)): ?>
                    No downloads found for the specified order.
                <?php else: ?>
                    You don't have any downloadable products yet. Purchase digital products to access downloads.
                <?php endif; ?>
            </p>
            <div class="d-flex gap-2 justify-content-center">
                <a href="<?php echo SITE_URL; ?>/products" class="btn btn-primary btn-lg">
                    <i class="bi bi-grid me-2"></i>
                    Browse Products
                </a>
                <a href="<?php echo SITE_URL; ?>/orders" class="btn btn-outline-primary btn-lg">
                    <i class="bi bi-bag me-2"></i>
                    View Orders
                </a>
            </div>
        </div>
    <?php endif; ?>
</div>

<style>
.download-card {
    transition: all 0.3s ease;
}

.download-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 16px var(--shadow-lg);
}

.info-item {
    padding: 0.5rem;
    background-color: var(--bg-secondary);
    border-radius: 6px;
}

.download-info {
    border-top: 1px solid var(--border-color);
    border-bottom: 1px solid var(--border-color);
    padding: 1rem 0;
}

.order-info {
    background-color: var(--bg-secondary);
    padding: 0.75rem;
    border-radius: 6px;
}

@media (max-width: 767.98px) {
    .info-item {
        margin-bottom: 0.5rem;
    }
}
</style>

<script>
// Confirm download action
document.querySelectorAll('form[method="POST"]').forEach(form => {
    form.addEventListener('submit', function(e) {
        const button = this.querySelector('button[type="submit"]');
        if (button && button.textContent.includes('Download')) {
            button.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Preparing Download...';
            button.disabled = true;
        }
    });
});
</script>

<?php include 'includes/footer.php'; ?>
