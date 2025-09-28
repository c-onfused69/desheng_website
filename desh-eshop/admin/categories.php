<?php
require_once '../config/config.php';

// Check if admin is logged in
if (!isAdminLoggedIn()) {
    redirect(SITE_URL . '/admin/login.php');
}

$page_title = 'Categories Management';
$current_admin = getCurrentAdmin();

// Handle actions
$action = $_GET['action'] ?? 'list';
$category_id = intval($_GET['id'] ?? 0);

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
                        $stmt = $db->prepare("UPDATE categories SET is_active = 1 WHERE id IN ($placeholders)");
                        $stmt->execute($selected_items);
                        $_SESSION['admin_success_message'] = count($selected_items) . ' categories activated successfully.';
                        break;
                        
                    case 'deactivate':
                        $stmt = $db->prepare("UPDATE categories SET is_active = 0 WHERE id IN ($placeholders)");
                        $stmt->execute($selected_items);
                        $_SESSION['admin_success_message'] = count($selected_items) . ' categories deactivated successfully.';
                        break;
                        
                    case 'delete':
                        // Check if categories have products
                        $stmt = $db->prepare("SELECT COUNT(*) as product_count FROM products WHERE category_id IN ($placeholders)");
                        $stmt->execute($selected_items);
                        $result = $stmt->fetch();
                        
                        if ($result['product_count'] > 0) {
                            $_SESSION['admin_error_message'] = 'Cannot delete categories that contain products.';
                        } else {
                            $stmt = $db->prepare("DELETE FROM categories WHERE id IN ($placeholders)");
                            $stmt->execute($selected_items);
                            $_SESSION['admin_success_message'] = count($selected_items) . ' categories deleted successfully.';
                        }
                        break;
                }
            } catch (Exception $e) {
                error_log("Bulk action error: " . $e->getMessage());
                $_SESSION['admin_error_message'] = 'Bulk action failed. Please try again.';
            }
        }
        
        redirect(SITE_URL . '/admin/categories.php');
    }
    
    // Handle add/edit category form submission
    if (isset($_POST['save_category'])) {
        $name = sanitize($_POST['name'] ?? '');
        $slug = sanitize($_POST['slug'] ?? '');
        $description = sanitize($_POST['description'] ?? '');
        $is_active = isset($_POST['is_active']) ? 1 : 0;
        $sort_order = intval($_POST['sort_order'] ?? 0);
        $meta_title = sanitize($_POST['meta_title'] ?? '');
        $meta_description = sanitize($_POST['meta_description'] ?? '');
        
        // Generate slug if empty
        if (empty($slug)) {
            $slug = generateSlug($name);
        }
        
        try {
            $db = getDB();
            
            if ($action === 'add') {
                // Check if slug already exists
                $stmt = $db->prepare("SELECT COUNT(*) FROM categories WHERE slug = ?");
                $stmt->execute([$slug]);
                if ($stmt->fetchColumn() > 0) {
                    $_SESSION['admin_error_message'] = 'A category with this slug already exists.';
                } else {
                    // Insert new category
                    $stmt = $db->prepare("
                        INSERT INTO categories (
                            name, slug, description, is_active, sort_order, 
                            meta_title, meta_description, created_at, updated_at
                        ) VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
                    ");
                    
                    $stmt->execute([
                        $name, $slug, $description, $is_active, $sort_order,
                        $meta_title, $meta_description
                    ]);
                    
                    $_SESSION['admin_success_message'] = 'Category added successfully!';
                }
                
            } elseif ($action === 'edit' && $category_id > 0) {
                // Check if slug already exists (excluding current category)
                $stmt = $db->prepare("SELECT COUNT(*) FROM categories WHERE slug = ? AND id != ?");
                $stmt->execute([$slug, $category_id]);
                if ($stmt->fetchColumn() > 0) {
                    $_SESSION['admin_error_message'] = 'A category with this slug already exists.';
                } else {
                    // Update existing category
                    $stmt = $db->prepare("
                        UPDATE categories SET 
                            name = ?, slug = ?, description = ?, is_active = ?, 
                            sort_order = ?, meta_title = ?, meta_description = ?,
                            updated_at = NOW()
                        WHERE id = ?
                    ");
                    
                    $stmt->execute([
                        $name, $slug, $description, $is_active, $sort_order,
                        $meta_title, $meta_description, $category_id
                    ]);
                    
                    $_SESSION['admin_success_message'] = 'Category updated successfully!';
                }
            }
            
            if (!isset($_SESSION['admin_error_message'])) {
                redirect(SITE_URL . '/admin/categories.php');
            }
            
        } catch (Exception $e) {
            error_log("Category save error: " . $e->getMessage());
            $_SESSION['admin_error_message'] = 'Error saving category: ' . $e->getMessage();
        }
    }
}

// Handle different actions
if ($action === 'add' || $action === 'edit') {
    // Get category data for editing
    $category = null;
    if ($action === 'edit' && $category_id > 0) {
        try {
            $db = getDB();
            $stmt = $db->prepare("SELECT * FROM categories WHERE id = ?");
            $stmt->execute([$category_id]);
            $category = $stmt->fetch();
            
            if (!$category) {
                $_SESSION['admin_error_message'] = 'Category not found.';
                redirect(SITE_URL . '/admin/categories.php');
            }
        } catch (Exception $e) {
            error_log("Category fetch error: " . $e->getMessage());
            $_SESSION['admin_error_message'] = 'Error loading category.';
            redirect(SITE_URL . '/admin/categories.php');
        }
    }
    
    include 'includes/header.php';
    ?>
    
    <div class="container-fluid">
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h2"><?php echo $action === 'add' ? 'Add New Category' : 'Edit Category'; ?></h1>
            <a href="<?php echo SITE_URL; ?>/admin/categories.php" class="btn btn-sm btn-secondary">
                <i class="bi bi-arrow-left me-1"></i>
                Back to Categories
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
                            <div class="mb-3">
                                <label for="name" class="form-label">Category Name *</label>
                                <input type="text" class="form-control" id="name" name="name" 
                                       value="<?php echo htmlspecialchars($category['name'] ?? ''); ?>" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="slug" class="form-label">URL Slug</label>
                                <input type="text" class="form-control" id="slug" name="slug" 
                                       value="<?php echo htmlspecialchars($category['slug'] ?? ''); ?>">
                                <div class="form-text">Leave empty to auto-generate from name</div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control" id="description" name="description" rows="4"><?php echo htmlspecialchars($category['description'] ?? ''); ?></textarea>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="sort_order" class="form-label">Sort Order</label>
                                        <input type="number" class="form-control" id="sort_order" name="sort_order" 
                                               value="<?php echo $category['sort_order'] ?? 0; ?>" min="0">
                                        <div class="form-text">Lower numbers appear first</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-check mt-4">
                                        <input class="form-check-input" type="checkbox" id="is_active" name="is_active" 
                                               <?php echo ($category['is_active'] ?? 1) ? 'checked' : ''; ?>>
                                        <label class="form-check-label" for="is_active">
                                            Active (visible to customers)
                                        </label>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="meta_title" class="form-label">Meta Title</label>
                                        <input type="text" class="form-control" id="meta_title" name="meta_title" 
                                               value="<?php echo htmlspecialchars($category['meta_title'] ?? ''); ?>">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="meta_description" class="form-label">Meta Description</label>
                                        <textarea class="form-control" id="meta_description" name="meta_description" rows="2"><?php echo htmlspecialchars($category['meta_description'] ?? ''); ?></textarea>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="d-flex gap-2">
                                <button type="submit" name="save_category" class="btn btn-primary">
                                    <i class="bi bi-save me-1"></i>
                                    <?php echo $action === 'add' ? 'Add Category' : 'Update Category'; ?>
                                </button>
                                <a href="<?php echo SITE_URL; ?>/admin/categories.php" class="btn btn-secondary">
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
    // Auto-generate slug from name
    document.getElementById('name').addEventListener('input', function() {
        const name = this.value;
        const slug = name.toLowerCase()
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
$status_filter = $_GET['status'] ?? '';

// Build WHERE clause
$where_conditions = ['1=1'];
$params = [];

if (!empty($search)) {
    $where_conditions[] = '(name LIKE ? OR description LIKE ?)';
    $search_param = '%' . $search . '%';
    $params[] = $search_param;
    $params[] = $search_param;
}

if ($status_filter !== '') {
    $where_conditions[] = 'is_active = ?';
    $params[] = intval($status_filter);
}

$where_clause = implode(' AND ', $where_conditions);

// Pagination
$page = intval($_GET['page'] ?? 1);
$per_page = 20;
$offset = ($page - 1) * $per_page;

try {
    $db = getDB();
    
    // Get total count
    $count_sql = "SELECT COUNT(*) FROM categories WHERE $where_clause";
    $stmt = $db->prepare($count_sql);
    $stmt->execute($params);
    $total_categories = $stmt->fetchColumn();
    $total_pages = ceil($total_categories / $per_page);
    
    // Get categories with product counts
    $sql = "
        SELECT c.*, COUNT(p.id) as product_count
        FROM categories c
        LEFT JOIN products p ON c.id = p.category_id AND p.is_active = 1
        WHERE $where_clause
        GROUP BY c.id
        ORDER BY c.sort_order ASC, c.name ASC
        LIMIT $per_page OFFSET $offset
    ";
    
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    $categories = $stmt->fetchAll();
    
    // Get statistics
    $stmt = $db->query("
        SELECT 
            COUNT(*) as total_categories,
            SUM(CASE WHEN is_active = 1 THEN 1 ELSE 0 END) as active_categories,
            SUM(CASE WHEN is_active = 0 THEN 1 ELSE 0 END) as inactive_categories
        FROM categories
    ");
    $stats = $stmt->fetch();
    
    // Ensure all stats are integers, not null
    $stats['total_categories'] = intval($stats['total_categories'] ?? 0);
    $stats['active_categories'] = intval($stats['active_categories'] ?? 0);
    $stats['inactive_categories'] = intval($stats['inactive_categories'] ?? 0);
    
} catch (Exception $e) {
    error_log("Categories page error: " . $e->getMessage());
    $categories = [];
    $stats = ['total_categories' => 0, 'active_categories' => 0, 'inactive_categories' => 0];
    $total_categories = 0;
    $total_pages = 0;
}

include 'includes/header.php';
?>

<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h2">Categories Management</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <div class="btn-group me-2">
                <a href="<?php echo SITE_URL; ?>/admin/categories.php?action=add" class="btn btn-sm btn-primary">
                    <i class="bi bi-plus-circle me-1"></i>
                    Add Category
                </a>
                <a href="<?php echo SITE_URL; ?>/admin/setup-categories.php" class="btn btn-sm btn-success">
                    <i class="bi bi-lightning me-1"></i>
                    Setup HVAC Categories
                </a>
                <a href="<?php echo SITE_URL; ?>/admin/products.php" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-box me-1"></i>
                    Products
                </a>
            </div>
        </div>
    </div>
    
    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Categories
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?php echo number_format($stats['total_categories']); ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-tags fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Active Categories
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?php echo number_format($stats['active_categories']); ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Inactive Categories
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?php echo number_format($stats['inactive_categories']); ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-x-circle fa-2x text-gray-300"></i>
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
                <div class="col-md-4">
                    <label for="search" class="form-label">Search</label>
                    <input type="text" class="form-control" id="search" name="search" 
                           placeholder="Search categories..." value="<?php echo htmlspecialchars($search); ?>">
                </div>
                
                <div class="col-md-3">
                    <label for="status" class="form-label">Status</label>
                    <select class="form-select" id="status" name="status">
                        <option value="">All Status</option>
                        <option value="1" <?php echo $status_filter === '1' ? 'selected' : ''; ?>>Active</option>
                        <option value="0" <?php echo $status_filter === '0' ? 'selected' : ''; ?>>Inactive</option>
                    </select>
                </div>
                
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="bi bi-search me-1"></i>
                        Search
                    </button>
                    <a href="<?php echo SITE_URL; ?>/admin/categories.php" class="btn btn-outline-secondary">
                        <i class="bi bi-x-circle me-1"></i>
                        Clear
                    </a>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Categories Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Categories List</h6>
        </div>
        <div class="card-body">
            <?php if (!empty($categories)): ?>
                <form method="POST" id="bulkForm">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="d-flex align-items-center">
                                <select class="form-select form-select-sm me-2" name="bulk_action" style="width: auto;">
                                    <option value="">Bulk Actions</option>
                                    <option value="activate">Activate</option>
                                    <option value="deactivate">Deactivate</option>
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
                                    <th>Name</th>
                                    <th>Slug</th>
                                    <th>Products</th>
                                    <th>Sort Order</th>
                                    <th>Status</th>
                                    <th>Created</th>
                                    <th width="120">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($categories as $category): ?>
                                <tr>
                                    <td>
                                        <input type="checkbox" name="selected_items[]" value="<?php echo $category['id']; ?>" 
                                               class="form-check-input item-checkbox">
                                    </td>
                                    <td>
                                        <div class="fw-bold"><?php echo htmlspecialchars($category['name']); ?></div>
                                        <?php if ($category['description']): ?>
                                            <small class="text-muted"><?php echo truncateText($category['description'], 100); ?></small>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <code><?php echo htmlspecialchars($category['slug']); ?></code>
                                    </td>
                                    <td>
                                        <span class="badge bg-info"><?php echo number_format($category['product_count']); ?> products</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary"><?php echo $category['sort_order']; ?></span>
                                    </td>
                                    <td>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input status-toggle" 
                                                   type="checkbox" 
                                                   <?php echo $category['is_active'] ? 'checked' : ''; ?>
                                                   data-item-id="<?php echo $category['id']; ?>"
                                                   data-item-type="category">
                                        </div>
                                    </td>
                                    <td>
                                        <small><?php echo date('M j, Y', strtotime($category['created_at'])); ?></small>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="<?php echo SITE_URL; ?>/admin/categories.php?action=edit&id=<?php echo $category['id']; ?>" 
                                               class="btn btn-sm btn-outline-primary" title="Edit">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <a href="<?php echo SITE_URL; ?>/categories.php?category=<?php echo $category['slug']; ?>" 
                                               class="btn btn-sm btn-outline-info" title="View" target="_blank">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-outline-danger delete-item" 
                                                    data-item-id="<?php echo $category['id']; ?>"
                                                    data-item-type="category"
                                                    data-item-name="<?php echo htmlspecialchars($category['name']); ?>"
                                                    title="Delete">
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
                
                <!-- Pagination -->
                <?php if ($total_pages > 1): ?>
                    <nav aria-label="Categories pagination" class="mt-4">
                        <ul class="pagination justify-content-center">
                            <?php if ($page > 1): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page - 1])); ?>">Previous</a>
                                </li>
                            <?php endif; ?>
                            
                            <?php for ($i = max(1, $page - 2); $i <= min($total_pages, $page + 2); $i++): ?>
                                <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                                    <a class="page-link" href="?<?php echo http_build_query(array_merge($_GET, ['page' => $i])); ?>"><?php echo $i; ?></a>
                                </li>
                            <?php endfor; ?>
                            
                            <?php if ($page < $total_pages): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page + 1])); ?>">Next</a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </nav>
                <?php endif; ?>
                
            <?php else: ?>
                <!-- No Categories Found -->
                <div class="text-center py-5">
                    <i class="bi bi-tags display-1 text-muted mb-3"></i>
                    <h4 class="text-muted">No categories found</h4>
                    <p class="text-muted">Start by adding your first category.</p>
                    <a href="<?php echo SITE_URL; ?>/admin/categories.php?action=add" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-2"></i>
                        Add Category
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
// Bulk actions functionality
document.getElementById('selectAll').addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('.item-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.checked = this.checked;
    });
    toggleBulkActionButton();
});

