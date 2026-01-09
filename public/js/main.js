/**
 * PC SHOP - Main JavaScript
 */

// Add to cart functionality
function addToCart(productId, quantity = 1, buyNow = false) {
    fetch('/cart-api.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `action=add&product_id=${productId}&quantity=${quantity}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update cart badge
            const cartBadge = document.querySelector('.cart-badge');
            if (cartBadge) {
                cartBadge.textContent = data.cart_count;
            } else if (data.cart_count > 0) {
                const cartLink = document.querySelector('.cart-link');
                const badge = document.createElement('span');
                badge.className = 'cart-badge';
                badge.textContent = data.cart_count;
                cartLink.appendChild(badge);
            }
            
            // Show message
            showMessage(data.message, 'success');
            
            // Redirect to cart if buy now
            if (buyNow) {
                setTimeout(() => {
                    window.location.href = '/cart.php';
                }, 500);
            }
        } else {
            showMessage(data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showMessage('Có lỗi xảy ra, vui lòng thử lại', 'error');
    });
}

// Show message
function showMessage(message, type = 'info') {
    const messageDiv = document.createElement('div');
    messageDiv.className = `flash-message flash-${type}`;
    messageDiv.innerHTML = `
        <div class="container">
            <span>${message}</span>
            <button class="close-flash" onclick="this.parentElement.parentElement.remove()">×</button>
        </div>
    `;
    
    const mainContent = document.querySelector('.main-content');
    if (mainContent) {
        mainContent.insertBefore(messageDiv, mainContent.firstChild);
        
        // Auto remove after 5 seconds
        setTimeout(() => {
            messageDiv.remove();
        }, 5000);
    }
}

// Add to cart button click handlers
document.addEventListener('DOMContentLoaded', function() {
    // Add to cart buttons
    document.querySelectorAll('.add-to-cart').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const productId = this.dataset.productId;
            const quantityInput = document.getElementById('quantity');
            const quantity = quantityInput ? parseInt(quantityInput.value) : 1;
            addToCart(productId, quantity);
        });
    });
    
    // Buy now buttons
    document.querySelectorAll('.buy-now').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const productId = this.dataset.productId;
            const quantityInput = document.getElementById('quantity');
            const quantity = quantityInput ? parseInt(quantityInput.value) : 1;
            addToCart(productId, quantity, true);
        });
    });
});

// Smooth scroll to top
function scrollToTop() {
    window.scrollTo({
        top: 0,
        behavior: 'smooth'
    });
}

// Auto-hide flash messages
document.addEventListener('DOMContentLoaded', function() {
    const flashMessages = document.querySelectorAll('.flash-message');
    flashMessages.forEach(message => {
        setTimeout(() => {
            message.style.opacity = '0';
            setTimeout(() => {
                message.remove();
            }, 300);
        }, 5000);
    });
});

// Image lazy loading fallback
document.addEventListener('DOMContentLoaded', function() {
    const images = document.querySelectorAll('img[data-src]');
    images.forEach(img => {
        img.src = img.dataset.src;
    });
});

// Form validation helper
function validateEmail(email) {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
}

function validatePhone(phone) {
    const re = /^[0-9]{10,11}$/;
    return re.test(phone.replace(/[^0-9]/g, ''));
}

// Debounce function for search
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// Search functionality
const searchInput = document.querySelector('.search-box input');
if (searchInput) {
    searchInput.addEventListener('input', debounce(function(e) {
        const query = e.target.value.trim();
        if (query.length >= 3) {
            // You can implement live search here
            console.log('Searching for:', query);
        }
    }, 500));
}

// Price formatter
function formatPrice(price) {
    return new Intl.NumberFormat('vi-VN', {
        style: 'currency',
        currency: 'VND'
    }).format(price);
}

// Quantity selector
function setupQuantitySelector() {
    const quantityInputs = document.querySelectorAll('input[type="number"]');
    quantityInputs.forEach(input => {
        input.addEventListener('change', function() {
            const min = parseInt(this.min) || 1;
            const max = parseInt(this.max) || Infinity;
            let value = parseInt(this.value) || min;
            
            if (value < min) value = min;
            if (value > max) value = max;
            
            this.value = value;
        });
    });
}

document.addEventListener('DOMContentLoaded', setupQuantitySelector);

// Mobile menu toggle (if needed)
function toggleMobileMenu() {
    const nav = document.querySelector('.main-nav');
    nav.classList.toggle('active');
}

// Loading indicator
function showLoading() {
    const loader = document.createElement('div');
    loader.className = 'loading-overlay';
    loader.innerHTML = '<div class="spinner"></div>';
    document.body.appendChild(loader);
}

function hideLoading() {
    const loader = document.querySelector('.loading-overlay');
    if (loader) {
        loader.remove();
    }
}

// Confirm delete
function confirmDelete(message = 'Bạn có chắc chắn muốn xóa?') {
    return confirm(message);
}

// Update cart count
function updateCartCount() {
    fetch('/cart-api.php?action=get_count')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const cartBadge = document.querySelector('.cart-badge');
                if (data.count > 0) {
                    if (cartBadge) {
                        cartBadge.textContent = data.count;
                    } else {
                        const cartLink = document.querySelector('.cart-link');
                        const badge = document.createElement('span');
                        badge.className = 'cart-badge';
                        badge.textContent = data.count;
                        cartLink.appendChild(badge);
                    }
                } else if (cartBadge) {
                    cartBadge.remove();
                }
            }
        });
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    // Update cart count
    updateCartCount();
    
    // Add smooth scrolling to all links with #
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                e.preventDefault();
                target.scrollIntoView({
                    behavior: 'smooth'
                });
            }
        });
    });
});

console.log('PC Shop JS loaded successfully');
