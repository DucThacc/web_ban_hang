<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/functions.php';

session_start();

// Already logged in
if (isLoggedIn()) {
    redirect('/index.php');
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitize($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($username)) {
        $errors[] = 'Vui lòng nhập tên đăng nhập hoặc email';
    }
    
    if (empty($password)) {
        $errors[] = 'Vui lòng nhập mật khẩu';
    }
    
    if (empty($errors)) {
        $pdo = getDBConnection();
        
        // Check username or email
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $username]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['password'])) {
            // Login successful
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['full_name'] = $user['full_name'];
            
            // Migrate cart from session to user
            $session_id = session_id();
            $stmt = $pdo->prepare("UPDATE carts SET user_id = ?, session_id = NULL WHERE session_id = ?");
            $stmt->execute([$user['id'], $session_id]);
            
            setFlashMessage('success', 'Đăng nhập thành công!');
            
            // Redirect
            $redirect = $_SESSION['redirect_after_login'] ?? '/index.php';
            unset($_SESSION['redirect_after_login']);
            redirect($redirect);
        } else {
            $errors[] = 'Tên đăng nhập hoặc mật khẩu không đúng';
        }
    }
}

$page_title = 'Đăng nhập';
require_once __DIR__ . '/includes/header.php';
?>

<div class="container">
    <div class="auth-page">
        <div class="auth-form-container">
            <h1><i class="fas fa-sign-in-alt"></i> Đăng nhập</h1>
            
            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger">
                    <?php foreach ($errors as $error): ?>
                        <p><?php echo htmlspecialchars($error); ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="" class="auth-form">
                <div class="form-group">
                    <label for="username"><i class="fas fa-user"></i> Tên đăng nhập hoặc Email</label>
                    <input type="text" id="username" name="username" class="form-control" 
                           value="<?php echo htmlspecialchars($username ?? ''); ?>" required autofocus>
                </div>
                
                <div class="form-group">
                    <label for="password"><i class="fas fa-lock"></i> Mật khẩu</label>
                    <input type="password" id="password" name="password" class="form-control" required>
                </div>
                
                <button type="submit" class="btn btn-primary btn-block">
                    <i class="fas fa-sign-in-alt"></i> Đăng nhập
                </button>
            </form>
            
            <div class="auth-footer">
                <p>Chưa có tài khoản? <a href="/register.php">Đăng ký ngay</a></p>
            </div>
            
            <div class="demo-accounts">
                <h4>Tài khoản demo:</h4>
                <ul>
                    <li><strong>Admin:</strong> admin / password123</li>
                    <li><strong>Customer:</strong> customer1 / password123</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
