<?php
// Session configuration (must be set before session_start)
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_only_cookies', 1);
    ini_set('session.cookie_secure', 0); // Set to 1 if using HTTPS
    @session_save_path('/tmp');
    session_start();
}

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/functions.php';

$pdo = getDBConnection();

// Get current page for active menu
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . ' - ' : ''; ?><?php echo SITE_NAME; ?></title>
    <meta name="description" content="<?php echo isset($page_description) ? $page_description : 'Cửa hàng máy tính và linh kiện PC uy tín, giá tốt'; ?>">
    
    <!-- CSS -->
    <link rel="stylesheet" href="/public/css/style.css">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="container">
            <div class="header-top">
                <div class="logo">
                    <a href="/index.php">
                        <i class="fas fa-desktop"></i>
                        <span>PC SHOP</span>
                    </a>
                </div>
                
                <div class="search-box">
                    <form action="/search.php" method="GET">
                        <input type="text" name="q" placeholder="Tìm kiếm sản phẩm..." value="<?php echo isset($_GET['q']) ? htmlspecialchars($_GET['q']) : ''; ?>">
                        <button type="submit"><i class="fas fa-search"></i></button>
                    </form>
                </div>
                
                <div class="header-actions">
                    <?php if (isLoggedIn()): ?>
                        <a href="/profile.php" class="header-link">
                            <i class="fas fa-user"></i>
                            <span><?php echo htmlspecialchars($_SESSION['username']); ?></span>
                        </a>
                    <?php else: ?>
                        <a href="/login.php" class="header-link">
                            <i class="fas fa-user"></i>
                            <span>Đăng nhập</span>
                        </a>
                    <?php endif; ?>
                    
                    <a href="/cart.php" class="header-link cart-link">
                        <i class="fas fa-shopping-cart"></i>
                        <span>Giỏ hàng</span>
                        <?php
                        // Get cart count
                        $cart_count = 0;
                        if (isLoggedIn()) {
                            $stmt = $pdo->prepare("SELECT SUM(quantity) as total FROM carts WHERE user_id = ?");
                            $stmt->execute([$_SESSION['user_id']]);
                        } else {
                            $session_id = session_id();
                            $stmt = $pdo->prepare("SELECT SUM(quantity) as total FROM carts WHERE session_id = ?");
                            $stmt->execute([$session_id]);
                        }
                        $result = $stmt->fetch();
                        $cart_count = $result['total'] ?? 0;
                        
                        if ($cart_count > 0):
                        ?>
                            <span class="cart-badge"><?php echo $cart_count; ?></span>
                        <?php endif; ?>
                    </a>
                </div>
            </div>
            
            <nav class="main-nav">
                <ul>
                    <li><a href="/index.php" class="<?php echo $current_page == 'index.php' ? 'active' : ''; ?>">Trang chủ</a></li>
                    <li class="dropdown">
                        <a href="/categories.php">Danh mục <i class="fas fa-chevron-down"></i></a>
                        <ul class="dropdown-menu">
                            <?php
                            // Get main categories
                            $stmt = $pdo->query("SELECT * FROM categories WHERE parent_id IS NULL AND is_active = 1 ORDER BY display_order LIMIT 10");
                            $categories = $stmt->fetchAll();
                            foreach ($categories as $category):
                            ?>
                                <li><a href="/category.php?slug=<?php echo $category['slug']; ?>"><?php echo htmlspecialchars($category['name']); ?></a></li>
                            <?php endforeach; ?>
                        </ul>
                    </li>
                    <li><a href="/products.php" class="<?php echo $current_page == 'products.php' ? 'active' : ''; ?>">Sản phẩm</a></li>
                    <?php if (isLoggedIn()): ?>
                        <li><a href="/orders.php" class="<?php echo $current_page == 'orders.php' ? 'active' : ''; ?>">Đơn hàng</a></li>
                    <?php endif; ?>
                    <?php if (isAdmin()): ?>
                        <li><a href="/admin/index.php" class="admin-link"><i class="fas fa-cog"></i> Quản trị</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </header>

    <!-- Flash Message -->
    <?php
    $flash = getFlashMessage();
    if ($flash):
    ?>
        <div class="flash-message flash-<?php echo $flash['type']; ?>">
            <div class="container">
                <span><?php echo htmlspecialchars($flash['message']); ?></span>
                <button class="close-flash" onclick="this.parentElement.parentElement.remove()">×</button>
            </div>
        </div>
    <?php endif; ?>

    <!-- Main Content -->
    <main class="main-content">
