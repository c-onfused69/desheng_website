<?php
require_once 'config/config.php';

// Check if user is logged in
if (!isLoggedIn()) {
    redirect(SITE_URL . '/login?redirect=' . urlencode($_SERVER['REQUEST_URI']));
}

$page_title = 'My Profile';
$page_description = 'Manage your account settings and view your order history.';
$body_class = 'profile-page';

$current_user = getCurrentUser();
$error_message = '';
$success_message = '';

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'update_profile') {
        $name = sanitize($_POST['name'] ?? '');
        $email = sanitize($_POST['email'] ?? '');
        $phone = sanitize($_POST['phone'] ?? '');
        $address = sanitize($_POST['address'] ?? '');
        $city = sanitize($_POST['city'] ?? '');
        $state = sanitize($_POST['state'] ?? '');
        $country = sanitize($_POST['country'] ?? '');
        $postal_code = sanitize($_POST['postal_code'] ?? '');
        
        if (empty($name) || empty($email)) {
            $error_message = 'Name and email are required.';
        } elseif (!validateEmail($email)) {
            $error_message = 'Please enter a valid email address.';
        } else {
            try {
                $db = getDB();
                
                // Check if email is already taken by another user
                $stmt = $db->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
                $stmt->execute([$email, $current_user['id']]);
                if ($stmt->fetch()) {
                    $error_message = 'This email address is already in use.';
                } else {
                    // Update profile
                    $stmt = $db->prepare("
                        UPDATE users 
                        SET name = ?, email = ?, phone = ?, address = ?, city = ?, state = ?, country = ?, postal_code = ?, updated_at = NOW()
                        WHERE id = ?
                    ");
                    
                    if ($stmt->execute([$name, $email, $phone, $address, $city, $state, $country, $postal_code, $current_user['id']])) {
                        $_SESSION['user_name'] = $name;
                        $_SESSION['user_email'] = $email;
                        $success_message = 'Profile updated successfully!';
                        
                        // Refresh current user data
                        $current_user = getCurrentUser();
                    } else {
                        $error_message = 'Failed to update profile. Please try again.';
                    }
                }
            } catch (Exception $e) {
                error_log("Profile update error: " . $e->getMessage());
                $error_message = 'An error occurred. Please try again.';
            }
        }
    } elseif ($_POST['action'] === 'change_password') {
        $current_password = $_POST['current_password'] ?? '';
        $new_password = $_POST['new_password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';
        
        if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
            $error_message = 'All password fields are required.';
        } elseif (!verifyPassword($current_password, $current_user['password'])) {
            $error_message = 'Current password is incorrect.';
        } elseif (strlen($new_password) < 8) {
            $error_message = 'New password must be at least 8 characters long.';
        } elseif ($new_password !== $confirm_password) {
            $error_message = 'New passwords do not match.';
        } else {
            try {
                $db = getDB();
                $hashed_password = hashPassword($new_password);
                
                $stmt = $db->prepare("UPDATE users SET password = ?, updated_at = NOW() WHERE id = ?");
                if ($stmt->execute([$hashed_password, $current_user['id']])) {
                    $success_message = 'Password changed successfully!';
                } else {
                    $error_message = 'Failed to change password. Please try again.';
                }
            } catch (Exception $e) {
                error_log("Password change error: " . $e->getMessage());
                $error_message = 'An error occurred. Please try again.';
            }
        }
    }
}

