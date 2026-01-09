<?php
$page_title = 'Giỏ hàng';
require_once __DIR__ . '/includes/header.php';

// Get cart items
if (isLoggedIn()) {
    $stmt = $pdo->prepare("SELECT c.*, p.name, p.slug, p.price, p.sale_price, p.main_image, p.quantity as stock 
                           FROM carts c 
                           JOIN products p ON c.product_id = p.id 
                           WHERE c.user_id = ? 
                           ORDER BY c.created_at DESC");
    $stmt->execute([$_SESSION['user_id']]);
} else {
    $session_id = session_id();
    $stmt = $pdo->prepare("SELECT c.*, p.name, p.slug, p.price, p.sale_price, p.main_image, p.quantity as stock 
                           FROM carts c 
                           JOIN products p ON c.product_id = p.id 
                           WHERE c.session_id = ? 
                           ORDER BY c.created_at DESC");
    $stmt->execute([$session_id]);
}

$cart_items = $stmt->fetchAll();

// Calculate totals
$subtotal = 0;
foreach ($cart_items as $item) {
    $price = $item['sale_price'] ?? $item['price'];
    $subtotal += $price * $item['quantity'];
}

$shipping_fee = $subtotal >= 5000000 ? 0 : 50000; // Free shipping for orders >= 5M
$total = $subtotal + $shipping_fee;
?>

<div class="container">
    <div class="page-header">
        <h1><i class="fas fa-shopping-cart"></i> Giỏ hàng của bạn</h1>
    </div>

    <?php if (empty($cart_items)): ?>
        <div class="empty-cart">
            <i class="fas fa-shopping-cart"></i>
            <h2>Giỏ hàng trống</h2>
            <p>Bạn chưa có sản phẩm nào trong giỏ hàng</p>
            <a href="/products.php" class="btn btn-primary">
                <i class="fas fa-shopping-bag"></i> Mua sắm ngay
            </a>
        </div>
    <?php else: ?>
        <div class="cart-content">
            <div class="cart-items">
                <?php foreach ($cart_items as $item): ?>
                <div class="cart-item">
                    <div class="cart-item-image">
                        <a href="/product.php?slug=<?php echo $item['slug']; ?>">
                            <img src="/public/uploads/products/<?php echo htmlspecialchars($item['main_image']); ?>" 
                                 alt="<?php echo htmlspecialchars($item['name']); ?>"
                                 onerror="this.src='/public/images/placeholder.jpg'">
                        </a>
                    </div>
                    
                    <div class="cart-item-details">
                        <h3><a href="/product.php?slug=<?php echo $item['slug']; ?>"><?php echo htmlspecialchars($item['name']); ?></a></h3>
                        <div class="cart-item-price">
                            <?php
                            $price = $item['sale_price'] ?? $item['price'];
                            echo formatPrice($price);
                            ?>
                        </div>
                        <div class="cart-item-stock">
                            <?php if ($item['stock'] < $item['quantity']): ?>
                                <span class="text-danger">Chỉ còn <?php echo $item['stock']; ?> sản phẩm</span>
                            <?php else: ?>
                                <span class="text-success">Còn hàng</span>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="cart-item-quantity">
                        <button type="button" onclick="updateQuantity(<?php echo $item['id']; ?>, -1, <?php echo $item['stock']; ?>)">
                            <i class="fas fa-minus"></i>
                        </button>
                        <input type="number" value="<?php echo $item['quantity']; ?>" 
                               id="qty-<?php echo $item['id']; ?>" 
                               min="1" max="<?php echo $item['stock']; ?>" readonly>
                        <button type="button" onclick="updateQuantity(<?php echo $item['id']; ?>, 1, <?php echo $item['stock']; ?>)">
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>
                    
                    <div class="cart-item-subtotal">
                        <?php echo formatPrice($price * $item['quantity']); ?>
                    </div>
                    
                    <div class="cart-item-remove">
                        <button type="button" onclick="removeFromCart(<?php echo $item['id']; ?>)" class="btn-remove">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <div class="cart-summary">
                <h2>Tóm tắt đơn hàng</h2>
                
                <div class="summary-row">
                    <span>Tạm tính:</span>
                    <span><?php echo formatPrice($subtotal); ?></span>
                </div>
                
                <div class="summary-row">
                    <span>Phí vận chuyển:</span>
                    <span><?php echo $shipping_fee > 0 ? formatPrice($shipping_fee) : 'Miễn phí'; ?></span>
                </div>
                
                <?php if ($subtotal < 5000000): ?>
                <div class="summary-note">
                    <i class="fas fa-info-circle"></i>
                    Mua thêm <?php echo formatPrice(5000000 - $subtotal); ?> để được miễn phí vận chuyển
                </div>
                <?php endif; ?>
                
                <div class="summary-total">
                    <span>Tổng cộng:</span>
                    <span class="total-price"><?php echo formatPrice($total); ?></span>
                </div>
                
                <a href="/checkout.php" class="btn btn-primary btn-block">
                    <i class="fas fa-credit-card"></i> Tiến hành thanh toán
                </a>
                
                <a href="/products.php" class="btn btn-secondary btn-block">
                    <i class="fas fa-arrow-left"></i> Tiếp tục mua hàng
                </a>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
function updateQuantity(cartId, change, maxStock) {
    const input = document.getElementById('qty-' + cartId);
    let newQty = parseInt(input.value) + change;
    
    if (newQty < 1) newQty = 1;
    if (newQty > maxStock) {
        alert('Số lượng vượt quá hàng có sẵn');
        return;
    }
    
    input.value = newQty;
    
    fetch('/cart-api.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'action=update&cart_id=' + cartId + '&quantity=' + newQty
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert(data.message);
        }
    });
}

function removeFromCart(cartId) {
    if (!confirm('Bạn có chắc muốn xóa sản phẩm này?')) return;
    
    fetch('/cart-api.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'action=remove&cart_id=' + cartId
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert(data.message);
        }
    });
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
