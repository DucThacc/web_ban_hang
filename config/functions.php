<?php
/**
 * Helper Functions
 */

/**
 * Sanitize input data
 * @param string $data
 * @return string
 */
function sanitize($data) {
    return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES, 'UTF-8');
}

/**
 * Redirect to a URL
 * @param string $url
 */
function redirect($url) {
    header("Location: " . $url);
    exit();
}

/**
 * Check if user is logged in
 * @return bool
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

/**
 * Check if user is admin
 * @return bool
 */
function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

/**
 * Require login
 */
function requireLogin() {
    if (!isLoggedIn()) {
        $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
        redirect('/login.php');
    }
}

/**
 * Require admin access
 */
function requireAdmin() {
    requireLogin();
    if (!isAdmin()) {
        redirect('/index.php');
    }
}

/**
 * Format price in VND
 * @param float $price
 * @return string
 */
function formatPrice($price) {
    return number_format($price, 0, ',', '.') . 'đ';
}

/**
 * Format date
 * @param string $date
 * @return string
 */
function formatDate($date) {
    return date('d/m/Y H:i', strtotime($date));
}

/**
 * Generate slug from string
 * @param string $str
 * @return string
 */
function generateSlug($str) {
    $str = mb_strtolower($str, 'UTF-8');
    
    // Vietnamese characters mapping
    $vietnamese = [
        'à' => 'a', 'á' => 'a', 'ả' => 'a', 'ã' => 'a', 'ạ' => 'a',
        'ă' => 'a', 'ằ' => 'a', 'ắ' => 'a', 'ẳ' => 'a', 'ẵ' => 'a', 'ặ' => 'a',
        'â' => 'a', 'ầ' => 'a', 'ấ' => 'a', 'ẩ' => 'a', 'ẫ' => 'a', 'ậ' => 'a',
        'đ' => 'd',
        'è' => 'e', 'é' => 'e', 'ẻ' => 'e', 'ẽ' => 'e', 'ẹ' => 'e',
        'ê' => 'e', 'ề' => 'e', 'ế' => 'e', 'ể' => 'e', 'ễ' => 'e', 'ệ' => 'e',
        'ì' => 'i', 'í' => 'i', 'ỉ' => 'i', 'ĩ' => 'i', 'ị' => 'i',
        'ò' => 'o', 'ó' => 'o', 'ỏ' => 'o', 'õ' => 'o', 'ọ' => 'o',
        'ô' => 'o', 'ồ' => 'o', 'ố' => 'o', 'ổ' => 'o', 'ỗ' => 'o', 'ộ' => 'o',
        'ơ' => 'o', 'ờ' => 'o', 'ớ' => 'o', 'ở' => 'o', 'ỡ' => 'o', 'ợ' => 'o',
        'ù' => 'u', 'ú' => 'u', 'ủ' => 'u', 'ũ' => 'u', 'ụ' => 'u',
        'ư' => 'u', 'ừ' => 'u', 'ứ' => 'u', 'ử' => 'u', 'ữ' => 'u', 'ự' => 'u',
        'ỳ' => 'y', 'ý' => 'y', 'ỷ' => 'y', 'ỹ' => 'y', 'ỵ' => 'y',
    ];
    
    $str = strtr($str, $vietnamese);
    $str = preg_replace('/[^a-z0-9\s-]/', '', $str);
    $str = preg_replace('/[\s-]+/', '-', $str);
    $str = trim($str, '-');
    
    return $str;
}

/**
 * Upload image file
 * @param array $file $_FILES array element
 * @param string $uploadDir Upload directory path
 * @param string $prefix Filename prefix
 * @return array ['success' => bool, 'filename' => string, 'error' => string]
 */
function uploadImage($file, $uploadDir, $prefix = 'img') {
    // Check if file was uploaded
    if (!isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
        return ['success' => false, 'error' => 'No file uploaded'];
    }
    
    // Check file size
    if ($file['size'] > MAX_UPLOAD_SIZE) {
        return ['success' => false, 'error' => 'File size exceeds limit (5MB)'];
    }
    
    // Check file type
    $fileExt = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($fileExt, ALLOWED_IMAGE_TYPES)) {
        return ['success' => false, 'error' => 'Invalid file type. Allowed: ' . implode(', ', ALLOWED_IMAGE_TYPES)];
    }
    
    // Check if it's actually an image
    $imageInfo = getimagesize($file['tmp_name']);
    if ($imageInfo === false) {
        return ['success' => false, 'error' => 'File is not a valid image'];
    }
    
    // Generate unique filename
    $filename = $prefix . '_' . time() . '_' . uniqid() . '.' . $fileExt;
    $targetPath = $uploadDir . $filename;
    
    // Create directory if not exists
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
    
    // Move uploaded file
    if (move_uploaded_file($file['tmp_name'], $targetPath)) {
        // Resize image if too large (optional)
        resizeImage($targetPath, 1920, 1080);
        
        return ['success' => true, 'filename' => $filename];
    }
    
    return ['success' => false, 'error' => 'Failed to move uploaded file'];
}

