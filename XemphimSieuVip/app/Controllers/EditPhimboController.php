<?php
require_once 'app/Models/EditPhimboModel.php';

class EditPhimboController
{
    private $model;
    public function __construct($db)
    {
        $this->model = new EditPhimboModel($db);
    }

    public function deleteEpisode($tapId)
    {
        // Kiểm tra quyền admin nếu cần
        $episode = $this->model->getEpisodeById($tapId);
        if (!$episode) {
            $_SESSION['error'] = 'Không tìm thấy tập phim!';
            header('Location: /movie/list');
            exit;
        }
        $movie_id = $episode['MaPhim'];
        $result = $this->model->deleteEpisode($tapId);
        if ($result['success']) {
            $_SESSION['success'] = $result['message'];
        } else {
            $_SESSION['error'] = $result['message'];
        }
        header('Location: /movie/manageEpisodes/' . $movie_id);
        exit;
    }

    public function editEpisode($tapId)
    {
        // Lấy thông tin tập phim
        $episode = $this->model->getEpisodeById($tapId);
        if (!$episode) {
            $_SESSION['error'] = 'Không tìm thấy tập phim!';
            header('Location: /movie/list');
            exit;
        }

        // Nếu submit form sửa
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $tenTap = $_POST['ten_tap'] ?? '';
            $link = $_POST['link'] ?? '';
            $result = $this->model->updateEpisode($tapId, $tenTap, $link);
            $_SESSION[$result['success'] ? 'success' : 'error'] = $result['message'];
            header('Location: /movie/manageEpisodes/' . $episode['MaPhim']);
            exit;
        }

        // Hiển thị form sửa
        require_once 'app/Views/movies/edit_episode.php';
    }

    // Bạn có thể thêm các action thêm/sửa tập phim tại đây
}
