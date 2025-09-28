<?php
require_once 'config/config.php';

$page_title = 'Contact Us';
$page_description = 'Get in touch with our team for support, questions, or custom solutions.';
$body_class = 'contact-page';

$error_message = '';
$success_message = '';

// Handle contact form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize($_POST['name'] ?? '');
    $email = sanitize($_POST['email'] ?? '');
    $subject = sanitize($_POST['subject'] ?? '');
    $message = sanitize($_POST['message'] ?? '');
    
    if (empty($name) || empty($email) || empty($subject) || empty($message)) {
        $error_message = 'Please fill in all required fields.';
    } elseif (!validateEmail($email)) {
        $error_message = 'Please enter a valid email address.';
    } else {
        try {
            $db = getDB();
            
            // Insert support ticket
            $stmt = $db->prepare("
                INSERT INTO support_tickets (name, email, subject, message, status, priority, created_at) 
                VALUES (?, ?, ?, ?, 'open', 'medium', NOW())
            ");
            
            if ($stmt->execute([$name, $email, $subject, $message])) {
                // Send notification email to admin
                $admin_subject = "New Contact Form Submission - " . $subject;
                $admin_message = "
                    <h3>New Contact Form Submission</h3>
                    <p><strong>Name:</strong> {$name}</p>
                    <p><strong>Email:</strong> {$email}</p>
                    <p><strong>Subject:</strong> {$subject}</p>
                    <p><strong>Message:</strong></p>
                    <p>" . nl2br(htmlspecialchars($message)) . "</p>
                    <p><strong>Submitted:</strong> " . date('F j, Y g:i A') . "</p>
                ";
                
                sendEmail(SITE_EMAIL, $admin_subject, $admin_message, true);
                
                // Send confirmation email to user
                $user_subject = "Thank you for contacting " . SITE_NAME;
                $user_message = "
                    <h3>Thank you for your message!</h3>
                    <p>Dear {$name},</p>
                    <p>We have received your message and will get back to you as soon as possible.</p>
                    <p><strong>Your message:</strong></p>
                    <p>" . nl2br(htmlspecialchars($message)) . "</p>
                    <p>Best regards,<br>The " . SITE_NAME . " Team</p>
                ";
                
                sendEmail($email, $user_subject, $user_message, true);
                
                $success_message = 'Thank you for your message! We will get back to you soon.';
                
                // Clear form data
                $name = $email = $subject = $message = '';
            } else {
                $error_message = 'Failed to send message. Please try again.';
            }
        } catch (Exception $e) {
            error_log("Contact form error: " . $e->getMessage());
            $error_message = 'An error occurred. Please try again.';
        }
    }
}

include 'includes/header.php';
?>

