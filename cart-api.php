<?php
/**
 * Cart API endpoints
 * Handle AJAX requests for cart operations
 */

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/functions.php';

session_start();

header('Content-Type: application/json');

$pdo = getDBConnection();
$action = $_POST['action'] ?? $_GET['action'] ?? '';

switch ($action) {
    case 'add':
        $product_id = intval($_POST['product_id'] ?? 0);
        $quantity = intval($_POST['quantity'] ?? 1);
        
        if ($product_id <= 0 || $quantity <= 0) {
            echo json_encode(['success' => false, 'message' => 'Invalid parameters']);
            exit;
        }
        
        // Check product exists and has stock
        $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ? AND is_active = 1");
        $stmt->execute([$product_id]);
        $product = $stmt->fetch();
        
        if (!$product) {
            echo json_encode(['success' => false, 'message' => 'Sản phẩm không tồn tại']);
            exit;
        }
        
        if ($product['quantity'] < $quantity) {
            echo json_encode(['success' => false, 'message' => 'Sản phẩm không đủ số lượng']);
            exit;
        }
        
        // Add to cart
        if (isLoggedIn()) {
            $user_id = $_SESSION['user_id'];
            
            // Check if product already in cart
            $stmt = $pdo->prepare("SELECT * FROM carts WHERE user_id = ? AND product_id = ?");
            $stmt->execute([$user_id, $product_id]);
            $existing = $stmt->fetch();
            
            if ($existing) {
                $new_quantity = $existing['quantity'] + $quantity;
                if ($new_quantity > $product['quantity']) {
                    echo json_encode(['success' => false, 'message' => 'Vượt quá số lượng có sẵn']);
                    exit;
                }
                
                $stmt = $pdo->prepare("UPDATE carts SET quantity = ? WHERE id = ?");
                $stmt->execute([$new_quantity, $existing['id']]);
            } else {
                $stmt = $pdo->prepare("INSERT INTO carts (user_id, product_id, quantity) VALUES (?, ?, ?)");
                $stmt->execute([$user_id, $product_id, $quantity]);
            }
        } else {
            $session_id = session_id();
            
            $stmt = $pdo->prepare("SELECT * FROM carts WHERE session_id = ? AND product_id = ?");
            $stmt->execute([$session_id, $product_id]);
            $existing = $stmt->fetch();
            
            if ($existing) {
                $new_quantity = $existing['quantity'] + $quantity;
                if ($new_quantity > $product['quantity']) {
                    echo json_encode(['success' => false, 'message' => 'Vượt quá số lượng có sẵn']);
                    exit;
                }
                
                $stmt = $pdo->prepare("UPDATE carts SET quantity = ? WHERE id = ?");
                $stmt->execute([$new_quantity, $existing['id']]);
            } else {
                $stmt = $pdo->prepare("INSERT INTO carts (session_id, product_id, quantity) VALUES (?, ?, ?)");
                $stmt->execute([$session_id, $product_id, $quantity]);
            }
        }
        
        // Get cart count
        if (isLoggedIn()) {
            $stmt = $pdo->prepare("SELECT SUM(quantity) as total FROM carts WHERE user_id = ?");
            $stmt->execute([$_SESSION['user_id']]);
        } else {
            $stmt = $pdo->prepare("SELECT SUM(quantity) as total FROM carts WHERE session_id = ?");
            $stmt->execute([session_id()]);
        }
        $cart_count = $stmt->fetch()['total'] ?? 0;
        
        echo json_encode([
            'success' => true,
            'message' => 'Đã thêm vào giỏ hàng',
            'cart_count' => $cart_count
        ]);
        break;
        
    case 'update':
        $cart_id = intval($_POST['cart_id'] ?? 0);
        $quantity = intval($_POST['quantity'] ?? 0);
        
        if ($cart_id <= 0 || $quantity < 0) {
            echo json_encode(['success' => false, 'message' => 'Invalid parameters']);
            exit;
        }
        
        if ($quantity == 0) {
            // Delete
            $stmt = $pdo->prepare("DELETE FROM carts WHERE id = ?");
            $stmt->execute([$cart_id]);
        } else {
            // Update
            $stmt = $pdo->prepare("UPDATE carts SET quantity = ? WHERE id = ?");
            $stmt->execute([$quantity, $cart_id]);
        }
        
        echo json_encode(['success' => true, 'message' => 'Đã cập nhật giỏ hàng']);
        break;
        
    case 'remove':
        $cart_id = intval($_POST['cart_id'] ?? $_GET['cart_id'] ?? 0);
        
        if ($cart_id <= 0) {
            echo json_encode(['success' => false, 'message' => 'Invalid parameters']);
            exit;
        }
        
        $stmt = $pdo->prepare("DELETE FROM carts WHERE id = ?");
        $stmt->execute([$cart_id]);
        
        echo json_encode(['success' => true, 'message' => 'Đã xóa khỏi giỏ hàng']);
        break;
        
    case 'get_count':
        if (isLoggedIn()) {
            $stmt = $pdo->prepare("SELECT SUM(quantity) as total FROM carts WHERE user_id = ?");
            $stmt->execute([$_SESSION['user_id']]);
        } else {
            $stmt = $pdo->prepare("SELECT SUM(quantity) as total FROM carts WHERE session_id = ?");
            $stmt->execute([session_id()]);
        }
        $cart_count = $stmt->fetch()['total'] ?? 0;
        
        echo json_encode(['success' => true, 'count' => $cart_count]);
        break;
        
    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
}
