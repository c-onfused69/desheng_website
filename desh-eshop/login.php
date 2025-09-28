<?php
require_once 'config/config.php';

$page_title = 'Login';
$page_description = 'Sign in to your account to access your digital products and orders.';
$body_class = 'login-page';

$error_message = '';
$success_message = '';

// Check if user is already logged in
if (isLoggedIn()) {
    redirect(SITE_URL . '/profile.php');
}

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitize($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $remember = isset($_POST['remember']);
    
    if (empty($email) || empty($password)) {
        $error_message = 'Please enter both email and password.';
    } elseif (!validateEmail($email)) {
        $error_message = 'Please enter a valid email address.';
    } else {
        try {
            $db = getDB();
            $stmt = $db->prepare("SELECT * FROM users WHERE email = ? AND is_active = 1");
            $stmt->execute([$email]);
            $user = $stmt->fetch();
            
            if ($user && password_verify($password, $user['password'])) {
                // Update last login
                $update_stmt = $db->prepare("UPDATE users SET last_login = NOW() WHERE id = ?");
                $update_stmt->execute([$user['id']]);
                
                // Set session
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_name'] = $user['name'];
                
                // Set remember me cookie if requested
                if ($remember) {
                    $token = bin2hex(random_bytes(32));
                    $expires = time() + (30 * 24 * 60 * 60); // 30 days
                    
                    // Store token in database
                    $token_stmt = $db->prepare("INSERT INTO user_tokens (user_id, token, expires_at) VALUES (?, ?, FROM_UNIXTIME(?))");
                    $token_stmt->execute([$user['id'], hash('sha256', $token), $expires]);
                    
                    // Set cookie
                    setcookie('remember_token', $token, $expires, '/', '', true, true);
                }
                
                // Redirect to intended page or profile
                $redirect_url = $_SESSION['redirect_after_login'] ?? SITE_URL . '/profile.php';
                unset($_SESSION['redirect_after_login']);
                redirect($redirect_url);
                
            } else {
                $error_message = 'Invalid email or password.';
            }
        } catch (Exception $e) {
            error_log("Login error: " . $e->getMessage());
            $error_message = 'An error occurred. Please try again.';
        }
    }
}

include 'includes/header.php';
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-5 col-md-7">
            <div class="product-card ecommerce-shadow-lg">
                <div class="card-body p-5">
                    <!-- Header -->
                    <div class="text-center mb-4">
                        <h1 class="h3 fw-bold text-primary mb-2">Welcome Back</h1>
                        <p class="text-muted">Sign in to your account to continue</p>
                    </div>
                    
                    <!-- Error/Success Messages -->
                    <?php if (!empty($error_message)): ?>
                        <div class="alert alert-danger" role="alert">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            <?php echo htmlspecialchars($error_message); ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($success_message)): ?>
                        <div class="alert alert-success" role="alert">
                            <i class="bi bi-check-circle me-2"></i>
                            <?php echo htmlspecialchars($success_message); ?>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Login Form -->
                    <form method="POST" action="">
                        <div class="mb-3">
                            <label for="email" class="form-label fw-semibold">Email Address</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="bi bi-envelope"></i>
                                </span>
                                <input type="email" 
                                       class="form-control" 
                                       id="email" 
                                       name="email" 
                                       placeholder="Enter your email"
                                       value="<?php echo htmlspecialchars($email ?? ''); ?>"
                                       required>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="password" class="form-label fw-semibold">Password</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="bi bi-lock"></i>
                                </span>
                                <input type="password" 
                                       class="form-control" 
                                       id="password" 
                                       name="password" 
                                       placeholder="Enter your password"
                                       required>
                                <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="remember" name="remember">
                                <label class="form-check-label" for="remember">
                                    Remember me for 30 days
                                </label>
                            </div>
                        </div>
                        
                        <div class="d-grid mb-3">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="bi bi-box-arrow-in-right me-2"></i>
                                Sign In
                            </button>
                        </div>
                        
                        <div class="text-center">
                            <a href="<?php echo SITE_URL; ?>/forgot-password.php" class="text-decoration-none">
                                Forgot your password?
                            </a>
                        </div>
                    </form>
                </div>
                
                <!-- Footer -->
                <div class="card-footer bg-transparent text-center py-3">
                    <p class="mb-0 text-muted">
                        Don't have an account? 
                        <a href="<?php echo SITE_URL; ?>/signup.php" class="text-decoration-none fw-semibold">
                            Create one here
                        </a>
                    </p>
                </div>
            </div>
            
            <!-- Additional Links -->
            <div class="text-center mt-4">
                <a href="<?php echo SITE_URL; ?>" class="btn btn-outline-primary">
                    <i class="bi bi-arrow-left me-2"></i>
                    Back to Home
                </a>
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
        icon.className = 'bi bi-eye-slash';
    } else {
        passwordInput.type = 'password';
        icon.className = 'bi bi-eye';
    }
});

// Auto-focus on first empty field
document.addEventListener('DOMContentLoaded', function() {
    const emailInput = document.getElementById('email');
    const passwordInput = document.getElementById('password');
    
    if (!emailInput.value) {
        emailInput.focus();
    } else {
        passwordInput.focus();
    }
});
</script>

<?php include 'includes/footer.php'; ?>