document.querySelectorAll('.item-checkbox').forEach(checkbox => {
    checkbox.addEventListener('change', toggleBulkActionButton);
});

function toggleBulkActionButton() {
    const checkedBoxes = document.querySelectorAll('.item-checkbox:checked');
    const bulkActionBtn = document.getElementById('bulkActionBtn');
    bulkActionBtn.disabled = checkedBoxes.length === 0;
}

// Status toggle functionality
document.querySelectorAll('.status-toggle').forEach(toggle => {
    toggle.addEventListener('change', function() {
        const itemId = this.dataset.itemId;
        const itemType = this.dataset.itemType;
        const isActive = this.checked ? 1 : 0;
        
        fetch('<?php echo SITE_URL; ?>/admin/api/toggle-status.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                item_id: itemId,
                item_type: itemType,
                is_active: isActive
            })
        })
        .then(response => response.json())
        .then(data => {
            if (!data.success) {
                this.checked = !this.checked; // Revert on error
                alert(data.message || 'Failed to update status');
            }
        })
        .catch(error => {
            this.checked = !this.checked; // Revert on error
            console.error('Status toggle error:', error);
            alert('Failed to update status');
        });
    });
});

// Delete functionality
document.querySelectorAll('.delete-item').forEach(button => {
    button.addEventListener('click', function() {
        const itemId = this.dataset.itemId;
        const itemName = this.dataset.itemName;
        
        if (confirm(`Are you sure you want to delete the category "${itemName}"? This action cannot be undone.`)) {
            fetch('<?php echo SITE_URL; ?>/admin/api/delete-item.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    item_id: itemId,
                    item_type: 'category'
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert(data.message || 'Failed to delete category');
                }
            })
            .catch(error => {
                console.error('Delete error:', error);
                alert('Failed to delete category');
            });
        }
    });
});
</script>

<?php include 'includes/footer.php'; ?>
