<?php
require_once '../config/config.php';

// Check if admin is logged in
if (!isAdminLoggedIn()) {
    redirect(SITE_URL . '/admin/login.php');
}

$success = false;
$error = '';
$messages = [];

try {
    $db = getDB();
    
    // Check current table structure
    $stmt = $db->query("SHOW COLUMNS FROM categories");
    $existing_columns = [];
    while ($row = $stmt->fetch()) {
        $existing_columns[] = $row['Field'];
    }
    
    $messages[] = "Current columns: " . implode(', ', $existing_columns);
    
    // Add parent_id column if it doesn't exist
    if (!in_array('parent_id', $existing_columns)) {
        $db->exec("ALTER TABLE categories ADD COLUMN parent_id INT NULL AFTER description");
        $messages[] = "✓ Added parent_id column for subcategories";
        
        // Add foreign key constraint
        $db->exec("ALTER TABLE categories ADD CONSTRAINT fk_categories_parent 
                  FOREIGN KEY (parent_id) REFERENCES categories(id) ON DELETE CASCADE");
        $messages[] = "✓ Added foreign key constraint for parent_id";
    } else {
        $messages[] = "• parent_id column already exists";
    }
    
    // Add meta_title column if it doesn't exist
    if (!in_array('meta_title', $existing_columns)) {
        $db->exec("ALTER TABLE categories ADD COLUMN meta_title VARCHAR(255) NULL AFTER sort_order");
        $messages[] = "✓ Added meta_title column for SEO";
    } else {
        $messages[] = "• meta_title column already exists";
    }
    
    // Add meta_description column if it doesn't exist
    if (!in_array('meta_description', $existing_columns)) {
        $db->exec("ALTER TABLE categories ADD COLUMN meta_description TEXT NULL AFTER meta_title");
        $messages[] = "✓ Added meta_description column for SEO";
    } else {
        $messages[] = "• meta_description column already exists";
    }
    
    $success = true;
    $messages[] = "🎉 Database migration completed successfully!";
    
} catch (Exception $e) {
    $error = $e->getMessage();
    $messages[] = "❌ Migration failed: " . $error;
    error_log("Migration error: " . $e->getMessage());
}

// Return JSON for AJAX calls
if (isset($_GET['ajax'])) {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => $success,
        'messages' => $messages,
        'error' => $error
    ]);
    exit;
}

// Regular page response
$page_title = 'Database Migration';
include 'includes/header.php';
?>

<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h2">Database Migration Results</h1>
        <a href="<?php echo SITE_URL; ?>/admin/categories.php" class="btn btn-sm btn-secondary">
            <i class="bi bi-arrow-left me-1"></i>
            Back to Categories
        </a>
    </div>
    
    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-<?php echo $success ? 'success' : 'danger'; ?>">
                        Migration Status: <?php echo $success ? 'SUCCESS' : 'FAILED'; ?>
                    </h6>
                </div>
                <div class="card-body">
                    <?php if ($success): ?>
                        <div class="alert alert-success">
                            <h5><i class="bi bi-check-circle me-2"></i>Migration Completed!</h5>
                            <p class="mb-0">Your database has been successfully updated with subcategory and SEO support.</p>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-danger">
                            <h5><i class="bi bi-exclamation-triangle me-2"></i>Migration Failed!</h5>
                            <p class="mb-0">There was an error during the migration process.</p>
                        </div>
                    <?php endif; ?>
                    
                    <h6>Migration Log:</h6>
                    <div class="bg-light p-3 rounded">
                        <?php foreach ($messages as $message): ?>
                            <div class="mb-1"><?php echo htmlspecialchars($message); ?></div>
                        <?php endforeach; ?>
                    </div>
                    
                    <?php if ($success): ?>
                        <div class="mt-4">
                            <h6>What's New:</h6>
                            <ul class="list-group">
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong>Subcategories Support</strong><br>
                                        <small class="text-muted">Create hierarchical categories (e.g., "Carrier AC" under "Air Conditioners")</small>
                                    </div>
                                    <span class="badge bg-success rounded-pill">Ready</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong>SEO Meta Fields</strong><br>
                                        <small class="text-muted">Add meta titles and descriptions for better search engine optimization</small>
                                    </div>
                                    <span class="badge bg-success rounded-pill">Ready</span>
                                </li>
                            </ul>
                        </div>
                        
                        <div class="mt-4 text-center">
                            <a href="<?php echo SITE_URL; ?>/admin/setup-categories.php" class="btn btn-success btn-lg">
                                <i class="bi bi-lightning me-2"></i>
                                Setup HVAC Categories Now
                            </a>
                            <a href="<?php echo SITE_URL; ?>/admin/categories.php?action=add" class="btn btn-primary btn-lg">
                                <i class="bi bi-plus-circle me-2"></i>
                                Add New Category
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-info">Next Steps</h6>
                </div>
                <div class="card-body">
                    <?php if ($success): ?>
                        <ol class="mb-0">
                            <li class="mb-2">
                                <strong>Create Categories</strong><br>
                                <small class="text-muted">Use the HVAC setup tool or add manually</small>
                            </li>
                            <li class="mb-2">
                                <strong>Add Subcategories</strong><br>
                                <small class="text-muted">Organize products by brands and types</small>
                            </li>
                            <li class="mb-2">
                                <strong>Set SEO Meta Data</strong><br>
                                <small class="text-muted">Add titles and descriptions for better SEO</small>
                            </li>
                            <li class="mb-2">
                                <strong>Add Products</strong><br>
                                <small class="text-muted">Start adding your HVAC products</small>
                            </li>
                            <li class="mb-0">
                                <strong>Launch Store</strong><br>
                                <small class="text-muted">Your HVAC e-commerce is ready!</small>
                            </li>
                        </ol>
                    <?php else: ?>
                        <div class="alert alert-warning">
                            <h6>Troubleshooting:</h6>
                            <ul class="mb-0">
                                <li>Check database permissions</li>
                                <li>Ensure MySQL is running</li>
                                <li>Verify database connection</li>
                                <li>Check error logs</li>
                            </ul>
                        </div>
                        
                        <div class="mt-3">
                            <a href="<?php echo SITE_URL; ?>/admin/run-migration.php" class="btn btn-warning btn-sm">
                                <i class="bi bi-arrow-clockwise me-1"></i>
                                Retry Migration
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
