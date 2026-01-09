<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/functions.php';

session_start();
requireLogin();

$pdo = getDBConnection();

// Handle review submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        setFlashMessage('error', 'Invalid CSRF token');
        redirect($_SERVER['HTTP_REFERER'] ?? '/index.php');
    }
    
    $product_id = intval($_POST['product_id'] ?? 0);
    $rating = intval($_POST['rating'] ?? 0);
    $comment = sanitize($_POST['comment'] ?? '');
    
    // Validation
    if ($product_id <= 0 || $rating < 1 || $rating > 5) {
        setFlashMessage('error', 'Dữ liệu không hợp lệ');
        redirect($_SERVER['HTTP_REFERER'] ?? '/index.php');
    }
    
    // Check if user bought this product
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM order_items oi 
                           JOIN orders o ON oi.order_id = o.id 
                           WHERE o.user_id = ? AND oi.product_id = ? AND o.status = 'completed'");
    $stmt->execute([$_SESSION['user_id'], $product_id]);
    
    if ($stmt->fetch()['count'] == 0) {
        setFlashMessage('error', 'Bạn chỉ có thể đánh giá sản phẩm đã mua');
        redirect($_SERVER['HTTP_REFERER'] ?? '/index.php');
    }
    
    // Check if already reviewed
    $stmt = $pdo->prepare("SELECT id FROM reviews WHERE user_id = ? AND product_id = ?");
    $stmt->execute([$_SESSION['user_id'], $product_id]);
    
    if ($stmt->fetch()) {
        setFlashMessage('error', 'Bạn đã đánh giá sản phẩm này rồi');
        redirect($_SERVER['HTTP_REFERER'] ?? '/index.php');
    }
    
    // Insert review
    $stmt = $pdo->prepare("INSERT INTO reviews (product_id, user_id, rating, comment, is_approved) 
                           VALUES (?, ?, ?, ?, 0)");
    
    if ($stmt->execute([$product_id, $_SESSION['user_id'], $rating, $comment])) {
        setFlashMessage('success', 'Đánh giá của bạn đã được gửi và đang chờ duyệt');
    } else {
        setFlashMessage('error', 'Có lỗi xảy ra, vui lòng thử lại');
    }
    
    redirect($_SERVER['HTTP_REFERER'] ?? '/index.php');
}

redirect('/index.php');
