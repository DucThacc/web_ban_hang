<?php
$page_title = 'Chi tiết sản phẩm';
require_once __DIR__ . '/includes/header.php';

// Get product from slug
$slug = $_GET['slug'] ?? '';
$stmt = $pdo->prepare("SELECT p.*, c.name as category_name, c.slug as category_slug 
                       FROM products p 
                       LEFT JOIN categories c ON p.category_id = c.id 
                       WHERE p.slug = ? AND p.is_active = 1");
$stmt->execute([$slug]);
$product = $stmt->fetch();

if (!$product) {
    redirect('/index.php');
}

$page_title = $product['name'];

// Update view count
$stmt = $pdo->prepare("UPDATE products SET view_count = view_count + 1 WHERE id = ?");
$stmt->execute([$product['id']]);

// Get product images
$stmt = $pdo->prepare("SELECT * FROM product_images WHERE product_id = ? ORDER BY display_order");
$stmt->execute([$product['id']]);
$product_images = $stmt->fetchAll();

// Get reviews
$stmt = $pdo->prepare("SELECT r.*, u.username, u.full_name 
                       FROM reviews r 
                       LEFT JOIN users u ON r.user_id = u.id 
                       WHERE r.product_id = ? AND r.is_approved = 1 
                       ORDER BY r.created_at DESC");
$stmt->execute([$product['id']]);
$reviews = $stmt->fetchAll();

// Calculate average rating
$stmt = $pdo->prepare("SELECT AVG(rating) as avg_rating, COUNT(*) as review_count 
                       FROM reviews 
                       WHERE product_id = ? AND is_approved = 1");
$stmt->execute([$product['id']]);
$rating_data = $stmt->fetch();
$avg_rating = round($rating_data['avg_rating'] ?? 0, 1);
$review_count = $rating_data['review_count'] ?? 0;

// Get related products
$stmt = $pdo->prepare("SELECT p.* FROM products p 
                       WHERE p.category_id = ? AND p.id != ? AND p.is_active = 1 
                       ORDER BY RAND() LIMIT 4");
$stmt->execute([$product['category_id'], $product['id']]);
$related_products = $stmt->fetchAll();

// Check if user bought this product (for review)
$can_review = false;
if (isLoggedIn()) {
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM order_items oi 
                           JOIN orders o ON oi.order_id = o.id 
                           WHERE o.user_id = ? AND oi.product_id = ? AND o.status = 'completed'");
    $stmt->execute([$_SESSION['user_id'], $product['id']]);
    $can_review = $stmt->fetch()['count'] > 0;
}
?>

<div class="container">
    <div class="breadcrumb">
        <a href="/index.php">Trang chủ</a>
        <i class="fas fa-chevron-right"></i>
        <a href="/category.php?slug=<?php echo $product['category_slug']; ?>"><?php echo htmlspecialchars($product['category_name']); ?></a>
        <i class="fas fa-chevron-right"></i>
        <span><?php echo htmlspecialchars($product['name']); ?></span>
    </div>

    <div class="product-detail">
        <!-- Product Images -->
        <div class="product-images">
            <div class="main-image">
                <img id="mainImage" src="/public/uploads/products/<?php echo htmlspecialchars($product['main_image']); ?>" 
                     alt="<?php echo htmlspecialchars($product['name']); ?>"
                     onerror="this.src='/public/images/placeholder.jpg'">
            </div>
            <?php if (!empty($product_images)): ?>
            <div class="thumbnail-images">
                <img src="/public/uploads/products/<?php echo htmlspecialchars($product['main_image']); ?>" 
                     onclick="changeMainImage(this.src)" class="active">
                <?php foreach ($product_images as $image): ?>
                <img src="/public/uploads/products/<?php echo htmlspecialchars($image['image_path']); ?>" 
                     onclick="changeMainImage(this.src)">
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>

        <!-- Product Info -->
        <div class="product-info-detail">
            <h1><?php echo htmlspecialchars($product['name']); ?></h1>
            
            <?php if ($product['brand']): ?>
            <div class="product-brand">
                <strong>Thương hiệu:</strong> <?php echo htmlspecialchars($product['brand']); ?>
            </div>
            <?php endif; ?>
            
            <?php if ($product['sku']): ?>
            <div class="product-sku">
                <strong>Mã SP:</strong> <?php echo htmlspecialchars($product['sku']); ?>
            </div>
            <?php endif; ?>

            <div class="product-rating">
                <div class="stars">
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                        <i class="fas fa-star <?php echo $i <= $avg_rating ? 'active' : ''; ?>"></i>
                    <?php endfor; ?>
                </div>
                <span><?php echo $avg_rating; ?>/5 (<?php echo $review_count; ?> đánh giá)</span>
            </div>

            <div class="product-price-detail">
                <?php if ($product['sale_price']): ?>
                    <div class="price-sale-large"><?php echo formatPrice($product['sale_price']); ?></div>
                    <div class="price-original-large"><?php echo formatPrice($product['price']); ?></div>
                    <div class="price-discount">Tiết kiệm <?php echo formatPrice($product['price'] - $product['sale_price']); ?> (<?php echo round((($product['price'] - $product['sale_price']) / $product['price']) * 100); ?>%)</div>
                <?php else: ?>
                    <div class="price-current-large"><?php echo formatPrice($product['price']); ?></div>
                <?php endif; ?>
            </div>

            <div class="product-stock">
                <?php if ($product['quantity'] > 0): ?>
                    <span class="in-stock"><i class="fas fa-check-circle"></i> Còn hàng (<?php echo $product['quantity']; ?> sản phẩm)</span>
                <?php else: ?>
                    <span class="out-of-stock"><i class="fas fa-times-circle"></i> Hết hàng</span>
                <?php endif; ?>
            </div>

            <?php if ($product['quantity'] > 0): ?>
            <div class="product-quantity">
                <label>Số lượng:</label>
                <div class="quantity-selector">
                    <button type="button" onclick="decreaseQuantity()"><i class="fas fa-minus"></i></button>
                    <input type="number" id="quantity" value="1" min="1" max="<?php echo $product['quantity']; ?>">
                    <button type="button" onclick="increaseQuantity()"><i class="fas fa-plus"></i></button>
                </div>
            </div>

            <div class="product-actions-detail">
                <button class="btn btn-primary btn-large add-to-cart-detail" data-product-id="<?php echo $product['id']; ?>">
                    <i class="fas fa-shopping-cart"></i> Thêm vào giỏ hàng
                </button>
                <button class="btn btn-success btn-large buy-now" data-product-id="<?php echo $product['id']; ?>">
                    <i class="fas fa-bolt"></i> Mua ngay
                </button>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Product Tabs -->
    <div class="product-tabs">
        <div class="tab-nav">
            <button class="tab-link active" onclick="openTab(event, 'description')">Mô tả sản phẩm</button>
            <button class="tab-link" onclick="openTab(event, 'specifications')">Thông số kỹ thuật</button>
            <button class="tab-link" onclick="openTab(event, 'reviews')">Đánh giá (<?php echo $review_count; ?>)</button>
        </div>

        <div id="description" class="tab-content active">
            <h2>Mô tả sản phẩm</h2>
            <div class="product-description">
                <?php echo nl2br(htmlspecialchars($product['description'])); ?>
            </div>
        </div>

        <div id="specifications" class="tab-content">
            <h2>Thông số kỹ thuật</h2>
            <div class="product-specifications">
                <?php if ($product['specifications']): ?>
                    <table class="specs-table">
                        <?php
                        $specs = explode("\n", $product['specifications']);
                        foreach ($specs as $spec):
                            if (empty(trim($spec))) continue;
                            $parts = explode(':', $spec, 2);
                            if (count($parts) == 2):
                        ?>
                        <tr>
                            <td class="spec-label"><?php echo htmlspecialchars(trim($parts[0])); ?></td>
                            <td class="spec-value"><?php echo htmlspecialchars(trim($parts[1])); ?></td>
                        </tr>
                        <?php 
                            endif;
                        endforeach; 
                        ?>
                    </table>
                <?php else: ?>
                    <p>Chưa có thông tin thông số kỹ thuật.</p>
                <?php endif; ?>
            </div>
        </div>

        <div id="reviews" class="tab-content">
            <h2>Đánh giá sản phẩm</h2>
            
            <?php if ($can_review && isLoggedIn()): ?>
            <div class="review-form">
                <h3>Viết đánh giá của bạn</h3>
                <form method="POST" action="/submit-review.php">
                    <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                    <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                    
                    <div class="form-group">
                        <label>Đánh giá của bạn:</label>
                        <div class="rating-input">
                            <input type="radio" name="rating" value="5" id="star5" required>
                            <label for="star5"><i class="fas fa-star"></i></label>
                            <input type="radio" name="rating" value="4" id="star4">
                            <label for="star4"><i class="fas fa-star"></i></label>
                            <input type="radio" name="rating" value="3" id="star3">
                            <label for="star3"><i class="fas fa-star"></i></label>
                            <input type="radio" name="rating" value="2" id="star2">
                            <label for="star2"><i class="fas fa-star"></i></label>
                            <input type="radio" name="rating" value="1" id="star1">
                            <label for="star1"><i class="fas fa-star"></i></label>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>Nhận xét:</label>
                        <textarea name="comment" rows="4" placeholder="Chia sẻ trải nghiệm của bạn về sản phẩm..."></textarea>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Gửi đánh giá</button>
                </form>
            </div>
            <?php endif; ?>

            <div class="reviews-list">
                <?php if (empty($reviews)): ?>
                    <p class="no-reviews">Chưa có đánh giá nào cho sản phẩm này.</p>
                <?php else: ?>
                    <?php foreach ($reviews as $review): ?>
                    <div class="review-item">
                        <div class="review-header">
                            <div class="review-author">
                                <i class="fas fa-user-circle"></i>
                                <strong><?php echo htmlspecialchars($review['full_name'] ?? $review['username']); ?></strong>
                            </div>
                            <div class="review-rating">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <i class="fas fa-star <?php echo $i <= $review['rating'] ? 'active' : ''; ?>"></i>
                                <?php endfor; ?>
                            </div>
                        </div>
                        <div class="review-date"><?php echo formatDate($review['created_at']); ?></div>
                        <div class="review-comment"><?php echo nl2br(htmlspecialchars($review['comment'])); ?></div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Related Products -->
    <?php if (!empty($related_products)): ?>
    <section class="section">
        <div class="section-header">
            <h2><i class="fas fa-box"></i> Sản phẩm liên quan</h2>
        </div>
        <div class="products-grid">
            <?php foreach ($related_products as $rp): ?>
            <div class="product-card">
                <a href="/product.php?slug=<?php echo $rp['slug']; ?>">
                    <div class="product-image">
                        <?php if ($rp['sale_price']): ?>
                            <span class="badge sale-badge">-<?php echo round((($rp['price'] - $rp['sale_price']) / $rp['price']) * 100); ?>%</span>
                        <?php endif; ?>
                        <img src="/public/uploads/products/<?php echo htmlspecialchars($rp['main_image']); ?>" 
                             alt="<?php echo htmlspecialchars($rp['name']); ?>"
                             onerror="this.src='/public/images/placeholder.jpg'">
                    </div>
                    <div class="product-info">
                        <h3 class="product-name"><?php echo htmlspecialchars($rp['name']); ?></h3>
                        <div class="product-price">
                            <?php if ($rp['sale_price']): ?>
                                <span class="price-sale"><?php echo formatPrice($rp['sale_price']); ?></span>
                                <span class="price-original"><?php echo formatPrice($rp['price']); ?></span>
                            <?php else: ?>
                                <span class="price-current"><?php echo formatPrice($rp['price']); ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                </a>
                <div class="product-actions">
                    <button class="btn btn-primary add-to-cart" data-product-id="<?php echo $rp['id']; ?>">
                        <i class="fas fa-shopping-cart"></i> Thêm vào giỏ
                    </button>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </section>
    <?php endif; ?>
</div>

<script>
function changeMainImage(src) {
    document.getElementById('mainImage').src = src;
    document.querySelectorAll('.thumbnail-images img').forEach(img => img.classList.remove('active'));
    event.target.classList.add('active');
}

function increaseQuantity() {
    const input = document.getElementById('quantity');
    const max = parseInt(input.max);
    if (parseInt(input.value) < max) {
        input.value = parseInt(input.value) + 1;
    }
}

function decreaseQuantity() {
    const input = document.getElementById('quantity');
    if (parseInt(input.value) > 1) {
        input.value = parseInt(input.value) - 1;
    }
}

function openTab(evt, tabName) {
    document.querySelectorAll('.tab-content').forEach(tab => tab.classList.remove('active'));
    document.querySelectorAll('.tab-link').forEach(link => link.classList.remove('active'));
    document.getElementById(tabName).classList.add('active');
    evt.currentTarget.classList.add('active');
}

// Add to cart with quantity
document.querySelectorAll('.add-to-cart-detail').forEach(btn => {
    btn.addEventListener('click', function() {
        const productId = this.dataset.productId;
        const quantity = document.getElementById('quantity').value;
        addToCart(productId, quantity);
    });
});

// Buy now
document.querySelectorAll('.buy-now').forEach(btn => {
    btn.addEventListener('click', function() {
        const productId = this.dataset.productId;
        const quantity = document.getElementById('quantity').value;
        addToCart(productId, quantity, true);
    });
});
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