<div class="container py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo SITE_URL; ?>">Home</a></li>
            <li class="breadcrumb-item active">Contact</li>
        </ol>
    </nav>
    
    <!-- Page Header -->
    <div class="text-center mb-5">
        <h1 class="display-4 fw-bold mb-3">Contact Us</h1>
        <p class="lead text-muted">
            We're here to help! Get in touch with our team for support, questions, or custom solutions.
        </p>
    </div>
    
    <div class="row">
        <!-- Contact Form -->
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h4 class="mb-0">
                        <i class="bi bi-envelope me-2"></i>
                        Send us a Message
                    </h4>
                </div>
                <div class="card-body">
                    <?php if ($error_message): ?>
                        <div class="alert alert-danger">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            <?php echo $error_message; ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($success_message): ?>
                        <div class="alert alert-success">
                            <i class="bi bi-check-circle me-2"></i>
                            <?php echo $success_message; ?>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST" class="needs-validation" novalidate>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">Full Name *</label>
                                <input type="text" 
                                       class="form-control" 
                                       id="name" 
                                       name="name" 
                                       value="<?php echo htmlspecialchars($name ?? ''); ?>"
                                       required>
                                <div class="invalid-feedback">
                                    Please enter your full name.
                                </div>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Email Address *</label>
                                <input type="email" 
                                       class="form-control" 
                                       id="email" 
                                       name="email" 
                                       value="<?php echo htmlspecialchars($email ?? ''); ?>"
                                       required>
                                <div class="invalid-feedback">
                                    Please enter a valid email address.
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="subject" class="form-label">Subject *</label>
                            <select class="form-select" id="subject" name="subject" required>
                                <option value="">Choose a subject...</option>
                                <option value="General Inquiry" <?php echo (isset($subject) && $subject === 'General Inquiry') ? 'selected' : ''; ?>>General Inquiry</option>
                                <option value="Technical Support" <?php echo (isset($subject) && $subject === 'Technical Support') ? 'selected' : ''; ?>>Technical Support</option>
                                <option value="Billing Question" <?php echo (isset($subject) && $subject === 'Billing Question') ? 'selected' : ''; ?>>Billing Question</option>
                                <option value="Product Request" <?php echo (isset($subject) && $subject === 'Product Request') ? 'selected' : ''; ?>>Product Request</option>
                                <option value="Partnership" <?php echo (isset($subject) && $subject === 'Partnership') ? 'selected' : ''; ?>>Partnership</option>
                                <option value="Other" <?php echo (isset($subject) && $subject === 'Other') ? 'selected' : ''; ?>>Other</option>
                            </select>
                            <div class="invalid-feedback">
                                Please select a subject.
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="message" class="form-label">Message *</label>
                            <textarea class="form-control" 
                                      id="message" 
                                      name="message" 
                                      rows="6" 
                                      placeholder="Please describe your inquiry in detail..."
                                      required><?php echo htmlspecialchars($message ?? ''); ?></textarea>
                            <div class="invalid-feedback">
                                Please enter your message.
                            </div>
                        </div>
                        
                        <div class="text-end">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="bi bi-send me-2"></i>
                                Send Message
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Contact Information -->
        <div class="col-lg-4">
            <!-- Contact Details -->
            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-info-circle me-2"></i>
                        Contact Information
                    </h5>
                </div>
                <div class="card-body">
                    <div class="contact-item mb-3">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-envelope-fill text-primary me-3 fs-4"></i>
                            <div>
                                <div class="fw-bold">Email</div>
                                <a href="mailto:<?php echo SITE_EMAIL; ?>" class="text-decoration-none">
                                    <?php echo SITE_EMAIL; ?>
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    <div class="contact-item mb-3">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-telephone-fill text-primary me-3 fs-4"></i>
                            <div>
                                <div class="fw-bold">Phone</div>
                                <a href="tel:+15551234567" class="text-decoration-none">
                                    +1 (555) 123-4567
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    <div class="contact-item mb-3">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-geo-alt-fill text-primary me-3 fs-4"></i>
                            <div>
                                <div class="fw-bold">Address</div>
                                <div class="text-muted">
                                    123 Business Street<br>
                                    Suite 100<br>
                                    City, State 12345
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="contact-item">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-clock-fill text-primary me-3 fs-4"></i>
                            <div>
                                <div class="fw-bold">Business Hours</div>
                                <div class="text-muted">
                                    Monday - Friday: 9:00 AM - 6:00 PM<br>
                                    Saturday: 10:00 AM - 4:00 PM<br>
                                    Sunday: Closed
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Quick Links -->
            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-link-45deg me-2"></i>
                        Quick Links
                    </h5>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        <a href="<?php echo SITE_URL; ?>/faq" class="list-group-item list-group-item-action border-0 px-0">
                            <i class="bi bi-question-circle me-2"></i>
                            Frequently Asked Questions
                        </a>
                        <a href="<?php echo SITE_URL; ?>/support" class="list-group-item list-group-item-action border-0 px-0">
                            <i class="bi bi-headset me-2"></i>
                            Support Center
                        </a>
                        <a href="<?php echo SITE_URL; ?>/products" class="list-group-item list-group-item-action border-0 px-0">
                            <i class="bi bi-grid me-2"></i>
                            Browse Products
                        </a>
                        <a href="<?php echo SITE_URL; ?>/about" class="list-group-item list-group-item-action border-0 px-0">
                            <i class="bi bi-info-circle me-2"></i>
                            About Us
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Social Media -->
            <div class="card shadow-sm">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-share me-2"></i>
                        Follow Us
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-flex gap-3">
                        <a href="#" class="btn btn-outline-primary btn-sm">
                            <i class="bi bi-facebook"></i>
                        </a>
                        <a href="#" class="btn btn-outline-info btn-sm">
                            <i class="bi bi-twitter"></i>
                        </a>
                        <a href="#" class="btn btn-outline-primary btn-sm">
                            <i class="bi bi-linkedin"></i>
                        </a>
                        <a href="#" class="btn btn-outline-danger btn-sm">
                            <i class="bi bi-instagram"></i>
                        </a>
                        <a href="#" class="btn btn-outline-dark btn-sm">
                            <i class="bi bi-github"></i>
                        </a>
                    </div>
                    <small class="text-muted mt-2 d-block">
                        Stay updated with our latest products and news!
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Form validation
(function() {
    'use strict';
    window.addEventListener('load', function() {
        var forms = document.getElementsByClassName('needs-validation');
        var validation = Array.prototype.filter.call(forms, function(form) {
            form.addEventListener('submit', function(event) {
                if (form.checkValidity() === false) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add('was-validated');
            }, false);
        });
    }, false);
})();
</script>

<?php include 'includes/footer.php'; ?>
