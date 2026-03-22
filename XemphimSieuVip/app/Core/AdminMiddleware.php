<?php

class AdminMiddleware {
    public static function handle() {
        // Kiểm tra session đã được khởi tạo chưa
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Kiểm tra người dùng đã đăng nhập và có quyền admin chưa
        if (!isset($_SESSION['vai_tro']) || $_SESSION['vai_tro'] !== 'admin') {
            $_SESSION['error'] = 'Bạn không có quyền truy cập trang này!';
            header('Location: /');
            exit;
        }
    }
} 