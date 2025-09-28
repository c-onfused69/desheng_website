<?php
require_once 'config/config.php';

$page_title = 'Products';
$page_description = 'Browse our collection of premium digital products, tools, and engineering solutions.';
$body_class = 'products-page';

// Get filters from URL
$category_filter = sanitize($_GET['category'] ?? '');
$search_query = sanitize($_GET['q'] ?? '');
$sort_by = sanitize($_GET['sort'] ?? 'newest');
$min_price = floatval($_GET['min_price'] ?? 0);
$max_price = floatval($_GET['max_price'] ?? 1000);
$page = intval($_GET['page'] ?? 1);
$per_page = PRODUCTS_PER_PAGE;
$offset = ($page - 1) * $per_page;

// Build WHERE clause
$where_conditions = ['p.is_active = 1'];
$params = [];

if (!empty($category_filter)) {
    $where_conditions[] = 'c.slug = ?';
    $params[] = $category_filter;
}

if (!empty($search_query)) {
    $where_conditions[] = '(p.title LIKE ? OR p.description LIKE ? OR p.short_description LIKE ?)';
    $search_param = '%' . $search_query . '%';
    $params[] = $search_param;
    $params[] = $search_param;
    $params[] = $search_param;
}

if ($min_price > 0) {
    $where_conditions[] = 'COALESCE(p.sale_price, p.price) >= ?';
    $params[] = $min_price;
}

if ($max_price > 0 && $max_price < 1000) {
    $where_conditions[] = 'COALESCE(p.sale_price, p.price) <= ?';
    $params[] = $max_price;
}

$where_clause = 'WHERE ' . implode(' AND ', $where_conditions);

// Build ORDER BY clause
$order_by = 'ORDER BY ';
switch ($sort_by) {
    case 'price_low':
        $order_by .= 'COALESCE(p.sale_price, p.price) ASC';
        break;
    case 'price_high':
        $order_by .= 'COALESCE(p.sale_price, p.price) DESC';
        break;
    case 'popular':
        $order_by .= 'p.sales_count DESC, p.views_count DESC';
        break;
    case 'rating':
        $order_by .= 'p.created_at DESC'; // Would be average rating if implemented
        break;
    case 'oldest':
        $order_by .= 'p.created_at ASC';
        break;
    default: // newest
        $order_by .= 'p.created_at DESC';
        break;
}

