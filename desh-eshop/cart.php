<?php
require_once 'config/config.php';

// Check if user is logged in
if (!isLoggedIn()) {
    redirect(SITE_URL . '/login?redirect=' . urlencode($_SERVER['REQUEST_URI']));
}

$page_title = 'Shopping Cart';
$page_description = 'Review your selected digital products before checkout.';
$body_class = 'cart-page';

$current_user = getCurrentUser();
$error_message = '';
$success_message = '';

// Handle cart actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'update_quantity') {
        $cart_item_id = intval($_POST['cart_item_id'] ?? 0);
        $quantity = intval($_POST['quantity'] ?? 1);
        
        if ($cart_item_id > 0 && $quantity > 0) {
            try {
                $db = getDB();
                $stmt = $db->prepare("
                    UPDATE cart 
                    SET quantity = ?, updated_at = NOW() 
                    WHERE id = ? AND user_id = ?
                ");
                $stmt->execute([$quantity, $cart_item_id, $current_user['id']]);
                $success_message = 'Cart updated successfully!';
            } catch (Exception $e) {
                error_log("Cart update error: " . $e->getMessage());
                $error_message = 'Failed to update cart.';
            }
        }
    } elseif ($action === 'remove_item') {
        $cart_item_id = intval($_POST['cart_item_id'] ?? 0);
        
        if ($cart_item_id > 0) {
            try {
                $db = getDB();
                $stmt = $db->prepare("DELETE FROM cart WHERE id = ? AND user_id = ?");
                $stmt->execute([$cart_item_id, $current_user['id']]);
                $success_message = 'Item removed from cart!';
            } catch (Exception $e) {
                error_log("Cart remove error: " . $e->getMessage());
                $error_message = 'Failed to remove item from cart.';
            }
        }
    } elseif ($action === 'clear_cart') {
        try {
            $db = getDB();
            $stmt = $db->prepare("DELETE FROM cart WHERE user_id = ?");
            $stmt->execute([$current_user['id']]);
            $success_message = 'Cart cleared successfully!';
        } catch (Exception $e) {
            error_log("Cart clear error: " . $e->getMessage());
            $error_message = 'Failed to clear cart.';
        }
    }
}

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
    error_log("Cart fetch error: " . $e->getMessage());
    $cart_items = [];
    $subtotal = $tax_amount = $total = 0;
}

include 'includes/header.php';
?>

