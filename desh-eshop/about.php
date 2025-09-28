<?php
require_once 'config/config.php';

$page_title = 'About Us';
$page_description = 'Learn more about Desh Engineering and our mission to provide quality digital products.';
$body_class = 'about-page';

include 'includes/header.php';
?>

<div class="container py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo SITE_URL; ?>">Home</a></li>
            <li class="breadcrumb-item active">About Us</li>
        </ol>
    </nav>
    
    <!-- Hero Section -->
    <div class="hero-section rounded mb-5">
        <div class="container text-center position-relative">
            <h1 class="display-4 fw-bold mb-3 text-primary">About Desh Engineering</h1>
            <p class="lead text-primary">
                Empowering businesses with premium digital products and innovative solutions.
            </p>
        </div>
    </div>
    
    <!-- About Content -->
    <div class="row mb-5">
        <div class="col-lg-6">
            <h2 class="fw-bold mb-4">Our Story</h2>
            <p class="mb-3">
                Founded with a vision to bridge the gap between innovative technology and practical business solutions, 
                Desh Engineering has been at the forefront of digital product development since our inception.
            </p>
            <p class="mb-3">
                We specialize in creating high-quality digital products that help businesses streamline their operations, 
                enhance their online presence, and achieve their goals more efficiently.
            </p>
            <p class="mb-4">
                Our team of experienced developers, designers, and engineers work tirelessly to ensure that every 
                product we deliver meets the highest standards of quality and functionality.
            </p>
        </div>
        <div class="col-lg-6">
            <img src="<?php echo SITE_URL; ?>/assets/images/about-hero.jpg" 
                 alt="About Desh Engineering" 
                 class="img-fluid rounded shadow"
                 onerror="this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNTAwIiBoZWlnaHQ9IjMwMCIgdmlld0JveD0iMCAwIDUwMCAzMDAiIGZpbGw9Im5vbmUiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+CjxyZWN0IHdpZHRoPSI1MDAiIGhlaWdodD0iMzAwIiBmaWxsPSIjRjhGOUZBIi8+CjxyZWN0IHg9IjEwMCIgeT0iNzUiIHdpZHRoPSIzMDAiIGhlaWdodD0iMTUwIiByeD0iMTAiIGZpbGw9IiM2RjQyQzEiIG9wYWNpdHk9IjAuMSIvPgo8dGV4dCB4PSIyNTAiIHk9IjE2MCIgZm9udC1mYW1pbHk9IkFyaWFsLCBzYW5zLXNlcmlmIiBmb250LXNpemU9IjE4IiBmaWxsPSIjNkY0MkMxIiB0ZXh0LWFuY2hvcj0ibWlkZGxlIj5BYm91dCBVczwvdGV4dD4KPC9zdmc+'">
        </div>
    </div>
    
    <!-- Mission & Vision -->
    <div class="row mb-5">
        <div class="col-lg-6">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body text-center p-4">
                    <div class="mb-3">
                        <i class="bi bi-bullseye display-4 text-primary"></i>
                    </div>
                    <h3 class="fw-bold mb-3">Our Mission</h3>
                    <p class="text-muted">
                        To provide innovative, high-quality digital products that empower businesses to achieve 
                        their goals and drive growth in the digital age.
                    </p>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body text-center p-4">
                    <div class="mb-3">
                        <i class="bi bi-eye display-4 text-primary"></i>
                    </div>
                    <h3 class="fw-bold mb-3">Our Vision</h3>
                    <p class="text-muted">
                        To be the leading provider of digital solutions that transform how businesses operate 
                        and connect with their customers worldwide.
                    </p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Values -->
    <div class="mb-5">
        <h2 class="text-center fw-bold mb-5">Our Values</h2>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="text-center">
                    <div class="mb-3">
                        <i class="bi bi-award display-4 text-primary"></i>
                    </div>
                    <h4 class="fw-bold mb-3">Quality</h4>
                    <p class="text-muted">
                        We never compromise on quality. Every product is thoroughly tested and refined 
                        to meet the highest standards.
                    </p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="text-center">
                    <div class="mb-3">
                        <i class="bi bi-lightbulb display-4 text-primary"></i>
                    </div>
                    <h4 class="fw-bold mb-3">Innovation</h4>
                    <p class="text-muted">
                        We stay ahead of technology trends and continuously innovate to provide 
                        cutting-edge solutions.
                    </p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="text-center">
                    <div class="mb-3">
                        <i class="bi bi-people display-4 text-primary"></i>
                    </div>
                    <h4 class="fw-bold mb-3">Customer Focus</h4>
                    <p class="text-muted">
                        Our customers are at the heart of everything we do. We listen, understand, 
                        and deliver solutions that exceed expectations.
                    </p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Team Section -->
    <div class="mb-5">
        <h2 class="text-center fw-bold mb-5">Meet Our Team</h2>
        <div class="row g-4">
            <div class="col-lg-4 col-md-6">
                <div class="product-card text-center">
                    <div class="card-body p-4">
                        <div class="mb-3">
                            <img src="<?php echo SITE_URL; ?>/assets/images/team-1.jpg" 
                                 alt="Team Member" 
                                 class="rounded-circle"
                                 width="100" height="100"
                                 style="object-fit: cover;"
                                 onerror="this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTAwIiBoZWlnaHQ9IjEwMCIgdmlld0JveD0iMCAwIDEwMCAxMDAiIGZpbGw9Im5vbmUiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PGNpcmNsZSBjeD0iNTAiIGN5PSI1MCIgcj0iNTAiIGZpbGw9IiM2RjQyQzEiLz48cGF0aCBkPSJNNTAgNTBjNS41MiAwIDEwLTQuNDggMTAtMTBzLTQuNDgtMTAtMTAtMTAtMTAgNC40OC0xMCAxMCA0LjQ4IDEwIDEwIDEwem0wIDVjLTYuNjcgMC0yMCAzLjMzLTIwIDEwdjVoNDB2LTVjMC02LjY3LTEzLjMzLTEwLTIwLTEweiIgZmlsbD0id2hpdGUiLz48L3N2Zz4='">
                        </div>
                        <h5 class="fw-bold mb-1">John Doe</h5>
                        <p class="text-primary mb-2">CEO & Founder</p>
                        <p class="text-muted small">
                            Leading the vision and strategy with over 15 years of experience in digital innovation.
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="product-card text-center">
                    <div class="card-body p-4">
                        <div class="mb-3">
                            <img src="<?php echo SITE_URL; ?>/assets/images/team-2.jpg" 
                                 alt="Team Member" 
                                 class="rounded-circle"
                                 width="100" height="100"
                                 style="object-fit: cover;"
                                 onerror="this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTAwIiBoZWlnaHQ9IjEwMCIgdmlld0JveD0iMCAwIDEwMCAxMDAiIGZpbGw9Im5vbmUiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PGNpcmNsZSBjeD0iNTAiIGN5PSI1MCIgcj0iNTAiIGZpbGw9IiM2RjQyQzEiLz48cGF0aCBkPSJNNTAgNTBjNS41MiAwIDEwLTQuNDggMTAtMTBzLTQuNDgtMTAtMTAtMTAtMTAgNC40OC0xMCAxMCA0LjQ4IDEwIDEwIDEwem0wIDVjLTYuNjcgMC0yMCAzLjMzLTIwIDEwdjVoNDB2LTVjMC02LjY3LTEzLjMzLTEwLTIwLTEweiIgZmlsbD0id2hpdGUiLz48L3N2Zz4='">
                        </div>
                        <h5 class="fw-bold mb-1">Jane Smith</h5>
                        <p class="text-primary mb-2">CTO</p>
                        <p class="text-muted small">
                            Driving technical excellence and innovation with expertise in modern technologies.
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="product-card text-center">
                    <div class="card-body p-4">
                        <div class="mb-3">
                            <img src="<?php echo SITE_URL; ?>/assets/images/team-3.jpg" 
                                 alt="Team Member" 
                                 class="rounded-circle"
                                 width="100" height="100"
                                 style="object-fit: cover;"
                                 onerror="this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTAwIiBoZWlnaHQ9IjEwMCIgdmlld0JveD0iMCAwIDEwMCAxMDAiIGZpbGw9Im5vbmUiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PGNpcmNsZSBjeD0iNTAiIGN5PSI1MCIgcj0iNTAiIGZpbGw9IiM2RjQyQzEiLz48cGF0aCBkPSJNNTAgNTBjNS41MiAwIDEwLTQuNDggMTAtMTBzLTQuNDgtMTAtMTAtMTAtMTAgNC40OC0xMCAxMCA0LjQ4IDEwIDEwIDEwem0wIDVjLTYuNjcgMC0yMCAzLjMzLTIwIDEwdjVoNDB2LTVjMC02LjY3LTEzLjMzLTEwLTIwLTEweiIgZmlsbD0id2hpdGUiLz48L3N2Zz4='">
                        </div>
                        <h5 class="fw-bold mb-1">Mike Johnson</h5>
                        <p class="text-primary mb-2">Lead Designer</p>
                        <p class="text-muted small">
                            Creating beautiful and intuitive user experiences that delight our customers.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- CTA Section -->
    <div class="gradient-bg text-white rounded py-5">
        <div class="container text-center">
            <h3 class="fw-bold mb-3">Ready to Get Started?</h3>
            <p class="lead mb-4">
                Discover our range of digital products and take your business to the next level.
            </p>
            <div class="d-flex gap-3 justify-content-center">
                <a href="<?php echo SITE_URL; ?>/products.php" class="btn btn-light btn-lg">
                    <i class="bi bi-grid me-2"></i>
                    Browse Products
                </a>
                <a href="<?php echo SITE_URL; ?>/contact.php" class="btn btn-outline-light btn-lg">
                    <i class="bi bi-envelope me-2"></i>
                    Contact Us
                </a>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
