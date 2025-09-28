<?php
require_once 'config/config.php';

// Check if user is logged in
if (!isLoggedIn()) {
    redirect(SITE_URL . '/login');
}

$order_number = sanitize($_GET['order'] ?? '');
if (empty($order_number)) {
    redirect(SITE_URL . '/cart');
}

try {
    $db = getDB();
    
    // Get order details
    $stmt = $db->prepare("
        SELECT o.*, COUNT(oi.id) as item_count
        FROM orders o
        LEFT JOIN order_items oi ON o.id = oi.order_id
        WHERE o.order_number = ? AND o.user_id = ? AND o.payment_status = 'pending'
        GROUP BY o.id
    ");
    $stmt->execute([$order_number, $_SESSION['user_id']]);
    $order = $stmt->fetch();
    
    if (!$order) {
        redirect(SITE_URL . '/orders');
    }
    
    // Get order items
    $stmt = $db->prepare("
        SELECT oi.*, p.slug,
               (SELECT image_path FROM product_images WHERE product_id = p.id AND is_primary = 1 LIMIT 1) as primary_image
        FROM order_items oi
        JOIN products p ON oi.product_id = p.id
        WHERE oi.order_id = ?
    ");
    $stmt->execute([$order['id']]);
    $order_items = $stmt->fetchAll();
    
} catch (Exception $e) {
    error_log("Payment processing error: " . $e->getMessage());
    redirect(SITE_URL . '/cart');
}

$page_title = 'Payment Processing';
$page_description = 'Complete your payment securely.';
$body_class = 'payment-page';

// Handle payment completion (webhook simulation)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'complete_payment') {
        $payment_id = sanitize($_POST['payment_id'] ?? '');
        $payment_status = sanitize($_POST['payment_status'] ?? '');
        
        if ($payment_status === 'success' && !empty($payment_id)) {
            try {
                $db->beginTransaction();
                
                // Update order status
                $stmt = $db->prepare("
                    UPDATE orders 
                    SET payment_status = 'paid', status = 'completed', transaction_id = ?, updated_at = NOW()
                    WHERE id = ?
                ");
                $stmt->execute([$payment_id, $order['id']]);
                
                // Update product sales count
                foreach ($order_items as $item) {
                    $stmt = $db->prepare("UPDATE products SET sales_count = sales_count + ? WHERE id = ?");
                    $stmt->execute([$item['quantity'], $item['product_id']]);
                }
                
                // Create download tokens for each product
                foreach ($order_items as $item) {
                    $download_token = generateToken();
                    $expires_at = date('Y-m-d H:i:s', strtotime('+' . getSetting('download_expiry_days', 30) . ' days'));
                    
                    $stmt = $db->prepare("
                        INSERT INTO downloads (user_id, order_id, product_id, download_token, max_downloads, expires_at)
                        VALUES (?, ?, ?, ?, ?, ?)
                    ");
                    $stmt->execute([
                        $order['user_id'], 
                        $order['id'], 
                        $item['product_id'], 
                        $download_token, 
                        getSetting('download_limit', 5),
                        $expires_at
                    ]);
                }
                
                // Clear user's cart
                $stmt = $db->prepare("DELETE FROM cart WHERE user_id = ?");
                $stmt->execute([$order['user_id']]);
                
                $db->commit();
                
                // Send confirmation email
                $email_subject = "Order Confirmation - " . $order['order_number'];
                $email_body = generateOrderConfirmationEmail($order, $order_items);
                sendEmail($order['billing_email'], $email_subject, $email_body, true);
                
                $_SESSION['success_message'] = 'Payment completed successfully! You can now download your products.';
                redirect(SITE_URL . '/order/' . $order['order_number']);
                
            } catch (Exception $e) {
                $db->rollBack();
                error_log("Payment completion error: " . $e->getMessage());
                $error_message = 'Payment processing failed. Please contact support.';
            }
        } else {
            $error_message = 'Payment failed. Please try again.';
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
            <li class="breadcrumb-item"><a href="<?php echo SITE_URL; ?>/checkout">Checkout</a></li>
            <li class="breadcrumb-item active">Payment</li>
        </ol>
    </nav>
    
    <!-- Payment Processing -->
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header text-center">
                    <h3 class="mb-0">Complete Your Payment</h3>
                    <p class="text-muted mb-0">Order #<?php echo htmlspecialchars($order['order_number']); ?></p>
                </div>
                <div class="card-body">
                    <!-- Order Summary -->
                    <div class="order-summary-section mb-4">
                        <h5 class="fw-bold mb-3">Order Summary</h5>
                        <div class="row">
                            <div class="col-md-8">
                                <?php foreach ($order_items as $item): ?>
                                    <div class="d-flex align-items-center mb-3">
                                        <img src="<?php echo $item['primary_image'] ? SITE_URL . '/uploads/products/' . $item['primary_image'] : SITE_URL . '/assets/images/placeholder-product.jpg'; ?>" 
                                             alt="<?php echo htmlspecialchars($item['product_title']); ?>"
                                             class="order-item-image me-3">
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1"><?php echo htmlspecialchars($item['product_title']); ?></h6>
                                            <small class="text-muted">Qty: <?php echo $item['quantity']; ?> × <?php echo formatCurrency($item['product_price']); ?></small>
                                        </div>
                                        <div class="fw-bold"><?php echo formatCurrency($item['total_price']); ?></div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <div class="col-md-4">
                                <div class="payment-summary">
                                    <div class="summary-item">
                                        <span>Subtotal:</span>
                                        <span><?php echo formatCurrency($order['subtotal']); ?></span>
                                    </div>
                                    <?php if ($order['discount_amount'] > 0): ?>
                                        <div class="summary-item text-success">
                                            <span>Discount:</span>
                                            <span>-<?php echo formatCurrency($order['discount_amount']); ?></span>
                                        </div>
                                    <?php endif; ?>
                                    <?php if ($order['tax_amount'] > 0): ?>
                                        <div class="summary-item">
                                            <span>Tax:</span>
                                            <span><?php echo formatCurrency($order['tax_amount']); ?></span>
                                        </div>
                                    <?php endif; ?>
                                    <div class="summary-total">
                                        <span>Total:</span>
                                        <span><?php echo formatCurrency($order['total_amount']); ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Payment Methods -->
                    <div class="payment-methods">
                        <h5 class="fw-bold mb-3">Choose Payment Method</h5>
                        
                        <?php if ($order['payment_method'] === 'stripe'): ?>
                            <!-- Stripe Payment -->
                            <div class="payment-option active" data-method="stripe">
                                <div class="payment-header">
                                    <i class="bi bi-credit-card me-2"></i>
                                    Credit Card (Stripe)
                                </div>
                                <div class="payment-content">
                                    <div id="stripe-payment-form">
                                        <div class="row">
                                            <div class="col-md-8 mb-3">
                                                <label class="form-label">Card Number</label>
                                                <input type="text" class="form-control" placeholder="1234 5678 9012 3456" id="card-number">
                                            </div>
                                            <div class="col-md-4 mb-3">
                                                <label class="form-label">CVC</label>
                                                <input type="text" class="form-control" placeholder="123" id="card-cvc">
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Expiry Month</label>
                                                <select class="form-select" id="card-month">
                                                    <option value="">Month</option>
                                                    <?php for ($i = 1; $i <= 12; $i++): ?>
                                                        <option value="<?php echo sprintf('%02d', $i); ?>"><?php echo sprintf('%02d', $i); ?></option>
                                                    <?php endfor; ?>
                                                </select>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Expiry Year</label>
                                                <select class="form-select" id="card-year">
                                                    <option value="">Year</option>
                                                    <?php for ($i = date('Y'); $i <= date('Y') + 10; $i++): ?>
                                                        <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                                                    <?php endfor; ?>
                                                </select>
                                            </div>
                                        </div>
                                        <button type="button" class="btn btn-primary btn-lg w-100" id="stripe-pay-btn">
                                            <i class="bi bi-lock me-2"></i>
                                            Pay <?php echo formatCurrency($order['total_amount']); ?>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            
                        <?php elseif ($order['payment_method'] === 'paypal'): ?>
                            <!-- PayPal Payment -->
                            <div class="payment-option active" data-method="paypal">
                                <div class="payment-header">
                                    <i class="bi bi-paypal me-2"></i>
                                    PayPal
                                </div>
                                <div class="payment-content">
                                    <div class="text-center py-4">
                                        <p class="mb-3">You will be redirected to PayPal to complete your payment.</p>
                                        <button type="button" class="btn btn-primary btn-lg" id="paypal-pay-btn">
                                            <i class="bi bi-paypal me-2"></i>
                                            Pay with PayPal
                                        </button>
                                    </div>
                                </div>
                            </div>
                            
                        <?php elseif ($order['payment_method'] === 'razorpay'): ?>
                            <!-- Razorpay Payment -->
                            <div class="payment-option active" data-method="razorpay">
                                <div class="payment-header">
                                    <i class="bi bi-wallet2 me-2"></i>
                                    Razorpay
                                </div>
                                <div class="payment-content">
                                    <div class="text-center py-4">
                                        <p class="mb-3">Pay securely using UPI, Cards, Net Banking, or Wallets.</p>
                                        <button type="button" class="btn btn-primary btn-lg" id="razorpay-pay-btn">
                                            <i class="bi bi-wallet2 me-2"></i>
                                            Pay <?php echo formatCurrency($order['total_amount']); ?>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Security Notice -->
                    <div class="security-notice mt-4">
                        <div class="alert alert-info">
                            <i class="bi bi-shield-check me-2"></i>
                            <strong>Secure Payment:</strong> Your payment information is encrypted and secure. We never store your payment details.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Demo Payment Success Modal -->
<div class="modal fade" id="paymentSuccessModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center p-5">
                <div class="success-icon mb-3">
                    <i class="bi bi-check-circle-fill text-success" style="font-size: 4rem;"></i>
                </div>
                <h4 class="fw-bold mb-3">Payment Successful!</h4>
                <p class="text-muted mb-4">Your order has been processed successfully. You will receive a confirmation email shortly.</p>
                <form method="POST" id="completePaymentForm">
                    <input type="hidden" name="action" value="complete_payment">
                    <input type="hidden" name="payment_id" id="paymentId">
                    <input type="hidden" name="payment_status" value="success">
                    <button type="submit" class="btn btn-primary btn-lg">
                        Continue to Downloads
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
.order-item-image {
    width: 60px;
    height: 60px;
    object-fit: cover;
    border-radius: 8px;
}

.payment-summary {
    background-color: var(--bg-secondary);
    padding: 1.5rem;
    border-radius: 8px;
}

.summary-item {
    display: flex;
    justify-content: space-between;
    margin-bottom: 0.5rem;
}

.summary-total {
    display: flex;
    justify-content: space-between;
    font-size: 1.25rem;
    font-weight: 700;
    padding-top: 1rem;
    border-top: 2px solid var(--border-color);
    margin-top: 1rem;
}

.payment-option {
    border: 2px solid var(--border-color);
    border-radius: 8px;
    margin-bottom: 1rem;
}

.payment-option.active {
    border-color: var(--primary-color);
}

.payment-header {
    background-color: var(--bg-secondary);
    padding: 1rem 1.5rem;
    font-weight: 600;
    border-bottom: 1px solid var(--border-color);
}

.payment-content {
    padding: 1.5rem;
}

.security-notice {
    border-top: 1px solid var(--border-color);
    padding-top: 1rem;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Stripe payment simulation
    document.getElementById('stripe-pay-btn')?.addEventListener('click', function() {
        const cardNumber = document.getElementById('card-number').value;
        const cardCvc = document.getElementById('card-cvc').value;
        const cardMonth = document.getElementById('card-month').value;
        const cardYear = document.getElementById('card-year').value;
        
        if (!cardNumber || !cardCvc || !cardMonth || !cardYear) {
            showNotification('Please fill in all card details', 'error');
            return;
        }
        
        // Simulate payment processing
        this.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Processing...';
        this.disabled = true;
        
        setTimeout(() => {
            const paymentId = 'stripe_' + Math.random().toString(36).substr(2, 9);
            document.getElementById('paymentId').value = paymentId;
            new bootstrap.Modal(document.getElementById('paymentSuccessModal')).show();
        }, 2000);
    });
    
    // PayPal payment simulation
    document.getElementById('paypal-pay-btn')?.addEventListener('click', function() {
        this.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Redirecting...';
        this.disabled = true;
        
        setTimeout(() => {
            const paymentId = 'paypal_' + Math.random().toString(36).substr(2, 9);
            document.getElementById('paymentId').value = paymentId;
            new bootstrap.Modal(document.getElementById('paymentSuccessModal')).show();
        }, 1500);
    });
    
    // Razorpay payment simulation
    document.getElementById('razorpay-pay-btn')?.addEventListener('click', function() {
        this.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Processing...';
        this.disabled = true;
        
        setTimeout(() => {
            const paymentId = 'razorpay_' + Math.random().toString(36).substr(2, 9);
            document.getElementById('paymentId').value = paymentId;
            new bootstrap.Modal(document.getElementById('paymentSuccessModal')).show();
        }, 1500);
    });
});
</script>

<?php
// Helper function to generate order confirmation email
function generateOrderConfirmationEmail($order, $order_items) {
    $html = "
    <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
        <div style='background: linear-gradient(135deg, #6f42c1, #8b5cf6); color: white; padding: 2rem; text-align: center;'>
            <h1 style='margin: 0;'>Order Confirmation</h1>
            <p style='margin: 0.5rem 0 0 0; opacity: 0.9;'>Thank you for your purchase!</p>
        </div>
        
        <div style='padding: 2rem; background: #f8f9fa;'>
            <h2 style='color: #333; margin-top: 0;'>Order Details</h2>
            <p><strong>Order Number:</strong> {$order['order_number']}</p>
            <p><strong>Order Date:</strong> " . date('F j, Y', strtotime($order['created_at'])) . "</p>
            <p><strong>Total Amount:</strong> " . formatCurrency($order['total_amount']) . "</p>
            
            <h3 style='color: #333; margin-top: 2rem;'>Items Purchased:</h3>
            <div style='background: white; border-radius: 8px; padding: 1rem;'>";
    
    foreach ($order_items as $item) {
        $html .= "
            <div style='border-bottom: 1px solid #dee2e6; padding: 1rem 0;'>
                <h4 style='margin: 0 0 0.5rem 0; color: #333;'>{$item['product_title']}</h4>
                <p style='margin: 0; color: #666;'>Quantity: {$item['quantity']} × " . formatCurrency($item['product_price']) . " = " . formatCurrency($item['total_price']) . "</p>
            </div>";
    }
    
    $html .= "
            </div>
            
            <div style='margin-top: 2rem; padding: 1.5rem; background: #e3f2fd; border-radius: 8px;'>
                <h3 style='color: #1976d2; margin-top: 0;'>What's Next?</h3>
                <p style='margin-bottom: 1rem;'>You can now download your digital products from your account dashboard.</p>
                <a href='" . SITE_URL . "/downloads' style='background: #6f42c1; color: white; padding: 12px 24px; text-decoration: none; border-radius: 5px; display: inline-block;'>Download Products</a>
            </div>
            
            <div style='margin-top: 2rem; text-align: center; color: #666;'>
                <p>Need help? Contact our support team at " . SITE_EMAIL . "</p>
                <p>Thank you for choosing " . SITE_NAME . "!</p>
            </div>
        </div>
    </div>";
    
    return $html;
}

include 'includes/footer.php';
?>
