<?php
require_once 'config/config.php';

// Redirect if already logged in
if (isLoggedIn()) {
    redirect(SITE_URL . '/profile');
}

$page_title = 'Forgot Password';
$page_description = 'Reset your password to regain access to your account.';
$body_class = 'auth-page';

$error_message = '';
$success_message = '';
$step = $_GET['step'] ?? 'request';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($step === 'request') {
        $email = sanitize($_POST['email'] ?? '');
        
        if (empty($email)) {
            $error_message = 'Please enter your email address.';
        } elseif (!validateEmail($email)) {
            $error_message = 'Please enter a valid email address.';
        } else {
            try {
                $db = getDB();
                $stmt = $db->prepare("SELECT id, name FROM users WHERE email = ? AND is_active = 1");
                $stmt->execute([$email]);
                $user = $stmt->fetch();
                
                if ($user) {
                    // Generate reset token
                    $reset_token = generateToken();
                    $expires_at = date('Y-m-d H:i:s', strtotime('+1 hour'));
                    
                    // Update user with reset token
                    $stmt = $db->prepare("
                        UPDATE users 
                        SET password_reset_token = ?, password_reset_expires = ? 
                        WHERE id = ?
                    ");
                    $stmt->execute([$reset_token, $expires_at, $user['id']]);
                    
                    // Send reset email
                    $reset_link = SITE_URL . "/forgot-password?step=reset&token=" . $reset_token;
                    $email_subject = "Password Reset Request - " . SITE_NAME;
                    $email_body = "
                        <h2>Password Reset Request</h2>
                        <p>Hello {$user['name']},</p>
                        <p>We received a request to reset your password. Click the link below to create a new password:</p>
                        <p><a href='{$reset_link}' style='background: #6f42c1; color: white; padding: 12px 24px; text-decoration: none; border-radius: 5px;'>Reset Password</a></p>
                        <p>This link will expire in 1 hour for security reasons.</p>
                        <p>If you didn't request this password reset, please ignore this email.</p>
                        <p>Best regards,<br>The " . SITE_NAME . " Team</p>
                    ";
                    
                    if (sendEmail($email, $email_subject, $email_body, true)) {
                        $success_message = 'Password reset instructions have been sent to your email address.';
                    } else {
                        $error_message = 'Failed to send reset email. Please try again.';
                    }
                } else {
                    // Don't reveal if email exists or not for security
                    $success_message = 'If an account with that email exists, password reset instructions have been sent.';
                }
            } catch (Exception $e) {
                error_log("Forgot password error: " . $e->getMessage());
                $error_message = 'An error occurred. Please try again.';
            }
        }
    } elseif ($step === 'reset') {
        $token = sanitize($_POST['token'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';
        
        if (empty($token) || empty($password) || empty($confirm_password)) {
            $error_message = 'Please fill in all required fields.';
        } elseif (strlen($password) < 8) {
            $error_message = 'Password must be at least 8 characters long.';
        } elseif ($password !== $confirm_password) {
            $error_message = 'Passwords do not match.';
        } else {
            try {
                $db = getDB();
                $stmt = $db->prepare("
                    SELECT id, name, email FROM users 
                    WHERE password_reset_token = ? 
                    AND password_reset_expires > NOW() 
                    AND is_active = 1
                ");
                $stmt->execute([$token]);
                $user = $stmt->fetch();
                
                if ($user) {
                    // Update password and clear reset token
                    $hashed_password = hashPassword($password);
                    $stmt = $db->prepare("
                        UPDATE users 
                        SET password = ?, password_reset_token = NULL, password_reset_expires = NULL 
                        WHERE id = ?
                    ");
                    
                    if ($stmt->execute([$hashed_password, $user['id']])) {
                        // Send confirmation email
                        $email_subject = "Password Changed Successfully - " . SITE_NAME;
                        $email_body = "
                            <h2>Password Changed Successfully</h2>
                            <p>Hello {$user['name']},</p>
                            <p>Your password has been changed successfully.</p>
                            <p>If you didn't make this change, please contact our support team immediately.</p>
                            <p>Best regards,<br>The " . SITE_NAME . " Team</p>
                        ";
                        sendEmail($user['email'], $email_subject, $email_body, true);
                        
                        $_SESSION['success_message'] = 'Password reset successfully. You can now login with your new password.';
                        redirect(SITE_URL . '/login');
                    } else {
                        $error_message = 'Failed to reset password. Please try again.';
                    }
                } else {
                    $error_message = 'Invalid or expired reset token. Please request a new password reset.';
                }
            } catch (Exception $e) {
                error_log("Password reset error: " . $e->getMessage());
                $error_message = 'An error occurred. Please try again.';
            }
        }
    }
}

// Validate reset token for reset step
if ($step === 'reset') {
    $token = sanitize($_GET['token'] ?? '');
    if (empty($token)) {
        redirect(SITE_URL . '/forgot-password');
    }
    
    try {
        $db = getDB();
        $stmt = $db->prepare("
            SELECT id FROM users 
            WHERE password_reset_token = ? 
            AND password_reset_expires > NOW() 
            AND is_active = 1
        ");
        $stmt->execute([$token]);
        if (!$stmt->fetch()) {
            $error_message = 'Invalid or expired reset token. Please request a new password reset.';
            $step = 'request';
        }
    } catch (Exception $e) {
        error_log("Token validation error: " . $e->getMessage());
        $error_message = 'An error occurred. Please try again.';
        $step = 'request';
    }
}

include 'includes/header.php';
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-5 col-md-7">
            <div class="card shadow-lg border-0">
                <div class="card-body p-5">
                    <?php if ($step === 'request'): ?>
                        <div class="text-center mb-4">
                            <div class="mb-3">
                                <i class="bi bi-shield-lock display-4 text-primary"></i>
                            </div>
                            <h2 class="fw-bold">Forgot Password?</h2>
                            <p class="text-muted">Enter your email address and we'll send you instructions to reset your password.</p>
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
                            <div class="mb-4">
                                <label for="email" class="form-label">Email Address</label>
                                <input type="email" 
                                       class="form-control form-control-lg" 
                                       id="email" 
                                       name="email" 
                                       placeholder="Enter your email address"
                                       required>
                                <div class="invalid-feedback">
                                    Please enter a valid email address.
                                </div>
                            </div>
                            
                            <button type="submit" class="btn btn-primary btn-lg w-100 mb-3">
                                <i class="bi bi-envelope me-2"></i>
                                Send Reset Instructions
                            </button>
                            
                            <div class="text-center">
                                <a href="<?php echo SITE_URL; ?>/login" class="text-decoration-none">
                                    <i class="bi bi-arrow-left me-1"></i>
                                    Back to Login
                                </a>
                            </div>
                        </form>
                        
                    <?php elseif ($step === 'reset'): ?>
                        <div class="text-center mb-4">
                            <div class="mb-3">
                                <i class="bi bi-key display-4 text-primary"></i>
                            </div>
                            <h2 class="fw-bold">Reset Password</h2>
                            <p class="text-muted">Enter your new password below.</p>
                        </div>
                        
                        <?php if ($error_message): ?>
                            <div class="alert alert-danger">
                                <i class="bi bi-exclamation-triangle me-2"></i>
                                <?php echo $error_message; ?>
                            </div>
                        <?php endif; ?>
                        
                        <form method="POST" class="needs-validation" novalidate>
                            <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
                            
                            <div class="mb-3">
                                <label for="password" class="form-label">New Password</label>
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
                            
                            <div class="mb-4">
                                <label for="confirm_password" class="form-label">Confirm New Password</label>
                                <input type="password" 
                                       class="form-control form-control-lg" 
                                       id="confirm_password" 
                                       name="confirm_password" 
                                       required>
                                <div class="invalid-feedback">
                                    Passwords do not match.
                                </div>
                            </div>
                            
                            <button type="submit" class="btn btn-primary btn-lg w-100 mb-3">
                                <i class="bi bi-check-circle me-2"></i>
                                Reset Password
                            </button>
                            
                            <div class="text-center">
                                <a href="<?php echo SITE_URL; ?>/login" class="text-decoration-none">
                                    <i class="bi bi-arrow-left me-1"></i>
                                    Back to Login
                                </a>
                            </div>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Toggle password visibility
document.getElementById('togglePassword')?.addEventListener('click', function() {
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
document.getElementById('confirm_password')?.addEventListener('input', function() {
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
