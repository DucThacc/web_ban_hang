<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/functions.php';

session_start();

if (session_status() === PHP_SESSION_ACTIVE) {
    session_destroy();
}

setFlashMessage('success', 'Đăng xuất thành công!');
redirect('/index.php');
