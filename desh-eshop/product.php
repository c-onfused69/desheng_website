<?php
require_once 'config/config.php';

// Get product slug from URL
$product_slug = $_GET['slug'] ?? '';
if (empty($product_slug)) {
    // Try to get from path info
    $path_info = $_SERVER['PATH_INFO'] ?? '';
    if (!empty($path_info)) {
        $product_slug = trim($path_info, '/');
    }
}

if (empty($product_slug)) {
    redirect(SITE_URL . '/products');
}

try {
    $db = getDB();
    
    // Get product details
    $stmt = $db->prepare("
        SELECT p.*, c.name as category_name, c.slug as category_slug
        FROM products p 
        LEFT JOIN categories c ON p.category_id = c.id 
        WHERE p.slug = ? AND p.is_active = 1
    ");
    $stmt->execute([$product_slug]);
    $product = $stmt->fetch();
    
    if (!$product) {
        redirect(SITE_URL . '/products');
    }
    
    // Update view count
    $stmt = $db->prepare("UPDATE products SET views_count = views_count + 1 WHERE id = ?");
    $stmt->execute([$product['id']]);
    
    // Get product images
    $stmt = $db->prepare("
        SELECT * FROM product_images 
        WHERE product_id = ? 
        ORDER BY is_primary DESC, sort_order ASC
    ");
    $stmt->execute([$product['id']]);
    $product_images = $stmt->fetchAll();
    
    // Get related products
    $stmt = $db->prepare("
        SELECT p.*, 
               (SELECT image_path FROM product_images WHERE product_id = p.id AND is_primary = 1 LIMIT 1) as primary_image
        FROM products p 
        WHERE p.category_id = ? AND p.id != ? AND p.is_active = 1 
        ORDER BY p.is_featured DESC, p.created_at DESC 
        LIMIT 4
    ");
    $stmt->execute([$product['category_id'], $product['id']]);
    $related_products = $stmt->fetchAll();
    
} catch (Exception $e) {
    error_log("Product page error: " . $e->getMessage());
    redirect(SITE_URL . '/products');
}

$page_title = $product['title'];
$page_description = $product['short_description'];
$body_class = 'product-page';

// Check if user already owns this product
$already_owned = false;
if (isLoggedIn()) {
    try {
        $stmt = $db->prepare("
            SELECT COUNT(*) as count 
            FROM order_items oi 
            JOIN orders o ON oi.order_id = o.id 
            WHERE o.user_id = ? AND oi.product_id = ? AND o.payment_status = 'paid'
        ");
        $stmt->execute([$_SESSION['user_id'], $product['id']]);
        $already_owned = $stmt->fetch()['count'] > 0;
    } catch (Exception $e) {
        error_log("Ownership check error: " . $e->getMessage());
    }
}

include 'includes/header.php';
?>

<div class="container py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo SITE_URL; ?>">Home</a></li>
            <li class="breadcrumb-item"><a href="<?php echo SITE_URL; ?>/products">Products</a></li>
            <?php if ($product['category_name']): ?>
                <li class="breadcrumb-item">
                    <a href="<?php echo SITE_URL; ?>/products?category=<?php echo $product['category_slug']; ?>">
                        <?php echo htmlspecialchars($product['category_name']); ?>
                    </a>
                </li>
            <?php endif; ?>
            <li class="breadcrumb-item active"><?php echo htmlspecialchars($product['title']); ?></li>
        </ol>
    </nav>
    
    <div class="row">
        <!-- Product Images -->
        <div class="col-lg-6">
            <div class="product-gallery">
                <?php if (!empty($product_images)): ?>
                    <!-- Main Image -->
                    <div class="main-image-container mb-3">
                        <img src="<?php echo SITE_URL . '/uploads/products/' . $product_images[0]['image_path']; ?>" 
                             alt="<?php echo htmlspecialchars($product['title']); ?>"
                             class="product-main-image img-fluid rounded shadow">
                    </div>
                    
                    <!-- Thumbnail Images -->
                    <?php if (count($product_images) > 1): ?>
                        <div class="thumbnail-container">
                            <div class="row g-2">
                                <?php foreach ($product_images as $index => $image): ?>
                                    <div class="col-3">
                                        <img src="<?php echo SITE_URL . '/uploads/products/' . $image['image_path']; ?>" 
                                             alt="<?php echo htmlspecialchars($image['alt_text'] ?: $product['title']); ?>"
                                             class="product-thumbnail img-fluid rounded cursor-pointer <?php echo $index === 0 ? 'active' : ''; ?>"
                                             data-full-image="<?php echo SITE_URL . '/uploads/products/' . $image['image_path']; ?>">
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php else: ?>
                    <img src="<?php echo SITE_URL; ?>/assets/images/placeholder-product.jpg" 
                         alt="<?php echo htmlspecialchars($product['title']); ?>"
                         class="product-main-image img-fluid rounded shadow">
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Product Details -->
        <div class="col-lg-6">
            <div class="product-details">
                <!-- Category Badge -->
                <?php if ($product['category_name']): ?>
                    <div class="mb-2">
                        <a href="<?php echo SITE_URL; ?>/products?category=<?php echo $product['category_slug']; ?>" 
                           class="badge bg-primary bg-opacity-10 text-primary text-decoration-none">
                            <?php echo htmlspecialchars($product['category_name']); ?>
                        </a>
                        <?php if ($product['is_featured']): ?>
                            <span class="badge bg-warning text-dark ms-1">Featured</span>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
                
                <!-- Product Title -->
                <h1 class="display-6 fw-bold mb-3"><?php echo htmlspecialchars($product['title']); ?></h1>
                
                <!-- Product Price -->
                <div class="price-section mb-4">
                    <?php if ($product['sale_price'] && $product['sale_price'] < $product['price']): ?>
                        <div class="d-flex align-items-center gap-3">
                            <span class="h2 text-primary mb-0"><?php echo formatCurrency($product['sale_price']); ?></span>
                            <span class="h4 text-muted text-decoration-line-through mb-0">
                                <?php echo formatCurrency($product['price']); ?>
                            </span>
                            <span class="badge bg-danger">
                                <?php echo round((($product['price'] - $product['sale_price']) / $product['price']) * 100); ?>% OFF
                            </span>
                        </div>
                    <?php else: ?>
                        <span class="h2 text-primary mb-0"><?php echo formatCurrency($product['price']); ?></span>
                    <?php endif; ?>
                </div>
                
                <!-- Short Description -->
                <?php if ($product['short_description']): ?>
                    <div class="short-description mb-4">
                        <p class="lead"><?php echo nl2br(htmlspecialchars($product['short_description'])); ?></p>
                    </div>
                <?php endif; ?>
                
                <!-- Product Stats -->
                <div class="product-stats mb-4">
                    <div class="row text-center">
                        <div class="col-4">
                            <div class="stat-item">
                                <div class="h5 text-primary mb-0"><?php echo number_format($product['views_count']); ?></div>
                                <small class="text-muted">Views</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="stat-item">
                                <div class="h5 text-primary mb-0"><?php echo number_format($product['sales_count']); ?></div>
                                <small class="text-muted">Sales</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="stat-item">
                                <div class="h5 text-primary mb-0"><?php echo $product['download_limit']; ?></div>
                                <small class="text-muted">Downloads</small>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Action Buttons -->
                <div class="action-buttons mb-4">
                    <?php if ($already_owned): ?>
                        <div class="alert alert-success">
                            <i class="bi bi-check-circle me-2"></i>
                            You already own this product. 
                            <a href="<?php echo SITE_URL; ?>/downloads" class="alert-link">Go to Downloads</a>
                        </div>
                    <?php else: ?>
                        <div class="d-grid gap-2 d-md-flex">
                            <?php if (isLoggedIn()): ?>
                                <button class="btn btn-primary btn-lg flex-md-fill add-to-cart" 
                                        data-product-id="<?php echo $product['id']; ?>">
                                    <i class="bi bi-cart-plus me-2"></i>
                                    Add to Cart
                                </button>
                                <button class="btn btn-outline-secondary add-to-wishlist" 
                                        data-product-id="<?php echo $product['id']; ?>"
                                        title="Add to Wishlist">
                                    <i class="bi bi-heart"></i>
                                </button>
                            <?php else: ?>
                                <a href="<?php echo SITE_URL; ?>/login?redirect=<?php echo urlencode($_SERVER['REQUEST_URI']); ?>" 
                                   class="btn btn-primary btn-lg">
                                    <i class="bi bi-box-arrow-in-right me-2"></i>
                                    Login to Purchase
                                </a>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
                
                <!-- Product Features -->
                <div class="product-features">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="feature-item d-flex align-items-center">
                                <i class="bi bi-download text-primary me-3 fs-4"></i>
                                <div>
                                    <div class="fw-bold">Instant Download</div>
                                    <small class="text-muted">Download immediately after purchase</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="feature-item d-flex align-items-center">
                                <i class="bi bi-arrow-clockwise text-primary me-3 fs-4"></i>
                                <div>
                                    <div class="fw-bold">Lifetime Updates</div>
                                    <small class="text-muted">Free updates for life</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="feature-item d-flex align-items-center">
                                <i class="bi bi-shield-check text-primary me-3 fs-4"></i>
                                <div>
                                    <div class="fw-bold">Secure Purchase</div>
                                    <small class="text-muted">SSL encrypted checkout</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="feature-item d-flex align-items-center">
                                <i class="bi bi-headset text-primary me-3 fs-4"></i>
                                <div>
                                    <div class="fw-bold">24/7 Support</div>
                                    <small class="text-muted">Get help when you need it</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Product Description -->
    <?php if ($product['description']): ?>
        <div class="row mt-5">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="mb-0">Product Description</h3>
                    </div>
                    <div class="card-body">
                        <div class="product-description">
                            <?php echo nl2br(htmlspecialchars($product['description'])); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
    
    <!-- Related Products -->
    <?php if (!empty($related_products)): ?>
        <div class="row mt-5">
            <div class="col-12">
                <h3 class="mb-4">Related Products</h3>
                <div class="row g-4">
                    <?php foreach ($related_products as $related_product): ?>
                        <div class="col-lg-3 col-md-6">
                            <div class="card product-card h-100">
                                <div class="position-relative overflow-hidden">
                                    <img src="<?php echo $related_product['primary_image'] ? SITE_URL . '/uploads/products/' . $related_product['primary_image'] : SITE_URL . '/assets/images/placeholder-product.jpg'; ?>" 
                                         class="card-img-top" 
                                         alt="<?php echo htmlspecialchars($related_product['title']); ?>">
                                </div>
                                
                                <div class="card-body d-flex flex-column">
                                    <h6 class="card-title">
                                        <a href="<?php echo SITE_URL; ?>/product/<?php echo $related_product['slug']; ?>" 
                                           class="text-decoration-none text-dark">
                                            <?php echo htmlspecialchars($related_product['title']); ?>
                                        </a>
                                    </h6>
                                    
                                    <p class="card-text text-muted small flex-grow-1">
                                        <?php echo truncateText($related_product['short_description'], 80); ?>
                                    </p>
                                    
                                    <div class="mt-auto">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div class="price">
                                                <?php if ($related_product['sale_price'] && $related_product['sale_price'] < $related_product['price']): ?>
                                                    <span class="fw-bold text-primary"><?php echo formatCurrency($related_product['sale_price']); ?></span>
                                                <?php else: ?>
                                                    <span class="fw-bold text-primary"><?php echo formatCurrency($related_product['price']); ?></span>
                                                <?php endif; ?>
                                            </div>
                                            
                                            <a href="<?php echo SITE_URL; ?>/product/<?php echo $related_product['slug']; ?>" 
                                               class="btn btn-outline-primary btn-sm">
                                                View
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<style>
.product-thumbnail {
    border: 2px solid transparent;
    transition: border-color 0.3s ease;
}

.product-thumbnail:hover,
.product-thumbnail.active {
    border-color: var(--primary-color);
}

.cursor-pointer {
    cursor: pointer;
}

.feature-item {
    padding: 1rem;
    background-color: var(--bg-secondary);
    border-radius: 8px;
}

.stat-item {
    padding: 1rem;
    background-color: var(--bg-secondary);
    border-radius: 8px;
}

.product-description {
    line-height: 1.8;
    font-size: 1.1rem;
}
</style>

<?php include 'includes/footer.php'; ?>
