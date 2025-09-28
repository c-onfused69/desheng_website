/**
 * Desh Engineering Ecommerce - Main JavaScript
 */

// Global variables
let cart = [];
let wishlist = [];

// DOM Content Loaded
document.addEventListener('DOMContentLoaded', function() {
    initializeApp();
});

// Initialize application
function initializeApp() {
    // Initialize tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    // Initialize popovers
    const popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
    popoverTriggerList.map(function (popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl);
    });
    
    // Initialize smooth scrolling
    initializeSmoothScrolling();
    
    // Initialize product interactions
    initializeProductInteractions();
    
    // Initialize cart functionality
    initializeCartFunctionality();
    
    // Initialize search functionality
    initializeSearchFunctionality();
    
    // Initialize form validations
    initializeFormValidations();
    
    // Initialize lazy loading
    initializeLazyLoading();
    
    // Initialize animations
    initializeAnimations();
}

// Smooth scrolling for anchor links
function initializeSmoothScrolling() {
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
}

// Product interactions
function initializeProductInteractions() {
    // Add to cart buttons
    document.querySelectorAll('.add-to-cart').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const productId = this.dataset.productId;
            const quantity = this.dataset.quantity || 1;
            addToCart(productId, quantity);
        });
    });
    
    // Add to wishlist buttons
    document.querySelectorAll('.add-to-wishlist').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const productId = this.dataset.productId;
            toggleWishlist(productId);
        });
    });
    
    // Quick view buttons
    document.querySelectorAll('.quick-view').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const productId = this.dataset.productId;
            showQuickView(productId);
        });
    });
    
    // Product image gallery
    initializeProductGallery();
}

// Product image gallery
function initializeProductGallery() {
    const mainImage = document.querySelector('.product-main-image');
    const thumbnails = document.querySelectorAll('.product-thumbnail');
    
    if (mainImage && thumbnails.length > 0) {
        thumbnails.forEach(thumbnail => {
            thumbnail.addEventListener('click', function() {
                const newSrc = this.dataset.fullImage || this.src;
                mainImage.src = newSrc;
                
                // Update active thumbnail
                thumbnails.forEach(t => t.classList.remove('active'));
                this.classList.add('active');
            });
        });
    }
}

// Cart functionality
function initializeCartFunctionality() {
    // Update quantity buttons
    document.querySelectorAll('.quantity-btn').forEach(button => {
        button.addEventListener('click', function() {
            const input = this.parentElement.querySelector('.quantity-input');
            const action = this.dataset.action;
            let currentValue = parseInt(input.value);
            
            if (action === 'increase') {
                input.value = currentValue + 1;
            } else if (action === 'decrease' && currentValue > 1) {
                input.value = currentValue - 1;
            }
            
            // Update cart item
            const cartItemId = input.dataset.cartItemId;
            if (cartItemId) {
                updateCartItem(cartItemId, input.value);
            }
        });
    });
    
    // Remove cart item buttons
    document.querySelectorAll('.remove-cart-item').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const cartItemId = this.dataset.cartItemId;
            removeCartItem(cartItemId);
        });
    });
    
    // Apply coupon
    const applyCouponBtn = document.querySelector('.apply-coupon');
    if (applyCouponBtn) {
        applyCouponBtn.addEventListener('click', function() {
            const couponCode = document.querySelector('.coupon-input').value;
            applyCoupon(couponCode);
        });
    }
}

// Add to cart function
async function addToCart(productId, quantity = 1) {
    try {
        showLoading();
        
        const response = await fetch('/desh-eshop/api/cart/add.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                product_id: productId,
                quantity: quantity
            })
        });
        
        const result = await response.json();
        
        if (result.success) {
            showNotification('Product added to cart!', 'success');
            updateCartCount();
            
            // Update button state
            const button = document.querySelector(`[data-product-id="${productId}"]`);
            if (button) {
                button.innerHTML = '<i class="bi bi-check"></i> Added';
                button.classList.add('btn-success');
                setTimeout(() => {
                    button.innerHTML = '<i class="bi bi-cart-plus"></i> Add to Cart';
                    button.classList.remove('btn-success');
                }, 2000);
            }
        } else {
            showNotification(result.message || 'Failed to add product to cart', 'error');
        }
    } catch (error) {
        console.error('Error adding to cart:', error);
        showNotification('An error occurred. Please try again.', 'error');
    } finally {
        hideLoading();
    }
}

