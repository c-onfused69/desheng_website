<?php
require_once 'config/config.php';

$page_title = 'Digital Products & Engineering Solutions';
$page_description = 'Discover premium digital products, tools, and engineering solutions. Download instantly after purchase with secure access and lifetime updates.';
$body_class = 'homepage';

// Get featured products
try {
    $db = getDB();
    $stmt = $db->query("
        SELECT p.*, c.name as category_name, 
               (SELECT image_path FROM product_images WHERE product_id = p.id AND is_primary = 1 LIMIT 1) as primary_image
        FROM products p 
        LEFT JOIN categories c ON p.category_id = c.id 
        WHERE p.is_active = 1 AND p.is_featured = 1 
        ORDER BY p.created_at DESC 
        LIMIT 8
    ");
    $featured_products = $stmt->fetchAll();
} catch (Exception $e) {
    $featured_products = [];
    error_log("Error fetching featured products: " . $e->getMessage());
}

// Get categories
try {
    $stmt = $db->query("
        SELECT c.*, COUNT(p.id) as product_count
        FROM categories c 
        LEFT JOIN products p ON c.id = p.category_id AND p.is_active = 1
        WHERE c.is_active = 1 
        GROUP BY c.id 
        ORDER BY c.sort_order ASC, c.name ASC 
        LIMIT 6
    ");
    $categories = $stmt->fetchAll();
} catch (Exception $e) {
    $categories = [];
    error_log("Error fetching categories: " . $e->getMessage());
}

// Get testimonials (mock data for now)
$testimonials = [
    [
        'name' => 'Sarah Johnson',
        'role' => 'Web Developer',
        'avatar' => '/assets/images/testimonials/sarah.jpg',
        'rating' => 5,
        'comment' => 'Amazing digital products! The quality is outstanding and the download process is seamless. Highly recommended!'
    ],
    [
        'name' => 'Mike Chen',
        'role' => 'Startup Founder',
        'avatar' => '/assets/images/testimonials/mike.jpg',
        'rating' => 5,
        'comment' => 'Desh Engineering has helped accelerate our product development. Their tools are professional and well-documented.'
    ],
    [
        'name' => 'Emily Rodriguez',
        'role' => 'Designer',
        'avatar' => '/assets/images/testimonials/emily.jpg',
        'rating' => 5,
        'comment' => 'The design templates are modern and customizable. Perfect for client projects. Great value for money!'
    ]
];

include 'includes/header.php';
?>

<!-- Hero Section -->
<section class="hero-section">
    <div class="container">
        <div class="row align-items-center min-vh-100">
            <div class="col-lg-6">
                <div class="hero-content animate-on-scroll">
                    <h1 class="display-4 fw-bold mb-4 text-primary">
                        Premium <span class="text-secondary">Digital Products</span> for Modern Businesses
                    </h1>
                    <p class="lead mb-4 text-primary">
                        Discover high-quality digital tools, templates, and engineering solutions. 
                        Download instantly, use immediately, and scale your business faster.
                    </p>
                    <div class="hero-buttons">
                        <a href="<?php echo SITE_URL; ?>/products.php" class="btn btn-primary btn-lg me-3 mb-2">
                            <i class="bi bi-grid-3x3-gap me-2"></i>
                            Explore Products
                        </a>
                        <a href="<?php echo SITE_URL; ?>/categories.php" class="btn btn-secondary btn-lg mb-2">
                            <i class="bi bi-tags me-2"></i>
                            Browse Categories
                        </a>
                    </div>
                    
                    <!-- Stats -->
                    <div class="row mt-5">
                        <div class="col-4">
                            <div class="text-center">
                                <div class="h3 fw-bold mb-0 text-primary">500+</div>
                                <small class="text-primary opacity-75">Products</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="text-center">
                                <div class="h3 fw-bold mb-0 text-primary">10K+</div>
                                <small class="text-primary opacity-75">Downloads</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="text-center">
                                <div class="h3 fw-bold mb-0 text-primary">98%</div>
                                <small class="text-primary opacity-75">Satisfaction</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="hero-image animate-on-scroll">
                    <img src="<?php echo SITE_URL; ?>/assets/images/hero-illustration.svg" 
                         alt="Digital Products Illustration" 
                         class="img-fluid">
                </div>
            </div>
        </div>
    </div>
    
    <!-- Scroll indicator -->
    <div class="text-center pb-4">
        <a href="#featured-products" class="text-primary text-decoration-none">
            <i class="bi bi-chevron-down fs-2 animate-bounce"></i>
        </a>
    </div>
</section>

<!-- Featured Products Section -->
<section id="featured-products" class="featured-products section-white">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 mx-auto text-center mb-5">
                <h2 class="display-5 fw-bold mb-3">Featured Products</h2>
                <p class="lead text-muted">
                    Hand-picked premium digital products that deliver exceptional value and quality.
                </p>
            </div>
        </div>
        
        <?php if (!empty($featured_products)): ?>
        <div class="row g-4">
            <?php foreach ($featured_products as $product): ?>
            <div class="col-lg-3 col-md-6">
                <div class="product-card h-100 animate-on-scroll">
                    <?php if ($product['sale_price'] && $product['sale_price'] < $product['price']): ?>
                    <div class="badge-offer position-absolute top-0 start-0 m-2">
                        <?php echo round((($product['price'] - $product['sale_price']) / $product['price']) * 100); ?>% OFF
                    </div>
                    <?php endif; ?>
                    
                    <div class="position-relative overflow-hidden">
                        <img src="<?php echo $product['primary_image'] ? SITE_URL . '/uploads/products/' . $product['primary_image'] : SITE_URL . '/assets/images/placeholder-product.jpg'; ?>" 
                             class="card-img-top" 
                             alt="<?php echo htmlspecialchars($product['title']); ?>"
                             loading="lazy">
                        
                        <div class="card-img-overlay d-flex align-items-center justify-content-center opacity-0 hover-overlay">
                            <div class="text-center">
                                <button class="btn btn-light btn-sm me-2 quick-view" 
                                        data-product-id="<?php echo $product['id']; ?>"
                                        title="Quick View">
                                    <i class="bi bi-eye"></i>
                                </button>
                                <button class="btn btn-light btn-sm add-to-wishlist" 
                                        data-product-id="<?php echo $product['id']; ?>"
                                        title="Add to Wishlist">
                                    <i class="bi bi-heart"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card-body d-flex flex-column">
                        <div class="mb-2">
                            <span class="badge bg-primary bg-opacity-10 text-primary">
                                <?php echo htmlspecialchars($product['category_name']); ?>
                            </span>
                        </div>
                        
                        <h5 class="card-title">
                            <a href="<?php echo SITE_URL; ?>/product/<?php echo $product['slug']; ?>" 
                               class="text-decoration-none text-dark">
                                <?php echo htmlspecialchars($product['title']); ?>
                            </a>
                        </h5>
                        
                        <p class="card-text text-muted small flex-grow-1">
                            <?php echo truncateText($product['short_description'], 80); ?>
                        </p>
                        
                        <div class="d-flex justify-content-between align-items-center mt-auto">
                            <div class="price">
                                <?php if ($product['sale_price'] && $product['sale_price'] < $product['price']): ?>
                                    <span class="price-current"><?php echo formatCurrency($product['sale_price']); ?></span>
                                    <span class="price-original ms-1">
                                        <?php echo formatCurrency($product['price']); ?>
                                    </span>
                                <?php else: ?>
                                    <span class="price-current"><?php echo formatCurrency($product['price']); ?></span>
                                <?php endif; ?>
                            </div>
                            
                            <button class="btn btn-primary btn-sm add-to-cart" 
                                    data-product-id="<?php echo $product['id']; ?>">
                                <i class="bi bi-cart-plus me-1"></i>Add to Cart
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        
        <div class="text-center mt-5">
            <a href="<?php echo SITE_URL; ?>/products.php" class="btn btn-primary btn-lg">
                <i class="bi bi-grid me-2"></i>
                View All Products
            </a>
        </div>
        <?php else: ?>
        <div class="text-center py-5">
            <i class="bi bi-box-seam display-1 text-muted mb-3"></i>
            <h4 class="text-muted">No featured products available</h4>
            <p class="text-muted">Check back soon for amazing digital products!</p>
        </div>
        <?php endif; ?>
    </div>
</section>

<!-- Categories Section -->
<section class="section-alt">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 mx-auto text-center mb-5">
                <h2 class="display-5 fw-bold mb-3">Browse by Category</h2>
                <p class="lead text-muted">
                    Find exactly what you need from our organized product categories.
                </p>
            </div>
        </div>
        
        <?php if (!empty($categories)): ?>
        <div class="row g-4">
            <?php foreach ($categories as $category): ?>
            <div class="col-lg-4 col-md-6">
                <a href="<?php echo SITE_URL; ?>/products.php?category=<?php echo $category['slug']; ?>" 
                   class="text-decoration-none">
                    <div class="product-card h-100 animate-on-scroll">
                        <div class="card-body text-center p-4">
                            <div class="category-icon mb-3">
                                <i class="bi bi-<?php echo getCategoryIcon($category['slug']); ?> display-4 text-primary"></i>
                            </div>
                            <h5 class="card-title"><?php echo htmlspecialchars($category['name']); ?></h5>
                            <p class="card-text text-muted">
                                <?php echo htmlspecialchars($category['description']); ?>
                            </p>
                            <div class="mt-3">
                                <span class="badge bg-primary text-white">
                                    <?php echo $category['product_count']; ?> Products
                                </span>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</section>

<!-- Why Choose Us Section -->
<section class="section-white">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 mx-auto text-center mb-5">
                <h2 class="display-5 fw-bold mb-3">Why Choose Desh Engineering?</h2>
                <p class="lead text-muted">
                    We're committed to delivering exceptional digital products and services.
                </p>
            </div>
        </div>
        
        <div class="row g-4">
            <div class="col-lg-4 col-md-6">
                <div class="text-center animate-on-scroll">
                    <div class="feature-icon mb-3">
                        <i class="bi bi-shield-check display-4 text-primary"></i>
                    </div>
                    <h5 class="fw-bold">Secure & Reliable</h5>
                    <p class="text-muted">
                        All downloads are secure with encrypted delivery and reliable access to your purchases.
                    </p>
                </div>
            </div>
            
            <div class="col-lg-4 col-md-6">
                <div class="text-center animate-on-scroll">
                    <div class="feature-icon mb-3">
                        <i class="bi bi-lightning-charge display-4 text-primary"></i>
                    </div>
                    <h5 class="fw-bold">Instant Download</h5>
                    <p class="text-muted">
                        Get immediate access to your digital products right after purchase completion.
                    </p>
                </div>
            </div>
            
            <div class="col-lg-4 col-md-6">
                <div class="text-center animate-on-scroll">
                    <div class="feature-icon mb-3">
                        <i class="bi bi-headset display-4 text-primary"></i>
                    </div>
                    <h5 class="fw-bold">24/7 Support</h5>
                    <p class="text-muted">
                        Our dedicated support team is always ready to help you with any questions or issues.
                    </p>
                </div>
            </div>
            
            <div class="col-lg-4 col-md-6">
                <div class="text-center animate-on-scroll">
                    <div class="feature-icon mb-3">
                        <i class="bi bi-arrow-clockwise display-4 text-primary"></i>
                    </div>
                    <h5 class="fw-bold">Lifetime Updates</h5>
                    <p class="text-muted">
                        Get free updates and improvements to your purchased products for life.
                    </p>
                </div>
            </div>
            
            <div class="col-lg-4 col-md-6">
                <div class="text-center animate-on-scroll">
                    <div class="feature-icon mb-3">
                        <i class="bi bi-cash-coin display-4 text-primary"></i>
                    </div>
                    <h5 class="fw-bold">Money Back Guarantee</h5>
                    <p class="text-muted">
                        30-day money-back guarantee on all digital products. Your satisfaction is guaranteed.
                    </p>
                </div>
            </div>
            
            <div class="col-lg-4 col-md-6">
                <div class="text-center animate-on-scroll">
                    <div class="feature-icon mb-3">
                        <i class="bi bi-star-fill display-4 text-primary"></i>
                    </div>
                    <h5 class="fw-bold">Premium Quality</h5>
                    <p class="text-muted">
                        All products are carefully curated and tested to ensure the highest quality standards.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Testimonials Section -->
<section class="section-alt">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 mx-auto text-center mb-5">
                <h2 class="display-5 fw-bold mb-3">What Our Customers Say</h2>
                <p class="lead text-muted">
                    Join thousands of satisfied customers who trust Desh Engineering.
                </p>
            </div>
        </div>
        
        <div class="row g-4">
            <?php foreach ($testimonials as $testimonial): ?>
            <div class="col-lg-4">
                <div class="product-card h-100 animate-on-scroll">
                    <div class="card-body">
                        <div class="testimonial-rating mb-3">
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <i class="bi bi-star<?php echo $i <= $testimonial['rating'] ? '-fill' : ''; ?>"></i>
                            <?php endfor; ?>
                        </div>
                        
                        <blockquote class="blockquote">
                            <p class="mb-3">"<?php echo htmlspecialchars($testimonial['comment']); ?>"</p>
                        </blockquote>
                        
                        <div class="d-flex align-items-center">
                            <img src="<?php echo SITE_URL . $testimonial['avatar']; ?>" 
                                 alt="<?php echo htmlspecialchars($testimonial['name']); ?>"
                                 class="testimonial-avatar me-3"
                                 onerror="this.src='<?php echo SITE_URL; ?>/assets/images/default-avatar.jpg'">
                            <div>
                                <h6 class="mb-0"><?php echo htmlspecialchars($testimonial['name']); ?></h6>
                                <small class="text-muted"><?php echo htmlspecialchars($testimonial['role']); ?></small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="gradient-bg text-white py-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <h2 class="display-6 fw-bold mb-3">Ready to Get Started?</h2>
                <p class="lead mb-0">
                    Join thousands of satisfied customers and discover premium digital products today.
                </p>
            </div>
            <div class="col-lg-4 text-lg-end">
                <a href="<?php echo SITE_URL; ?>/products.php" class="btn btn-light btn-lg">
                    <i class="bi bi-arrow-right me-2"></i>
                    Start Shopping
                </a>
            </div>
        </div>
    </div>
</section>

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
