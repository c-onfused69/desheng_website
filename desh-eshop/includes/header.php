<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . ' - ' . SITE_NAME : SITE_NAME; ?></title>
    <meta name="description" content="<?php echo isset($page_description) ? $page_description : 'Digital products and engineering solutions by Desh Engineering'; ?>">
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="<?php echo SITE_URL; ?>/assets/images/favicon.ico">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    
    <!-- Material Design Bootstrap -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.4.2/mdb.min.css" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link href="<?php echo SITE_URL; ?>/assets/css/style.css" rel="stylesheet">
    
    <!-- PWA Meta Tags -->
    <meta name="theme-color" content="#27367B">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="<?php echo SITE_NAME; ?>">
    
    <!-- Open Graph Meta Tags -->
    <meta property="og:title" content="<?php echo isset($page_title) ? $page_title : SITE_NAME; ?>">
    <meta property="og:description" content="<?php echo isset($page_description) ? $page_description : 'Digital products and engineering solutions'; ?>">
    <meta property="og:image" content="<?php echo SITE_URL; ?>/assets/images/og-image.jpg">
    <meta property="og:url" content="<?php echo SITE_URL . $_SERVER['REQUEST_URI']; ?>">
    <meta property="og:type" content="website">
    
    <!-- Additional head content -->
    <?php if (isset($additional_head)) echo $additional_head; ?>