// Update cart item
async function updateCartItem(cartItemId, quantity) {
    try {
        const response = await fetch('/desh-eshop/api/cart/update.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                cart_item_id: cartItemId,
                quantity: quantity
            })
        });
        
        const result = await response.json();
        
        if (result.success) {
            updateCartTotals();
        } else {
            showNotification(result.message || 'Failed to update cart', 'error');
        }
    } catch (error) {
        console.error('Error updating cart:', error);
        showNotification('An error occurred. Please try again.', 'error');
    }
}

// Remove cart item
async function removeCartItem(cartItemId) {
    if (!confirm('Are you sure you want to remove this item from your cart?')) {
        return;
    }
    
    try {
        const response = await fetch('/desh-eshop/api/cart/remove.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                cart_item_id: cartItemId
            })
        });
        
        const result = await response.json();
        
        if (result.success) {
            // Remove item from DOM
            const cartItem = document.querySelector(`[data-cart-item-id="${cartItemId}"]`).closest('.cart-item');
            cartItem.remove();
            
            updateCartTotals();
            updateCartCount();
            showNotification('Item removed from cart', 'success');
        } else {
            showNotification(result.message || 'Failed to remove item', 'error');
        }
    } catch (error) {
        console.error('Error removing cart item:', error);
        showNotification('An error occurred. Please try again.', 'error');
    }
}

// Toggle wishlist
async function toggleWishlist(productId) {
    try {
        const response = await fetch('/desh-eshop/api/wishlist/toggle.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                product_id: productId
            })
        });
        
        const result = await response.json();
        
        if (result.success) {
            const button = document.querySelector(`.add-to-wishlist[data-product-id="${productId}"]`);
            if (button) {
                const icon = button.querySelector('i');
                if (result.added) {
                    icon.classList.remove('bi-heart');
                    icon.classList.add('bi-heart-fill');
                    button.classList.add('text-danger');
                    showNotification('Added to wishlist!', 'success');
                } else {
                    icon.classList.remove('bi-heart-fill');
                    icon.classList.add('bi-heart');
                    button.classList.remove('text-danger');
                    showNotification('Removed from wishlist', 'info');
                }
            }
        } else {
            showNotification(result.message || 'Failed to update wishlist', 'error');
        }
    } catch (error) {
        console.error('Error updating wishlist:', error);
        showNotification('An error occurred. Please try again.', 'error');
    }
}

// Search functionality
function initializeSearchFunctionality() {
    const searchForm = document.querySelector('.search-form');
    const searchInput = document.querySelector('.search-input');
    const searchSuggestions = document.querySelector('.search-suggestions');
    
    if (searchInput) {
        let searchTimeout;
        
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            const query = this.value.trim();
            
            if (query.length >= 2) {
                searchTimeout = setTimeout(() => {
                    fetchSearchSuggestions(query);
                }, 300);
            } else {
                hideSearchSuggestions();
            }
        });
        
        // Hide suggestions when clicking outside
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.search-container')) {
                hideSearchSuggestions();
            }
        });
    }
}

// Fetch search suggestions
async function fetchSearchSuggestions(query) {
    try {
        const response = await fetch(`/desh-eshop/api/search/suggestions.php?q=${encodeURIComponent(query)}`);
        const suggestions = await response.json();
        
        displaySearchSuggestions(suggestions);
    } catch (error) {
        console.error('Error fetching search suggestions:', error);
    }
}

// Display search suggestions
function displaySearchSuggestions(suggestions) {
    const suggestionsContainer = document.querySelector('.search-suggestions');
    if (!suggestionsContainer) return;
    
    if (suggestions.length === 0) {
        hideSearchSuggestions();
        return;
    }
    
    let html = '<div class="list-group">';
    suggestions.forEach(suggestion => {
        html += `
            <a href="/desh-eshop/products/${suggestion.slug}" class="list-group-item list-group-item-action">
                <div class="d-flex align-items-center">
                    <img src="${suggestion.image}" alt="${suggestion.title}" class="me-3" style="width: 40px; height: 40px; object-fit: cover; border-radius: 4px;">
                    <div>
                        <div class="fw-bold">${suggestion.title}</div>
                        <small class="text-muted">${suggestion.category}</small>
                    </div>
                </div>
            </a>
        `;
    });
    html += '</div>';
    
    suggestionsContainer.innerHTML = html;
    suggestionsContainer.classList.remove('d-none');
}

