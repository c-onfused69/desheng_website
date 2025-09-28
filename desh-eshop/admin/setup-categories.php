<?php
require_once '../config/config.php';

// Check if admin is logged in
if (!isAdminLoggedIn()) {
    redirect(SITE_URL . '/admin/login.php');
}

$page_title = 'Setup Categories';
$current_admin = getCurrentAdmin();

// Categories data structure
$categories_data = [
    [
        'name' => 'Air Conditioners',
        'description' => 'Complete range of air conditioning units from leading brands',
        'subcategories' => [
            'Carrier AC',
            'Chigo AC', 
            'General AC',
            'Gree AC',
            'Green Aire',
            'Midea AC',
            'York AC',
            'LG AC',
            'Mitsubishi AC',
            'Panasonic AC',
            'Daikin AC'
        ]
    ],
    [
        'name' => 'Accessories',
        'description' => 'Essential AC accessories and components',
        'subcategories' => [
            'Filter Drier',
            'Massive',
            'Water Treatment Kits',
            'York Accessories',
            'Remote Controllers',
            'Thermostats',
            'Mounting Kits & Brackets'
        ]
    ],
    [
        'name' => 'Cables & Wiring',
        'description' => 'High-quality electrical cables and wiring solutions',
        'subcategories' => [
            'ABS Cable',
            'ABS Cable 40/76 3 Core',
            'ABS Cable 70/76 3 Core',
            'BBS Cable',
            'BBS Cable 23/76 3 Core',
            'BBS Cable 40/76 3 Core',
            'BBS Cable 70/76 3 Core',
            'Bizli Cable',
            'BRB Cable',
            'Partex Cable',
            'Partex Cable 23/76 3 Core',
            'Partex Cable 40/76 3 Core',
            'Partex Cable 70/76 3 Core',
            'SQ Cable'
        ]
    ],
    [
        'name' => 'Compressors',
        'description' => 'Premium compressors from trusted manufacturers',
        'subcategories' => [
            'Bitzer',
            'Bristol',
            'Chigo',
            'Copeland',
            'Daikin',
            'Danfoss',
            'Donper',
            'GMCC',
            'Gree',
            'Hitachi',
            'Invotech',
            'Kulthorn',
            'LG',
            'Mitsubishi',
            'Panasonic',
            'Secop',
            'Tecumseh',
            'Walton'
        ]
    ],
    [
        'name' => 'Compressor Oils',
        'description' => 'Specialized lubricants for optimal compressor performance',
        'subcategories' => [
            'Bitzer Compressor Oil',
            'B100',
            'B 5.2',
            'B320SH',
            'BSE 170',
            'BSE 32',
            'Danfoss Compressor Oil',
            '160 SZ POE',
            '160P Mineral',
            '175 PZ POE',
            '320 SZ POE',
            'BOGE Compressor Oil OZ 120',
            'Emkarate Compressor Oil RL 68H',
            'Suniso Compressor Oil 4GS'
        ]
    ],
    [
        'name' => 'Copper & Tubing',
        'description' => 'High-grade copper pipes and tubing systems',
        'subcategories' => [
            'Copper Straight Pipe',
            'Copper Coils',
            'Copper Fittings',
            'Capillary Tubes'
        ]
    ],
    [
        'name' => 'Insulation Materials',
        'description' => 'Thermal insulation solutions for HVAC systems',
        'subcategories' => [
            'Insulation Pipe',
            'Rubber Insulation Sheet',
            'Aluminum Foil Insulation',
            'Duct Insulation Materials'
        ]
    ],
    [
        'name' => 'Refrigerants & Chemicals',
        'description' => 'Refrigerants and maintenance chemicals for AC systems',
        'subcategories' => [
            'R22 Refrigerant',
            'R32 Refrigerant',
            'R134a Refrigerant',
            'R404a Refrigerant',
            'R407c Refrigerant',
            'R410a Refrigerant',
            'R507 Refrigerant',
            'Cleaning Chemicals (Coil Cleaners, Descalers)'
        ]
    ],
    [
        'name' => 'Spare Parts',
        'description' => 'Genuine spare parts for all AC systems',
        'subcategories' => [
            'VRF Spare Parts',
            'Chiller Spare Parts',
            'Split AC Spare Parts',
            'Window AC Spare Parts',
            'PCB Boards & Controllers',
            'Sensors & Switches',
            'Motors & Fans'
        ]
    ],
    [
        'name' => 'Air Treatment & Ventilation',
        'description' => 'Air quality and ventilation solutions',
        'subcategories' => [
            'Dehumidifiers',
            'Humidifiers',
            'Air Purifiers',
            'Ventilation Fans',
            'Fresh Air Units'
        ]
    ],
    [
        'name' => 'Chillers',
        'description' => 'Industrial and commercial chiller systems',
        'subcategories' => [
            'Water-Cooled Chillers',
            'Air-Cooled Chillers',
            'Chiller Accessories & Parts'
        ]
    ],
    [
        'name' => 'VRF/VRV Systems',
        'description' => 'Variable Refrigerant Flow systems and components',
        'subcategories' => [
            'Indoor Units',
            'Outdoor Units',
            'Controllers & Modules',
            'VRF Spare Parts'
        ]
    ],
    [
        'name' => 'Tools & Equipment',
        'description' => 'Professional HVAC tools and equipment',
        'subcategories' => [
            'Vacuum Pumps',
            'Manifold Gauges',
            'Leak Detectors',
            'Flaring & Swaging Tools',
            'Brazing & Welding Kits',
            'Pipe Benders & Cutters'
        ]
    ]
];

