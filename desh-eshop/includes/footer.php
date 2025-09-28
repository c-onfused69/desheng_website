    </main>
    
    <!-- Footer -->
    <footer class="bg-dark text-light py-5 mt-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 col-md-6 mb-4">
                    <h5 class="fw-bold mb-3" style="color: #12A0C6;"><?php echo SITE_NAME; ?></h5>
                    <p class="text-light">Your trusted partner for digital products and engineering solutions. We provide high-quality digital tools and resources to help your business grow.</p>
                    <div class="social-links">
                        <a href="#" class="me-3" style="color: #12A0C6;"><i class="bi bi-facebook fs-5"></i></a>
                        <a href="#" class="me-3" style="color: #12A0C6;"><i class="bi bi-twitter fs-5"></i></a>
                        <a href="#" class="me-3" style="color: #12A0C6;"><i class="bi bi-linkedin fs-5"></i></a>
                        <a href="#" class="me-3" style="color: #12A0C6;"><i class="bi bi-instagram fs-5"></i></a>
                    </div>
                </div>
                
                <div class="col-lg-2 col-md-6 mb-4">
                    <h6 class="fw-bold mb-3" style="color: #12A0C6;">Quick Links</h6>
                    <ul class="list-unstyled">
                        <li><a href="<?php echo SITE_URL; ?>" class="text-dark text-decoration-none">Home</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/products.php" class="text-dark text-decoration-none">Products</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/categories.php" class="text-dark text-decoration-none">Categories</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/about.php" class="text-dark text-decoration-none">About Us</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/contact.php" class="text-dark text-decoration-none">Contact</a></li>
                    </ul>
                </div>
                
                <div class="col-lg-2 col-md-6 mb-4">
                    <h6 class="fw-bold mb-3" style="color: #12A0C6;">Support</h6>
                    <ul class="list-unstyled">
                        <li><a href="<?php echo SITE_URL; ?>/faq.php" class="text-dark text-decoration-none">FAQ</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/support.php" class="text-dark text-decoration-none">Help Center</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/privacy.php" class="text-dark text-decoration-none">Privacy Policy</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/terms.php" class="text-dark text-decoration-none">Terms of Service</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/refund.php" class="text-dark text-decoration-none">Refund Policy</a></li>
                    </ul>
                </div>
                
                <div class="col-lg-4 col-md-6 mb-4">
                    <h6 class="fw-bold mb-3" style="color: #12A0C6;">Newsletter</h6>
                    <p class="text-light">Subscribe to get updates on new products and special offers.</p>
                    <form class="newsletter-form" id="newsletterForm">
                        <div class="input-group">
                            <input type="email" class="form-control" placeholder="Enter your email" required>
                            <button class="btn btn-primary" type="submit">
                                <i class="bi bi-envelope"></i>
                            </button>
                        </div>
                    </form>
                    
                    <div class="mt-4">
                        <h6 class="fw-bold mb-2" style="color: #12A0C6;">Contact Info</h6>
                        <p class="text-light mb-1"><i class="bi bi-envelope me-2" style="color: #12A0C6;"></i><?php echo SITE_EMAIL; ?></p>
                        <p class="text-light mb-1"><i class="bi bi-telephone me-2" style="color: #12A0C6;"></i>+1 (555) 123-4567</p>
                        <p class="text-light"><i class="bi bi-geo-alt me-2" style="color: #12A0C6;"></i>123 Business St, City, State 12345</p>
                    </div>
                </div>
            </div>
            
            <hr class="my-4">
            
            <div class="row align-items-center">
                <div class="col-md-6">
                    <p class="text-light mb-0">&copy; <?php echo date('Y'); ?> <?php echo SITE_NAME; ?>. All rights reserved.</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <div class="payment-methods">
                        <span class="me-3" style="color: #12A0C6;">We Accept:</span>
                        <i class="bi bi-credit-card-2-front fs-4 me-2" style="color: #12A0C6;"></i>
                        <i class="bi bi-paypal fs-4 me-2" style="color: #12A0C6;"></i>
                        <img src="<?php echo SITE_URL; ?>/assets/images/razorpay.png" alt="Razorpay" height="20" class="me-2">
                    </div>
                </div>
            </div>
        </div>
    </footer>
    
    <!-- Mobile Bottom Navigation -->
    <div class="mobile-bottom-nav d-lg-none fixed-bottom bg-white border-top">
        <div class="container-fluid">
            <div class="row text-center">
                <div class="col">
                    <a href="<?php echo SITE_URL; ?>" class="d-block py-2 text-decoration-none <?php echo (basename($_SERVER['PHP_SELF']) == 'index.php' || $_SERVER['REQUEST_URI'] == '/desh-eshop/') ? 'text-primary' : 'text-muted'; ?>">
                        <i class="bi bi-house fs-5 d-block"></i>
                        <small>Home</small>
                    </a>
                </div>
                <div class="col">
                    <a href="<?php echo SITE_URL; ?>/products.php" class="d-block py-2 text-decoration-none <?php echo (strpos($_SERVER['REQUEST_URI'], '/products') !== false) ? 'text-primary' : 'text-muted'; ?>">
                        <i class="bi bi-grid fs-5 d-block"></i>
                        <small>Products</small>
                    </a>
                </div>
                <div class="col">
                    <a href="<?php echo SITE_URL; ?>/cart" class="d-block py-2 text-decoration-none position-relative <?php echo (strpos($_SERVER['REQUEST_URI'], '/cart') !== false) ? 'text-primary' : 'text-muted'; ?>">
                        <i class="bi bi-cart3 fs-5 d-block"></i>
                        <small>Cart</small>
                        <?php if (isLoggedIn() && getCartCount() > 0): ?>
                        <span class="position-absolute top-0 start-50 translate-middle badge rounded-pill bg-danger" style="font-size: 0.5rem;">
                            <?php echo getCartCount(); ?>
                        </span>
                        <?php endif; ?>
                    </a>
                </div>
                <div class="col">
                    <?php if (isLoggedIn()): ?>
                    <a href="<?php echo SITE_URL; ?>/profile" class="d-block py-2 text-decoration-none <?php echo (strpos($_SERVER['REQUEST_URI'], '/profile') !== false || strpos($_SERVER['REQUEST_URI'], '/orders') !== false) ? 'text-primary' : 'text-muted'; ?>">
                        <i class="bi bi-person fs-5 d-block"></i>
                        <small>Profile</small>
                    </a>
                    <?php else: ?>
                    <a href="<?php echo SITE_URL; ?>/login" class="d-block py-2 text-decoration-none <?php echo (strpos($_SERVER['REQUEST_URI'], '/login') !== false) ? 'text-primary' : 'text-muted'; ?>">
                        <i class="bi bi-box-arrow-in-right fs-5 d-block"></i>
                        <small>Login</small>
                    </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Back to Top Button -->
    <button class="btn btn-primary btn-floating position-fixed bottom-0 end-0 m-4 d-none" id="backToTop" style="z-index: 1000;">
        <i class="bi bi-arrow-up"></i>
    </button>
    
    <!-- Loading Overlay -->
    <div class="loading-overlay d-none" id="loadingOverlay">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Material Design Bootstrap JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.4.2/mdb.min.js"></script>
    
    <!-- Custom JS -->
    <script src="<?php echo SITE_URL; ?>/assets/js/app.js"></script>
    
    <!-- Additional scripts -->
    <?php if (isset($additional_scripts)) echo $additional_scripts; ?>
    
    <script>
        // Initialize theme
        document.addEventListener('DOMContentLoaded', function() {
            const savedTheme = localStorage.getItem('theme') || 'light';
            document.documentElement.setAttribute('data-theme', savedTheme);
            updateThemeIcon(savedTheme);
            
            // Update mobile theme toggle
            const mobileToggle = document.getElementById('mobileThemeToggle');
            if (mobileToggle) {
                mobileToggle.checked = savedTheme === 'dark';
            }
        });
        
        function updateThemeIcon(theme) {
            const themeIcon = document.getElementById('themeIcon');
            if (themeIcon) {
                themeIcon.className = theme === 'dark' ? 'bi bi-moon-fill' : 'bi bi-sun-fill';
            }
        }
        
        // Theme toggle functionality
        document.getElementById('themeToggle')?.addEventListener('click', function() {
            const currentTheme = document.documentElement.getAttribute('data-theme');
            const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
            
            document.documentElement.setAttribute('data-theme', newTheme);
            localStorage.setItem('theme', newTheme);
            updateThemeIcon(newTheme);
            
            // Update mobile toggle
            const mobileToggle = document.getElementById('mobileThemeToggle');
            if (mobileToggle) {
                mobileToggle.checked = newTheme === 'dark';
            }
        });
        
        // Mobile theme toggle
        document.getElementById('mobileThemeToggle')?.addEventListener('change', function() {
            const newTheme = this.checked ? 'dark' : 'light';
            document.documentElement.setAttribute('data-theme', newTheme);
            localStorage.setItem('theme', newTheme);
            updateThemeIcon(newTheme);
        });
        
        // Newsletter form
        document.getElementById('newsletterForm')?.addEventListener('submit', function(e) {
            e.preventDefault();
            const email = this.querySelector('input[type="email"]').value;
            
            // Here you would typically send the email to your backend
            alert('Thank you for subscribing! We\'ll keep you updated with our latest products.');
            this.reset();
        });
        
        // Back to top button
        const backToTop = document.getElementById('backToTop');
        if (backToTop) {
            window.addEventListener('scroll', function() {
                if (window.pageYOffset > 300) {
                    backToTop.classList.remove('d-none');
                } else {
                    backToTop.classList.add('d-none');
                }
            });
            
            backToTop.addEventListener('click', function() {
                window.scrollTo({ top: 0, behavior: 'smooth' });
            });
        }
    </script>
</body>
</html>
