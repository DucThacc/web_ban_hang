<?php
$page_title = 'Danh mục sản phẩm';
require_once __DIR__ . '/includes/header.php';

// Get category from slug
$slug = $_GET['slug'] ?? '';
$stmt = $pdo->prepare("SELECT * FROM categories WHERE slug = ? AND is_active = 1");
$stmt->execute([$slug]);
$category = $stmt->fetch();

if (!$category) {
    redirect('/index.php');
}

$page_title = $category['name'];

// Pagination
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$per_page = 12;

// Filters
$brand = $_GET['brand'] ?? '';
$min_price = $_GET['min_price'] ?? '';
$max_price = $_GET['max_price'] ?? '';
$sort = $_GET['sort'] ?? 'newest';

// Build query
$where_conditions = ["p.category_id = :category_id", "p.is_active = 1"];
$params = [':category_id' => $category['id']];

if ($brand) {
    $where_conditions[] = "p.brand = :brand";
    $params[':brand'] = $brand;
}

if ($min_price) {
    $where_conditions[] = "COALESCE(p.sale_price, p.price) >= :min_price";
    $params[':min_price'] = $min_price;
}

if ($max_price) {
    $where_conditions[] = "COALESCE(p.sale_price, p.price) <= :max_price";
    $params[':max_price'] = $max_price;
}

$where_sql = implode(' AND ', $where_conditions);

// Sort
$order_sql = match($sort) {
    'price_asc' => 'COALESCE(p.sale_price, p.price) ASC',
    'price_desc' => 'COALESCE(p.sale_price, p.price) DESC',
    'name_asc' => 'p.name ASC',
    'name_desc' => 'p.name DESC',
    default => 'p.created_at DESC'
};

// Get total count
$count_sql = "SELECT COUNT(*) as total FROM products p WHERE $where_sql";
$stmt = $pdo->prepare($count_sql);
$stmt->execute($params);
$total = $stmt->fetch()['total'];

// Pagination
$pagination = paginate($total, $per_page, $page);

// Get products
$sql = "SELECT p.*, c.name as category_name 
        FROM products p 
        LEFT JOIN categories c ON p.category_id = c.id 
        WHERE $where_sql 
        ORDER BY $order_sql 
        LIMIT {$pagination['per_page']} OFFSET {$pagination['offset']}";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll();

// Get brands for filter
$stmt = $pdo->prepare("SELECT DISTINCT brand FROM products WHERE category_id = ? AND brand IS NOT NULL ORDER BY brand");
$stmt->execute([$category['id']]);
$brands = $stmt->fetchAll(PDO::FETCH_COLUMN);
?>

<div class="container">
    <div class="breadcrumb">
        <a href="/index.php">Trang chủ</a>
        <i class="fas fa-chevron-right"></i>
        <span><?php echo htmlspecialchars($category['name']); ?></span>
    </div>

    <div class="category-header">
        <h1><?php echo htmlspecialchars($category['name']); ?></h1>
        <?php if ($category['description']): ?>
            <p><?php echo htmlspecialchars($category['description']); ?></p>
        <?php endif; ?>
    </div>

    <div class="category-content">
        <!-- Sidebar Filters -->
        <aside class="filters-sidebar">
            <form method="GET" action="" id="filterForm">
                <input type="hidden" name="slug" value="<?php echo htmlspecialchars($slug); ?>">
                
                <div class="filter-section">
                    <h3><i class="fas fa-filter"></i> Bộ lọc</h3>
                    <button type="button" class="btn btn-sm btn-secondary" onclick="clearFilters()">Xóa lọc</button>
                </div>

                <!-- Brand Filter -->
                <?php if (!empty($brands)): ?>
                <div class="filter-section">
                    <h4>Thương hiệu</h4>
                    <?php foreach ($brands as $b): ?>
                    <label class="filter-checkbox">
                        <input type="radio" name="brand" value="<?php echo htmlspecialchars($b); ?>" 
                               <?php echo $brand === $b ? 'checked' : ''; ?> onchange="this.form.submit()">
                        <span><?php echo htmlspecialchars($b); ?></span>
                    </label>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>

                <!-- Price Filter -->
                <div class="filter-section">
                    <h4>Khoảng giá</h4>
                    <div class="price-inputs">
                        <input type="number" name="min_price" placeholder="Từ" value="<?php echo htmlspecialchars($min_price); ?>">
                        <span>-</span>
                        <input type="number" name="max_price" placeholder="Đến" value="<?php echo htmlspecialchars($max_price); ?>">
                    </div>
                    <button type="submit" class="btn btn-sm btn-primary">Áp dụng</button>
                </div>
            </form>
        </aside>

        <!-- Products Grid -->
        <div class="products-main">
            <div class="products-toolbar">
                <div class="products-count">
                    Tìm thấy <strong><?php echo $total; ?></strong> sản phẩm
                </div>
                <div class="products-sort">
                    <label>Sắp xếp:</label>
                    <select name="sort" onchange="window.location.href=updateURLParameter('sort', this.value)">
                        <option value="newest" <?php echo $sort === 'newest' ? 'selected' : ''; ?>>Mới nhất</option>
                        <option value="price_asc" <?php echo $sort === 'price_asc' ? 'selected' : ''; ?>>Giá tăng dần</option>
                        <option value="price_desc" <?php echo $sort === 'price_desc' ? 'selected' : ''; ?>>Giá giảm dần</option>
                        <option value="name_asc" <?php echo $sort === 'name_asc' ? 'selected' : ''; ?>>Tên A-Z</option>
                        <option value="name_desc" <?php echo $sort === 'name_desc' ? 'selected' : ''; ?>>Tên Z-A</option>
                    </select>
                </div>
            </div>

            <?php if (empty($products)): ?>
                <div class="empty-state">
                    <i class="fas fa-box-open"></i>
                    <h3>Không tìm thấy sản phẩm</h3>
                    <p>Vui lòng thử lại với bộ lọc khác</p>
                </div>
            <?php else: ?>
                <div class="products-grid">
                    <?php foreach ($products as $product): ?>
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

                <!-- Pagination -->
                <?php if ($pagination['total_pages'] > 1): ?>
                <div class="pagination">
                    <?php if ($page > 1): ?>
                        <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page - 1])); ?>" class="pagination-link">
                            <i class="fas fa-chevron-left"></i>
                        </a>
                    <?php endif; ?>
                    
                    <?php for ($i = 1; $i <= $pagination['total_pages']; $i++): ?>
                        <?php if ($i == $page): ?>
                            <span class="pagination-link active"><?php echo $i; ?></span>
                        <?php elseif ($i == 1 || $i == $pagination['total_pages'] || abs($i - $page) <= 2): ?>
                            <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $i])); ?>" class="pagination-link"><?php echo $i; ?></a>
                        <?php elseif (abs($i - $page) == 3): ?>
                            <span class="pagination-ellipsis">...</span>
                        <?php endif; ?>
                    <?php endfor; ?>
                    
                    <?php if ($page < $pagination['total_pages']): ?>
                        <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page + 1])); ?>" class="pagination-link">
                            <i class="fas fa-chevron-right"></i>
                        </a>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
function clearFilters() {
    window.location.href = '?slug=<?php echo htmlspecialchars($slug); ?>';
}

function updateURLParameter(param, value) {
    const url = new URL(window.location);
    url.searchParams.set(param, value);
    return url.toString();
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
