<?php
require_once 'app/Models/LichSuModel.php';

class LichSuController
{
    private $lichSuModel;

    public function __construct($db)
    {
        $this->lichSuModel = new LichSuModel($db);
        if (session_status() === PHP_SESSION_NONE) session_start();
    }

    // Hiển thị lịch sử xem
    public function index()
    {
        if (!isset($_SESSION['username'])) {
            $_SESSION['error'] = 'Bạn cần đăng nhập để xem lịch sử!';
            header('Location: /Account/login');
            exit;
        }
        $username = $_SESSION['username'];
        $history = $this->lichSuModel->getHistoryByUser($username);
        require 'app/Views/movies/lichsuxem.php';
    }

    // Xóa lịch sử (toàn bộ hoặc 1 phim)
    public function delete()
    {
        if (!isset($_SESSION['username'])) {
            $_SESSION['error'] = 'Bạn cần đăng nhập!';
            header('Location: /Account/login');
            exit;
        }
        $username = $_SESSION['username'];
        $maPhim = isset($_GET['maPhim']) ? intval($_GET['maPhim']) : null;
        $this->lichSuModel->deleteHistory($username, $maPhim);
        header('Location: /LichSu/index');
        exit;
    }
}