$success_message = '';
$error_message = '';

// Check if database is ready
try {
    $db = getDB();
    $stmt = $db->query("SHOW COLUMNS FROM categories LIKE 'parent_id'");
    $has_parent_id = $stmt->fetch();
} catch (Exception $e) {
    $has_parent_id = false;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_categories'])) {
    try {
        $db = getDB();
        
        // Check if parent_id column exists
        $stmt = $db->query("SHOW COLUMNS FROM categories LIKE 'parent_id'");
        $has_parent_id = $stmt->fetch();
        
        if (!$has_parent_id) {
            $error_message = 'Database migration required. Please run the migration first to add subcategory support.';
        } else {
            $db->beginTransaction();
        
        $created_main = 0;
        $created_sub = 0;
        $sort_order = 1;
        
        foreach ($categories_data as $category_data) {
            // Create main category
            $main_slug = generateSlug($category_data['name']);
            
            // Check if main category already exists
            $stmt = $db->prepare("SELECT id FROM categories WHERE slug = ?");
            $stmt->execute([$main_slug]);
            $existing_main = $stmt->fetch();
            
            if (!$existing_main) {
                $stmt = $db->prepare("
                    INSERT INTO categories (
                        name, slug, description, is_active, sort_order, 
                        created_at, updated_at
                    ) VALUES (?, ?, ?, 1, ?, NOW(), NOW())
                ");
                
                $stmt->execute([
                    $category_data['name'],
                    $main_slug,
                    $category_data['description'],
                    $sort_order
                ]);
                
                $main_category_id = $db->lastInsertId();
                $created_main++;
            } else {
                $main_category_id = $existing_main['id'];
            }
            
            // Create subcategories
            $sub_sort_order = 1;
            foreach ($category_data['subcategories'] as $subcategory_name) {
                $sub_slug = generateSlug($subcategory_name);
                
                // Check if subcategory already exists
                $stmt = $db->prepare("SELECT id FROM categories WHERE slug = ?");
                $stmt->execute([$sub_slug]);
                $existing_sub = $stmt->fetch();
                
                if (!$existing_sub) {
                    $stmt = $db->prepare("
                        INSERT INTO categories (
                            name, slug, description, parent_id, is_active, sort_order,
                            created_at, updated_at
                        ) VALUES (?, ?, ?, ?, 1, ?, NOW(), NOW())
                    ");
                    
                    $stmt->execute([
                        $subcategory_name,
                        $sub_slug,
                        "High-quality {$subcategory_name} products",
                        $main_category_id,
                        $sub_sort_order
                    ]);
                    
                    $created_sub++;
                }
                $sub_sort_order++;
            }
            
            $sort_order++;
        }
        
            $db->commit();
            $success_message = "Successfully created {$created_main} main categories and {$created_sub} subcategories!";
        }
        
    } catch (Exception $e) {
        if (isset($db) && $db->inTransaction()) {
            $db->rollback();
        }
        error_log("Category creation error: " . $e->getMessage());
        $error_message = 'Error creating categories: ' . $e->getMessage();
    }
}

include 'includes/header.php';
?>

<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h2">Setup Categories</h1>
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
    
    <?php if (!$has_parent_id): ?>
        <div class="alert alert-warning">
            <h6><i class="bi bi-exclamation-triangle me-2"></i>Database Migration Required</h6>
            <p class="mb-3">To create subcategories (like "Carrier AC" under "Air Conditioners"), your database needs to be updated first.</p>
            <a href="<?php echo SITE_URL; ?>/admin/migrate-categories.php" class="btn btn-warning">
                <i class="bi bi-database me-2"></i>
                Run Database Migration
            </a>
        </div>
    <?php endif; ?>
    
    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">HVAC Categories Setup</h6>
                </div>
                <div class="card-body">
                    <p class="mb-4">This will create a comprehensive category structure for your HVAC business including:</p>
                    
                    <div class="row">
                        <?php foreach ($categories_data as $index => $category): ?>
                            <div class="col-md-6 mb-3">
                                <div class="card border-left-primary h-100">
                                    <div class="card-body p-3">
                                        <h6 class="text-primary mb-2"><?php echo $index + 1; ?>. <?php echo $category['name']; ?></h6>
                                        <small class="text-muted"><?php echo count($category['subcategories']); ?> subcategories</small>
                                        <div class="mt-2">
                                            <?php foreach (array_slice($category['subcategories'], 0, 3) as $sub): ?>
                                                <span class="badge bg-light text-dark me-1"><?php echo $sub; ?></span>
                                            <?php endforeach; ?>
                                            <?php if (count($category['subcategories']) > 3): ?>
                                                <span class="badge bg-secondary">+<?php echo count($category['subcategories']) - 3; ?> more</span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <div class="alert alert-info mt-4">
                        <h6><i class="bi bi-info-circle me-2"></i>What will be created:</h6>
                        <ul class="mb-0">
                            <li><strong><?php echo count($categories_data); ?> Main Categories</strong> - Primary product categories</li>
                            <li><strong><?php echo array_sum(array_map(function($cat) { return count($cat['subcategories']); }, $categories_data)); ?> Subcategories</strong> - Detailed product classifications</li>
                            <li><strong>SEO-friendly URLs</strong> - Auto-generated slugs for all categories</li>
                            <li><strong>Proper sorting</strong> - Logical order for easy navigation</li>
                        </ul>
                    </div>
                    
                    <form method="POST" action="">
                        <div class="d-flex gap-2 justify-content-center">
                            <button type="submit" name="create_categories" class="btn btn-primary btn-lg">
                                <i class="bi bi-plus-circle me-2"></i>
                                Create All Categories
                            </button>
                            <a href="<?php echo SITE_URL; ?>/admin/categories.php" class="btn btn-outline-secondary btn-lg">
                                <i class="bi bi-list me-2"></i>
                                View Existing Categories
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-success">Category Benefits</h6>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled">
                        <li class="mb-2">
                            <i class="bi bi-check-circle text-success me-2"></i>
                            <strong>Organized Product Catalog</strong><br>
                            <small class="text-muted">Easy navigation for customers</small>
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-check-circle text-success me-2"></i>
                            <strong>Brand-wise Classification</strong><br>
                            <small class="text-muted">Separate categories for each brand</small>
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-check-circle text-success me-2"></i>
                            <strong>Technical Specifications</strong><br>
                            <small class="text-muted">Detailed product categorization</small>
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-check-circle text-success me-2"></i>
                            <strong>Professional Structure</strong><br>
                            <small class="text-muted">Industry-standard organization</small>
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-check-circle text-success me-2"></i>
                            <strong>SEO Optimized</strong><br>
                            <small class="text-muted">Search engine friendly URLs</small>
                        </li>
                    </ul>
                </div>
            </div>
            
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-info">Next Steps</h6>
                </div>
                <div class="card-body">
                    <ol class="mb-0">
                        <li class="mb-2">Create categories using the button</li>
                        <li class="mb-2">Add products to each category</li>
                        <li class="mb-2">Upload product images</li>
                        <li class="mb-2">Set pricing in BDT (৳)</li>
                        <li class="mb-0">Launch your HVAC store!</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
