<?php
$page_title = 'Đặt hàng thành công';
require_once __DIR__ . '/includes/header.php';

$order_code = $_GET['code'] ?? '';

if (empty($order_code)) {
    redirect('/index.php');
}

// Get order details
$stmt = $pdo->prepare("SELECT * FROM orders WHERE order_code = ?");
$stmt->execute([$order_code]);
$order = $stmt->fetch();

if (!$order) {
    redirect('/index.php');
}

// Get order items
$stmt = $pdo->prepare("SELECT * FROM order_items WHERE order_id = ?");
$stmt->execute([$order['id']]);
$order_items = $stmt->fetchAll();
?>

<div class="container">
    <div class="order-success-page">
        <div class="success-icon">
            <i class="fas fa-check-circle"></i>
        </div>
        
        <h1>Đặt hàng thành công!</h1>
        <p class="success-message">Cảm ơn bạn đã đặt hàng. Chúng tôi sẽ liên hệ với bạn sớm nhất.</p>
        
        <div class="order-info-box">
            <h2>Thông tin đơn hàng</h2>
            
            <div class="order-detail-row">
                <span class="label">Mã đơn hàng:</span>
                <span class="value"><strong><?php echo htmlspecialchars($order['order_code']); ?></strong></span>
            </div>
            
            <div class="order-detail-row">
                <span class="label">Ngày đặt:</span>
                <span class="value"><?php echo formatDate($order['created_at']); ?></span>
            </div>
            
            <div class="order-detail-row">
                <span class="label">Tổng tiền:</span>
                <span class="value total-amount"><?php echo formatPrice($order['total']); ?></span>
            </div>
            
            <div class="order-detail-row">
                <span class="label">Trạng thái:</span>
                <span class="value">
                    <span class="badge badge-warning"><?php echo getOrderStatusLabel($order['status']); ?></span>
                </span>
            </div>
            
            <div class="order-detail-row">
                <span class="label">Người nhận:</span>
                <span class="value"><?php echo htmlspecialchars($order['customer_name']); ?></span>
            </div>
            
            <div class="order-detail-row">
                <span class="label">Số điện thoại:</span>
                <span class="value"><?php echo htmlspecialchars($order['customer_phone']); ?></span>
            </div>
            
            <div class="order-detail-row">
                <span class="label">Địa chỉ:</span>
                <span class="value"><?php echo htmlspecialchars($order['customer_address']); ?></span>
            </div>
        </div>
        
        <div class="order-items-box">
            <h3>Sản phẩm đã đặt</h3>
            <?php foreach ($order_items as $item): ?>
            <div class="order-item-row">
                <img src="/public/uploads/products/<?php echo htmlspecialchars($item['product_image']); ?>" 
                     alt="<?php echo htmlspecialchars($item['product_name']); ?>"
                     onerror="this.src='/public/images/placeholder.jpg'">
                <div class="item-info">
                    <h4><?php echo htmlspecialchars($item['product_name']); ?></h4>
                    <span>Số lượng: <?php echo $item['quantity']; ?></span>
                </div>
                <div class="item-price">
                    <?php echo formatPrice($item['subtotal']); ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        
        <div class="success-actions">
            <?php if (isLoggedIn()): ?>
                <a href="/orders.php" class="btn btn-primary">
                    <i class="fas fa-list"></i> Xem đơn hàng của tôi
                </a>
            <?php endif; ?>
            <a href="/index.php" class="btn btn-secondary">
                <i class="fas fa-home"></i> Về trang chủ
            </a>
            <a href="/products.php" class="btn btn-success">
                <i class="fas fa-shopping-bag"></i> Tiếp tục mua sắm
            </a>
        </div>
    </div>
</div>

<style>
.order-success-page {
    max-width: 800px;
    margin: 50px auto;
    text-align: center;
}

.success-icon {
    font-size: 80px;
    color: var(--success-color);
    margin-bottom: 20px;
}

.order-success-page h1 {
    font-size: 36px;
    color: var(--success-color);
    margin-bottom: 10px;
}

.success-message {
    font-size: 18px;
    color: var(--text-muted);
    margin-bottom: 40px;
}

.order-info-box,
.order-items-box {
    background: #fff;
    border: 1px solid var(--border-color);
    border-radius: 10px;
    padding: 30px;
    margin-bottom: 30px;
    text-align: left;
}

.order-info-box h2,
.order-items-box h3 {
    margin-bottom: 20px;
    padding-bottom: 15px;
    border-bottom: 2px solid var(--border-color);
}

.order-detail-row {
    display: flex;
    justify-content: space-between;
    padding: 12px 0;
    border-bottom: 1px solid var(--light-color);
}

.order-detail-row .label {
    font-weight: 500;
    color: var(--text-muted);
}

.order-detail-row .value {
    text-align: right;
}

.total-amount {
    font-size: 24px;
    font-weight: bold;
    color: var(--danger-color);
}

.order-item-row {
    display: flex;
    align-items: center;
    gap: 20px;
    padding: 15px;
    border-bottom: 1px solid var(--light-color);
}

.order-item-row img {
    width: 80px;
    height: 80px;
    object-fit: cover;
    border-radius: 5px;
}

.order-item-row .item-info {
    flex: 1;
}

.order-item-row h4 {
    margin-bottom: 8px;
}

.order-item-row .item-price {
    font-size: 18px;
    font-weight: bold;
    color: var(--primary-color);
}

.success-actions {
    display: flex;
    gap: 15px;
    justify-content: center;
    flex-wrap: wrap;
}
</style>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