// Hide search suggestions
function hideSearchSuggestions() {
    const suggestionsContainer = document.querySelector('.search-suggestions');
    if (suggestionsContainer) {
        suggestionsContainer.classList.add('d-none');
    }
}

// Form validations
function initializeFormValidations() {
    // Bootstrap form validation
    const forms = document.querySelectorAll('.needs-validation');
    
    Array.prototype.slice.call(forms).forEach(function(form) {
        form.addEventListener('submit', function(event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            
            form.classList.add('was-validated');
        }, false);
    });
    
    // Custom validations
    const emailInputs = document.querySelectorAll('input[type="email"]');
    emailInputs.forEach(input => {
        input.addEventListener('blur', function() {
            validateEmail(this);
        });
    });
    
    const passwordInputs = document.querySelectorAll('input[type="password"]');
    passwordInputs.forEach(input => {
        input.addEventListener('input', function() {
            validatePassword(this);
        });
    });
}

// Email validation
function validateEmail(input) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    const isValid = emailRegex.test(input.value);
    
    if (input.value && !isValid) {
        input.setCustomValidity('Please enter a valid email address');
    } else {
        input.setCustomValidity('');
    }
}

// Password validation
function validatePassword(input) {
    const password = input.value;
    const minLength = 8;
    const hasUpperCase = /[A-Z]/.test(password);
    const hasLowerCase = /[a-z]/.test(password);
    const hasNumbers = /\d/.test(password);
    const hasSpecialChar = /[!@#$%^&*(),.?":{}|<>]/.test(password);
    
    let message = '';
    
    if (password.length < minLength) {
        message = `Password must be at least ${minLength} characters long`;
    } else if (!hasUpperCase) {
        message = 'Password must contain at least one uppercase letter';
    } else if (!hasLowerCase) {
        message = 'Password must contain at least one lowercase letter';
    } else if (!hasNumbers) {
        message = 'Password must contain at least one number';
    } else if (!hasSpecialChar) {
        message = 'Password must contain at least one special character';
    }
    
    input.setCustomValidity(message);
    
    // Update password strength indicator
    updatePasswordStrength(input, password);
}

// Update password strength indicator
function updatePasswordStrength(input, password) {
    const strengthIndicator = input.parentElement.querySelector('.password-strength');
    if (!strengthIndicator) return;
    
    let strength = 0;
    let strengthText = '';
    let strengthClass = '';
    
    if (password.length >= 8) strength++;
    if (/[A-Z]/.test(password)) strength++;
    if (/[a-z]/.test(password)) strength++;
    if (/\d/.test(password)) strength++;
    if (/[!@#$%^&*(),.?":{}|<>]/.test(password)) strength++;
    
    switch (strength) {
        case 0:
        case 1:
            strengthText = 'Very Weak';
            strengthClass = 'text-danger';
            break;
        case 2:
            strengthText = 'Weak';
            strengthClass = 'text-warning';
            break;
        case 3:
            strengthText = 'Fair';
            strengthClass = 'text-info';
            break;
        case 4:
            strengthText = 'Good';
            strengthClass = 'text-primary';
            break;
        case 5:
            strengthText = 'Strong';
            strengthClass = 'text-success';
            break;
    }
    
    strengthIndicator.textContent = strengthText;
    strengthIndicator.className = `password-strength small ${strengthClass}`;
}

// Lazy loading
function initializeLazyLoading() {
    const images = document.querySelectorAll('img[data-src]');
    
    if ('IntersectionObserver' in window) {
        const imageObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    img.src = img.dataset.src;
                    img.classList.remove('lazy');
                    imageObserver.unobserve(img);
                }
            });
        });
        
        images.forEach(img => imageObserver.observe(img));
    } else {
        // Fallback for browsers without IntersectionObserver
        images.forEach(img => {
            img.src = img.dataset.src;
            img.classList.remove('lazy');
        });
    }
}

