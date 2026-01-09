<?php
$page_title = 'Trang chủ';
require_once __DIR__ . '/includes/header.php';

// Get banners
$stmt = $pdo->query("SELECT * FROM banners WHERE is_active = 1 ORDER BY display_order LIMIT 5");
$banners = $stmt->fetchAll();

// Get featured products
$stmt = $pdo->query("SELECT p.*, c.name as category_name FROM products p 
                     LEFT JOIN categories c ON p.category_id = c.id 
                     WHERE p.is_featured = 1 AND p.is_active = 1 
                     ORDER BY p.created_at DESC LIMIT 8");
$featured_products = $stmt->fetchAll();

// Get featured categories
$stmt = $pdo->query("SELECT * FROM categories WHERE parent_id IS NULL AND is_active = 1 ORDER BY display_order LIMIT 8");
$featured_categories = $stmt->fetchAll();

// Get new products
$stmt = $pdo->query("SELECT p.*, c.name as category_name FROM products p 
                     LEFT JOIN categories c ON p.category_id = c.id 
                     WHERE p.is_active = 1 
                     ORDER BY p.created_at DESC LIMIT 8");
$new_products = $stmt->fetchAll();
?>

<div class="container">
    <!-- Banner Slider -->
    <?php if (!empty($banners)): ?>
    <section class="banner-slider">
        <div class="slider-container">
            <?php foreach ($banners as $index => $banner): ?>
            <div class="slide <?php echo $index === 0 ? 'active' : ''; ?>">
                <a href="<?php echo htmlspecialchars($banner['link'] ?? '#'); ?>">
                    <img src="/public/uploads/banners/<?php echo htmlspecialchars($banner['image_path']); ?>" 
                         alt="<?php echo htmlspecialchars($banner['title']); ?>"
                         onerror="this.src='/public/images/placeholder-banner.jpg'">
                </a>
            </div>
            <?php endforeach; ?>
        </div>
        
        <button class="slider-btn prev" onclick="changeSlide(-1)">
            <i class="fas fa-chevron-left"></i>
        </button>
        <button class="slider-btn next" onclick="changeSlide(1)">
            <i class="fas fa-chevron-right"></i>
        </button>
        
        <div class="slider-dots">
            <?php foreach ($banners as $index => $banner): ?>
            <span class="dot <?php echo $index === 0 ? 'active' : ''; ?>" onclick="currentSlide(<?php echo $index; ?>)"></span>
            <?php endforeach; ?>
        </div>
    </section>
    <?php endif; ?>

    <!-- Featured Categories -->
    <?php if (!empty($featured_categories)): ?>
    <section class="section">
        <div class="section-header">
            <h2><i class="fas fa-th-large"></i> Danh mục nổi bật</h2>
        </div>
        <div class="categories-grid">
            <?php foreach ($featured_categories as $category): ?>
            <a href="/category.php?slug=<?php echo $category['slug']; ?>" class="category-card">
                <div class="category-icon">
                    <i class="fas fa-laptop"></i>
                </div>
                <h3><?php echo htmlspecialchars($category['name']); ?></h3>
            </a>
            <?php endforeach; ?>
        </div>
    </section>
    <?php endif; ?>

    <!-- Featured Products -->
    <?php if (!empty($featured_products)): ?>
    <section class="section">
        <div class="section-header">
            <h2><i class="fas fa-star"></i> Sản phẩm nổi bật</h2>
            <a href="/products.php" class="view-all">Xem tất cả <i class="fas fa-arrow-right"></i></a>
        </div>
        <div class="products-grid">
            <?php foreach ($featured_products as $product): ?>
            <div class="product-card">
                <a href="/product.php?slug=<?php echo $product['slug']; ?>">
                    <div class="product-image">
                        <?php if ($product['sale_price']): ?>
                            <span class="badge sale-badge">-<?php echo round((($product['price'] - $product['sale_price']) / $product['price']) * 100); ?>%</span>
                        <?php endif; ?>
                        <?php if ($product['is_featured']): ?>
                            <span class="badge featured-badge"><i class="fas fa-star"></i></span>
                        <?php endif; ?>
                        <img src="/public/uploads/products/<?php echo htmlspecialchars($product['main_image']); ?>" 
                             alt="<?php echo htmlspecialchars($product['name']); ?>"
                             onerror="this.src='/public/images/placeholder.jpg'">
                    </div>
                    <div class="product-info">
                        <div class="product-category"><?php echo htmlspecialchars($product['category_name']); ?></div>
                        <h3 class="product-name"><?php echo htmlspecialchars($product['name']); ?></h3>
                        <div class="product-price">
                            <?php if ($product['sale_price']): ?>
                                <span class="price-sale"><?php echo formatPrice($product['sale_price']); ?></span>
                                <span class="price-original"><?php echo formatPrice($product['price']); ?></span>
                            <?php else: ?>
                                <span class="price-current"><?php echo formatPrice($product['price']); ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                </a>
                <div class="product-actions">
                    <button class="btn btn-primary add-to-cart" data-product-id="<?php echo $product['id']; ?>">
                        <i class="fas fa-shopping-cart"></i> Thêm vào giỏ
                    </button>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </section>
    <?php endif; ?>

    <!-- New Products -->
    <?php if (!empty($new_products)): ?>
    <section class="section">
        <div class="section-header">
            <h2><i class="fas fa-clock"></i> Sản phẩm mới</h2>
            <a href="/products.php?sort=newest" class="view-all">Xem tất cả <i class="fas fa-arrow-right"></i></a>
        </div>
        <div class="products-grid">
            <?php foreach ($new_products as $product): ?>
            <div class="product-card">
                <a href="/product.php?slug=<?php echo $product['slug']; ?>">
                    <div class="product-image">
                        <?php if ($product['sale_price']): ?>
                            <span class="badge sale-badge">-<?php echo round((($product['price'] - $product['sale_price']) / $product['price']) * 100); ?>%</span>
                        <?php endif; ?>
                        <img src="/public/uploads/products/<?php echo htmlspecialchars($product['main_image']); ?>" 
                             alt="<?php echo htmlspecialchars($product['name']); ?>"
                             onerror="this.src='/public/images/placeholder.jpg'">
                    </div>
                    <div class="product-info">
                        <div class="product-category"><?php echo htmlspecialchars($product['category_name']); ?></div>
                        <h3 class="product-name"><?php echo htmlspecialchars($product['name']); ?></h3>
                        <div class="product-price">
                            <?php if ($product['sale_price']): ?>
                                <span class="price-sale"><?php echo formatPrice($product['sale_price']); ?></span>
                                <span class="price-original"><?php echo formatPrice($product['price']); ?></span>
                            <?php else: ?>
                                <span class="price-current"><?php echo formatPrice($product['price']); ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                </a>
                <div class="product-actions">
                    <button class="btn btn-primary add-to-cart" data-product-id="<?php echo $product['id']; ?>">
                        <i class="fas fa-shopping-cart"></i> Thêm vào giỏ
                    </button>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </section>
    <?php endif; ?>

    <!-- Why Choose Us -->
    <section class="section features">
        <div class="features-grid">
            <div class="feature-item">
                <i class="fas fa-shipping-fast"></i>
                <h3>Giao hàng nhanh</h3>
                <p>Miễn phí vận chuyển cho đơn hàng trên 5 triệu</p>
            </div>
            <div class="feature-item">
                <i class="fas fa-shield-alt"></i>
                <h3>Bảo hành chính hãng</h3>
                <p>Bảo hành đầy đủ từ nhà sản xuất</p>
            </div>
            <div class="feature-item">
                <i class="fas fa-headset"></i>
                <h3>Hỗ trợ 24/7</h3>
                <p>Tư vấn nhiệt tình, chuyên nghiệp</p>
            </div>
            <div class="feature-item">
                <i class="fas fa-undo"></i>
                <h3>Đổi trả dễ dàng</h3>
                <p>Đổi trả trong vòng 7 ngày</p>
            </div>
        </div>
    </section>
</div>

<script>
// Auto slide banner every 5 seconds
let currentSlideIndex = 0;
let slideInterval;

function showSlide(n) {
    const slides = document.querySelectorAll('.slide');
    const dots = document.querySelectorAll('.dot');
    
    if (slides.length === 0) return;
    
    if (n >= slides.length) currentSlideIndex = 0;
    if (n < 0) currentSlideIndex = slides.length - 1;
    
    slides.forEach(slide => slide.classList.remove('active'));
    dots.forEach(dot => dot.classList.remove('active'));
    
    slides[currentSlideIndex].classList.add('active');
    if (dots[currentSlideIndex]) {
        dots[currentSlideIndex].classList.add('active');
    }
}

function changeSlide(n) {
    clearInterval(slideInterval);
    currentSlideIndex += n;
    showSlide(currentSlideIndex);
    startAutoSlide();
}

function currentSlide(n) {
    clearInterval(slideInterval);
    currentSlideIndex = n;
    showSlide(currentSlideIndex);
    startAutoSlide();
}

function startAutoSlide() {
    slideInterval = setInterval(() => {
        currentSlideIndex++;
        showSlide(currentSlideIndex);
    }, 5000);
}

// Start auto slide on page load
if (document.querySelector('.slider-container')) {
    startAutoSlide();
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