</head>
<body class="<?php echo isset($body_class) ? $body_class : ''; ?>">
    
    <!-- Desktop Navigation -->
    <nav class="navbar navbar-expand-lg d-none d-lg-block shadow-sm border-bottom">
        <div class="container">
            <a class="navbar-brand fw-bold" href="<?php echo SITE_URL; ?>">
                <img src="<?php echo SITE_URL; ?>/assets/images/logo.svg" 
                     alt="<?php echo SITE_NAME; ?>" 
                     height="40" 
                     class="me-2">
            </a>
            
            <div class="navbar-nav me-auto">
                <a class="nav-link" href="<?php echo SITE_URL; ?>">Home</a>
                <a class="nav-link" href="<?php echo SITE_URL; ?>/products.php">Products</a>
                <a class="nav-link" href="<?php echo SITE_URL; ?>/categories.php">Categories</a>
                <a class="nav-link" href="<?php echo SITE_URL; ?>/about.php">About</a>
                <a class="nav-link" href="<?php echo SITE_URL; ?>/contact.php">Contact</a>
            </div>
            
            <div class="d-flex align-items-center">
                <!-- Search -->
                <form class="d-flex me-3" action="<?php echo SITE_URL; ?>/search" method="GET">
                    <div class="input-group">
                        <input class="form-control" type="search" name="q" placeholder="Search products..." value="<?php echo isset($_GET['q']) ? htmlspecialchars($_GET['q']) : ''; ?>">
                        <button class="btn btn-outline-primary" type="submit">
                            <i class="bi bi-search"></i>
                        </button>
                    </div>
                </form>
                
                <!-- Theme Toggle -->
                <button class="btn btn-outline-secondary me-2" id="themeToggle" title="Toggle theme">
                    <i class="bi bi-sun-fill" id="themeIcon"></i>
                </button>
                
                <!-- Cart -->
                <?php if (isLoggedIn()): ?>
                <a href="<?php echo SITE_URL; ?>/cart" class="btn btn-outline-primary me-2 position-relative">
                    <i class="bi bi-cart3"></i>
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" id="cartCount">
                        <?php echo getCartCount(); ?>
                    </span>
                </a>
                <?php endif; ?>
                
                <!-- User Menu -->
                <?php if (isLoggedIn()): ?>
                    <?php $current_user = getCurrentUser(); ?>
                    <div class="dropdown">
                        <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle me-1"></i>
                            <?php echo htmlspecialchars($current_user['name']); ?>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="<?php echo SITE_URL; ?>/profile"><i class="bi bi-person me-2"></i>Profile</a></li>
                            <li><a class="dropdown-item" href="<?php echo SITE_URL; ?>/orders"><i class="bi bi-bag me-2"></i>My Orders</a></li>
                            <li><a class="dropdown-item" href="<?php echo SITE_URL; ?>/downloads"><i class="bi bi-download me-2"></i>Downloads</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="<?php echo SITE_URL; ?>/logout"><i class="bi bi-box-arrow-right me-2"></i>Logout</a></li>
                        </ul>
                    </div>
                <?php else: ?>
                    <a href="<?php echo SITE_URL; ?>/login" class="btn btn-outline-primary me-2">Login</a>
                    <a href="<?php echo SITE_URL; ?>/signup" class="btn btn-primary">Sign Up</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>
    
    <!-- Mobile App Bar -->
    <div class="mobile-appbar d-lg-none border-bottom">
        <div class="container-fluid px-3 py-2">
            <div class="d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center">
                    <button class="btn btn-link text-white p-0 me-3" data-bs-toggle="offcanvas" data-bs-target="#mobileMenu">
                        <i class="bi bi-list fs-4"></i>
                    </button>
                    <a href="<?php echo SITE_URL; ?>" class="text-white text-decoration-none fw-bold">
                        <?php echo SITE_NAME; ?>
                    </a>
                </div>
                
                <div class="d-flex align-items-center">
                    <button class="btn btn-link text-white p-0 me-2" data-bs-toggle="modal" data-bs-target="#searchModal">
                        <i class="bi bi-search fs-5"></i>
                    </button>
                    <?php if (isLoggedIn()): ?>
                    <a href="<?php echo SITE_URL; ?>/cart" class="btn btn-link text-white p-0 position-relative">
                        <i class="bi bi-cart3 fs-5"></i>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.6rem;" id="mobileCartCount">
                            <?php echo getCartCount(); ?>
                        </span>
                    </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Mobile Side Menu -->
    <div class="offcanvas offcanvas-start" tabindex="-1" id="mobileMenu">
        <div class="offcanvas-header bg-primary text-white">
            <h5 class="offcanvas-title"><?php echo SITE_NAME; ?></h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"></button>
        </div>
        <div class="offcanvas-body p-0">
            <div class="list-group list-group-flush">
                <?php if (isLoggedIn()): ?>
                    <?php $current_user = getCurrentUser(); ?>
                    <div class="list-group-item bg-light">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-person-circle fs-3 me-3 text-primary"></i>
                            <div>
                                <div class="fw-bold"><?php echo htmlspecialchars($current_user['name']); ?></div>
                                <small class="text-muted"><?php echo htmlspecialchars($current_user['email']); ?></small>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
                
                <a href="<?php echo SITE_URL; ?>" class="list-group-item list-group-item-action">
                    <i class="bi bi-house me-3"></i>Home
                </a>
                <a href="<?php echo SITE_URL; ?>/products.php" class="list-group-item list-group-item-action">
                    <i class="bi bi-grid me-3"></i>Products
                </a>
                <a href="<?php echo SITE_URL; ?>/categories.php" class="list-group-item list-group-item-action">
                    <i class="bi bi-tags me-3"></i>Categories
                </a>
                <a href="<?php echo SITE_URL; ?>/about.php" class="list-group-item list-group-item-action">
                    <i class="bi bi-info-circle me-3"></i>About Us
                </a>
                
                <?php if (isLoggedIn()): ?>
                    <a href="<?php echo SITE_URL; ?>/profile" class="list-group-item list-group-item-action">
                        <i class="bi bi-person me-3"></i>Profile
                    </a>
                    <a href="<?php echo SITE_URL; ?>/orders" class="list-group-item list-group-item-action">
                        <i class="bi bi-bag me-3"></i>My Orders
                    </a>
                    <a href="<?php echo SITE_URL; ?>/downloads" class="list-group-item list-group-item-action">
                        <i class="bi bi-download me-3"></i>Downloads
                    </a>
                <?php endif; ?>
                
                <a href="<?php echo SITE_URL; ?>/contact.php" class="list-group-item list-group-item-action">
                    <i class="bi bi-envelope me-3"></i>Contact
                </a>
                <a href="<?php echo SITE_URL; ?>/faq.php" class="list-group-item list-group-item-action">
                    <i class="bi bi-question-circle me-3"></i>FAQ
                </a>
                
                <div class="list-group-item">
                    <div class="d-flex align-items-center justify-content-between">
                        <span><i class="bi bi-moon me-3"></i>Dark Mode</span>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="mobileThemeToggle">
                        </div>
                    </div>
                </div>
                
                <?php if (isLoggedIn()): ?>
                    <a href="<?php echo SITE_URL; ?>/logout" class="list-group-item list-group-item-action text-danger">
                        <i class="bi bi-box-arrow-right me-3"></i>Logout
                    </a>
                <?php else: ?>
                    <a href="<?php echo SITE_URL; ?>/login" class="list-group-item list-group-item-action">
                        <i class="bi bi-box-arrow-in-right me-3"></i>Login
                    </a>
                    <a href="<?php echo SITE_URL; ?>/signup" class="list-group-item list-group-item-action">
                        <i class="bi bi-person-plus me-3"></i>Sign Up
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Mobile Search Modal -->
    <div class="modal fade" id="searchModal" tabindex="-1">
        <div class="modal-dialog modal-fullscreen-sm-down">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Search Products</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form action="<?php echo SITE_URL; ?>/search" method="GET">
                        <div class="input-group mb-3">
                            <input type="search" class="form-control form-control-lg" name="q" placeholder="What are you looking for?" autofocus>
                            <button class="btn btn-primary" type="submit">
                                <i class="bi bi-search"></i>
                            </button>
                        </div>
                    </form>
                    
                    <!-- Popular searches or recent searches can go here -->
                    <div class="mt-4">
                        <h6 class="text-muted mb-3">Popular Categories</h6>
                        <div class="d-flex flex-wrap gap-2">
                            <a href="<?php echo SITE_URL; ?>/products?category=web-development" class="btn btn-outline-primary btn-sm">Web Development</a>
                            <a href="<?php echo SITE_URL; ?>/products?category=mobile-apps" class="btn btn-outline-primary btn-sm">Mobile Apps</a>
                            <a href="<?php echo SITE_URL; ?>/products?category=graphics-design" class="btn btn-outline-primary btn-sm">Graphics & Design</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Main Content -->
    <main class="main-content <?php echo isset($main_class) ? $main_class : ''; ?>">
        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle me-2"></i>
                <?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['error_message'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle me-2"></i>
                <?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
