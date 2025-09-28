<?php
require_once 'config/config.php';

$page_title = 'Support Center';
$page_description = 'Get help and support for your digital products and account.';
$body_class = 'support-page';

include 'includes/header.php';
?>

<div class="container py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo SITE_URL; ?>">Home</a></li>
            <li class="breadcrumb-item active">Support Center</li>
        </ol>
    </nav>
    
    <!-- Page Header -->
    <div class="text-center mb-5">
        <h1 class="display-4 fw-bold mb-3">Support Center</h1>
        <p class="lead text-muted">
            We're here to help! Find answers, get support, and learn how to make the most of our products.
        </p>
    </div>
    
    <!-- Support Options -->
    <div class="row g-4 mb-5">
        <div class="col-lg-4 col-md-6">
            <div class="card h-100 text-center border-0 shadow-sm">
                <div class="card-body p-4">
                    <div class="mb-3">
                        <i class="bi bi-question-circle display-4 text-primary"></i>
                    </div>
                    <h4 class="fw-bold mb-3">FAQ</h4>
                    <p class="text-muted mb-4">
                        Find quick answers to the most commonly asked questions about our products and services.
                    </p>
                    <a href="<?php echo SITE_URL; ?>/faq" class="btn btn-primary">
                        Browse FAQ
                    </a>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4 col-md-6">
            <div class="card h-100 text-center border-0 shadow-sm">
                <div class="card-body p-4">
                    <div class="mb-3">
                        <i class="bi bi-envelope display-4 text-primary"></i>
                    </div>
                    <h4 class="fw-bold mb-3">Contact Support</h4>
                    <p class="text-muted mb-4">
                        Need personalized help? Send us a message and our support team will get back to you.
                    </p>
                    <a href="<?php echo SITE_URL; ?>/contact" class="btn btn-primary">
                        Contact Us
                    </a>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4 col-md-6">
            <div class="card h-100 text-center border-0 shadow-sm">
                <div class="card-body p-4">
                    <div class="mb-3">
                        <i class="bi bi-book display-4 text-primary"></i>
                    </div>
                    <h4 class="fw-bold mb-3">Documentation</h4>
                    <p class="text-muted mb-4">
                        Detailed guides and documentation to help you get the most out of your digital products.
                    </p>
                    <a href="<?php echo SITE_URL; ?>/docs" class="btn btn-primary">
                        View Docs
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Quick Help Topics -->
    <div class="row">
        <div class="col-lg-8">
            <h2 class="fw-bold mb-4">Popular Help Topics</h2>
            
            <div class="accordion" id="helpAccordion">
                <div class="accordion-item">
                    <h3 class="accordion-header">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#help1">
                            How do I download my purchased products?
                        </button>
                    </h3>
                    <div id="help1" class="accordion-collapse collapse show" data-bs-parent="#helpAccordion">
                        <div class="accordion-body">
                            After completing your purchase, you can download your products from your account dashboard. 
                            Go to <strong>My Orders</strong> or <strong>Downloads</strong> section to access your files. 
                            You'll also receive an email with download links immediately after purchase.
                        </div>
                    </div>
                </div>
                
                <div class="accordion-item">
                    <h3 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#help2">
                            What payment methods do you accept?
                        </button>
                    </h3>
                    <div id="help2" class="accordion-collapse collapse" data-bs-parent="#helpAccordion">
                        <div class="accordion-body">
                            We accept all major credit cards (Visa, Mastercard, American Express), PayPal, and Razorpay. 
                            All payments are processed securely with SSL encryption to protect your information.
                        </div>
                    </div>
                </div>
                
                <div class="accordion-item">
                    <h3 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#help3">
                            Can I get a refund if I'm not satisfied?
                        </button>
                    </h3>
                    <div id="help3" class="accordion-collapse collapse" data-bs-parent="#helpAccordion">
                        <div class="accordion-body">
                            Yes! We offer a 30-day money-back guarantee on all digital products. If you're not completely 
                            satisfied with your purchase, contact our support team within 30 days for a full refund.
                        </div>
                    </div>
                </div>
                
                <div class="accordion-item">
                    <h3 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#help4">
                            How many times can I download a product?
                        </button>
                    </h3>
                    <div id="help4" class="accordion-collapse collapse" data-bs-parent="#helpAccordion">
                        <div class="accordion-body">
                            Most products allow up to 5 downloads within 30 days of purchase. This gives you flexibility 
                            to download on multiple devices or re-download if needed. Check individual product pages for 
                            specific download limits.
                        </div>
                    </div>
                </div>
                
                <div class="accordion-item">
                    <h3 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#help5">
                            Do you provide technical support for products?
                        </button>
                    </h3>
                    <div id="help5" class="accordion-collapse collapse" data-bs-parent="#helpAccordion">
                        <div class="accordion-body">
                            Yes, we provide technical support for all our products. You can contact us through our support 
                            ticket system or email. Our team will help you with installation, setup, and any technical 
                            issues you may encounter.
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Contact Info Sidebar -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-headset me-2"></i>
                        Need More Help?
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <h6 class="fw-bold">Email Support</h6>
                        <p class="text-muted mb-1">
                            <i class="bi bi-envelope me-2"></i>
                            <a href="mailto:<?php echo SITE_EMAIL; ?>"><?php echo SITE_EMAIL; ?></a>
                        </p>
                        <small class="text-muted">We typically respond within 24 hours</small>
                    </div>
                    
                    <div class="mb-3">
                        <h6 class="fw-bold">Phone Support</h6>
                        <p class="text-muted mb-1">
                            <i class="bi bi-telephone me-2"></i>
                            <a href="tel:+15551234567">+1 (555) 123-4567</a>
                        </p>
                        <small class="text-muted">Mon-Fri: 9:00 AM - 6:00 PM EST</small>
                    </div>
                    
                    <div class="mb-3">
                        <h6 class="fw-bold">Live Chat</h6>
                        <p class="text-muted mb-2">
                            <i class="bi bi-chat-dots me-2"></i>
                            Available during business hours
                        </p>
                        <button class="btn btn-outline-primary btn-sm w-100" onclick="alert('Live chat feature coming soon!')">
                            Start Chat
                        </button>
                    </div>
                    
                    <hr>
                    
                    <div class="text-center">
                        <h6 class="fw-bold mb-2">Quick Links</h6>
                        <div class="d-grid gap-2">
                            <a href="<?php echo SITE_URL; ?>/orders" class="btn btn-outline-secondary btn-sm">My Orders</a>
                            <a href="<?php echo SITE_URL; ?>/downloads" class="btn btn-outline-secondary btn-sm">Downloads</a>
                            <a href="<?php echo SITE_URL; ?>/profile" class="btn btn-outline-secondary btn-sm">Account Settings</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
