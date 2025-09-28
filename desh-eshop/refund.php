<?php
require_once 'config/config.php';

$page_title = 'Refund Policy';
$page_description = 'Learn about our refund policy and how to request a refund for digital products.';
$body_class = 'refund-page';

include 'includes/header.php';
?>

<div class="container py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo SITE_URL; ?>">Home</a></li>
            <li class="breadcrumb-item active">Refund Policy</li>
        </ol>
    </nav>
    
    <!-- Page Header -->
    <div class="text-center mb-5">
        <h1 class="display-4 fw-bold mb-3">Refund Policy</h1>
        <p class="lead text-muted">
            We stand behind our products with a 30-day money-back guarantee.
        </p>
        <small class="text-muted">Last updated: <?php echo date('F j, Y'); ?></small>
    </div>
    
    <div class="row">
        <div class="col-lg-8">
            <!-- 30-Day Guarantee -->
            <div class="card border-0 shadow-sm mb-5">
                <div class="card-body text-center p-5">
                    <div class="mb-4">
                        <i class="bi bi-shield-check display-1 text-success"></i>
                    </div>
                    <h2 class="fw-bold text-success mb-3">30-Day Money-Back Guarantee</h2>
                    <p class="lead mb-4">
                        We're confident you'll love our digital products. If you're not completely satisfied, 
                        we'll refund your money within 30 days of purchase.
                    </p>
                    <a href="<?php echo SITE_URL; ?>/contact" class="btn btn-success btn-lg">
                        <i class="bi bi-envelope me-2"></i>
                        Request Refund
                    </a>
                </div>
            </div>
            
            <!-- Refund Eligibility -->
            <section class="mb-5">
                <h2 class="fw-bold mb-4">Refund Eligibility</h2>
                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="card h-100 border-success">
                            <div class="card-header bg-success text-white">
                                <h5 class="mb-0">
                                    <i class="bi bi-check-circle me-2"></i>
                                    Eligible for Refund
                                </h5>
                            </div>
                            <div class="card-body">
                                <ul class="list-unstyled">
                                    <li class="mb-2"><i class="bi bi-check text-success me-2"></i>Product doesn't work as described</li>
                                    <li class="mb-2"><i class="bi bi-check text-success me-2"></i>Technical issues we cannot resolve</li>
                                    <li class="mb-2"><i class="bi bi-check text-success me-2"></i>Product significantly differs from description</li>
                                    <li class="mb-2"><i class="bi bi-check text-success me-2"></i>Duplicate purchase (accidental)</li>
                                    <li class="mb-2"><i class="bi bi-check text-success me-2"></i>Product is defective or corrupted</li>
                                    <li class="mb-2"><i class="bi bi-check text-success me-2"></i>Billing error or unauthorized charge</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="card h-100 border-danger">
                            <div class="card-header bg-danger text-white">
                                <h5 class="mb-0">
                                    <i class="bi bi-x-circle me-2"></i>
                                    Not Eligible for Refund
                                </h5>
                            </div>
                            <div class="card-body">
                                <ul class="list-unstyled">
                                    <li class="mb-2"><i class="bi bi-x text-danger me-2"></i>Change of mind after 30 days</li>
                                    <li class="mb-2"><i class="bi bi-x text-danger me-2"></i>Lack of technical skills to use product</li>
                                    <li class="mb-2"><i class="bi bi-x text-danger me-2"></i>Product doesn't meet expectations (subjective)</li>
                                    <li class="mb-2"><i class="bi bi-x text-danger me-2"></i>Already used product in completed project</li>
                                    <li class="mb-2"><i class="bi bi-x text-danger me-2"></i>Violation of license terms</li>
                                    <li class="mb-2"><i class="bi bi-x text-danger me-2"></i>Fraudulent or abusive refund requests</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            
            <!-- Refund Process -->
            <section class="mb-5">
                <h2 class="fw-bold mb-4">How to Request a Refund</h2>
                <div class="row g-4">
                    <div class="col-md-3">
                        <div class="text-center">
                            <div class="step-number bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                                <span class="fw-bold fs-4">1</span>
                            </div>
                            <h5 class="fw-bold">Contact Us</h5>
                            <p class="text-muted">Send us an email or use our contact form within 30 days of purchase.</p>
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="text-center">
                            <div class="step-number bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                                <span class="fw-bold fs-4">2</span>
                            </div>
                            <h5 class="fw-bold">Provide Details</h5>
                            <p class="text-muted">Include your order number, email address, and reason for the refund request.</p>
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="text-center">
                            <div class="step-number bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                                <span class="fw-bold fs-4">3</span>
                            </div>
                            <h5 class="fw-bold">Review Process</h5>
                            <p class="text-muted">Our team will review your request and respond within 24-48 hours.</p>
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="text-center">
                            <div class="step-number bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                                <span class="fw-bold fs-4">4</span>
                            </div>
                            <h5 class="fw-bold">Get Refund</h5>
                            <p class="text-muted">If approved, refunds are processed within 5-10 business days.</p>
                        </div>
                    </div>
                </div>
            </section>
            
            <!-- Required Information -->
            <section class="mb-5">
                <h2 class="fw-bold mb-4">Information Required for Refund Request</h2>
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h5 class="fw-bold mb-3">Required Information:</h5>
                                <ul>
                                    <li>Order number or transaction ID</li>
                                    <li>Email address used for purchase</li>
                                    <li>Product name(s) purchased</li>
                                    <li>Purchase date</li>
                                    <li>Detailed reason for refund request</li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <h5 class="fw-bold mb-3">Additional Information (if applicable):</h5>
                                <ul>
                                    <li>Screenshots of technical issues</li>
                                    <li>Error messages encountered</li>
                                    <li>Steps taken to resolve the issue</li>
                                    <li>System specifications</li>
                                    <li>Browser/software version</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            
            <!-- Refund Timeline -->
            <section class="mb-5">
                <h2 class="fw-bold mb-4">Refund Timeline</h2>
                <div class="timeline">
                    <div class="timeline-item">
                        <div class="timeline-marker bg-primary"></div>
                        <div class="timeline-content">
                            <h5 class="fw-bold">Request Submitted</h5>
                            <p class="text-muted mb-0">We acknowledge receipt of your refund request immediately.</p>
                        </div>
                    </div>
                    
                    <div class="timeline-item">
                        <div class="timeline-marker bg-warning"></div>
                        <div class="timeline-content">
                            <h5 class="fw-bold">Under Review (24-48 hours)</h5>
                            <p class="text-muted mb-0">Our team reviews your request and may contact you for additional information.</p>
                        </div>
                    </div>
                    
                    <div class="timeline-item">
                        <div class="timeline-marker bg-info"></div>
                        <div class="timeline-content">
                            <h5 class="fw-bold">Decision Made</h5>
                            <p class="text-muted mb-0">You'll receive an email with our decision and next steps.</p>
                        </div>
                    </div>
                    
                    <div class="timeline-item">
                        <div class="timeline-marker bg-success"></div>
                        <div class="timeline-content">
                            <h5 class="fw-bold">Refund Processed (5-10 business days)</h5>
                            <p class="text-muted mb-0">If approved, the refund is processed to your original payment method.</p>
                        </div>
                    </div>
                </div>
            </section>
            
            <!-- Special Circumstances -->
            <section class="mb-5">
                <h2 class="fw-bold mb-4">Special Circumstances</h2>
                
                <div class="accordion" id="specialCircumstances">
                    <div class="accordion-item">
                        <h3 class="accordion-header">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#partial-refunds">
                                Partial Refunds
                            </button>
                        </h3>
                        <div id="partial-refunds" class="accordion-collapse collapse show" data-bs-parent="#specialCircumstances">
                            <div class="accordion-body">
                                In some cases, we may offer partial refunds for bundle purchases where only some products are defective or unsatisfactory. This is evaluated on a case-by-case basis.
                            </div>
                        </div>
                    </div>
                    
                    <div class="accordion-item">
                        <h3 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#chargebacks">
                                Chargebacks and Disputes
                            </button>
                        </h3>
                        <div id="chargebacks" class="accordion-collapse collapse" data-bs-parent="#specialCircumstances">
                            <div class="accordion-body">
                                Please contact us directly before initiating a chargeback with your bank. Chargebacks can take months to resolve and may result in account suspension. We prefer to resolve issues directly and quickly.
                            </div>
                        </div>
                    </div>
                    
                    <div class="accordion-item">
                        <h3 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#technical-support">
                                Technical Support First
                            </button>
                        </h3>
                        <div id="technical-support" class="accordion-collapse collapse" data-bs-parent="#specialCircumstances">
                            <div class="accordion-body">
                                Before requesting a refund for technical issues, we encourage you to contact our support team. Many issues can be resolved quickly with proper guidance, allowing you to keep and use the product.
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
        
        <!-- Sidebar -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-headset me-2"></i>
                        Need Help?
                    </h5>
                </div>
                <div class="card-body">
                    <p class="mb-3">Have questions about our refund policy or need to request a refund?</p>
                    
                    <div class="d-grid gap-2">
                        <a href="<?php echo SITE_URL; ?>/contact" class="btn btn-primary">
                            <i class="bi bi-envelope me-2"></i>
                            Contact Support
                        </a>
                        <a href="<?php echo SITE_URL; ?>/faq" class="btn btn-outline-primary">
                            <i class="bi bi-question-circle me-2"></i>
                            View FAQ
                        </a>
                    </div>
                    
                    <hr>
                    
                    <div class="contact-info">
                        <h6 class="fw-bold mb-2">Contact Information</h6>
                        <p class="mb-1"><small><i class="bi bi-envelope me-2"></i><?php echo SITE_EMAIL; ?></small></p>
                        <p class="mb-1"><small><i class="bi bi-telephone me-2"></i>+1 (555) 123-4567</small></p>
                        <p class="mb-0"><small><i class="bi bi-clock me-2"></i>Mon-Fri: 9 AM - 6 PM EST</small></p>
                    </div>
                </div>
            </div>
            
            <!-- Quick Stats -->
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <h6 class="fw-bold mb-3">Our Commitment</h6>
                    <div class="row text-center">
                        <div class="col-4">
                            <div class="stat-item">
                                <div class="h4 text-primary mb-1">30</div>
                                <small class="text-muted">Days Guarantee</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="stat-item">
                                <div class="h4 text-primary mb-1">24h</div>
                                <small class="text-muted">Response Time</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="stat-item">
                                <div class="h4 text-primary mb-1">95%</div>
                                <small class="text-muted">Satisfaction Rate</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.step-number {
    font-size: 1.5rem;
}

.timeline {
    position: relative;
    padding-left: 2rem;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 15px;
    top: 0;
    bottom: 0;
    width: 2px;
    background-color: var(--border-color);
}

.timeline-item {
    position: relative;
    margin-bottom: 2rem;
}

.timeline-marker {
    position: absolute;
    left: -2rem;
    top: 0;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    border: 2px solid white;
}

.timeline-content {
    padding-left: 1rem;
}

.stat-item {
    padding: 0.5rem;
}

@media (max-width: 767.98px) {
    .timeline {
        padding-left: 1.5rem;
    }
    
    .timeline-marker {
        left: -1.5rem;
    }
}
</style>

<?php include 'includes/footer.php'; ?>
