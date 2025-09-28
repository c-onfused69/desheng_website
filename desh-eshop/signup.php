<?php
require_once 'config/config.php';

// Redirect if already logged in
if (isLoggedIn()) {
    redirect(SITE_URL . '/profile');
}

$page_title = 'Sign Up';
$page_description = 'Create your account to access premium digital products and start downloading instantly.';
$body_class = 'auth-page';

$error_message = '';
$success_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize($_POST['name'] ?? '');
    $email = sanitize($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $agree_terms = isset($_POST['agree_terms']);
    
    // Validation
    if (empty($name) || empty($email) || empty($password) || empty($confirm_password)) {
        $error_message = 'Please fill in all required fields.';
    } elseif (!validateEmail($email)) {
        $error_message = 'Please enter a valid email address.';
    } elseif (strlen($password) < 8) {
        $error_message = 'Password must be at least 8 characters long.';
    } elseif ($password !== $confirm_password) {
        $error_message = 'Passwords do not match.';
    } elseif (!$agree_terms) {
        $error_message = 'Please agree to the Terms of Service and Privacy Policy.';
    } else {
        try {
            $db = getDB();
            
            // Check if email already exists
            $stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                $error_message = 'An account with this email address already exists.';
            } else {
                // Create new user
                $hashed_password = hashPassword($password);
                $verification_token = generateToken();
                
                $stmt = $db->prepare("
                    INSERT INTO users (name, email, password, email_verification_token, created_at) 
                    VALUES (?, ?, ?, ?, NOW())
                ");
                
                if ($stmt->execute([$name, $email, $hashed_password, $verification_token])) {
                    $user_id = $db->lastInsertId();
                    
                    // Send verification email (optional)
                    $verification_link = SITE_URL . "/verify-email?token=" . $verification_token;
                    $email_subject = "Welcome to " . SITE_NAME . " - Verify Your Email";
                    $email_body = "
                        <h2>Welcome to " . SITE_NAME . "!</h2>
                        <p>Thank you for creating an account. Please click the link below to verify your email address:</p>
                        <p><a href='{$verification_link}' style='background: #6f42c1; color: white; padding: 12px 24px; text-decoration: none; border-radius: 5px;'>Verify Email Address</a></p>
                        <p>If you didn't create this account, please ignore this email.</p>
                        <p>Best regards,<br>The " . SITE_NAME . " Team</p>
                    ";
                    
                    sendEmail($email, $email_subject, $email_body, true);
                    
                    // Auto login the user
                    $_SESSION['user_id'] = $user_id;
                    $_SESSION['user_name'] = $name;
                    $_SESSION['user_email'] = $email;
                    
                    $_SESSION['success_message'] = 'Account created successfully! Welcome to ' . SITE_NAME . '.';
                    redirect(SITE_URL . '/profile.php');
                } else {
                    $error_message = 'Failed to create account. Please try again.';
                }
            }
        } catch (Exception $e) {
            error_log("Signup error: " . $e->getMessage());
            $error_message = 'An error occurred. Please try again.';
        }
    }
}

include 'includes/header.php';
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-6 col-md-8">
            <div class="product-card ecommerce-shadow-lg">
                <div class="card-body p-5">
                    <div class="text-center mb-4">
                        <h2 class="fw-bold text-primary">Create Account</h2>
                        <p class="text-muted">Join thousands of satisfied customers</p>
                    </div>
                    
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
                        <div class="mb-3">
                            <label for="name" class="form-label">Full Name</label>
                            <input type="text" 
                                   class="form-control form-control-lg" 
                                   id="name" 
                                   name="name" 
                                   value="<?php echo htmlspecialchars($name ?? ''); ?>"
                                   required>
                            <div class="invalid-feedback">
                                Please enter your full name.
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="email" class="form-label">Email Address</label>
                            <input type="email" 
                                   class="form-control form-control-lg" 
                                   id="email" 
                                   name="email" 
                                   value="<?php echo htmlspecialchars($email ?? ''); ?>"
                                   required>
                            <div class="invalid-feedback">
                                Please enter a valid email address.
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <div class="input-group">
                                <input type="password" 
                                       class="form-control form-control-lg" 
                                       id="password" 
                                       name="password" 
                                       minlength="8"
                                       required>
                                <button class="btn btn-outline-secondary" 
                                        type="button" 
                                        id="togglePassword">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                            <div class="password-strength mt-1"></div>
                            <div class="invalid-feedback">
                                Password must be at least 8 characters long.
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="confirm_password" class="form-label">Confirm Password</label>
                            <input type="password" 
                                   class="form-control form-control-lg" 
                                   id="confirm_password" 
                                   name="confirm_password" 
                                   required>
                            <div class="invalid-feedback" id="passwordMismatch">
                                Passwords do not match.
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" 
                                       type="checkbox" 
                                       id="agree_terms" 
                                       name="agree_terms" 
                                       required>
                                <label class="form-check-label" for="agree_terms">
                                    I agree to the 
                                    <a href="<?php echo SITE_URL; ?>/terms.php" class="text-decoration-none" target="_blank">Terms of Service</a> 
                                    and 
                                    <a href="<?php echo SITE_URL; ?>/privacy.php" class="text-decoration-none" target="_blank">Privacy Policy</a>
                                </label>
                                <div class="invalid-feedback">
                                    You must agree to the terms and conditions.
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" 
                                       type="checkbox" 
                                       id="newsletter" 
                                       name="newsletter">
                                <label class="form-check-label" for="newsletter">
                                    Subscribe to our newsletter for updates and special offers
                                </label>
                            </div>
                        </div>
                        
                        <button type="submit" class="btn btn-primary btn-lg w-100 mb-3">
                            <i class="bi bi-person-plus me-2"></i>
                            Create Account
                        </button>
                        
                        <div class="text-center">
                            <p class="mb-0">Already have an account? 
                                <a href="<?php echo SITE_URL; ?>/login.php" class="text-decoration-none fw-bold">
                                    Sign In
                                </a>
                            </p>
                        </div>
                    </form>
                    
                    <!-- Social Signup (Optional) -->
                    <div class="mt-4">
                        <div class="text-center mb-3">
                            <span class="text-muted">Or sign up with</span>
                        </div>
                        <div class="d-grid gap-2">
                            <button class="btn btn-outline-dark" type="button">
                                <i class="bi bi-google me-2"></i>
                                Sign up with Google
                            </button>
                            <button class="btn btn-outline-primary" type="button">
                                <i class="bi bi-facebook me-2"></i>
                                Sign up with Facebook
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Toggle password visibility
document.getElementById('togglePassword').addEventListener('click', function() {
    const passwordInput = document.getElementById('password');
    const icon = this.querySelector('i');
    
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        icon.classList.remove('bi-eye');
        icon.classList.add('bi-eye-slash');
    } else {
        passwordInput.type = 'password';
        icon.classList.remove('bi-eye-slash');
        icon.classList.add('bi-eye');
    }
});

// Password confirmation validation
document.getElementById('confirm_password').addEventListener('input', function() {
    const password = document.getElementById('password').value;
    const confirmPassword = this.value;
    
    if (confirmPassword && password !== confirmPassword) {
        this.setCustomValidity('Passwords do not match');
    } else {
        this.setCustomValidity('');
    }
});

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
