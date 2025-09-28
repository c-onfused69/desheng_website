<?php
require_once 'config/config.php';

$page_title = 'Privacy Policy';
$page_description = 'Learn how we collect, use, and protect your personal information.';
$body_class = 'privacy-page';

include 'includes/header.php';
?>

<div class="container py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo SITE_URL; ?>">Home</a></li>
            <li class="breadcrumb-item active">Privacy Policy</li>
        </ol>
    </nav>
    
    <!-- Page Header -->
    <div class="text-center mb-5">
        <h1 class="display-4 fw-bold mb-3">Privacy Policy</h1>
        <p class="lead text-muted">
            Your privacy is important to us. This policy explains how we collect, use, and protect your information.
        </p>
        <small class="text-muted">Last updated: <?php echo date('F j, Y'); ?></small>
    </div>
    
    <div class="row">
        <div class="col-lg-9">
            <div class="privacy-content">
                <section class="mb-5">
                    <h2 class="fw-bold mb-3">1. Information We Collect</h2>
                    <p>We collect information you provide directly to us, such as when you:</p>
                    <ul>
                        <li>Create an account or make a purchase</li>
                        <li>Subscribe to our newsletter</li>
                        <li>Contact us for support</li>
                        <li>Participate in surveys or promotions</li>
                    </ul>
                    
                    <h4 class="fw-bold mt-4 mb-2">Personal Information</h4>
                    <p>This may include:</p>
                    <ul>
                        <li>Name and contact information (email, phone, address)</li>
                        <li>Payment information (processed securely by third-party providers)</li>
                        <li>Account credentials and preferences</li>
                        <li>Communication history with our support team</li>
                    </ul>
                    
                    <h4 class="fw-bold mt-4 mb-2">Automatically Collected Information</h4>
                    <p>We may automatically collect certain information about your device and usage:</p>
                    <ul>
                        <li>IP address and browser information</li>
                        <li>Pages visited and time spent on our site</li>
                        <li>Referring websites and search terms</li>
                        <li>Device type and operating system</li>
                    </ul>
                </section>
                
                <section class="mb-5">
                    <h2 class="fw-bold mb-3">2. How We Use Your Information</h2>
                    <p>We use the information we collect to:</p>
                    <ul>
                        <li>Process transactions and deliver digital products</li>
                        <li>Provide customer support and respond to inquiries</li>
                        <li>Send important updates about your account or purchases</li>
                        <li>Improve our website and services</li>
                        <li>Send marketing communications (with your consent)</li>
                        <li>Prevent fraud and ensure security</li>
                        <li>Comply with legal obligations</li>
                    </ul>
                </section>
                
                <section class="mb-5">
                    <h2 class="fw-bold mb-3">3. Information Sharing</h2>
                    <p>We do not sell, trade, or rent your personal information to third parties. We may share your information only in the following circumstances:</p>
                    
                    <h4 class="fw-bold mt-4 mb-2">Service Providers</h4>
                    <p>We work with trusted third-party service providers who help us operate our business, such as:</p>
                    <ul>
                        <li>Payment processors (Stripe, PayPal, Razorpay)</li>
                        <li>Email service providers</li>
                        <li>Web hosting and analytics services</li>
                        <li>Customer support tools</li>
                    </ul>
                    
                    <h4 class="fw-bold mt-4 mb-2">Legal Requirements</h4>
                    <p>We may disclose your information if required by law or to:</p>
                    <ul>
                        <li>Comply with legal processes or government requests</li>
                        <li>Protect our rights, property, or safety</li>
                        <li>Prevent fraud or illegal activities</li>
                        <li>Enforce our terms of service</li>
                    </ul>
                </section>
                
                <section class="mb-5">
                    <h2 class="fw-bold mb-3">4. Data Security</h2>
                    <p>We implement appropriate security measures to protect your personal information:</p>
                    <ul>
                        <li>SSL encryption for data transmission</li>
                        <li>Secure servers and databases</li>
                        <li>Regular security audits and updates</li>
                        <li>Limited access to personal information</li>
                        <li>Employee training on data protection</li>
                    </ul>
                    <p>However, no method of transmission over the internet is 100% secure. While we strive to protect your information, we cannot guarantee absolute security.</p>
                </section>
                
                <section class="mb-5">
                    <h2 class="fw-bold mb-3">5. Cookies and Tracking</h2>
                    <p>We use cookies and similar technologies to:</p>
                    <ul>
                        <li>Remember your preferences and login status</li>
                        <li>Analyze website traffic and usage patterns</li>
                        <li>Provide personalized content and recommendations</li>
                        <li>Improve our website functionality</li>
                    </ul>
                    <p>You can control cookies through your browser settings, but disabling cookies may affect website functionality.</p>
                </section>
                
                <section class="mb-5">
                    <h2 class="fw-bold mb-3">6. Your Rights and Choices</h2>
                    <p>You have the following rights regarding your personal information:</p>
                    
                    <h4 class="fw-bold mt-4 mb-2">Access and Updates</h4>
                    <ul>
                        <li>View and update your account information</li>
                        <li>Request a copy of your personal data</li>
                        <li>Correct inaccurate information</li>
                    </ul>
                    
                    <h4 class="fw-bold mt-4 mb-2">Marketing Communications</h4>
                    <ul>
                        <li>Unsubscribe from marketing emails</li>
                        <li>Opt out of promotional communications</li>
                        <li>Manage notification preferences</li>
                    </ul>
                    
                    <h4 class="fw-bold mt-4 mb-2">Data Deletion</h4>
                    <ul>
                        <li>Request deletion of your account</li>
                        <li>Remove personal information (subject to legal requirements)</li>
                    </ul>
                </section>
                
                <section class="mb-5">
                    <h2 class="fw-bold mb-3">7. Children's Privacy</h2>
                    <p>Our services are not intended for children under 13 years of age. We do not knowingly collect personal information from children under 13. If we become aware that we have collected such information, we will take steps to delete it promptly.</p>
                </section>
                
                <section class="mb-5">
                    <h2 class="fw-bold mb-3">8. International Users</h2>
                    <p>If you are accessing our services from outside the United States, please note that your information may be transferred to, stored, and processed in the United States where our servers are located and our central database is operated.</p>
                </section>
                
                <section class="mb-5">
                    <h2 class="fw-bold mb-3">9. Changes to This Policy</h2>
                    <p>We may update this Privacy Policy from time to time. We will notify you of any changes by:</p>
                    <ul>
                        <li>Posting the new policy on this page</li>
                        <li>Updating the "Last updated" date</li>
                        <li>Sending email notifications for significant changes</li>
                    </ul>
                    <p>Your continued use of our services after any changes constitutes acceptance of the updated policy.</p>
                </section>
                
                <section class="mb-5">
                    <h2 class="fw-bold mb-3">10. Contact Us</h2>
                    <p>If you have any questions about this Privacy Policy or our data practices, please contact us:</p>
                    <div class="contact-info bg-light p-4 rounded">
                        <p class="mb-2"><strong>Email:</strong> <a href="mailto:privacy@deshengineering.com">privacy@deshengineering.com</a></p>
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
                        <a class="nav-link" href="#information-collect">Information We Collect</a>
                        <a class="nav-link" href="#how-we-use">How We Use Information</a>
                        <a class="nav-link" href="#information-sharing">Information Sharing</a>
                        <a class="nav-link" href="#data-security">Data Security</a>
                        <a class="nav-link" href="#cookies">Cookies and Tracking</a>
                        <a class="nav-link" href="#your-rights">Your Rights</a>
                        <a class="nav-link" href="#children">Children's Privacy</a>
                        <a class="nav-link" href="#international">International Users</a>
                        <a class="nav-link" href="#changes">Policy Changes</a>
                        <a class="nav-link" href="#contact">Contact Us</a>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.privacy-content h2 {
    color: var(--primary-color);
    border-bottom: 2px solid var(--primary-color);
    padding-bottom: 0.5rem;
}

.privacy-content h4 {
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
