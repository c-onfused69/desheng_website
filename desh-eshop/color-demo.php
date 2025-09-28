<?php
require_once 'config/config.php';

$page_title = 'E-commerce Optimized Color Palette Demo';
$page_description = 'Demonstration of the new e-commerce optimized color palette designed for trust and conversion.';
$body_class = 'color-demo-page';

include 'includes/header.php';
?>

<div class="container py-4">
    <!-- Page Header -->
    <div class="text-center mb-5">
        <h1 class="display-4 fw-bold">E-commerce Optimized Color Palette</h1>
        <p class="lead">
            Professional colors designed for trust, reliability, and conversion optimization.
        </p>
        <div class="alert alert-success">
            <i class="bi bi-check-circle me-2"></i>
            <strong>Perfect Navigation:</strong> Non-transparent, non-fixed header with clean white background and proper spacing.
        </div>
    </div>
    
    <!-- Color Palette Display -->
    <div class="row mb-5">
        <div class="col-12">
            <h2 class="fw-bold mb-4">Primary E-commerce Colors</h2>
            <div class="row g-4">
                <div class="col-lg-4 col-md-6">
                    <div class="card text-center product-card">
                        <div class="card-body p-4" style="background: #27367B; color: white; border-radius: 8px;">
                            <h4 class="mb-2">#27367B</h4>
                            <h6 class="mb-2">Deep Navy Blue</h6>
                            <p class="mb-0"><strong>Primary Brand Color</strong></p>
                            <small>Headers, Primary Buttons, Brand Elements</small>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="card text-center product-card">
                        <div class="card-body p-4" style="background: #FFFFFF; color: #000000; border: 2px solid #E5E7EB; border-radius: 8px;">
                            <h4 class="mb-2">#FFFFFF</h4>
                            <h6 class="mb-2">Pure White</h6>
                            <p class="mb-0"><strong>Base Background</strong></p>
                            <small>Main Background, Product Cards, Clean Areas</small>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="card text-center product-card">
                        <div class="card-body p-4" style="background: #12A0C6; color: white; border-radius: 8px;">
                            <h4 class="mb-2">#12A0C6</h4>
                            <h6 class="mb-2">Vibrant Teal</h6>
                            <p class="mb-0"><strong>Secondary Accent</strong></p>
                            <small>CTAs, Highlights, Discount Labels</small>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="card text-center product-card">
                        <div class="card-body p-4" style="background: #A6C4E0; color: #27367B; border-radius: 8px;">
                            <h4 class="mb-2">#A6C4E0</h4>
                            <h6 class="mb-2">Soft Light Blue</h6>
                            <p class="mb-0"><strong>Subtle Background</strong></p>
                            <small>Section Dividers, Form Backgrounds</small>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="card text-center product-card">
                        <div class="card-body p-4" style="background: #3A347A; color: white; border-radius: 8px;">
                            <h4 class="mb-2">#3A347A</h4>
                            <h6 class="mb-2">Dark Indigo</h6>
                            <p class="mb-0"><strong>Deep Accent</strong></p>
                            <small>Footer, Category Headings, Depth</small>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="card text-center product-card">
                        <div class="card-body p-4" style="background: #000000; color: white; border-radius: 8px;">
                            <h4 class="mb-2">#000000</h4>
                            <h6 class="mb-2">Black</h6>
                            <p class="mb-0"><strong>Text & Essentials</strong></p>
                            <small>Primary Text, Icons, Borders</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- E-commerce Components Demo -->
    <div class="row mb-5">
        <div class="col-12">
            <h2 class="fw-bold mb-4">E-commerce Components</h2>
            
            <!-- Buttons -->
            <div class="mb-4">
                <h4 class="mb-3">Action Buttons</h4>
                <div class="d-flex flex-wrap gap-3">
                    <button class="btn btn-primary">
                        <i class="bi bi-cart-plus me-2"></i>Add to Cart
                    </button>
                    <button class="btn btn-secondary">
                        <i class="bi bi-heart me-2"></i>Wishlist
                    </button>
                    <button class="btn btn-outline-primary">
                        <i class="bi bi-eye me-2"></i>Quick View
                    </button>
                    <button class="btn btn-primary btn-lg">
                        <i class="bi bi-credit-card me-2"></i>Buy Now
                    </button>
                </div>
            </div>
            
            <!-- Badges -->
            <div class="mb-4">
                <h4 class="mb-3">Offer & Status Badges</h4>
                <div class="d-flex flex-wrap gap-3">
                    <span class="badge-offer">50% OFF</span>
                    <span class="badge-limited">Limited Time</span>
                    <span class="badge bg-success">In Stock</span>
                    <span class="badge bg-warning text-dark">Low Stock</span>
                    <span class="badge bg-danger">Out of Stock</span>
                </div>
            </div>
            
            <!-- Price Display -->
            <div class="mb-4">
                <h4 class="mb-3">Price Display</h4>
                <div class="d-flex align-items-center gap-3">
                    <span class="price-current">৳4,999</span>
                    <span class="price-original">৳9,999</span>
                    <span class="price-discount">(50% off)</span>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Card Demonstrations -->
    <div class="row mb-5">
        <div class="col-12">
            <h2 class="fw-bold mb-4">Card Styles</h2>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Standard Card</h5>
                </div>
                <div class="card-body">
                    <p class="card-text">This is a standard card with the new color palette applied.</p>
                    <a href="#" class="btn btn-primary">Learn More</a>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card gradient-light-bg">
                <div class="card-body">
                    <h5 class="card-title">Gradient Light Card</h5>
                    <p class="card-text">This card uses the light gradient background.</p>
                    <a href="#" class="btn btn-outline-primary">Explore</a>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card gradient-bg">
                <div class="card-body">
                    <h5 class="card-title">Gradient Card</h5>
                    <p class="card-text">This card uses the primary gradient background.</p>
                    <a href="#" class="btn btn-light">Get Started</a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Alert Demonstrations -->
    <div class="row mb-5">
        <div class="col-12">
            <h2 class="fw-bold mb-4">Alert Styles</h2>
            <div class="alert alert-primary" role="alert">
                <i class="bi bi-info-circle me-2"></i>
                This is a primary alert with the new color scheme.
            </div>
            <div class="alert alert-info" role="alert">
                <i class="bi bi-lightbulb me-2"></i>
                This is an info alert using the light blue color.
            </div>
        </div>
    </div>
    
    <!-- Badge Demonstrations -->
    <div class="row mb-5">
        <div class="col-12">
            <h2 class="fw-bold mb-4">Badge Styles</h2>
            <div class="d-flex flex-wrap gap-2 mb-3">
                <span class="badge bg-primary">Primary Badge</span>
                <span class="badge bg-info">Info Badge</span>
                <span class="badge bg-light">Light Badge</span>
                <span class="badge bg-success">Success Badge</span>
                <span class="badge bg-warning">Warning Badge</span>
                <span class="badge bg-danger">Danger Badge</span>
            </div>
        </div>
    </div>
    
    <!-- Form Demonstrations -->
    <div class="row mb-5">
        <div class="col-md-6">
            <h2 class="fw-bold mb-4">Form Elements</h2>
            <form>
                <div class="mb-3">
                    <label for="demoInput" class="form-label">Sample Input</label>
                    <input type="text" class="form-control" id="demoInput" placeholder="Focus to see the blue accent">
                </div>
                <div class="mb-3">
                    <label for="demoSelect" class="form-label">Sample Select</label>
                    <select class="form-select" id="demoSelect">
                        <option>Choose an option</option>
                        <option>Option 1</option>
                        <option>Option 2</option>
                    </select>
                </div>
                <div class="mb-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="demoCheck">
                        <label class="form-check-label" for="demoCheck">
                            Sample checkbox
                        </label>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Submit Form</button>
            </form>
        </div>
        <div class="col-md-6">
            <h2 class="fw-bold mb-4">Progress & Loading</h2>
            <div class="mb-3">
                <label class="form-label">Progress Bar</label>
                <div class="progress">
                    <div class="progress-bar" role="progressbar" style="width: 75%"></div>
                </div>
            </div>
            <div class="mb-3">
                <button class="btn btn-primary" type="button" disabled>
                    <span class="spinner-border spinner-border-sm me-2" role="status"></span>
                    Loading...
                </button>
            </div>
        </div>
    </div>
    
    <!-- Utility Classes -->
    <div class="row mb-5">
        <div class="col-12">
            <h2 class="fw-bold mb-4">Utility Classes</h2>
            <div class="row g-3">
                <div class="col-md-4">
                    <div class="p-3 drone-shadow rounded">
                        <h6>Drone Shadow</h6>
                        <p class="mb-0">Uses bright blue-tinted shadow</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="p-3 drone-shadow-lg rounded">
                        <h6>Drone Shadow Large</h6>
                        <p class="mb-0">Larger bright blue shadow</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="p-3 border-left-primary clean-card">
                        <h6>Clean Card Style</h6>
                        <p class="mb-0">Clean white card with subtle border</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Typography -->
    <div class="row mb-5">
        <div class="col-12">
            <h2 class="fw-bold mb-4">Typography Colors</h2>
            <p class="text-primary">This is primary text color (#0D47A1)</p>
            <p class="text-secondary">This is secondary text color (#1976D2)</p>
            <p class="text-muted">This is muted text color (#42A5F5)</p>
            <p><a href="#">This is a link with hover effect</a></p>
        </div>
    </div>
    
    <!-- Hero Section Demo -->
    <div class="hero-section rounded mb-5">
        <div class="container text-center position-relative">
            <h1 class="display-4 fw-bold mb-3 text-primary">Hero Section</h1>
            <p class="lead mb-4 text-primary">
                This hero section uses the light gradient background with a subtle grid pattern overlay.
            </p>
            <div class="d-flex gap-3 justify-content-center">
                <button class="btn btn-primary btn-lg">Get Started</button>
                <button class="btn btn-outline-primary btn-lg">Learn More</button>
            </div>
        </div>
    </div>
    
    <!-- Back to Home -->
    <div class="text-center">
        <a href="<?php echo SITE_URL; ?>" class="btn btn-outline-primary">
            <i class="bi bi-arrow-left me-2"></i>
            Back to Home
        </a>
    </div>
</div>

<style>
.color-demo-page .card {
    transition: transform 0.3s ease;
}

.color-demo-page .card:hover {
    transform: translateY(-2px);
}

.progress {
    height: 8px;
    border-radius: 4px;
}
</style>

<?php include 'includes/footer.php'; ?>