<div class="container py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo SITE_URL; ?>">Home</a></li>
            <li class="breadcrumb-item active">Shopping Cart</li>
        </ol>
    </nav>
    
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="display-6 fw-bold mb-0">Shopping Cart</h1>
        <?php if (!empty($cart_items)): ?>
            <form method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to clear your cart?');">
                <input type="hidden" name="action" value="clear_cart">
                <button type="submit" class="btn btn-outline-danger btn-sm">
                    <i class="bi bi-trash me-2"></i>
                    Clear Cart
                </button>
            </form>
        <?php endif; ?>
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
    
    <?php if (!empty($cart_items)): ?>
        <div class="row">
            <!-- Cart Items -->
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="bi bi-cart3 me-2"></i>
                            Cart Items (<?php echo count($cart_items); ?>)
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        <?php foreach ($cart_items as $index => $item): ?>
                            <?php
                            $price = $item['sale_price'] && $item['sale_price'] < $item['price'] ? $item['sale_price'] : $item['price'];
                            $item_total = $price * $item['quantity'];
                            ?>
                            <div class="cart-item p-4 <?php echo $index < count($cart_items) - 1 ? 'border-bottom' : ''; ?>">
                                <div class="row align-items-center">
                                    <div class="col-md-2">
                                        <img src="<?php echo $item['primary_image'] ? SITE_URL . '/uploads/products/' . $item['primary_image'] : SITE_URL . '/assets/images/placeholder-product.jpg'; ?>" 
                                             alt="<?php echo htmlspecialchars($item['title']); ?>"
                                             class="cart-item-image img-fluid rounded">
                                    </div>
                                    
                                    <div class="col-md-4">
                                        <h6 class="fw-bold mb-1">
                                            <a href="<?php echo SITE_URL; ?>/product/<?php echo $item['slug']; ?>" 
                                               class="text-decoration-none text-dark">
                                                <?php echo htmlspecialchars($item['title']); ?>
                                            </a>
                                        </h6>
                                        <p class="text-muted small mb-0">
                                            <?php echo truncateText($item['short_description'], 80); ?>
                                        </p>
                                    </div>
                                    
                                    <div class="col-md-2">
                                        <div class="price">
                                            <?php if ($item['sale_price'] && $item['sale_price'] < $item['price']): ?>
                                                <div class="fw-bold text-primary"><?php echo formatCurrency($item['sale_price']); ?></div>
                                                <small class="text-muted text-decoration-line-through">
                                                    <?php echo formatCurrency($item['price']); ?>
                                                </small>
                                            <?php else: ?>
                                                <div class="fw-bold text-primary"><?php echo formatCurrency($item['price']); ?></div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-2">
                                        <div class="quantity-controls">
                                            <form method="POST" class="d-inline quantity-form">
                                                <input type="hidden" name="action" value="update_quantity">
                                                <input type="hidden" name="cart_item_id" value="<?php echo $item['id']; ?>">
                                                <div class="input-group">
                                                    <button type="button" class="btn btn-outline-secondary btn-sm quantity-btn" data-action="decrease">
                                                        <i class="bi bi-dash"></i>
                                                    </button>
                                                    <input type="number" 
                                                           class="form-control form-control-sm text-center quantity-input" 
                                                           name="quantity" 
                                                           value="<?php echo $item['quantity']; ?>" 
                                                           min="1" 
                                                           max="10"
                                                           data-cart-item-id="<?php echo $item['id']; ?>">
                                                    <button type="button" class="btn btn-outline-secondary btn-sm quantity-btn" data-action="increase">
                                                        <i class="bi bi-plus"></i>
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-2">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div class="fw-bold"><?php echo formatCurrency($item_total); ?></div>
                                            <form method="POST" class="d-inline">
                                                <input type="hidden" name="action" value="remove_item">
                                                <input type="hidden" name="cart_item_id" value="<?php echo $item['id']; ?>">
                                                <button type="submit" 
                                                        class="btn btn-outline-danger btn-sm"
                                                        onclick="return confirm('Remove this item from cart?');"
                                                        title="Remove item">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                
                <!-- Continue Shopping -->
                <div class="mt-3">
                    <a href="<?php echo SITE_URL; ?>/products" class="btn btn-outline-primary">
                        <i class="bi bi-arrow-left me-2"></i>
                        Continue Shopping
                    </a>
                </div>
            </div>
            
            <!-- Order Summary -->
            <div class="col-lg-4">
                <div class="order-summary">
                    <h5 class="fw-bold mb-3">
                        <i class="bi bi-receipt me-2"></i>
                        Order Summary
                    </h5>
                    
                    <div class="order-summary-item">
                        <span>Subtotal (<?php echo count($cart_items); ?> items)</span>
                        <span class="cart-subtotal"><?php echo formatCurrency($subtotal); ?></span>
                    </div>
                    
                    <?php if ($tax_amount > 0): ?>
                        <div class="order-summary-item">
                            <span>Tax (<?php echo getSetting('tax_rate', 0); ?>%)</span>
                            <span class="cart-tax"><?php echo formatCurrency($tax_amount); ?></span>
                        </div>
                    <?php endif; ?>
                    
                    <div class="order-summary-total">
                        <span>Total</span>
                        <span class="cart-total"><?php echo formatCurrency($total); ?></span>
                    </div>
                    
                    <!-- Coupon Code -->
                    <div class="mt-4">
                        <div class="input-group">
                            <input type="text" 
                                   class="form-control coupon-input" 
                                   placeholder="Coupon code">
                            <button class="btn btn-outline-secondary apply-coupon" type="button">
                                Apply
                            </button>
                        </div>
                    </div>
                    
                    <!-- Checkout Button -->
                    <div class="d-grid mt-4">
                        <a href="<?php echo SITE_URL; ?>/checkout" class="btn btn-primary btn-lg">
                            <i class="bi bi-credit-card me-2"></i>
                            Proceed to Checkout
                        </a>
                    </div>
                    
                    <!-- Security Badge -->
                    <div class="text-center mt-3">
                        <small class="text-muted">
                            <i class="bi bi-shield-check me-1"></i>
                            Secure checkout with SSL encryption
                        </small>
                    </div>
                </div>
            </div>
        </div>
        
    <?php else: ?>
        <!-- Empty Cart -->
        <div class="text-center py-5">
            <i class="bi bi-cart-x display-1 text-muted mb-4"></i>
            <h3 class="text-muted mb-3">Your cart is empty</h3>
            <p class="text-muted mb-4">
                Looks like you haven't added any items to your cart yet. 
                Start shopping to fill it up!
            </p>
            <div class="d-flex gap-2 justify-content-center">
                <a href="<?php echo SITE_URL; ?>/products" class="btn btn-primary btn-lg">
                    <i class="bi bi-grid me-2"></i>
                    Browse Products
                </a>
                <a href="<?php echo SITE_URL; ?>" class="btn btn-outline-primary btn-lg">
                    <i class="bi bi-house me-2"></i>
                    Go Home
                </a>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Quantity controls
    document.querySelectorAll('.quantity-btn').forEach(button => {
        button.addEventListener('click', function() {
            const input = this.parentElement.querySelector('.quantity-input');
            const action = this.dataset.action;
            let currentValue = parseInt(input.value);
            
            if (action === 'increase' && currentValue < 10) {
                input.value = currentValue + 1;
            } else if (action === 'decrease' && currentValue > 1) {
                input.value = currentValue - 1;
            }
            
            // Auto-submit the form after a short delay
            clearTimeout(input.updateTimeout);
            input.updateTimeout = setTimeout(() => {
                input.closest('form').submit();
            }, 1000);
        });
    });
    
    // Quantity input change
    document.querySelectorAll('.quantity-input').forEach(input => {
        input.addEventListener('change', function() {
            if (this.value < 1) this.value = 1;
            if (this.value > 10) this.value = 10;
            
            clearTimeout(this.updateTimeout);
            this.updateTimeout = setTimeout(() => {
                this.closest('form').submit();
            }, 500);
        });
    });
    
    // Apply coupon
    document.querySelector('.apply-coupon')?.addEventListener('click', function() {
        const couponCode = document.querySelector('.coupon-input').value.trim();
        if (couponCode) {
            applyCoupon(couponCode);
        } else {
            showNotification('Please enter a coupon code', 'warning');
        }
    });
    
    // Enter key on coupon input
    document.querySelector('.coupon-input')?.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            document.querySelector('.apply-coupon').click();
        }
    });
});

// Update cart totals after quantity changes
function updateCartTotals() {
    fetch('<?php echo SITE_URL; ?>/api/cart/totals.php')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.querySelector('.cart-subtotal').textContent = data.subtotal_formatted;
                document.querySelector('.cart-tax').textContent = data.tax_formatted;
                document.querySelector('.cart-total').textContent = data.total_formatted;
                updateCartCount();
            }
        })
        .catch(error => console.error('Error updating cart totals:', error));
}
</script>

<?php include 'includes/footer.php'; ?>
