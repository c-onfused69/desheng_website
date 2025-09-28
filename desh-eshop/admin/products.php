<?php
require_once '../config/config.php';

// Check if admin is logged in
if (!isAdminLoggedIn()) {
    redirect(SITE_URL . '/admin/login.php');
}

$page_title = 'Products Management';
$current_admin = getCurrentAdmin();

// Handle actions
$action = $_GET['action'] ?? 'list';
$product_id = intval($_GET['id'] ?? 0);

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['bulk_action']) && isset($_POST['selected_items'])) {
        // Handle bulk actions
        $bulk_action = sanitize($_POST['bulk_action']);
        $selected_items = array_map('intval', $_POST['selected_items']);
        
        if (!empty($selected_items)) {
            try {
                $db = getDB();
                $placeholders = str_repeat('?,', count($selected_items) - 1) . '?';
                
                switch ($bulk_action) {
                    case 'activate':
                        $stmt = $db->prepare("UPDATE products SET is_active = 1 WHERE id IN ($placeholders)");
                        $stmt->execute($selected_items);
                        $_SESSION['admin_success_message'] = count($selected_items) . ' products activated successfully.';
                        break;
                        
                    case 'deactivate':
                        $stmt = $db->prepare("UPDATE products SET is_active = 0 WHERE id IN ($placeholders)");
                        $stmt->execute($selected_items);
                        $_SESSION['admin_success_message'] = count($selected_items) . ' products deactivated successfully.';
                        break;
                        
                    case 'feature':
                        $stmt = $db->prepare("UPDATE products SET is_featured = 1 WHERE id IN ($placeholders)");
                        $stmt->execute($selected_items);
                        $_SESSION['admin_success_message'] = count($selected_items) . ' products marked as featured.';
                        break;
                        
                    case 'unfeature':
                        $stmt = $db->prepare("UPDATE products SET is_featured = 0 WHERE id IN ($placeholders)");
                        $stmt->execute($selected_items);
                        $_SESSION['admin_success_message'] = count($selected_items) . ' products unmarked as featured.';
                        break;
                        
                    case 'delete':
                        // First delete related records
                        $stmt = $db->prepare("DELETE FROM product_images WHERE product_id IN ($placeholders)");
                        $stmt->execute($selected_items);
                        
                        $stmt = $db->prepare("DELETE FROM cart WHERE product_id IN ($placeholders)");
                        $stmt->execute($selected_items);
                        
                        // Then delete products
                        $stmt = $db->prepare("DELETE FROM products WHERE id IN ($placeholders)");
                        $stmt->execute($selected_items);
                        $_SESSION['admin_success_message'] = count($selected_items) . ' products deleted successfully.';
                        break;
                }
            } catch (Exception $e) {
                error_log("Bulk action error: " . $e->getMessage());
                $_SESSION['admin_error_message'] = 'Bulk action failed. Please try again.';
            }
        }
        
        redirect(SITE_URL . '/admin/products.php');
    }
    
    // Handle add/edit product form submission
    if (isset($_POST['save_product'])) {
        $title = sanitize($_POST['title'] ?? '');
        $slug = sanitize($_POST['slug'] ?? '');
        $description = sanitize($_POST['description'] ?? '');
        $short_description = sanitize($_POST['short_description'] ?? '');
        $price = floatval($_POST['price'] ?? 0);
        $sale_price = !empty($_POST['sale_price']) ? floatval($_POST['sale_price']) : null;
        $category_id = intval($_POST['category_id'] ?? 0);
        $sku = sanitize($_POST['sku'] ?? '');
        $is_active = isset($_POST['is_active']) ? 1 : 0;
        $is_featured = isset($_POST['is_featured']) ? 1 : 0;
        $meta_title = sanitize($_POST['meta_title'] ?? '');
        $meta_description = sanitize($_POST['meta_description'] ?? '');
        
        // Generate slug if empty
        if (empty($slug)) {
            $slug = generateSlug($title);
        }
        
        try {
            $db = getDB();
            
            if ($action === 'add') {
                // Insert new product
                $stmt = $db->prepare("
                    INSERT INTO products (
                        title, slug, description, short_description, price, sale_price,
                        category_id, sku, is_active, is_featured, meta_title, meta_description,
                        created_at, updated_at
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
                ");
                
                $stmt->execute([
                    $title, $slug, $description, $short_description, $price, $sale_price,
                    $category_id, $sku, $is_active, $is_featured, $meta_title, $meta_description
                ]);
                
                $_SESSION['admin_success_message'] = 'Product added successfully!';
                
            } elseif ($action === 'edit' && $product_id > 0) {
                // Update existing product
                $stmt = $db->prepare("
                    UPDATE products SET 
                        title = ?, slug = ?, description = ?, short_description = ?, 
                        price = ?, sale_price = ?, category_id = ?, sku = ?, 
                        is_active = ?, is_featured = ?, meta_title = ?, meta_description = ?,
                        updated_at = NOW()
                    WHERE id = ?
                ");
                
                $stmt->execute([
                    $title, $slug, $description, $short_description, $price, $sale_price,
                    $category_id, $sku, $is_active, $is_featured, $meta_title, $meta_description,
                    $product_id
                ]);
                
                $_SESSION['admin_success_message'] = 'Product updated successfully!';
            }
            
            redirect(SITE_URL . '/admin/products.php');
            
        } catch (Exception $e) {
            error_log("Product save error: " . $e->getMessage());
            $_SESSION['admin_error_message'] = 'Error saving product: ' . $e->getMessage();
        }
    }
}

// Handle different actions
if ($action === 'add' || $action === 'edit') {
    // Get product data for editing
    $product = null;
    if ($action === 'edit' && $product_id > 0) {
        try {
            $db = getDB();
            $stmt = $db->prepare("SELECT * FROM products WHERE id = ?");
            $stmt->execute([$product_id]);
            $product = $stmt->fetch();
            
            if (!$product) {
                $_SESSION['admin_error_message'] = 'Product not found.';
                redirect(SITE_URL . '/admin/products.php');
            }
        } catch (Exception $e) {
            error_log("Product fetch error: " . $e->getMessage());
            $_SESSION['admin_error_message'] = 'Error loading product.';
            redirect(SITE_URL . '/admin/products.php');
        }
    }
    
    // Get categories for dropdown
    try {
        $db = getDB();
        $stmt = $db->query("SELECT * FROM categories WHERE is_active = 1 ORDER BY name ASC");
        $categories = $stmt->fetchAll();
    } catch (Exception $e) {
        $categories = [];
    }
    
    include 'includes/header.php';
    ?>
    
    <div class="container-fluid">
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h2"><?php echo $action === 'add' ? 'Add New Product' : 'Edit Product'; ?></h1>
            <a href="<?php echo SITE_URL; ?>/admin/products.php" class="btn btn-sm btn-secondary">
                <i class="bi bi-arrow-left me-1"></i>
                Back to Products
            </a>
        </div>
        
        <?php if (isset($_SESSION['admin_error_message'])): ?>
            <div class="alert alert-danger">
                <?php echo $_SESSION['admin_error_message']; unset($_SESSION['admin_error_message']); ?>
            </div>
        <?php endif; ?>
        
        <div class="row">
            <div class="col-lg-8">
                <div class="card shadow mb-4">
                    <div class="card-body">
                        <form method="POST" action="">
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="mb-3">
                                        <label for="title" class="form-label">Product Title *</label>
                                        <input type="text" class="form-control" id="title" name="title" 
                                               value="<?php echo htmlspecialchars($product['title'] ?? ''); ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="sku" class="form-label">SKU</label>
                                        <input type="text" class="form-control" id="sku" name="sku" 
                                               value="<?php echo htmlspecialchars($product['sku'] ?? ''); ?>">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="slug" class="form-label">URL Slug</label>
                                <input type="text" class="form-control" id="slug" name="slug" 
                                       value="<?php echo htmlspecialchars($product['slug'] ?? ''); ?>">
                                <div class="form-text">Leave empty to auto-generate from title</div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="short_description" class="form-label">Short Description</label>
                                <textarea class="form-control" id="short_description" name="short_description" rows="3"><?php echo htmlspecialchars($product['short_description'] ?? ''); ?></textarea>
                            </div>
                            
                            <div class="mb-3">
                                <label for="description" class="form-label">Full Description</label>
                                <textarea class="form-control" id="description" name="description" rows="8"><?php echo htmlspecialchars($product['description'] ?? ''); ?></textarea>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="price" class="form-label">Regular Price (৳) *</label>
                                        <input type="number" class="form-control" id="price" name="price" 
                                               step="0.01" min="0" value="<?php echo $product['price'] ?? ''; ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="sale_price" class="form-label">Sale Price (৳)</label>
                                        <input type="number" class="form-control" id="sale_price" name="sale_price" 
                                               step="0.01" min="0" value="<?php echo $product['sale_price'] ?? ''; ?>">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="category_id" class="form-label">Category</label>
                                <select class="form-select" id="category_id" name="category_id">
                                    <option value="">Select Category</option>
                                    <?php foreach ($categories as $category): ?>
                                        <option value="<?php echo $category['id']; ?>" 
                                                <?php echo ($product['category_id'] ?? '') == $category['id'] ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($category['name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="meta_title" class="form-label">Meta Title</label>
                                        <input type="text" class="form-control" id="meta_title" name="meta_title" 
                                               value="<?php echo htmlspecialchars($product['meta_title'] ?? ''); ?>">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="meta_description" class="form-label">Meta Description</label>
                                        <textarea class="form-control" id="meta_description" name="meta_description" rows="2"><?php echo htmlspecialchars($product['meta_description'] ?? ''); ?></textarea>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-check mb-3">
                                        <input class="form-check-input" type="checkbox" id="is_active" name="is_active" 
                                               <?php echo ($product['is_active'] ?? 1) ? 'checked' : ''; ?>>
                                        <label class="form-check-label" for="is_active">
                                            Active (visible to customers)
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-check mb-3">
                                        <input class="form-check-input" type="checkbox" id="is_featured" name="is_featured" 
                                               <?php echo ($product['is_featured'] ?? 0) ? 'checked' : ''; ?>>
                                        <label class="form-check-label" for="is_featured">
                                            Featured Product
                                        </label>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="d-flex gap-2">
                                <button type="submit" name="save_product" class="btn btn-primary">
                                    <i class="bi bi-save me-1"></i>
                                    <?php echo $action === 'add' ? 'Add Product' : 'Update Product'; ?>
                                </button>
                                <a href="<?php echo SITE_URL; ?>/admin/products.php" class="btn btn-secondary">
                                    Cancel
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script>
    // Auto-generate slug from title
    document.getElementById('title').addEventListener('input', function() {
        const title = this.value;
        const slug = title.toLowerCase()
            .replace(/[^a-z0-9\s-]/g, '')
            .replace(/\s+/g, '-')
            .replace(/-+/g, '-')
            .trim('-');
        document.getElementById('slug').value = slug;
    });
    </script>
    
    <?php
    include 'includes/footer.php';
    exit;
}

// Get filters
$search = sanitize($_GET['search'] ?? '');
$category_filter = intval($_GET['category'] ?? 0);
$status_filter = $_GET['status'] ?? '';
$featured_filter = $_GET['featured'] ?? '';

// Build WHERE clause
$where_conditions = ['1=1'];
$params = [];

if (!empty($search)) {
    $where_conditions[] = '(p.title LIKE ? OR p.description LIKE ? OR p.sku LIKE ?)';
    $search_param = '%' . $search . '%';
    $params[] = $search_param;
    $params[] = $search_param;
    $params[] = $search_param;
}

if ($category_filter > 0) {
    $where_conditions[] = 'p.category_id = ?';
    $params[] = $category_filter;
}

if ($status_filter !== '') {
    $where_conditions[] = 'p.is_active = ?';
    $params[] = $status_filter === 'active' ? 1 : 0;
}

if ($featured_filter !== '') {
    $where_conditions[] = 'p.is_featured = ?';
    $params[] = $featured_filter === 'featured' ? 1 : 0;
}

$where_clause = 'WHERE ' . implode(' AND ', $where_conditions);

try {
    $db = getDB();
    
    // Get products
    $stmt = $db->prepare("
        SELECT p.*, c.name as category_name,
               (SELECT image_path FROM product_images WHERE product_id = p.id AND is_primary = 1 LIMIT 1) as primary_image,
               (SELECT COUNT(*) FROM order_items oi JOIN orders o ON oi.order_id = o.id WHERE oi.product_id = p.id AND o.payment_status = 'paid') as total_sales
        FROM products p 
        LEFT JOIN categories c ON p.category_id = c.id 
        {$where_clause}
        ORDER BY p.created_at DESC
    ");
    $stmt->execute($params);
    $products = $stmt->fetchAll();
    
    // Get categories for filter
    $stmt = $db->query("SELECT * FROM categories WHERE is_active = 1 ORDER BY name ASC");
    $categories = $stmt->fetchAll();
    
    // Get statistics
    $stmt = $db->query("
        SELECT 
            COUNT(*) as total_products,
            SUM(CASE WHEN is_active = 1 THEN 1 ELSE 0 END) as active_products,
            SUM(CASE WHEN is_featured = 1 THEN 1 ELSE 0 END) as featured_products,
            COALESCE(SUM(sales_count), 0) as total_sales
        FROM products
    ");
    $stats = $stmt->fetch();
    
    // Ensure all stats are integers, not null
    $stats['total_products'] = intval($stats['total_products'] ?? 0);
    $stats['active_products'] = intval($stats['active_products'] ?? 0);
    $stats['featured_products'] = intval($stats['featured_products'] ?? 0);
    $stats['total_sales'] = intval($stats['total_sales'] ?? 0);
    
} catch (Exception $e) {
    error_log("Products page error: " . $e->getMessage());
    $products = [];
    $categories = [];
    $stats = ['total_products' => 0, 'active_products' => 0, 'featured_products' => 0, 'total_sales' => 0];
}

include 'includes/header.php';
?>

<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Products Management</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <div class="btn-group me-2">
                <a href="<?php echo SITE_URL; ?>/admin/products.php?action=add" class="btn btn-sm btn-primary">
                    <i class="bi bi-plus-circle me-1"></i>
                    Add Product
                </a>
                <a href="<?php echo SITE_URL; ?>/admin/categories.php" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-tags me-1"></i>
                    Categories
                </a>
            </div>
            <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
                <i class="bi bi-download me-1"></i>
                Export
            </button>
            <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="#">Export CSV</a></li>
                <li><a class="dropdown-item" href="#">Export Excel</a></li>
                <li><a class="dropdown-item" href="#">Export PDF</a></li>
            </ul>
        </div>
    </div>
    
    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Products
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?php echo number_format($stats['total_products']); ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-box fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Active Products
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?php echo number_format($stats['active_products']); ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Featured Products
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?php echo number_format($stats['featured_products']); ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-star fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Total Sales
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?php echo number_format($stats['total_sales']); ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-graph-up fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Filters -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Filters & Search</h6>
        </div>
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-3">
                    <label for="search" class="form-label">Search</label>
                    <input type="text" class="form-control" id="search" name="search" 
                           placeholder="Search products..." value="<?php echo htmlspecialchars($search); ?>">
                </div>
                
                <div class="col-md-2">
                    <label for="category" class="form-label">Category</label>
                    <select class="form-select" id="category" name="category">
                        <option value="">All Categories</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?php echo $category['id']; ?>" 
                                    <?php echo $category_filter == $category['id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($category['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="col-md-2">
                    <label for="status" class="form-label">Status</label>
                    <select class="form-select" id="status" name="status">
                        <option value="">All Status</option>
                        <option value="active" <?php echo $status_filter === 'active' ? 'selected' : ''; ?>>Active</option>
                        <option value="inactive" <?php echo $status_filter === 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                    </select>
                </div>
                
                <div class="col-md-2">
                    <label for="featured" class="form-label">Featured</label>
                    <select class="form-select" id="featured" name="featured">
                        <option value="">All Products</option>
                        <option value="featured" <?php echo $featured_filter === 'featured' ? 'selected' : ''; ?>>Featured</option>
                        <option value="not_featured" <?php echo $featured_filter === 'not_featured' ? 'selected' : ''; ?>>Not Featured</option>
                    </select>
                </div>
                
                <div class="col-md-3">
                    <label class="form-label">&nbsp;</label>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-search me-1"></i>
                            Filter
                        </button>
                        <a href="<?php echo SITE_URL; ?>/admin/products.php" class="btn btn-outline-secondary">
                            <i class="bi bi-x-circle me-1"></i>
                            Clear
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Products Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Products List</h6>
            <div class="d-flex align-items-center">
                <small class="text-muted me-3"><?php echo count($products); ?> products found</small>
            </div>
        </div>
        <div class="card-body">
            <?php if (!empty($products)): ?>
                <!-- Bulk Actions -->
                <form method="POST" id="bulkActionForm">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="d-flex align-items-center">
                                <select class="form-select form-select-sm me-2" name="bulk_action" style="width: auto;">
                                    <option value="">Bulk Actions</option>
                                    <option value="activate">Activate</option>
                                    <option value="deactivate">Deactivate</option>
                                    <option value="feature">Mark as Featured</option>
                                    <option value="unfeature">Remove Featured</option>
                                    <option value="delete">Delete</option>
                                </select>
                                <button type="submit" class="btn btn-sm btn-outline-primary" id="bulkActionBtn" disabled>
                                    Apply
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table table-bordered data-table" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th width="30">
                                        <input type="checkbox" id="selectAll" class="form-check-input">
                                    </th>
                                    <th width="80">Image</th>
                                    <th>Product</th>
                                    <th>Category</th>
                                    <th>Price</th>
                                    <th>Sales</th>
                                    <th>Status</th>
                                    <th>Featured</th>
                                    <th>Created</th>
                                    <th width="120">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($products as $product): ?>
                                <tr>
                                    <td>
                                        <input type="checkbox" name="selected_items[]" value="<?php echo $product['id']; ?>" 
                                               class="form-check-input item-checkbox">
                                    </td>
                                    <td>
                                        <img src="<?php echo $product['primary_image'] ? SITE_URL . '/uploads/products/' . $product['primary_image'] : SITE_URL . '/assets/images/placeholder-product.jpg'; ?>" 
                                             alt="<?php echo htmlspecialchars($product['title']); ?>"
                                             class="img-thumbnail" style="width: 60px; height: 60px; object-fit: cover;">
                                    </td>
                                    <td>
                                        <div class="fw-bold"><?php echo htmlspecialchars($product['title']); ?></div>
                                        <small class="text-muted"><?php echo htmlspecialchars($product['sku'] ?? 'No SKU'); ?></small>
                                        <br>
                                        <small class="text-muted">Views: <?php echo number_format($product['views_count']); ?></small>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary">
                                            <?php echo htmlspecialchars($product['category_name'] ?? 'Uncategorized'); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if ($product['sale_price'] && $product['sale_price'] < $product['price']): ?>
                                            <div class="fw-bold text-success"><?php echo formatCurrency($product['sale_price']); ?></div>
                                            <small class="text-muted text-decoration-line-through">
                                                <?php echo formatCurrency($product['price']); ?>
                                            </small>
                                        <?php else: ?>
                                            <div class="fw-bold"><?php echo formatCurrency($product['price']); ?></div>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="fw-bold"><?php echo number_format($product['total_sales']); ?></span>
                                    </td>
                                    <td>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input status-toggle" 
                                                   type="checkbox" 
                                                   <?php echo $product['is_active'] ? 'checked' : ''; ?>
                                                   data-item-id="<?php echo $product['id']; ?>"
                                                   data-item-type="product">
                                        </div>
                                    </td>
                                    <td>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input featured-toggle" 
                                                   type="checkbox" 
                                                   <?php echo $product['is_featured'] ? 'checked' : ''; ?>
                                                   data-item-id="<?php echo $product['id']; ?>"
                                                   data-item-type="product-featured">
                                        </div>
                                    </td>
                                    <td>
                                        <small><?php echo date('M j, Y', strtotime($product['created_at'])); ?></small>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="<?php echo SITE_URL; ?>/admin/products.php?action=edit&id=<?php echo $product['id']; ?>" 
                                               class="btn btn-sm btn-outline-primary" title="Edit">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <a href="<?php echo SITE_URL; ?>/product/<?php echo $product['slug']; ?>" 
                                               class="btn btn-sm btn-outline-info" title="View" target="_blank">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-outline-danger btn-delete" 
                                                    onclick="deleteProduct(<?php echo $product['id']; ?>)" title="Delete">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </form>
            <?php else: ?>
                <div class="text-center py-5">
                    <i class="bi bi-box display-1 text-muted mb-3"></i>
                    <h4 class="text-muted">No products found</h4>
                    <p class="text-muted">Start by adding your first product.</p>
                    <a href="<?php echo SITE_URL; ?>/admin/products.php?action=add" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-2"></i>
                        Add Product
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
// Handle bulk actions
document.getElementById('selectAll')?.addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('.item-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.checked = this.checked;
    });
    updateBulkActionButton();
});

document.querySelectorAll('.item-checkbox').forEach(checkbox => {
    checkbox.addEventListener('change', updateBulkActionButton);
});

function updateBulkActionButton() {
    const selectedItems = document.querySelectorAll('.item-checkbox:checked');
    const bulkActionBtn = document.getElementById('bulkActionBtn');
    
    if (bulkActionBtn) {
        bulkActionBtn.disabled = selectedItems.length === 0;
        bulkActionBtn.textContent = selectedItems.length > 0 
            ? `Apply to ${selectedItems.length} item(s)` 
            : 'Apply';
    }
}

// Handle bulk action form submission
document.getElementById('bulkActionForm')?.addEventListener('submit', function(e) {
    const selectedItems = document.querySelectorAll('.item-checkbox:checked');
    const bulkAction = document.querySelector('select[name="bulk_action"]').value;
    
    if (selectedItems.length === 0) {
        e.preventDefault();
        alert('Please select items to perform bulk action.');
        return;
    }
    
    if (!bulkAction) {
        e.preventDefault();
        alert('Please select an action.');
        return;
    }
    
    if (bulkAction === 'delete') {
        if (!confirm(`Are you sure you want to delete ${selectedItems.length} product(s)? This action cannot be undone.`)) {
            e.preventDefault();
            return;
        }
    } else {
        if (!confirm(`Are you sure you want to ${bulkAction} ${selectedItems.length} product(s)?`)) {
            e.preventDefault();
            return;
        }
    }
});

// Handle status toggles
document.querySelectorAll('.status-toggle, .featured-toggle').forEach(toggle => {
    toggle.addEventListener('change', function() {
        const itemId = this.dataset.itemId;
        const itemType = this.dataset.itemType;
        const newStatus = this.checked ? 1 : 0;
        const originalState = this.checked;
        
        fetch('<?php echo SITE_URL; ?>/admin/api/update-status.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                type: itemType,
                id: itemId,
                status: newStatus
            })
        })
        .then(response => response.json())
        .then(data => {
            if (!data.success) {
                // Revert toggle state
                this.checked = !originalState;
                alert(data.message || 'Failed to update status');
            }
        })
        .catch(error => {
            console.error('Status update error:', error);
            this.checked = !originalState;
            alert('An error occurred while updating status');
        });
    });
});

// Delete product function
function deleteProduct(productId) {
    if (!confirm('Are you sure you want to delete this product? This action cannot be undone.')) {
        return;
    }
    
    fetch('<?php echo SITE_URL; ?>/admin/api/delete-product.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            id: productId
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert(data.message || 'Failed to delete product');
        }
    })
    .catch(error => {
        console.error('Delete error:', error);
        alert('An error occurred while deleting the product');
    });
}
</script>

<?php include 'includes/footer.php'; ?>
