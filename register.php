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
    $email = sanitize($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $full_name = sanitize($_POST['full_name'] ?? '');
    $phone = sanitize($_POST['phone'] ?? '');
    
    // Validation
    if (empty($username)) {
        $errors[] = 'Vui lòng nhập tên đăng nhập';
    } elseif (strlen($username) < 3) {
        $errors[] = 'Tên đăng nhập phải có ít nhất 3 ký tự';
    }
    
    if (empty($email)) {
        $errors[] = 'Vui lòng nhập email';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Email không hợp lệ';
    }
    
    if (empty($password)) {
        $errors[] = 'Vui lòng nhập mật khẩu';
    } elseif (strlen($password) < 6) {
        $errors[] = 'Mật khẩu phải có ít nhất 6 ký tự';
    }
    
    if ($password !== $confirm_password) {
        $errors[] = 'Mật khẩu xác nhận không khớp';
    }
    
    if (empty($errors)) {
        $pdo = getDBConnection();
        
        // Check if username exists
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->execute([$username]);
        if ($stmt->fetch()) {
            $errors[] = 'Tên đăng nhập đã tồn tại';
        }
        
        // Check if email exists
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $errors[] = 'Email đã được sử dụng';
        }
        
        if (empty($errors)) {
            // Create account
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            $stmt = $pdo->prepare("INSERT INTO users (username, email, password, full_name, phone, role) 
                                   VALUES (?, ?, ?, ?, ?, 'customer')");
            
            if ($stmt->execute([$username, $email, $hashed_password, $full_name, $phone])) {
                setFlashMessage('success', 'Đăng ký thành công! Vui lòng đăng nhập.');
                redirect('/login.php');
            } else {
                $errors[] = 'Có lỗi xảy ra, vui lòng thử lại';
            }
        }
    }
}

$page_title = 'Đăng ký';
require_once __DIR__ . '/includes/header.php';
?>

<div class="container">
    <div class="auth-page">
        <div class="auth-form-container">
            <h1><i class="fas fa-user-plus"></i> Đăng ký tài khoản</h1>
            
            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger">
                    <?php foreach ($errors as $error): ?>
                        <p><?php echo htmlspecialchars($error); ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="" class="auth-form">
                <div class="form-group">
                    <label for="username"><i class="fas fa-user"></i> Tên đăng nhập *</label>
                    <input type="text" id="username" name="username" class="form-control" 
                           value="<?php echo htmlspecialchars($username ?? ''); ?>" required autofocus>
                </div>
                
                <div class="form-group">
                    <label for="email"><i class="fas fa-envelope"></i> Email *</label>
                    <input type="email" id="email" name="email" class="form-control" 
                           value="<?php echo htmlspecialchars($email ?? ''); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="full_name"><i class="fas fa-id-card"></i> Họ và tên</label>
                    <input type="text" id="full_name" name="full_name" class="form-control" 
                           value="<?php echo htmlspecialchars($full_name ?? ''); ?>">
                </div>
                
                <div class="form-group">
                    <label for="phone"><i class="fas fa-phone"></i> Số điện thoại</label>
                    <input type="text" id="phone" name="phone" class="form-control" 
                           value="<?php echo htmlspecialchars($phone ?? ''); ?>">
                </div>
                
                <div class="form-group">
                    <label for="password"><i class="fas fa-lock"></i> Mật khẩu *</label>
                    <input type="password" id="password" name="password" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="confirm_password"><i class="fas fa-lock"></i> Xác nhận mật khẩu *</label>
                    <input type="password" id="confirm_password" name="confirm_password" class="form-control" required>
                </div>
                
                <button type="submit" class="btn btn-primary btn-block">
                    <i class="fas fa-user-plus"></i> Đăng ký
                </button>
            </form>
            
            <div class="auth-footer">
                <p>Đã có tài khoản? <a href="/login.php">Đăng nhập ngay</a></p>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
