<?php
session_start();
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/functions.php';

requireAdmin();

$pdo = getDBConnection();

// Get statistics
// Total orders
$stmt = $pdo->query("SELECT COUNT(*) as total FROM orders");
$total_orders = $stmt->fetch()['total'];

// Total revenue
$stmt = $pdo->query("SELECT SUM(total) as revenue FROM orders WHERE status != 'cancelled'");
$total_revenue = $stmt->fetch()['revenue'] ?? 0;

// Pending orders
$stmt = $pdo->query("SELECT COUNT(*) as total FROM orders WHERE status = 'pending'");
$pending_orders = $stmt->fetch()['total'];

// Total products
$stmt = $pdo->query("SELECT COUNT(*) as total FROM products WHERE is_active = 1");
$total_products = $stmt->fetch()['total'];

// Recent orders
$stmt = $pdo->query("SELECT o.*, u.username FROM orders o 
                     LEFT JOIN users u ON o.user_id = u.id 
                     ORDER BY o.created_at DESC LIMIT 10");
$recent_orders = $stmt->fetchAll();

// Revenue by date (last 7 days)
$stmt = $pdo->query("SELECT DATE(created_at) as date, SUM(total) as revenue 
                     FROM orders 
                     WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 7 DAY) AND status != 'cancelled'
                     GROUP BY DATE(created_at) 
                     ORDER BY date");
$revenue_by_date = $stmt->fetchAll();

$page_title = 'Dashboard - Admin';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?></title>
    <link rel="stylesheet" href="/public/css/style.css">
    <link rel="stylesheet" href="/public/css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="admin-body">
    <div class="admin-container">
        <!-- Sidebar -->
        <aside class="admin-sidebar">
            <div class="admin-logo">
                <i class="fas fa-desktop"></i>
                <span>PC SHOP ADMIN</span>
            </div>
            
            <nav class="admin-nav">
                <a href="/admin/index.php" class="active"><i class="fas fa-chart-line"></i> Dashboard</a>
                <a href="/admin/products.php"><i class="fas fa-box"></i> Sản phẩm</a>
                <a href="/admin/categories.php"><i class="fas fa-tags"></i> Danh mục</a>
                <a href="/admin/orders.php"><i class="fas fa-shopping-bag"></i> Đơn hàng</a>
                <a href="/admin/banners.php"><i class="fas fa-images"></i> Banners</a>
                <a href="/admin/users.php"><i class="fas fa-users"></i> Người dùng</a>
                <a href="/admin/reviews.php"><i class="fas fa-star"></i> Đánh giá</a>
            </nav>
            
            <div class="admin-user">
                <i class="fas fa-user-circle"></i>
                <div>
                    <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong>
                    <span>Administrator</span>
                </div>
            </div>
            
            <a href="/logout.php" class="admin-logout"><i class="fas fa-sign-out-alt"></i> Đăng xuất</a>
            <a href="/index.php" class="admin-logout"><i class="fas fa-home"></i> Về trang chủ</a>
        </aside>

        <!-- Main Content -->
        <main class="admin-main">
            <div class="admin-header">
                <h1><i class="fas fa-chart-line"></i> Dashboard</h1>
                <div class="admin-breadcrumb">
                    <span>Admin</span> / <span>Dashboard</span>
                </div>
            </div>

            <?php
            $flash = getFlashMessage();
            if ($flash):
            ?>
                <div class="alert alert-<?php echo $flash['type']; ?>">
                    <?php echo htmlspecialchars($flash['message']); ?>
                </div>
            <?php endif; ?>

            <!-- Statistics Cards -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon blue">
                        <i class="fas fa-shopping-bag"></i>
                    </div>
                    <div class="stat-details">
                        <h3><?php echo number_format($total_orders); ?></h3>
                        <p>Tổng đơn hàng</p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon green">
                        <i class="fas fa-dollar-sign"></i>
                    </div>
                    <div class="stat-details">
                        <h3><?php echo formatPrice($total_revenue); ?></h3>
                        <p>Tổng doanh thu</p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon orange">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="stat-details">
                        <h3><?php echo number_format($pending_orders); ?></h3>
                        <p>Đơn chờ xử lý</p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon purple">
                        <i class="fas fa-box"></i>
                    </div>
                    <div class="stat-details">
                        <h3><?php echo number_format($total_products); ?></h3>
                        <p>Sản phẩm</p>
                    </div>
                </div>
            </div>

            <!-- Revenue Chart -->
            <div class="admin-card">
                <h2><i class="fas fa-chart-bar"></i> Doanh thu 7 ngày gần đây</h2>
                <div class="chart-container">
                    <canvas id="revenueChart"></canvas>
                </div>
            </div>

            <!-- Recent Orders -->
            <div class="admin-card">
                <h2><i class="fas fa-list"></i> Đơn hàng gần đây</h2>
                <div class="table-responsive">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Mã đơn</th>
                                <th>Khách hàng</th>
                                <th>Tổng tiền</th>
                                <th>Trạng thái</th>
                                <th>Ngày đặt</th>
                                <th>Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recent_orders as $order): ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($order['order_code']); ?></strong></td>
                                <td><?php echo htmlspecialchars($order['customer_name']); ?></td>
                                <td><?php echo formatPrice($order['total']); ?></td>
                                <td>
                                    <span class="badge badge-<?php echo getOrderStatusClass($order['status']); ?>">
                                        <?php echo getOrderStatusLabel($order['status']); ?>
                                    </span>
                                </td>
                                <td><?php echo formatDate($order['created_at']); ?></td>
                                <td>
                                    <a href="/admin/order-detail.php?id=<?php echo $order['id']; ?>" class="btn btn-sm btn-primary">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Revenue Chart
        const ctx = document.getElementById('revenueChart').getContext('2d');
        const revenueData = <?php echo json_encode($revenue_by_date); ?>;
        
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: revenueData.map(d => d.date),
                datasets: [{
                    label: 'Doanh thu (VNĐ)',
                    data: revenueData.map(d => d.revenue),
                    borderColor: 'rgb(75, 192, 192)',
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    tension: 0.1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return value.toLocaleString('vi-VN') + 'đ';
                            }
                        }
                    }
                }
            }
        });
    </script>
</body>
</html>
