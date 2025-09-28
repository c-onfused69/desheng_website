<?php
require_once '../config/config.php';

// Check if admin is logged in
if (!isAdminLoggedIn()) {
    redirect(SITE_URL . '/admin/login.php');
}

$page_title = 'Database Migration - Categories';
$current_admin = getCurrentAdmin();

$success_message = '';
$error_message = '';

// Handle migration
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['run_migration'])) {
    try {
        $db = getDB();
        
        // Check if parent_id column already exists
        $stmt = $db->query("SHOW COLUMNS FROM categories LIKE 'parent_id'");
        $column_exists = $stmt->fetch();
        
        if (!$column_exists) {
            // Add parent_id column
            $db->exec("ALTER TABLE categories ADD COLUMN parent_id INT NULL AFTER description");
            
            // Add foreign key constraint
            $db->exec("ALTER TABLE categories ADD CONSTRAINT fk_categories_parent 
                      FOREIGN KEY (parent_id) REFERENCES categories(id) ON DELETE CASCADE");
            
            // Add meta fields for SEO
            $stmt = $db->query("SHOW COLUMNS FROM categories LIKE 'meta_title'");
            $meta_title_exists = $stmt->fetch();
            
            if (!$meta_title_exists) {
                $db->exec("ALTER TABLE categories ADD COLUMN meta_title VARCHAR(255) NULL AFTER sort_order");
                $db->exec("ALTER TABLE categories ADD COLUMN meta_description TEXT NULL AFTER meta_title");
            }
            
            $success_message = 'Database migration completed successfully! Categories now support subcategories and SEO meta fields.';
        } else {
            $success_message = 'Database is already up to date. No migration needed.';
        }
        
    } catch (Exception $e) {
        error_log("Migration error: " . $e->getMessage());
        $error_message = 'Migration failed: ' . $e->getMessage();
    }
}

// Check current database structure
try {
    $db = getDB();
    $stmt = $db->query("SHOW COLUMNS FROM categories");
    $columns = $stmt->fetchAll();
    
    $has_parent_id = false;
    $has_meta_fields = false;
    
    foreach ($columns as $column) {
        if ($column['Field'] === 'parent_id') {
            $has_parent_id = true;
        }
        if ($column['Field'] === 'meta_title') {
            $has_meta_fields = true;
        }
    }
    
} catch (Exception $e) {
    $error_message = 'Error checking database structure: ' . $e->getMessage();
    $columns = [];
    $has_parent_id = false;
    $has_meta_fields = false;
}

include 'includes/header.php';
?>

<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h2">Database Migration - Categories</h1>
        <a href="<?php echo SITE_URL; ?>/admin/categories.php" class="btn btn-sm btn-secondary">
            <i class="bi bi-arrow-left me-1"></i>
            Back to Categories
        </a>
    </div>
    
    <?php if ($success_message): ?>
        <div class="alert alert-success">
            <i class="bi bi-check-circle me-2"></i>
            <?php echo $success_message; ?>
        </div>
    <?php endif; ?>
    
    <?php if ($error_message): ?>
        <div class="alert alert-danger">
            <i class="bi bi-exclamation-triangle me-2"></i>
            <?php echo $error_message; ?>
        </div>
    <?php endif; ?>
    
    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Database Structure Check</h6>
                </div>
                <div class="card-body">
                    <h6>Current Categories Table Structure:</h6>
                    <div class="table-responsive mb-4">
                        <table class="table table-sm table-bordered">
                            <thead>
                                <tr>
                                    <th>Field</th>
                                    <th>Type</th>
                                    <th>Null</th>
                                    <th>Key</th>
                                    <th>Default</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($columns as $column): ?>
                                <tr>
                                    <td><code><?php echo $column['Field']; ?></code></td>
                                    <td><?php echo $column['Type']; ?></td>
                                    <td><?php echo $column['Null']; ?></td>
                                    <td><?php echo $column['Key']; ?></td>
                                    <td><?php echo $column['Default'] ?? 'NULL'; ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card border-<?php echo $has_parent_id ? 'success' : 'warning'; ?>">
                                <div class="card-body text-center">
                                    <i class="bi bi-<?php echo $has_parent_id ? 'check-circle text-success' : 'exclamation-triangle text-warning'; ?> display-4 mb-2"></i>
                                    <h6>Subcategories Support</h6>
                                    <p class="mb-0">
                                        <?php if ($has_parent_id): ?>
                                            <span class="text-success">✓ Ready</span><br>
                                            <small class="text-muted">parent_id column exists</small>
                                        <?php else: ?>
                                            <span class="text-warning">⚠ Needs Migration</span><br>
                                            <small class="text-muted">parent_id column missing</small>
                                        <?php endif; ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card border-<?php echo $has_meta_fields ? 'success' : 'warning'; ?>">
                                <div class="card-body text-center">
                                    <i class="bi bi-<?php echo $has_meta_fields ? 'check-circle text-success' : 'exclamation-triangle text-warning'; ?> display-4 mb-2"></i>
                                    <h6>SEO Meta Fields</h6>
                                    <p class="mb-0">
                                        <?php if ($has_meta_fields): ?>
                                            <span class="text-success">✓ Ready</span><br>
                                            <small class="text-muted">meta_title & meta_description exist</small>
                                        <?php else: ?>
                                            <span class="text-warning">⚠ Needs Migration</span><br>
                                            <small class="text-muted">meta fields missing</small>
                                        <?php endif; ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <?php if (!$has_parent_id || !$has_meta_fields): ?>
                        <div class="alert alert-info mt-4">
                            <h6><i class="bi bi-info-circle me-2"></i>Migration Required</h6>
                            <p class="mb-2">To support the HVAC category structure with subcategories and SEO optimization, we need to add:</p>
                            <ul class="mb-3">
                                <?php if (!$has_parent_id): ?>
                                    <li><strong>parent_id</strong> - For creating subcategories (e.g., "Carrier AC" under "Air Conditioners")</li>
                                <?php endif; ?>
                                <?php if (!$has_meta_fields): ?>
                                    <li><strong>meta_title & meta_description</strong> - For SEO optimization</li>
                                <?php endif; ?>
                            </ul>
                            
                            <div class="d-flex gap-2">
                                <form method="POST" action="">
                                    <button type="submit" name="run_migration" class="btn btn-warning">
                                        <i class="bi bi-database me-2"></i>
                                        Run Database Migration
                                    </button>
                                </form>
                                <a href="<?php echo SITE_URL; ?>/admin/run-migration.php" class="btn btn-success">
                                    <i class="bi bi-lightning me-2"></i>
                                    Quick Migration
                                </a>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-success mt-4">
                            <h6><i class="bi bi-check-circle me-2"></i>Database Ready!</h6>
                            <p class="mb-3">Your database is ready for the HVAC category structure. You can now proceed to create categories.</p>
                            <a href="<?php echo SITE_URL; ?>/admin/setup-categories.php" class="btn btn-success">
                                <i class="bi bi-plus-circle me-2"></i>
                                Setup HVAC Categories
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-info">Migration Details</h6>
                </div>
                <div class="card-body">
                    <h6>What will be added:</h6>
                    <ul class="list-unstyled">
                        <li class="mb-2">
                            <i class="bi bi-plus-circle text-success me-2"></i>
                            <strong>parent_id</strong> column<br>
                            <small class="text-muted">INT NULL with foreign key constraint</small>
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-plus-circle text-success me-2"></i>
                            <strong>meta_title</strong> column<br>
                            <small class="text-muted">VARCHAR(255) for SEO titles</small>
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-plus-circle text-success me-2"></i>
                            <strong>meta_description</strong> column<br>
                            <small class="text-muted">TEXT for SEO descriptions</small>
                        </li>
                    </ul>
                    
                    <div class="alert alert-warning">
                        <small>
                            <i class="bi bi-exclamation-triangle me-1"></i>
                            <strong>Safe Migration:</strong> This migration only adds new columns and doesn't modify existing data.
                        </small>
                    </div>
                </div>
            </div>
            
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-success">After Migration</h6>
                </div>
                <div class="card-body">
                    <p class="mb-3">Once migration is complete, you'll be able to:</p>
                    <ul class="list-unstyled">
                        <li class="mb-2">
                            <i class="bi bi-check text-success me-2"></i>
                            Create main categories
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-check text-success me-2"></i>
                            Add subcategories under each main category
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-check text-success me-2"></i>
                            Set SEO meta titles and descriptions
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-check text-success me-2"></i>
                            Organize products hierarchically
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
