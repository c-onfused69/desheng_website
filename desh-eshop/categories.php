<?php
require_once 'config/config.php';

$page_title = 'Product Categories';
$page_description = 'Browse our product categories to find exactly what you need.';
$body_class = 'categories-page';

try {
    $db = getDB();
    
    // Get categories with product counts
    $stmt = $db->query("
        SELECT c.*, COUNT(p.id) as product_count,
               (SELECT image_path FROM product_images pi 
                JOIN products pr ON pi.product_id = pr.id 
                WHERE pr.category_id = c.id AND pi.is_primary = 1 
                LIMIT 1) as category_image
        FROM categories c 
        LEFT JOIN products p ON c.id = p.category_id AND p.is_active = 1
        WHERE c.is_active = 1 
        GROUP BY c.id 
        ORDER BY c.sort_order ASC, c.name ASC
    ");
    $categories = $stmt->fetchAll();
    
} catch (Exception $e) {
    error_log("Categories page error: " . $e->getMessage());
    $categories = [];
}

include 'includes/header.php';
?>

<div class="container py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo SITE_URL; ?>">Home</a></li>
            <li class="breadcrumb-item active">Categories</li>
        </ol>
    </nav>
    
    <!-- Page Header -->
    <div class="text-center mb-5">
        <h1 class="display-4 fw-bold mb-3">Product Categories</h1>
        <p class="lead text-muted">
            Explore our organized collection of digital products and find exactly what you need.
        </p>
    </div>
    
    <?php if (!empty($categories)): ?>
        <!-- Categories Grid -->
        <div class="row g-4">
            <?php foreach ($categories as $category): ?>
                <div class="col-lg-4 col-md-6">
                    <a href="<?php echo SITE_URL; ?>/products.php?category=<?php echo $category['slug']; ?>" 
                       class="text-decoration-none">
                        <div class="product-card h-100 animate-on-scroll">
                            <?php if ($category['category_image']): ?>
                                <img src="<?php echo SITE_URL . '/uploads/products/' . $category['category_image']; ?>" 
                                     class="card-img-top" 
                                     alt="<?php echo htmlspecialchars($category['name']); ?>"
                                     style="height: 200px; object-fit: cover;">
                            <?php else: ?>
                                <div class="card-img-top d-flex align-items-center justify-content-center bg-light" 
                                     style="height: 200px;">
                                    <i class="bi bi-<?php echo getCategoryIcon($category['slug']); ?> display-1 text-primary"></i>
                                </div>
                            <?php endif; ?>
                            
                            <div class="card-body text-center">
                                <h5 class="card-title"><?php echo htmlspecialchars($category['name']); ?></h5>
                                <p class="card-text text-muted">
                                    <?php echo htmlspecialchars($category['description']); ?>
                                </p>
                                <div class="mt-3">
                                    <span class="badge bg-primary text-white fs-6">
                                        <?php echo number_format($category['product_count']); ?> Products
                                    </span>
                                </div>
                            </div>
                            
                            <div class="card-footer bg-transparent text-center">
                                <span class="btn btn-primary">
                                    Browse Products
                                    <i class="bi bi-arrow-right ms-2"></i>
                                </span>
                            </div>
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
        
        <!-- Call to Action -->
        <div class="text-center mt-5 py-5">
            <h3 class="fw-bold mb-3">Can't find what you're looking for?</h3>
            <p class="text-muted mb-4">
                Use our search feature to find specific products or contact us for custom solutions.
            </p>
            <div class="d-flex gap-2 justify-content-center">
                <a href="<?php echo SITE_URL; ?>/products.php" class="btn btn-primary btn-lg">
                    <i class="bi bi-search me-2"></i>
                    Search All Products
                </a>
                <a href="<?php echo SITE_URL; ?>/contact.php" class="btn btn-secondary btn-lg">
                    <i class="bi bi-envelope me-2"></i>
                    Contact Us
                </a>
            </div>
        </div>
        
    <?php else: ?>
        <!-- No Categories -->
        <div class="text-center py-5">
            <i class="bi bi-tags display-1 text-muted mb-4"></i>
            <h3 class="text-muted mb-3">No categories available</h3>
            <p class="text-muted mb-4">
                Categories will appear here once products are added to the system.
            </p>
            <a href="<?php echo SITE_URL; ?>/products.php" class="btn btn-primary btn-lg">
                <i class="bi bi-grid me-2"></i>
                View All Products
            </a>
        </div>
    <?php endif; ?>
</div>

<style>
.category-card {
    transition: all 0.3s ease;
    border: none;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.category-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
}

.category-card .card-img-top {
    transition: transform 0.3s ease;
}

.category-card:hover .card-img-top {
    transform: scale(1.05);
}

@media (max-width: 767.98px) {
    .category-card {
        margin-bottom: 1.5rem;
    }
}
</style>

<?php
// Helper function for category icons
function getCategoryIcon($slug) {
    $icons = [
        'web-development' => 'code-slash',
        'mobile-apps' => 'phone',
        'graphics-design' => 'palette',
        'digital-marketing' => 'megaphone',
        'business-tools' => 'briefcase',
        'default' => 'box'
    ];
    
    return isset($icons[$slug]) ? $icons[$slug] : $icons['default'];
}

include 'includes/footer.php';
?>
