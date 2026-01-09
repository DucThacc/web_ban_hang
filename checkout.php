<?php
$page_title = 'Thanh toán';
require_once __DIR__ . '/includes/header.php';

// Get cart items
if (isLoggedIn()) {
    $stmt = $pdo->prepare("SELECT c.*, p.name, p.price, p.sale_price, p.main_image, p.quantity as stock 
                           FROM carts c 
                           JOIN products p ON c.product_id = p.id 
                           WHERE c.user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    
    // Get user info
    $stmt_user = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt_user->execute([$_SESSION['user_id']]);
    $user = $stmt_user->fetch();
} else {
    $stmt = $pdo->prepare("SELECT c.*, p.name, p.price, p.sale_price, p.main_image, p.quantity as stock 
                           FROM carts c 
                           JOIN products p ON c.product_id = p.id 
                           WHERE c.session_id = ?");
    $stmt->execute([session_id()]);
    $user = null;
}

$cart_items = $stmt->fetchAll();

// Empty cart
if (empty($cart_items)) {
    redirect('/cart.php');
}

// Calculate totals
$subtotal = 0;
foreach ($cart_items as $item) {
    $price = $item['sale_price'] ?? $item['price'];
    $subtotal += $price * $item['quantity'];
}

$shipping_fee = $subtotal >= 5000000 ? 0 : 50000;
$total = $subtotal + $shipping_fee;

// Process checkout
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $customer_name = sanitize($_POST['customer_name'] ?? '');
    $customer_email = sanitize($_POST['customer_email'] ?? '');
    $customer_phone = sanitize($_POST['customer_phone'] ?? '');
    $customer_address = sanitize($_POST['customer_address'] ?? '');
    $note = sanitize($_POST['note'] ?? '');
    $update_profile = isset($_POST['update_profile']) && isLoggedIn();
    
    // Validation
    if (empty($customer_name)) $errors[] = 'Vui lòng nhập họ tên';
    if (empty($customer_email)) $errors[] = 'Vui lòng nhập email';
    if (empty($customer_phone)) $errors[] = 'Vui lòng nhập số điện thoại';
    if (empty($customer_address)) $errors[] = 'Vui lòng nhập địa chỉ';
    
    if (empty($errors)) {
        try {
            $pdo->beginTransaction();
            
            // Generate order code
            $order_code = 'ORD-' . date('Ymd') . '-' . str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT);
            
            // Check order code uniqueness
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM orders WHERE order_code = ?");
            $stmt->execute([$order_code]);
            if ($stmt->fetchColumn() > 0) {
                $order_code .= '-' . uniqid();
            }
            
            // Create order
            $stmt = $pdo->prepare("INSERT INTO orders (user_id, order_code, customer_name, customer_email, customer_phone, customer_address, subtotal, shipping_fee, total, note, status) 
                                   VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending')");
            
            $user_id = isLoggedIn() ? $_SESSION['user_id'] : null;
            $stmt->execute([
                $user_id,
                $order_code,
                $customer_name,
                $customer_email,
                $customer_phone,
                $customer_address,
                $subtotal,
                $shipping_fee,
                $total,
                $note
            ]);
            
            $order_id = $pdo->lastInsertId();
            
            // Create order items and update stock
            foreach ($cart_items as $item) {
                $price = $item['sale_price'] ?? $item['price'];
                $item_subtotal = $price * $item['quantity'];
                
                $stmt = $pdo->prepare("INSERT INTO order_items (order_id, product_id, product_name, product_image, price, quantity, subtotal) 
                                       VALUES (?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([
                    $order_id,
                    $item['product_id'],
                    $item['name'],
                    $item['main_image'],
                    $price,
                    $item['quantity'],
                    $item_subtotal
                ]);
                
                // Update product stock
                $stmt = $pdo->prepare("UPDATE products SET quantity = quantity - ? WHERE id = ?");
                $stmt->execute([$item['quantity'], $item['product_id']]);
            }
            
            // Clear cart
            if (isLoggedIn()) {
                $stmt = $pdo->prepare("DELETE FROM carts WHERE user_id = ?");
                $stmt->execute([$_SESSION['user_id']]);
            } else {
                $stmt = $pdo->prepare("DELETE FROM carts WHERE session_id = ?");
                $stmt->execute([session_id()]);
            }
            
            // Update user profile if requested
            if ($update_profile && isLoggedIn()) {
                $stmt = $pdo->prepare("UPDATE users SET full_name = ?, phone = ?, address = ? WHERE id = ?");
                $stmt->execute([$customer_name, $customer_phone, $customer_address, $_SESSION['user_id']]);
            }
            
            $pdo->commit();
            
            setFlashMessage('success', 'Đặt hàng thành công! Mã đơn hàng: ' . $order_code);
            redirect('/order-success.php?code=' . $order_code);
            
        } catch (Exception $e) {
            $pdo->rollBack();
            $errors[] = 'Có lỗi xảy ra: ' . $e->getMessage();
        }
    }
}
?>

<div class="container">
    <div class="page-header">
        <h1><i class="fas fa-credit-card"></i> Thanh toán</h1>
    </div>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <?php foreach ($errors as $error): ?>
                <p><?php echo htmlspecialchars($error); ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <div class="checkout-content">
        <form method="POST" action="" class="checkout-form">
            <div class="checkout-info">
                <h2><i class="fas fa-user"></i> Thông tin người nhận</h2>
                
                <div class="form-group">
                    <label>Họ và tên *</label>
                    <input type="text" name="customer_name" class="form-control" 
                           value="<?php echo htmlspecialchars($user['full_name'] ?? $customer_name ?? ''); ?>" required>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label>Email *</label>
                        <input type="email" name="customer_email" class="form-control" 
                               value="<?php echo htmlspecialchars($user['email'] ?? $customer_email ?? ''); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Số điện thoại *</label>
                        <input type="text" name="customer_phone" class="form-control" 
                               value="<?php echo htmlspecialchars($user['phone'] ?? $customer_phone ?? ''); ?>" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label>Địa chỉ nhận hàng *</label>
                    <textarea name="customer_address" class="form-control" rows="3" required><?php echo htmlspecialchars($user['address'] ?? $customer_address ?? ''); ?></textarea>
                </div>
                
                <div class="form-group">
                    <label>Ghi chú đơn hàng</label>
                    <textarea name="note" class="form-control" rows="2" placeholder="Ví dụ: Giao giờ hành chính..."><?php echo htmlspecialchars($note ?? ''); ?></textarea>
                </div>
                
                <?php if (isLoggedIn() && (!$user['full_name'] || !$user['phone'] || !$user['address'])): ?>
                <div class="form-group">
                    <label class="checkbox-label">
                        <input type="checkbox" name="update_profile" checked>
                        <span>Cập nhật thông tin vào tài khoản của tôi</span>
                    </label>
                </div>
                <?php endif; ?>
            </div>

            <div class="checkout-summary">
                <h2><i class="fas fa-file-invoice"></i> Đơn hàng của bạn</h2>
                
                <div class="order-items">
                    <?php foreach ($cart_items as $item): ?>
                    <div class="order-item">
                        <div class="order-item-image">
                            <img src="/public/uploads/products/<?php echo htmlspecialchars($item['main_image']); ?>" 
                                 alt="<?php echo htmlspecialchars($item['name']); ?>"
                                 onerror="this.src='/public/images/placeholder.jpg'">
                        </div>
                        <div class="order-item-info">
                            <h4><?php echo htmlspecialchars($item['name']); ?></h4>
                            <span>x<?php echo $item['quantity']; ?></span>
                        </div>
                        <div class="order-item-price">
                            <?php
                            $price = $item['sale_price'] ?? $item['price'];
                            echo formatPrice($price * $item['quantity']);
                            ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                
                <div class="order-total-summary">
                    <div class="summary-row">
                        <span>Tạm tính:</span>
                        <span><?php echo formatPrice($subtotal); ?></span>
                    </div>
                    
                    <div class="summary-row">
                        <span>Phí vận chuyển:</span>
                        <span><?php echo $shipping_fee > 0 ? formatPrice($shipping_fee) : 'Miễn phí'; ?></span>
                    </div>
                    
                    <div class="summary-total">
                        <span>Tổng cộng:</span>
                        <span><?php echo formatPrice($total); ?></span>
                    </div>
                </div>
                
                <div class="payment-method">
                    <h3>Phương thức thanh toán</h3>
                    <label class="radio-label">
                        <input type="radio" name="payment_method" value="COD" checked>
                        <span><i class="fas fa-money-bill-wave"></i> Thanh toán khi nhận hàng (COD)</span>
                    </label>
                </div>
                
                <button type="submit" class="btn btn-primary btn-block btn-large">
                    <i class="fas fa-check"></i> Đặt hàng
                </button>
                
                <a href="/cart.php" class="btn btn-secondary btn-block">
                    <i class="fas fa-arrow-left"></i> Quay lại giỏ hàng
                </a>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
