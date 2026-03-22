<?php
require_once 'app/Models/YeuThichModel.php';

class YeuThichController
{
    private $favoriteModel;

    public function __construct($db)
    {
        $this->favoriteModel = new YeuThichModel($db);
    }

    // Thêm hoặc xóa phim yêu thích
    public function toggleYeuThich()
    {
        $movieId = $_GET['id'] ?? null;

        if (!$movieId) {
            $_SESSION['error'] = 'Dữ liệu không hợp lệ.';
            header('Location: /movie/list');
            exit;
        }

        $userId = $_SESSION['user_id'] ?? null;

        if (!$userId) {
            $_SESSION['error'] = 'Bạn cần đăng nhập để sử dụng chức năng này.';
            header('Location: /account/login');
            exit;
        }

        if ($this->favoriteModel->isFavorite($movieId, $userId)) {
            $this->favoriteModel->removeFavorite($movieId, $userId);
            $_SESSION['success'] = 'Đã bỏ yêu thích.';
        } else {
            $this->favoriteModel->addFavorite($movieId, $userId);
            $_SESSION['success'] = 'Đã thêm vào yêu thích.';
        }

        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit;
    }

    // Hiển thị danh sách phim yêu thích
    public function listYeuthich()
    {
        $userId = $_SESSION['user_id'] ?? null;

        if (!$userId) {
            echo "Bạn cần đăng nhập để xem danh sách yêu thích.";
            return;
        }

        $movies = $this->favoriteModel->getFavoritesByUser($userId);

        require 'app/Views/favorites/listYeuthich.php';
    }
}
