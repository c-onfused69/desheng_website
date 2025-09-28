<?php
require_once 'config/config.php';

$page_title = 'Terms of Service';
$page_description = 'Read our terms and conditions for using our digital products and services.';
$body_class = 'terms-page';

include 'includes/header.php';
?>

<div class="container py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo SITE_URL; ?>">Home</a></li>
            <li class="breadcrumb-item active">Terms of Service</li>
        </ol>
    </nav>
    
    <!-- Page Header -->
    <div class="text-center mb-5">
        <h1 class="display-4 fw-bold mb-3">Terms of Service</h1>
        <p class="lead text-muted">
            Please read these terms carefully before using our services.
        </p>
        <small class="text-muted">Last updated: <?php echo date('F j, Y'); ?></small>
    </div>
    
    <div class="row">
        <div class="col-lg-9">
            <div class="terms-content">
                <section class="mb-5">
                    <h2 class="fw-bold mb-3">1. Acceptance of Terms</h2>
                    <p>By accessing and using <?php echo SITE_NAME; ?> ("we," "our," or "us"), you accept and agree to be bound by the terms and provision of this agreement. If you do not agree to abide by the above, please do not use this service.</p>
                </section>
                
                <section class="mb-5">
                    <h2 class="fw-bold mb-3">2. Description of Service</h2>
                    <p><?php echo SITE_NAME; ?> provides digital products including but not limited to:</p>
                    <ul>
                        <li>Web development tools and templates</li>
                        <li>Mobile application resources</li>
                        <li>Graphics and design assets</li>
                        <li>Digital marketing tools</li>
                        <li>Business productivity software</li>
                    </ul>
                    <p>All products are delivered digitally through our platform.</p>
                </section>
                
                <section class="mb-5">
                    <h2 class="fw-bold mb-3">3. User Accounts</h2>
                    <h4 class="fw-bold mt-4 mb-2">Account Registration</h4>
                    <p>To purchase and download products, you must create an account. You agree to:</p>
                    <ul>
                        <li>Provide accurate, current, and complete information</li>
                        <li>Maintain and update your information as needed</li>
                        <li>Keep your login credentials secure and confidential</li>
                        <li>Be responsible for all activities under your account</li>
                        <li>Notify us immediately of any unauthorized use</li>
                    </ul>
                    
                    <h4 class="fw-bold mt-4 mb-2">Account Termination</h4>
                    <p>We reserve the right to terminate accounts that violate these terms or engage in fraudulent activities.</p>
                </section>
                
                <section class="mb-5">
                    <h2 class="fw-bold mb-3">4. Purchases and Payments</h2>
                    <h4 class="fw-bold mt-4 mb-2">Pricing and Payment</h4>
                    <ul>
                        <li>All prices are displayed in BDT (৳) unless otherwise specified</li>
                        <li>Prices are subject to change without notice</li>
                        <li>Payment is required before product delivery</li>
                        <li>We accept major credit cards, PayPal, and Razorpay</li>
                        <li>All transactions are processed securely</li>
                    </ul>
                    
                    <h4 class="fw-bold mt-4 mb-2">Order Processing</h4>
                    <ul>
                        <li>Orders are typically processed immediately upon payment</li>
                        <li>Download links are provided via email and account dashboard</li>
                        <li>We reserve the right to refuse or cancel orders</li>
                    </ul>
                </section>
                
                <section class="mb-5">
                    <h2 class="fw-bold mb-3">5. Digital Product Delivery</h2>
                    <h4 class="fw-bold mt-4 mb-2">Download Access</h4>
                    <ul>
                        <li>Products are available for immediate download after purchase</li>
                        <li>Download links expire after 30 days</li>
                        <li>Maximum of 5 downloads per product unless otherwise specified</li>
                        <li>You are responsible for backing up downloaded files</li>
                    </ul>
                    
                    <h4 class="fw-bold mt-4 mb-2">Technical Requirements</h4>
                    <p>You are responsible for ensuring your system meets the technical requirements for our products. We recommend checking product specifications before purchase.</p>
                </section>
                
                <section class="mb-5">
                    <h2 class="fw-bold mb-3">6. License and Usage Rights</h2>
                    <h4 class="fw-bold mt-4 mb-2">Standard License</h4>
                    <p>Unless otherwise specified, our standard license grants you:</p>
                    <ul>
                        <li>Right to use the product for personal or commercial projects</li>
                        <li>Right to modify and customize the product</li>
                        <li>Right to use in unlimited projects</li>
                    </ul>
                    
                    <h4 class="fw-bold mt-4 mb-2">Restrictions</h4>
                    <p>You may NOT:</p>
                    <ul>
                        <li>Resell, redistribute, or share the original files</li>
                        <li>Claim ownership of the original design</li>
                        <li>Use products for illegal or harmful purposes</li>
                        <li>Remove or modify copyright notices</li>
                    </ul>
                </section>
                
                <section class="mb-5">
                    <h2 class="fw-bold mb-3">7. Refund Policy</h2>
                    <h4 class="fw-bold mt-4 mb-2">30-Day Money-Back Guarantee</h4>
                    <p>We offer a 30-day money-back guarantee on all digital products. Refunds may be requested if:</p>
                    <ul>
                        <li>The product doesn't work as described</li>
                        <li>You experience technical issues we cannot resolve</li>
                        <li>The product is significantly different from the description</li>
                    </ul>
                    
                    <h4 class="fw-bold mt-4 mb-2">Refund Process</h4>
                    <ul>
                        <li>Contact our support team within 30 days of purchase</li>
                        <li>Provide order details and reason for refund</li>
                        <li>Refunds are processed within 5-10 business days</li>
                        <li>Refunds are issued to the original payment method</li>
                    </ul>
                </section>
                
                <section class="mb-5">
                    <h2 class="fw-bold mb-3">8. Intellectual Property</h2>
                    <p>All content on our website, including but not limited to text, graphics, logos, images, and software, is the property of <?php echo SITE_NAME; ?> or its licensors and is protected by copyright and other intellectual property laws.</p>
                    
                    <h4 class="fw-bold mt-4 mb-2">User Content</h4>
                    <p>By submitting content to our website (reviews, comments, etc.), you grant us a non-exclusive, royalty-free license to use, modify, and display such content.</p>
                </section>
                
                <section class="mb-5">
                    <h2 class="fw-bold mb-3">9. Prohibited Uses</h2>
                    <p>You may not use our service:</p>
                    <ul>
                        <li>For any unlawful purpose or to solicit others to perform unlawful acts</li>
                        <li>To violate any international, federal, provincial, or state regulations, rules, laws, or local ordinances</li>
                        <li>To infringe upon or violate our intellectual property rights or the intellectual property rights of others</li>
                        <li>To harass, abuse, insult, harm, defame, slander, disparage, intimidate, or discriminate</li>
                        <li>To submit false or misleading information</li>
                        <li>To upload or transmit viruses or any other type of malicious code</li>
                        <li>To spam, phish, pharm, pretext, spider, crawl, or scrape</li>
                        <li>For any obscene or immoral purpose</li>
                    </ul>
                </section>
                
                <section class="mb-5">
                    <h2 class="fw-bold mb-3">10. Disclaimers and Limitation of Liability</h2>
                    <h4 class="fw-bold mt-4 mb-2">Disclaimer of Warranties</h4>
                    <p>Our products are provided "as is" without any warranties, express or implied. We do not warrant that our products will be error-free or uninterrupted.</p>
                    
                    <h4 class="fw-bold mt-4 mb-2">Limitation of Liability</h4>
                    <p>In no event shall <?php echo SITE_NAME; ?> be liable for any indirect, incidental, special, consequential, or punitive damages, including without limitation, loss of profits, data, use, goodwill, or other intangible losses.</p>
                </section>
                
                <section class="mb-5">
                    <h2 class="fw-bold mb-3">11. Privacy Policy</h2>
                    <p>Your privacy is important to us. Please review our Privacy Policy, which also governs your use of the service, to understand our practices.</p>
                </section>
                
                <section class="mb-5">
                    <h2 class="fw-bold mb-3">12. Changes to Terms</h2>
                    <p>We reserve the right to modify these terms at any time. Changes will be effective immediately upon posting. Your continued use of the service after changes are posted constitutes acceptance of the new terms.</p>
                </section>
                
                <section class="mb-5">
                    <h2 class="fw-bold mb-3">13. Governing Law</h2>
                    <p>These terms shall be governed by and construed in accordance with the laws of the State in which <?php echo SITE_NAME; ?> is incorporated, without regard to its conflict of law provisions.</p>
                </section>
                
                <section class="mb-5">
                    <h2 class="fw-bold mb-3">14. Contact Information</h2>
                    <p>If you have any questions about these Terms of Service, please contact us:</p>
                    <div class="contact-info bg-light p-4 rounded">
                        <p class="mb-2"><strong>Email:</strong> <a href="mailto:<?php echo SITE_EMAIL; ?>"><?php echo SITE_EMAIL; ?></a></p>
                        <p class="mb-2"><strong>Phone:</strong> <a href="tel:+15551234567">+1 (555) 123-4567</a></p>
                        <p class="mb-0"><strong>Address:</strong> 123 Business Street, Suite 100, City, State 12345</p>
                    </div>
                </section>
            </div>
        </div>
        
        <!-- Table of Contents Sidebar -->
        <div class="col-lg-3">
            <div class="card sticky-top" style="top: 100px;">
                <div class="card-header">
                    <h6 class="mb-0">Table of Contents</h6>
                </div>
                <div class="card-body p-0">
                    <nav class="nav nav-pills flex-column">
                        <a class="nav-link" href="#acceptance">Acceptance of Terms</a>
                        <a class="nav-link" href="#service">Description of Service</a>
                        <a class="nav-link" href="#accounts">User Accounts</a>
                        <a class="nav-link" href="#payments">Purchases & Payments</a>
                        <a class="nav-link" href="#delivery">Product Delivery</a>
                        <a class="nav-link" href="#license">License & Usage</a>
                        <a class="nav-link" href="#refunds">Refund Policy</a>
                        <a class="nav-link" href="#intellectual">Intellectual Property</a>
                        <a class="nav-link" href="#prohibited">Prohibited Uses</a>
                        <a class="nav-link" href="#liability">Limitation of Liability</a>
                        <a class="nav-link" href="#privacy">Privacy Policy</a>
                        <a class="nav-link" href="#changes">Changes to Terms</a>
                        <a class="nav-link" href="#governing">Governing Law</a>
                        <a class="nav-link" href="#contact">Contact</a>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.terms-content h2 {
    color: var(--primary-color);
    border-bottom: 2px solid var(--primary-color);
    padding-bottom: 0.5rem;
}

.terms-content h4 {
    color: var(--primary-color);
}

.contact-info {
    border-left: 4px solid var(--primary-color);
}

@media (max-width: 991.98px) {
    .sticky-top {
        position: static !important;
        margin-bottom: 2rem;
    }
}
</style>

<?php include 'includes/footer.php'; ?>