// Animations
function initializeAnimations() {
    // Animate elements on scroll
    const animatedElements = document.querySelectorAll('.animate-on-scroll');
    
    if ('IntersectionObserver' in window) {
        const animationObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('fade-in-up');
                }
            });
        }, {
            threshold: 0.1
        });
        
        animatedElements.forEach(el => animationObserver.observe(el));
    }
}

// Utility functions
function updateCartCount() {
    fetch('/desh-eshop/api/cart/count.php')
        .then(response => response.json())
        .then(data => {
            const cartCounts = document.querySelectorAll('#cartCount, #mobileCartCount');
            cartCounts.forEach(element => {
                element.textContent = data.count || 0;
                element.style.display = data.count > 0 ? 'inline' : 'none';
            });
        })
        .catch(error => console.error('Error updating cart count:', error));
}

function updateCartTotals() {
    fetch('/desh-eshop/api/cart/totals.php')
        .then(response => response.json())
        .then(data => {
            const subtotalElements = document.querySelectorAll('.cart-subtotal');
            const taxElements = document.querySelectorAll('.cart-tax');
            const totalElements = document.querySelectorAll('.cart-total');
            
            subtotalElements.forEach(el => el.textContent = data.subtotal);
            taxElements.forEach(el => el.textContent = data.tax);
            totalElements.forEach(el => el.textContent = data.total);
        })
        .catch(error => console.error('Error updating cart totals:', error));
}

function applyCoupon(couponCode) {
    if (!couponCode.trim()) {
        showNotification('Please enter a coupon code', 'warning');
        return;
    }
    
    fetch('/desh-eshop/api/coupons/apply.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ coupon_code: couponCode })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification(`Coupon applied! You saved ${data.discount_amount}`, 'success');
            updateCartTotals();
        } else {
            showNotification(data.message || 'Invalid coupon code', 'error');
        }
    })
    .catch(error => {
        console.error('Error applying coupon:', error);
        showNotification('An error occurred. Please try again.', 'error');
    });
}

function showQuickView(productId) {
    // Implementation for quick view modal
    fetch(`/desh-eshop/api/products/${productId}.php`)
        .then(response => response.json())
        .then(product => {
            // Create and show quick view modal
            const modalHtml = createQuickViewModal(product);
            document.body.insertAdjacentHTML('beforeend', modalHtml);
            
            const modal = new bootstrap.Modal(document.getElementById('quickViewModal'));
            modal.show();
            
            // Remove modal from DOM when hidden
            document.getElementById('quickViewModal').addEventListener('hidden.bs.modal', function() {
                this.remove();
            });
        })
        .catch(error => console.error('Error loading product:', error));
}

function createQuickViewModal(product) {
    return `
        <div class="modal fade" id="quickViewModal" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">${product.title}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <img src="${product.image}" alt="${product.title}" class="img-fluid rounded">
                            </div>
                            <div class="col-md-6">
                                <p class="text-muted">${product.short_description}</p>
                                <div class="price mb-3">
                                    <span class="h4 text-primary">$${product.price}</span>
                                    ${product.sale_price ? `<span class="text-muted text-decoration-line-through ms-2">$${product.sale_price}</span>` : ''}
                                </div>
                                <button class="btn btn-primary add-to-cart" data-product-id="${product.id}">
                                    <i class="bi bi-cart-plus"></i> Add to Cart
                                </button>
                                <button class="btn btn-outline-secondary ms-2 add-to-wishlist" data-product-id="${product.id}">
                                    <i class="bi bi-heart"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
}

function showLoading() {
    const loadingOverlay = document.getElementById('loadingOverlay');
    if (loadingOverlay) {
        loadingOverlay.classList.remove('d-none');
    }
}

function hideLoading() {
    const loadingOverlay = document.getElementById('loadingOverlay');
    if (loadingOverlay) {
        loadingOverlay.classList.add('d-none');
    }
}

function showNotification(message, type = 'info') {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `alert alert-${type === 'error' ? 'danger' : type} alert-dismissible fade show position-fixed`;
    notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    notification.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(notification);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        if (notification.parentElement) {
            notification.remove();
        }
    }, 5000);
}

// Export functions for global use
window.addToCart = addToCart;
window.toggleWishlist = toggleWishlist;
window.showQuickView = showQuickView;
window.showNotification = showNotification;
window.updateCartCount = updateCartCount;
