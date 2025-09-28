<?php
require_once 'config/config.php';

$search_query = sanitize($_GET['q'] ?? '');
$page_title = !empty($search_query) ? 'Search Results for "' . $search_query . '"' : 'Search Products';
$page_description = 'Search our collection of digital products and find exactly what you need.';
$body_class = 'search-page';

// Redirect to products page with search parameter
if (!empty($search_query)) {
    redirect(SITE_URL . '/products?q=' . urlencode($search_query));
} else {
    // Show search page
    include 'includes/header.php';
    ?>
    
    <div class="container py-4">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo SITE_URL; ?>">Home</a></li>
                <li class="breadcrumb-item active">Search</li>
            </ol>
        </nav>
        
        <!-- Search Form -->
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="text-center mb-5">
                    <h1 class="display-4 fw-bold mb-3">Search Products</h1>
                    <p class="lead text-muted">
                        Find the perfect digital product for your needs
                    </p>
                </div>
                
                <div class="card shadow-sm">
                    <div class="card-body p-4">
                        <form action="<?php echo SITE_URL; ?>/products" method="GET">
                            <div class="input-group input-group-lg mb-3">
                                <input type="search" 
                                       class="form-control" 
                                       name="q" 
                                       placeholder="What are you looking for?" 
                                       autofocus>
                                <button class="btn btn-primary" type="submit">
                                    <i class="bi bi-search me-2"></i>
                                    Search
                                </button>
                            </div>
                        </form>
                        
                        <!-- Popular Searches -->
                        <div class="mt-4">
                            <h6 class="text-muted mb-3">Popular Searches:</h6>
                            <div class="d-flex flex-wrap gap-2">
                                <a href="<?php echo SITE_URL; ?>/products?q=website+template" class="btn btn-outline-primary btn-sm">Website Template</a>
                                <a href="<?php echo SITE_URL; ?>/products?q=mobile+app" class="btn btn-outline-primary btn-sm">Mobile App</a>
                                <a href="<?php echo SITE_URL; ?>/products?q=logo+design" class="btn btn-outline-primary btn-sm">Logo Design</a>
                                <a href="<?php echo SITE_URL; ?>/products?q=business+plan" class="btn btn-outline-primary btn-sm">Business Plan</a>
                                <a href="<?php echo SITE_URL; ?>/products?q=marketing+tool" class="btn btn-outline-primary btn-sm">Marketing Tool</a>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Browse by Category -->
                <div class="text-center mt-5">
                    <h4 class="fw-bold mb-3">Or browse by category</h4>
                    <a href="<?php echo SITE_URL; ?>/categories" class="btn btn-outline-primary btn-lg">
                        <i class="bi bi-tags me-2"></i>
                        View All Categories
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <?php include 'includes/footer.php';
}
?>
