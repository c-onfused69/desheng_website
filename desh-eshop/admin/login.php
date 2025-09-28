<?php
require_once '../config/config.php';

// Redirect if already logged in
if (isAdminLoggedIn()) {
    redirect(SITE_URL . '/admin');
}

$error_message = '';
$success_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitize($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $remember_me = isset($_POST['remember_me']);
    
    if (empty($email) || empty($password)) {
        $error_message = 'Please fill in all required fields.';
    } elseif (!validateEmail($email)) {
        $error_message = 'Please enter a valid email address.';
    } else {
        try {
            $db = getDB();
            $stmt = $db->prepare("SELECT * FROM admin_users WHERE email = ? AND is_active = 1");
            $stmt->execute([$email]);
            $admin = $stmt->fetch();
            
            if ($admin && verifyPassword($password, $admin['password'])) {
                // Login successful
                $_SESSION['admin_id'] = $admin['id'];
                $_SESSION['admin_name'] = $admin['name'];
                $_SESSION['admin_email'] = $admin['email'];
                $_SESSION['admin_role'] = $admin['role'];
                
                // Set remember me cookie if requested
                if ($remember_me) {
                    $token = generateToken();
                    setcookie('admin_remember_token', $token, time() + (30 * 24 * 60 * 60), '/admin/');
                    
                    // Store token in database (simplified approach)
                    $stmt = $db->prepare("UPDATE admin_users SET password_reset_token = ? WHERE id = ?");
                    $stmt->execute([$token, $admin['id']]);
                }
                
                // Log admin login
                error_log("Admin login: " . $admin['email'] . " from " . ($_SERVER['REMOTE_ADDR'] ?? 'unknown'));
                
                $_SESSION['admin_success_message'] = 'Welcome back, ' . $admin['name'] . '!';
                redirect(SITE_URL . '/admin');
            } else {
                $error_message = 'Invalid email or password.';
                // Log failed login attempt
                error_log("Failed admin login attempt: " . $email . " from " . ($_SERVER['REMOTE_ADDR'] ?? 'unknown'));
            }
        } catch (Exception $e) {
            error_log("Admin login error: " . $e->getMessage());
            $error_message = 'An error occurred. Please try again.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - <?php echo SITE_NAME; ?></title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    
    <!-- Custom Admin CSS -->
    <link href="<?php echo SITE_URL; ?>/admin/assets/css/admin.css" rel="stylesheet">
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="<?php echo SITE_URL; ?>/assets/images/favicon.ico">
</head>
<body class="login-container d-flex align-items-center justify-content-center">
    
    <div class="login-card card fade-in">
        <div class="login-header">
            <div class="mb-3">
                <i class="bi bi-shield-lock display-4 text-primary"></i>
            </div>
            <h2>Admin Login</h2>
            <p class="text-muted">Access the <?php echo SITE_NAME; ?> admin panel</p>
        </div>
        
        <div class="login-body">
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
                    <label for="email" class="form-label">Email Address</label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="bi bi-envelope"></i>
                        </span>
                        <input type="email" 
                               class="form-control" 
                               id="email" 
                               name="email" 
                               value="<?php echo htmlspecialchars($email ?? ''); ?>"
                               placeholder="Enter your email"
                               required>
                        <div class="invalid-feedback">
                            Please enter a valid email address.
                        </div>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
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
                        <button class="btn btn-outline-secondary" 
                                type="button" 
                                id="togglePassword">
                            <i class="bi bi-eye"></i>
                        </button>
                        <div class="invalid-feedback">
                            Please enter your password.
                        </div>
                    </div>
                </div>
                
                <div class="mb-3">
                    <div class="form-check">
                        <input class="form-check-input" 
                               type="checkbox" 
                               id="remember_me" 
                               name="remember_me">
                        <label class="form-check-label" for="remember_me">
                            Remember me for 30 days
                        </label>
                    </div>
                </div>
                
                <button type="submit" class="btn btn-primary w-100 mb-3">
                    <i class="bi bi-box-arrow-in-right me-2"></i>
                    Sign In
                </button>
                
                <div class="text-center">
                    <a href="<?php echo SITE_URL; ?>/admin/forgot-password.php" class="text-decoration-none">
                        Forgot your password?
                    </a>
                </div>
            </form>
        </div>
        
        <div class="card-footer text-center bg-light">
            <small class="text-muted">
                <a href="<?php echo SITE_URL; ?>" class="text-decoration-none">
                    <i class="bi bi-arrow-left me-1"></i>
                    Back to Website
                </a>
            </small>
        </div>
    </div>
    
    <!-- Security Notice -->
    <div class="position-fixed bottom-0 start-0 p-3">
        <div class="toast show" role="alert">
            <div class="toast-header">
                <i class="bi bi-shield-check text-success me-2"></i>
                <strong class="me-auto">Security Notice</strong>
                <button type="button" class="btn-close" data-bs-dismiss="toast"></button>
            </div>
            <div class="toast-body">
                This is a secure admin area. All login attempts are logged and monitored.
            </div>
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
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
        
        // Auto-hide toast after 10 seconds
        setTimeout(function() {
            const toast = document.querySelector('.toast');
            if (toast) {
                const bsToast = new bootstrap.Toast(toast);
                bsToast.hide();
            }
        }, 10000);
        
        // Focus on email input
        document.getElementById('email').focus();
        
        // Add loading state to submit button
        document.querySelector('form').addEventListener('submit', function() {
            const submitBtn = this.querySelector('button[type="submit"]');
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Signing In...';
            submitBtn.disabled = true;
        });
    </script>
</body>
</html>