try {
    $db = getDB();
    
    // Get total count for pagination
    $count_sql = "
        SELECT COUNT(*) as total
        FROM products p 
        LEFT JOIN categories c ON p.category_id = c.id 
        {$where_clause}
    ";
    $count_stmt = $db->prepare($count_sql);
    $count_stmt->execute($params);
    $total_products = $count_stmt->fetch()['total'];
    $total_pages = ceil($total_products / $per_page);
    
    // Get products
    $products_sql = "
        SELECT p.*, c.name as category_name, c.slug as category_slug,
               (SELECT image_path FROM product_images WHERE product_id = p.id AND is_primary = 1 LIMIT 1) as primary_image
        FROM products p 
        LEFT JOIN categories c ON p.category_id = c.id 
        {$where_clause}
        {$order_by}
        LIMIT {$per_page} OFFSET {$offset}
    ";
    $products_stmt = $db->prepare($products_sql);
    $products_stmt->execute($params);
    $products = $products_stmt->fetchAll();
    
    // Get categories for filter
    $categories_stmt = $db->query("
        SELECT c.*, COUNT(p.id) as product_count
        FROM categories c 
        LEFT JOIN products p ON c.id = p.category_id AND p.is_active = 1
        WHERE c.is_active = 1 
        GROUP BY c.id 
        ORDER BY c.name ASC
    ");
    $categories = $categories_stmt->fetchAll();
    
} catch (Exception $e) {
    error_log("Products page error: " . $e->getMessage());
    $products = [];
    $categories = [];
    $total_products = 0;
    $total_pages = 0;
}

include 'includes/header.php';
?>

<div class="container py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo SITE_URL; ?>">Home</a></li>
            <li class="breadcrumb-item active">Products</li>
            <?php if (!empty($category_filter)): ?>
                <li class="breadcrumb-item active"><?php echo ucfirst(str_replace('-', ' ', $category_filter)); ?></li>
            <?php endif; ?>
        </ol>
    </nav>
    
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-lg-8">
            <h1 class="display-6 fw-bold mb-2">
                <?php if (!empty($search_query)): ?>
                    Search Results for "<?php echo htmlspecialchars($search_query); ?>"
                <?php elseif (!empty($category_filter)): ?>
                    <?php echo ucfirst(str_replace('-', ' ', $category_filter)); ?> Products
                <?php else: ?>
                    All Products
                <?php endif; ?>
            </h1>
            <p class="text-muted mb-0">
                <?php echo number_format($total_products); ?> products found
            </p>
        </div>
        <div class="col-lg-4 text-lg-end">
            <!-- Sort Dropdown -->
            <div class="dropdown">
                <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    <i class="bi bi-sort-down me-2"></i>
                    Sort by: <?php echo ucfirst(str_replace('_', ' ', $sort_by)); ?>
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item <?php echo $sort_by === 'newest' ? 'active' : ''; ?>" 
                           href="<?php echo updateUrlParam('sort', 'newest'); ?>">Newest</a></li>
                    <li><a class="dropdown-item <?php echo $sort_by === 'oldest' ? 'active' : ''; ?>" 
                           href="<?php echo updateUrlParam('sort', 'oldest'); ?>">Oldest</a></li>
                    <li><a class="dropdown-item <?php echo $sort_by === 'price_low' ? 'active' : ''; ?>" 
                           href="<?php echo updateUrlParam('sort', 'price_low'); ?>">Price: Low to High</a></li>
                    <li><a class="dropdown-item <?php echo $sort_by === 'price_high' ? 'active' : ''; ?>" 
                           href="<?php echo updateUrlParam('sort', 'price_high'); ?>">Price: High to Low</a></li>
                    <li><a class="dropdown-item <?php echo $sort_by === 'popular' ? 'active' : ''; ?>" 
                           href="<?php echo updateUrlParam('sort', 'popular'); ?>">Most Popular</a></li>
                </ul>
            </div>
        </div>
    </div>
    
    <div class="row">
        <!-- Filters Sidebar -->
        <div class="col-lg-3">
            <div class="filters-sidebar">
                <h5 class="fw-bold mb-3">
                    <i class="bi bi-funnel me-2"></i>
                    Filters
                </h5>
                
                <!-- Search Filter -->
                <div class="filter-group">
                    <h6>Search</h6>
                    <form method="GET" class="mb-3">
                        <?php if (!empty($category_filter)): ?>
                            <input type="hidden" name="category" value="<?php echo htmlspecialchars($category_filter); ?>">
                        <?php endif; ?>
                        <div class="input-group">
                            <input type="text" 
                                   class="form-control" 
                                   name="q" 
                                   placeholder="Search products..." 
                                   value="<?php echo htmlspecialchars($search_query); ?>">
                            <button class="btn btn-primary" type="submit">
                                <i class="bi bi-search"></i>
                            </button>
                        </div>
                    </form>
                </div>
                
                <!-- Category Filter -->
                <div class="filter-group">
                    <h6>Categories</h6>
                    <div class="list-group list-group-flush">
                        <a href="<?php echo removeUrlParam('category'); ?>" 
                           class="list-group-item list-group-item-action border-0 px-0 <?php echo empty($category_filter) ? 'active' : ''; ?>">
                            All Categories
                        </a>
                        <?php foreach ($categories as $category): ?>
                            <a href="<?php echo updateUrlParam('category', $category['slug']); ?>" 
                               class="list-group-item list-group-item-action border-0 px-0 d-flex justify-content-between align-items-center <?php echo $category_filter === $category['slug'] ? 'active' : ''; ?>">
                                <?php echo htmlspecialchars($category['name']); ?>
                                <span class="badge bg-secondary"><?php echo $category['product_count']; ?></span>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
                
                <!-- Price Range Filter -->
                <div class="filter-group">
                    <h6>Price Range</h6>
                    <form method="GET" id="priceFilterForm">
                        <?php if (!empty($category_filter)): ?>
                            <input type="hidden" name="category" value="<?php echo htmlspecialchars($category_filter); ?>">
                        <?php endif; ?>
                        <?php if (!empty($search_query)): ?>
                            <input type="hidden" name="q" value="<?php echo htmlspecialchars($search_query); ?>">
                        <?php endif; ?>
                        <input type="hidden" name="sort" value="<?php echo htmlspecialchars($sort_by); ?>">
                        
                        <div class="row g-2 mb-3">
                            <div class="col">
                                <input type="number" 
                                       class="form-control form-control-sm" 
                                       name="min_price" 
                                       placeholder="Min" 
                                       value="<?php echo $min_price > 0 ? $min_price : ''; ?>"
                                       min="0" 
                                       step="0.01">
                            </div>
                            <div class="col">
                                <input type="number" 
                                       class="form-control form-control-sm" 
                                       name="max_price" 
                                       placeholder="Max" 
                                       value="<?php echo $max_price < 1000 ? $max_price : ''; ?>"
                                       min="0" 
                                       step="0.01">
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary btn-sm w-100">Apply</button>
                    </form>
                </div>
                
                <!-- Clear Filters -->
                <?php if (!empty($category_filter) || !empty($search_query) || $min_price > 0 || $max_price < 1000): ?>
                <div class="filter-group">
                    <a href="<?php echo SITE_URL; ?>/products.php" class="btn btn-outline-secondary btn-sm w-100">
                        <i class="bi bi-x-circle me-2"></i>
                        Clear All Filters
                    </a>
                </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Products Grid -->
        <div class="col-lg-9">
            <?php if (!empty($products)): ?>
                <div class="row g-4">
                    <?php foreach ($products as $product): ?>
                        <div class="col-lg-4 col-md-6">
                            <div class="product-card h-100">
                                <?php if ($product['sale_price'] && $product['sale_price'] < $product['price']): ?>
                                    <div class="badge-offer position-absolute top-0 start-0 m-2 z-2">
                                        <?php echo round((($product['price'] - $product['sale_price']) / $product['price']) * 100); ?>% OFF
                                    </div>
                                <?php endif; ?>
                                
                                <?php if ($product['is_featured']): ?>
                                    <div class="badge bg-primary position-absolute top-0 end-0 m-2 z-2">
                                        Featured
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
                                        <a href="<?php echo SITE_URL; ?>/products.php?category=<?php echo $product['category_slug']; ?>" 
                                           class="badge bg-primary bg-opacity-10 text-primary text-decoration-none">
                                            <?php echo htmlspecialchars($product['category_name']); ?>
                                        </a>
                                    </div>
                                    
                                    <h5 class="card-title">
                                        <a href="<?php echo SITE_URL; ?>/product/<?php echo $product['slug']; ?>" 
                                           class="text-decoration-none text-dark">
                                            <?php echo htmlspecialchars($product['title']); ?>
                                        </a>
                                    </h5>
                                    
                                    <p class="card-text text-muted small flex-grow-1">
                                        <?php echo truncateText($product['short_description'], 100); ?>
                                    </p>
                                    
                                    <div class="mt-auto">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
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
                                            
                                            <div class="text-muted small">
                                                <i class="bi bi-eye me-1"></i>
                                                <?php echo number_format($product['views_count']); ?>
                                            </div>
                                        </div>
                                        
                                        <div class="d-grid">
                                            <button class="btn btn-primary add-to-cart" 
                                                    data-product-id="<?php echo $product['id']; ?>">
                                                <i class="bi bi-cart-plus me-2"></i>
                                                Add to Cart
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <!-- Pagination -->
                <?php if ($total_pages > 1): ?>
                    <nav aria-label="Products pagination" class="mt-5">
                        <ul class="pagination justify-content-center">
                            <?php if ($page > 1): ?>
                                <li class="page-item">
                                    <a class="page-link" href="<?php echo updateUrlParam('page', $page - 1); ?>">
                                        <i class="bi bi-chevron-left"></i>
                                    </a>
                                </li>
                            <?php endif; ?>
                            
                            <?php
                            $start_page = max(1, $page - 2);
                            $end_page = min($total_pages, $page + 2);
                            
                            if ($start_page > 1): ?>
                                <li class="page-item">
                                    <a class="page-link" href="<?php echo updateUrlParam('page', 1); ?>">1</a>
                                </li>
                                <?php if ($start_page > 2): ?>
                                    <li class="page-item disabled">
                                        <span class="page-link">...</span>
                                    </li>
                                <?php endif; ?>
                            <?php endif; ?>
                            
                            <?php for ($i = $start_page; $i <= $end_page; $i++): ?>
                                <li class="page-item <?php echo $i === $page ? 'active' : ''; ?>">
                                    <a class="page-link" href="<?php echo updateUrlParam('page', $i); ?>"><?php echo $i; ?></a>
                                </li>
                            <?php endfor; ?>
                            
                            <?php if ($end_page < $total_pages): ?>
                                <?php if ($end_page < $total_pages - 1): ?>
                                    <li class="page-item disabled">
                                        <span class="page-link">...</span>
                                    </li>
                                <?php endif; ?>
                                <li class="page-item">
                                    <a class="page-link" href="<?php echo updateUrlParam('page', $total_pages); ?>"><?php echo $total_pages; ?></a>
                                </li>
                            <?php endif; ?>
                            
                            <?php if ($page < $total_pages): ?>
                                <li class="page-item">
                                    <a class="page-link" href="<?php echo updateUrlParam('page', $page + 1); ?>">
                                        <i class="bi bi-chevron-right"></i>
                                    </a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </nav>
                <?php endif; ?>
                
            <?php else: ?>
                <!-- No Products Found -->
                <div class="text-center py-5">
                    <i class="bi bi-search display-1 text-muted mb-3"></i>
                    <h3 class="text-muted mb-3">No products found</h3>
                    <p class="text-muted mb-4">
                        <?php if (!empty($search_query)): ?>
                            No products match your search criteria. Try adjusting your search terms or filters.
                        <?php else: ?>
                            There are no products in this category yet. Check back soon!
                        <?php endif; ?>
                    </p>
                    <div class="d-flex gap-2 justify-content-center">
                        <a href="<?php echo SITE_URL; ?>/products" class="btn btn-primary">
                            <i class="bi bi-grid me-2"></i>
                            View All Products
                        </a>
                        <a href="<?php echo SITE_URL; ?>" class="btn btn-outline-primary">
                            <i class="bi bi-house me-2"></i>
                            Go Home
                        </a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Add hover effects CSS -->
<style>
.hover-overlay {
    background: rgba(0, 0, 0, 0.7);
    transition: opacity 0.3s ease;
}

.product-card:hover .hover-overlay {
    opacity: 1 !important;
}

.product-card .card-img-overlay {
    border-radius: 0;
}
</style>

<?php
// Helper functions for URL manipulation
function updateUrlParam($param, $value) {
    $params = $_GET;
    $params[$param] = $value;
    unset($params['page']); // Reset page when changing filters
    if ($param === 'page') {
        $params['page'] = $value; // Keep page if specifically updating page
    }
    return SITE_URL . '/products?' . http_build_query($params);
}

function removeUrlParam($param) {
    $params = $_GET;
    unset($params[$param]);
    unset($params['page']); // Reset page when removing filters
    return SITE_URL . '/products' . (!empty($params) ? '?' . http_build_query($params) : '');
}

include 'includes/footer.php';
?>