// Get user statistics
try {
    $db = getDB();
    
    // Get order count and total spent
    $stmt = $db->prepare("
        SELECT COUNT(*) as order_count, COALESCE(SUM(total_amount), 0) as total_spent
        FROM orders 
        WHERE user_id = ? AND payment_status = 'paid'
    ");
    $stmt->execute([$current_user['id']]);
    $stats = $stmt->fetch();
    
    // Get recent orders
    $stmt = $db->prepare("
        SELECT o.*, COUNT(oi.id) as item_count
        FROM orders o
        LEFT JOIN order_items oi ON o.id = oi.order_id
        WHERE o.user_id = ?
        GROUP BY o.id
        ORDER BY o.created_at DESC
        LIMIT 5
    ");
    $stmt->execute([$current_user['id']]);
    $recent_orders = $stmt->fetchAll();
    
} catch (Exception $e) {
    error_log("Profile stats error: " . $e->getMessage());
    $stats = ['order_count' => 0, 'total_spent' => 0];
    $recent_orders = [];
}

include 'includes/header.php';
?>

<div class="container py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo SITE_URL; ?>">Home</a></li>
            <li class="breadcrumb-item active">My Profile</li>
        </ol>
    </nav>
    
    <div class="row">
        <!-- Sidebar -->
        <div class="col-lg-3">
            <div class="card">
                <div class="card-body text-center">
                    <div class="profile-avatar mb-3">
                        <img src="<?php echo SITE_URL; ?>/assets/images/default-avatar.jpg" 
                             alt="Profile Picture" 
                             class="rounded-circle" 
                             width="80" 
                             height="80"
                             onerror="this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iODAiIGhlaWdodD0iODAiIHZpZXdCb3g9IjAgMCA4MCA4MCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPGNpcmNsZSBjeD0iNDAiIGN5PSI0MCIgcj0iNDAiIGZpbGw9IiM2RjQyQzEiLz4KPHN2ZyB3aWR0aD0iNDAiIGhlaWdodD0iNDAiIHZpZXdCb3g9IjAgMCAyNCAyNCIgZmlsbD0ibm9uZSIgeD0iMjAiIHk9IjIwIj4KPHA+dGggZD0iTTEyIDEyYzIuMjEgMCA0LTEuNzkgNC00cy0xLjc5LTQtNC00LTQgMS43OS00IDQgMS43OSA0IDQgNHptMCAyYy0yLjY3IDAtOCAxLjM0LTggNHYyaDE2di0yYzAtMi42Ni01LjMzLTQtOC00eiIgZmlsbD0id2hpdGUiLz4KPHN2Zz4KPHN2Zz4='">
                    </div>
                    <h5 class="fw-bold mb-1"><?php echo htmlspecialchars($current_user['name']); ?></h5>
                    <p class="text-muted small mb-3"><?php echo htmlspecialchars($current_user['email']); ?></p>
                    <div class="row text-center">
                        <div class="col-6">
                            <div class="fw-bold text-primary"><?php echo $stats['order_count']; ?></div>
                            <small class="text-muted">Orders</small>
                        </div>
                        <div class="col-6">
                            <div class="fw-bold text-primary"><?php echo formatCurrency($stats['total_spent']); ?></div>
                            <small class="text-muted">Spent</small>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Navigation Menu -->
            <div class="list-group list-group-flush mt-3">
                <a href="#profile" class="list-group-item list-group-item-action active" data-bs-toggle="pill">
                    <i class="bi bi-person me-2"></i>
                    Profile Information
                </a>
                <a href="#security" class="list-group-item list-group-item-action" data-bs-toggle="pill">
                    <i class="bi bi-shield-lock me-2"></i>
                    Security
                </a>
                <a href="#orders" class="list-group-item list-group-item-action" data-bs-toggle="pill">
                    <i class="bi bi-bag me-2"></i>
                    Order History
                </a>
                <a href="<?php echo SITE_URL; ?>/downloads" class="list-group-item list-group-item-action">
                    <i class="bi bi-download me-2"></i>
                    Downloads
                </a>
                <a href="<?php echo SITE_URL; ?>/wishlist" class="list-group-item list-group-item-action">
                    <i class="bi bi-heart me-2"></i>
                    Wishlist
                </a>
                <a href="<?php echo SITE_URL; ?>/support" class="list-group-item list-group-item-action">
                    <i class="bi bi-headset me-2"></i>
                    Support
                </a>
            </div>
        </div>
        
        <!-- Main Content -->
        <div class="col-lg-9">
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
            
            <div class="tab-content">
                <!-- Profile Information Tab -->
                <div class="tab-pane fade show active" id="profile">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="bi bi-person me-2"></i>
                                Profile Information
                            </h5>
                        </div>
                        <div class="card-body">
                            <form method="POST" class="needs-validation" novalidate>
                                <input type="hidden" name="action" value="update_profile">
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="name" class="form-label">Full Name *</label>
                                        <input type="text" 
                                               class="form-control" 
                                               id="name" 
                                               name="name" 
                                               value="<?php echo htmlspecialchars($current_user['name']); ?>"
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
                                               value="<?php echo htmlspecialchars($current_user['email']); ?>"
                                               required>
                                        <div class="invalid-feedback">
                                            Please enter a valid email address.
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="phone" class="form-label">Phone Number</label>
                                        <input type="tel" 
                                               class="form-control" 
                                               id="phone" 
                                               name="phone" 
                                               value="<?php echo htmlspecialchars($current_user['phone'] ?? ''); ?>">
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label for="country" class="form-label">Country</label>
                                        <input type="text" 
                                               class="form-control" 
                                               id="country" 
                                               name="country" 
                                               value="<?php echo htmlspecialchars($current_user['country'] ?? ''); ?>">
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="address" class="form-label">Address</label>
                                    <textarea class="form-control" 
                                              id="address" 
                                              name="address" 
                                              rows="3"><?php echo htmlspecialchars($current_user['address'] ?? ''); ?></textarea>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label for="city" class="form-label">City</label>
                                        <input type="text" 
                                               class="form-control" 
                                               id="city" 
                                               name="city" 
                                               value="<?php echo htmlspecialchars($current_user['city'] ?? ''); ?>">
                                    </div>
                                    
                                    <div class="col-md-4 mb-3">
                                        <label for="state" class="form-label">State/Province</label>
                                        <input type="text" 
                                               class="form-control" 
                                               id="state" 
                                               name="state" 
                                               value="<?php echo htmlspecialchars($current_user['state'] ?? ''); ?>">
                                    </div>
                                    
                                    <div class="col-md-4 mb-3">
                                        <label for="postal_code" class="form-label">Postal Code</label>
                                        <input type="text" 
                                               class="form-control" 
                                               id="postal_code" 
                                               name="postal_code" 
                                               value="<?php echo htmlspecialchars($current_user['postal_code'] ?? ''); ?>">
                                    </div>
                                </div>
                                
                                <div class="text-end">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-check-circle me-2"></i>
                                        Update Profile
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                
                <!-- Security Tab -->
                <div class="tab-pane fade" id="security">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="bi bi-shield-lock me-2"></i>
                                Change Password
                            </h5>
                        </div>
                        <div class="card-body">
                            <form method="POST" class="needs-validation" novalidate>
                                <input type="hidden" name="action" value="change_password">
                                
                                <div class="mb-3">
                                    <label for="current_password" class="form-label">Current Password</label>
                                    <input type="password" 
                                           class="form-control" 
                                           id="current_password" 
                                           name="current_password" 
                                           required>
                                    <div class="invalid-feedback">
                                        Please enter your current password.
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="new_password" class="form-label">New Password</label>
                                    <input type="password" 
                                           class="form-control" 
                                           id="new_password" 
                                           name="new_password" 
                                           minlength="8"
                                           required>
                                    <div class="password-strength mt-1"></div>
                                    <div class="invalid-feedback">
                                        Password must be at least 8 characters long.
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="confirm_password" class="form-label">Confirm New Password</label>
                                    <input type="password" 
                                           class="form-control" 
                                           id="confirm_password" 
                                           name="confirm_password" 
                                           required>
                                    <div class="invalid-feedback">
                                        Passwords do not match.
                                    </div>
                                </div>
                                
                                <div class="text-end">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-shield-check me-2"></i>
                                        Change Password
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                
                <!-- Orders Tab -->
                <div class="tab-pane fade" id="orders">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">
                                <i class="bi bi-bag me-2"></i>
                                Recent Orders
                            </h5>
                            <a href="<?php echo SITE_URL; ?>/orders" class="btn btn-outline-primary btn-sm">
                                View All Orders
                            </a>
                        </div>
                        <div class="card-body">
                            <?php if (!empty($recent_orders)): ?>
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Order #</th>
                                                <th>Date</th>
                                                <th>Items</th>
                                                <th>Total</th>
                                                <th>Status</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($recent_orders as $order): ?>
                                                <tr>
                                                    <td>
                                                        <strong><?php echo htmlspecialchars($order['order_number']); ?></strong>
                                                    </td>
                                                    <td><?php echo date('M j, Y', strtotime($order['created_at'])); ?></td>
                                                    <td><?php echo $order['item_count']; ?> item(s)</td>
                                                    <td><?php echo formatCurrency($order['total_amount']); ?></td>
                                                    <td>
                                                        <span class="badge bg-<?php echo getOrderStatusColor($order['status']); ?>">
                                                            <?php echo ucfirst($order['status']); ?>
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <a href="<?php echo SITE_URL; ?>/order/<?php echo $order['order_number']; ?>" 
                                                           class="btn btn-outline-primary btn-sm">
                                                            View
                                                        </a>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php else: ?>
                                <div class="text-center py-4">
                                    <i class="bi bi-bag display-4 text-muted mb-3"></i>
                                    <h5 class="text-muted">No orders yet</h5>
                                    <p class="text-muted">Start shopping to see your orders here.</p>
                                    <a href="<?php echo SITE_URL; ?>/products" class="btn btn-primary">
                                        <i class="bi bi-grid me-2"></i>
                                        Browse Products
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Password confirmation validation
document.getElementById('confirm_password')?.addEventListener('input', function() {
    const password = document.getElementById('new_password').value;
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

<?php
// Helper function for order status colors
function getOrderStatusColor($status) {
    switch ($status) {
        case 'completed':
            return 'success';
        case 'processing':
            return 'primary';
        case 'pending':
            return 'warning';
        case 'cancelled':
        case 'refunded':
            return 'danger';
        default:
            return 'secondary';
    }
}

include 'includes/footer.php';
?>
