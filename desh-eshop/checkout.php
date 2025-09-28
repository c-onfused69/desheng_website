<?php
require_once 'config/config.php';

// Check if user is logged in
if (!isLoggedIn()) {
    redirect(SITE_URL . '/login?redirect=' . urlencode($_SERVER['REQUEST_URI']));
}

$page_title = 'Checkout';
$page_description = 'Complete your purchase securely.';
$body_class = 'checkout-page';

$current_user = getCurrentUser();
$error_message = '';
$success_message = '';

// Get cart items
try {
    $db = getDB();
    $stmt = $db->prepare("
        SELECT c.*, p.title, p.slug, p.price, p.sale_price, p.short_description,
               (SELECT image_path FROM product_images WHERE product_id = p.id AND is_primary = 1 LIMIT 1) as primary_image
        FROM cart c
        JOIN products p ON c.product_id = p.id
        WHERE c.user_id = ? AND p.is_active = 1
        ORDER BY c.created_at DESC
    ");
    $stmt->execute([$current_user['id']]);
    $cart_items = $stmt->fetchAll();
    
    if (empty($cart_items)) {
        redirect(SITE_URL . '/cart');
    }
    
    // Calculate totals
    $subtotal = 0;
    foreach ($cart_items as $item) {
        $price = $item['sale_price'] && $item['sale_price'] < $item['price'] ? $item['sale_price'] : $item['price'];
        $subtotal += $price * $item['quantity'];
    }
    
    $tax_rate = floatval(getSetting('tax_rate', 0)) / 100;
    $tax_amount = $subtotal * $tax_rate;
    $total = $subtotal + $tax_amount;
    
} catch (Exception $e) {
    error_log("Checkout fetch error: " . $e->getMessage());
    redirect(SITE_URL . '/cart');
}

// Handle checkout form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $billing_name = sanitize($_POST['billing_name'] ?? '');
    $billing_email = sanitize($_POST['billing_email'] ?? '');
    $billing_phone = sanitize($_POST['billing_phone'] ?? '');
    $billing_address = sanitize($_POST['billing_address'] ?? '');
    $billing_city = sanitize($_POST['billing_city'] ?? '');
    $billing_state = sanitize($_POST['billing_state'] ?? '');
    $billing_country = sanitize($_POST['billing_country'] ?? '');
    $billing_postal_code = sanitize($_POST['billing_postal_code'] ?? '');
    $payment_method = sanitize($_POST['payment_method'] ?? 'stripe');
    $coupon_code = sanitize($_POST['coupon_code'] ?? '');
    
    // Validation
    if (empty($billing_name) || empty($billing_email)) {
        $error_message = 'Name and email are required.';
    } elseif (!validateEmail($billing_email)) {
        $error_message = 'Please enter a valid email address.';
    } else {
        try {
            $db->beginTransaction();
            
            // Apply coupon if provided
            $discount_amount = 0;
            $coupon_id = null;
            
            if (!empty($coupon_code)) {
                $stmt = $db->prepare("
                    SELECT * FROM coupons 
                    WHERE code = ? AND is_active = 1 
                    AND (starts_at IS NULL OR starts_at <= NOW())
                    AND (expires_at IS NULL OR expires_at >= NOW())
                    AND (usage_limit IS NULL OR used_count < usage_limit)
                    AND minimum_amount <= ?
                ");
                $stmt->execute([$coupon_code, $subtotal]);
                $coupon = $stmt->fetch();
                
                if ($coupon) {
                    $coupon_id = $coupon['id'];
                    if ($coupon['type'] === 'fixed') {
                        $discount_amount = min($coupon['value'], $subtotal);
                    } else { // percentage
                        $discount_amount = ($subtotal * $coupon['value']) / 100;
                    }
                    
                    // Recalculate total
                    $subtotal_after_discount = $subtotal - $discount_amount;
                    $tax_amount = $subtotal_after_discount * $tax_rate;
                    $total = $subtotal_after_discount + $tax_amount;
                }
            }
            
            // Create order
            $order_number = generateOrderNumber();
            $stmt = $db->prepare("
                INSERT INTO orders (
                    user_id, order_number, status, payment_status, payment_method,
                    subtotal, tax_amount, discount_amount, total_amount, currency,
                    billing_name, billing_email, billing_phone, billing_address,
                    billing_city, billing_state, billing_country, billing_postal_code,
                    created_at
                ) VALUES (?, ?, 'pending', 'pending', ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
            ");
            
            $stmt->execute([
                $current_user['id'], $order_number, $payment_method,
                $subtotal, $tax_amount, $discount_amount, $total, getSetting('currency', 'BDT'),
                $billing_name, $billing_email, $billing_phone, $billing_address,
                $billing_city, $billing_state, $billing_country, $billing_postal_code
            ]);
            
            $order_id = $db->lastInsertId();
            
            // Add order items
            foreach ($cart_items as $item) {
                $price = $item['sale_price'] && $item['sale_price'] < $item['price'] ? $item['sale_price'] : $item['price'];
                $item_total = $price * $item['quantity'];
                
                $stmt = $db->prepare("
                    INSERT INTO order_items (order_id, product_id, product_title, product_price, quantity, total_price)
                    VALUES (?, ?, ?, ?, ?, ?)
                ");
                $stmt->execute([$order_id, $item['product_id'], $item['title'], $price, $item['quantity'], $item_total]);
            }
            
            // Record coupon usage
            if ($coupon_id) {
                $stmt = $db->prepare("
                    INSERT INTO coupon_usage (coupon_id, user_id, order_id, discount_amount)
                    VALUES (?, ?, ?, ?)
                ");
                $stmt->execute([$coupon_id, $current_user['id'], $order_id, $discount_amount]);
                
                // Update coupon usage count
                $stmt = $db->prepare("UPDATE coupons SET used_count = used_count + 1 WHERE id = ?");
                $stmt->execute([$coupon_id]);
            }
            
            $db->commit();
            
            // Redirect to payment processing
            $_SESSION['checkout_order_id'] = $order_id;
            redirect(SITE_URL . '/process-payment?order=' . $order_number);
            
        } catch (Exception $e) {
            $db->rollBack();
            error_log("Checkout error: " . $e->getMessage());
            $error_message = 'An error occurred during checkout. Please try again.';
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
            <li class="breadcrumb-item"><a href="<?php echo SITE_URL; ?>/cart">Cart</a></li>
            <li class="breadcrumb-item active">Checkout</li>
        </ol>
    </nav>
    
    <!-- Page Header -->
    <div class="text-center mb-4">
        <h1 class="display-6 fw-bold">Secure Checkout</h1>
        <p class="text-muted">Complete your purchase safely and securely</p>
    </div>
    
    <!-- Checkout Progress -->
    <div class="checkout-progress mb-5">
        <div class="row text-center">
            <div class="col-4">
                <div class="step completed">
                    <div class="step-icon">
                        <i class="bi bi-cart-check"></i>
                    </div>
                    <div class="step-title">Cart</div>
                </div>
            </div>
            <div class="col-4">
                <div class="step active">
                    <div class="step-icon">
                        <i class="bi bi-credit-card"></i>
                    </div>
                    <div class="step-title">Checkout</div>
                </div>
            </div>
            <div class="col-4">
                <div class="step">
                    <div class="step-icon">
                        <i class="bi bi-check-circle"></i>
                    </div>
                    <div class="step-title">Complete</div>
                </div>
            </div>
        </div>
    </div>
    
    <?php if ($error_message): ?>
        <div class="alert alert-danger">
            <i class="bi bi-exclamation-triangle me-2"></i>
            <?php echo $error_message; ?>
        </div>
    <?php endif; ?>
    
    <form method="POST" class="needs-validation" novalidate>
        <div class="row">
            <!-- Billing Information -->
            <div class="col-lg-8">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="bi bi-person me-2"></i>
                            Billing Information
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="billing_name" class="form-label">Full Name *</label>
                                <input type="text" 
                                       class="form-control" 
                                       id="billing_name" 
                                       name="billing_name" 
                                       value="<?php echo htmlspecialchars($current_user['name']); ?>"
                                       required>
                                <div class="invalid-feedback">
                                    Please enter your full name.
                                </div>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="billing_email" class="form-label">Email Address *</label>
                                <input type="email" 
                                       class="form-control" 
                                       id="billing_email" 
                                       name="billing_email" 
                                       value="<?php echo htmlspecialchars($current_user['email']); ?>"
                                       required>
                                <div class="invalid-feedback">
                                    Please enter a valid email address.
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="billing_phone" class="form-label">Phone Number</label>
                                <input type="tel" 
                                       class="form-control" 
                                       id="billing_phone" 
                                       name="billing_phone" 
                                       value="<?php echo htmlspecialchars($current_user['phone'] ?? ''); ?>">
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="billing_country" class="form-label">Country</label>
                                <input type="text" 
                                       class="form-control" 
                                       id="billing_country" 
                                       name="billing_country" 
                                       value="<?php echo htmlspecialchars($current_user['country'] ?? ''); ?>">
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="billing_address" class="form-label">Address</label>
                            <textarea class="form-control" 
                                      id="billing_address" 
                                      name="billing_address" 
                                      rows="3"><?php echo htmlspecialchars($current_user['address'] ?? ''); ?></textarea>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="billing_city" class="form-label">City</label>
                                <input type="text" 
                                       class="form-control" 
                                       id="billing_city" 
                                       name="billing_city" 
                                       value="<?php echo htmlspecialchars($current_user['city'] ?? ''); ?>">
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label for="billing_state" class="form-label">State/Province</label>
                                <input type="text" 
                                       class="form-control" 
                                       id="billing_state" 
                                       name="billing_state" 
                                       value="<?php echo htmlspecialchars($current_user['state'] ?? ''); ?>">
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label for="billing_postal_code" class="form-label">Postal Code</label>
                                <input type="text" 
                                       class="form-control" 
                                       id="billing_postal_code" 
                                       name="billing_postal_code" 
                                       value="<?php echo htmlspecialchars($current_user['postal_code'] ?? ''); ?>">
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Payment Method -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="bi bi-credit-card me-2"></i>
                            Payment Method
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <div class="form-check payment-option">
                                    <input class="form-check-input" 
                                           type="radio" 
                                           name="payment_method" 
                                           id="stripe" 
                                           value="stripe" 
                                           checked>
                                    <label class="form-check-label w-100" for="stripe">
                                        <div class="payment-card">
                                            <i class="bi bi-credit-card fs-3 text-primary"></i>
                                            <div class="ms-3">
                                                <div class="fw-bold">Credit Card</div>
                                                <small class="text-muted">Visa, Mastercard, Amex</small>
                                            </div>
                                        </div>
                                    </label>
                                </div>
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <div class="form-check payment-option">
                                    <input class="form-check-input" 
                                           type="radio" 
                                           name="payment_method" 
                                           id="paypal" 
                                           value="paypal">
                                    <label class="form-check-label w-100" for="paypal">
                                        <div class="payment-card">
                                            <i class="bi bi-paypal fs-3 text-primary"></i>
                                            <div class="ms-3">
                                                <div class="fw-bold">PayPal</div>
                                                <small class="text-muted">Pay with PayPal</small>
                                            </div>
                                        </div>
                                    </label>
                                </div>
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <div class="form-check payment-option">
                                    <input class="form-check-input" 
                                           type="radio" 
                                           name="payment_method" 
                                           id="razorpay" 
                                           value="razorpay">
                                    <label class="form-check-label w-100" for="razorpay">
                                        <div class="payment-card">
                                            <i class="bi bi-wallet2 fs-3 text-primary"></i>
                                            <div class="ms-3">
                                                <div class="fw-bold">Razorpay</div>
                                                <small class="text-muted">UPI, Cards, Wallets</small>
                                            </div>
                                        </div>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Coupon Code -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="bi bi-tag me-2"></i>
                            Discount Code
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="input-group">
                            <input type="text" 
                                   class="form-control" 
                                   name="coupon_code" 
                                   placeholder="Enter coupon code">
                            <button class="btn btn-outline-secondary" 
                                    type="button" 
                                    id="applyCoupon">
                                Apply
                            </button>
                        </div>
                        <small class="text-muted">Have a discount code? Enter it above to save on your order.</small>
                    </div>
                </div>
            </div>
            
            <!-- Order Summary -->
            <div class="col-lg-4">
                <div class="card order-summary">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="bi bi-receipt me-2"></i>
                            Order Summary
                        </h5>
                    </div>
                    <div class="card-body">
                        <!-- Order Items -->
                        <div class="order-items mb-3">
                            <?php foreach ($cart_items as $item): ?>
                                <?php
                                $price = $item['sale_price'] && $item['sale_price'] < $item['price'] ? $item['sale_price'] : $item['price'];
                                $item_total = $price * $item['quantity'];
                                ?>
                                <div class="order-item d-flex align-items-center mb-3">
                                    <img src="<?php echo $item['primary_image'] ? SITE_URL . '/uploads/products/' . $item['primary_image'] : SITE_URL . '/assets/images/placeholder-product.jpg'; ?>" 
                                         alt="<?php echo htmlspecialchars($item['title']); ?>"
                                         class="order-item-image me-3">
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1"><?php echo htmlspecialchars($item['title']); ?></h6>
                                        <small class="text-muted">Qty: <?php echo $item['quantity']; ?></small>
                                    </div>
                                    <div class="fw-bold"><?php echo formatCurrency($item_total); ?></div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        
                        <hr>
                        
                        <!-- Totals -->
                        <div class="order-summary-item">
                            <span>Subtotal</span>
                            <span><?php echo formatCurrency($subtotal); ?></span>
                        </div>
                        
                        <?php if ($tax_amount > 0): ?>
                            <div class="order-summary-item">
                                <span>Tax</span>
                                <span><?php echo formatCurrency($tax_amount); ?></span>
                            </div>
                        <?php endif; ?>
                        
                        <div class="order-summary-total">
                            <span>Total</span>
                            <span><?php echo formatCurrency($total); ?></span>
                        </div>
                        
                        <!-- Place Order Button -->
                        <div class="d-grid mt-4">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="bi bi-lock me-2"></i>
                                Place Order
                            </button>
                        </div>
                        
                        <!-- Security Notice -->
                        <div class="text-center mt-3">
                            <small class="text-muted">
                                <i class="bi bi-shield-check me-1"></i>
                                Your payment information is secure and encrypted
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<style>
.checkout-progress .step {
    position: relative;
}

.checkout-progress .step:not(:last-child)::after {
    content: '';
    position: absolute;
    top: 20px;
    right: -50%;
    width: 100%;
    height: 2px;
    background-color: #dee2e6;
    z-index: -1;
}

.checkout-progress .step.completed::after,
.checkout-progress .step.active::after {
    background-color: var(--primary-color);
}

.step-icon {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background-color: #dee2e6;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 8px;
    color: #6c757d;
}

.step.completed .step-icon,
.step.active .step-icon {
    background-color: var(--primary-color);
    color: white;
}

.payment-option {
    height: 100%;
}

.payment-card {
    display: flex;
    align-items: center;
    padding: 1rem;
    border: 2px solid #dee2e6;
    border-radius: 8px;
    transition: all 0.3s ease;
    height: 100%;
}

.form-check-input:checked + .form-check-label .payment-card {
    border-color: var(--primary-color);
    background-color: var(--primary-color);
    background-opacity: 0.1;
}

.order-item-image {
    width: 50px;
    height: 50px;
    object-fit: cover;
    border-radius: 4px;
}

.order-summary-item {
    display: flex;
    justify-content: space-between;
    margin-bottom: 8px;
}

.order-summary-total {
    display: flex;
    justify-content: space-between;
    font-size: 1.25rem;
    font-weight: 700;
    padding-top: 1rem;
    border-top: 2px solid #dee2e6;
    margin-top: 1rem;
}
</style>

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

// Apply coupon functionality
document.getElementById('applyCoupon')?.addEventListener('click', function() {
    const couponInput = document.querySelector('input[name="coupon_code"]');
    const couponCode = couponInput.value.trim();
    
    if (!couponCode) {
        showNotification('Please enter a coupon code', 'warning');
        return;
    }
    
    // Here you would typically make an AJAX call to validate the coupon
    // For now, we'll just show a message
    showNotification('Coupon will be applied when you place the order', 'info');
});
</script>

<?php include 'includes/footer.php'; ?>