/**
 * Resize image to fit within max dimensions while maintaining aspect ratio
 * @param string $imagePath
 * @param int $maxWidth
 * @param int $maxHeight
 */
function resizeImage($imagePath, $maxWidth, $maxHeight) {
    list($width, $height, $type) = getimagesize($imagePath);
    
    // Don't resize if already smaller
    if ($width <= $maxWidth && $height <= $maxHeight) {
        return;
    }
    
    // Calculate new dimensions
    $ratio = min($maxWidth / $width, $maxHeight / $height);
    $newWidth = round($width * $ratio);
    $newHeight = round($height * $ratio);
    
    // Create new image
    $newImage = imagecreatetruecolor($newWidth, $newHeight);
    
    // Load source image based on type
    switch ($type) {
        case IMAGETYPE_JPEG:
            $source = imagecreatefromjpeg($imagePath);
            break;
        case IMAGETYPE_PNG:
            $source = imagecreatefrompng($imagePath);
            imagealphablending($newImage, false);
            imagesavealpha($newImage, true);
            break;
        case IMAGETYPE_GIF:
            $source = imagecreatefromgif($imagePath);
            break;
        case IMAGETYPE_WEBP:
            $source = imagecreatefromwebp($imagePath);
            break;
        default:
            return;
    }
    
    // Resize
    imagecopyresampled($newImage, $source, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
    
    // Save based on type
    switch ($type) {
        case IMAGETYPE_JPEG:
            imagejpeg($newImage, $imagePath, 90);
            break;
        case IMAGETYPE_PNG:
            imagepng($newImage, $imagePath, 9);
            break;
        case IMAGETYPE_GIF:
            imagegif($newImage, $imagePath);
            break;
        case IMAGETYPE_WEBP:
            imagewebp($newImage, $imagePath, 90);
            break;
    }
    
    imagedestroy($source);
    imagedestroy($newImage);
}

/**
 * Delete image file
 * @param string $filename
 * @param string $uploadDir
 * @return bool
 */
function deleteImage($filename, $uploadDir) {
    if (empty($filename)) {
        return false;
    }
    
    $filePath = $uploadDir . $filename;
    if (file_exists($filePath)) {
        return unlink($filePath);
    }
    
    return false;
}

/**
 * Generate CSRF token
 * @return string
 */
function generateCSRFToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verify CSRF token
 * @param string $token
 * @return bool
 */
function verifyCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Set flash message
 * @param string $type (success, error, warning, info)
 * @param string $message
 */
function setFlashMessage($type, $message) {
    $_SESSION['flash_message'] = [
        'type' => $type,
        'message' => $message
    ];
}

/**
 * Get and clear flash message
 * @return array|null
 */
function getFlashMessage() {
    if (isset($_SESSION['flash_message'])) {
        $message = $_SESSION['flash_message'];
        unset($_SESSION['flash_message']);
        return $message;
    }
    return null;
}

/**
 * Paginate results
 * @param int $total Total number of items
 * @param int $perPage Items per page
 * @param int $currentPage Current page number
 * @return array ['offset' => int, 'total_pages' => int]
 */
function paginate($total, $perPage = 12, $currentPage = 1) {
    $totalPages = ceil($total / $perPage);
    $currentPage = max(1, min($currentPage, $totalPages));
    $offset = ($currentPage - 1) * $perPage;
    
    return [
        'offset' => $offset,
        'total_pages' => $totalPages,
        'current_page' => $currentPage,
        'per_page' => $perPage,
        'total' => $total
    ];
}

/**
 * Get order status label in Vietnamese
 * @param string $status
 * @return string
 */
function getOrderStatusLabel($status) {
    $labels = [
        'pending' => 'Chờ xác nhận',
        'confirmed' => 'Đã xác nhận',
        'processing' => 'Đang xử lý',
        'shipping' => 'Đang giao hàng',
        'completed' => 'Hoàn thành',
        'cancelled' => 'Đã hủy'
    ];
    
    return $labels[$status] ?? $status;
}

/**
 * Get order status badge class
 * @param string $status
 * @return string
 */
function getOrderStatusClass($status) {
    $classes = [
        'pending' => 'warning',
        'confirmed' => 'info',
        'processing' => 'primary',
        'shipping' => 'info',
        'completed' => 'success',
        'cancelled' => 'danger'
    ];
    
    return $classes[$status] ?? 'secondary';
}
