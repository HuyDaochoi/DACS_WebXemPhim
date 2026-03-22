<?php
// app/controllers/WatchController.php
include __DIR__ . '/../models/WatchModel.php';

class WatchController
{
    private $model;

    public function __construct()
    {
        $this->model = new WatchModel();
    }

    // Action để xem phim
    public function watch()
    {
        // Lấy ID phim từ URL
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        if ($id <= 0 && isset($_GET['tap'])) {
            $tapId = intval($_GET['tap']);
            $id = $this->model->getMovieIdByEpisodeId($tapId);
        }
        if ($id <= 0) {
            die("Phim không hợp lệ.");
        }

        // Lấy thông tin phim từ model
        $movie = $this->model->getMovieDetails($id);

        // Kiểm tra nếu phim tồn tại
        if (!$movie) {
            die("Dữ liệu phim không hợp lệ.");
        }

        // Tăng lượt xem
        $this->model->incrementViewCount($id);

        $episodes = [];
        $episodeLink = null; // Link tập phim bộ
        $movieLink = null;   // Link phim lẻ
        $tapId = null;

        if ($movie['PhanLoai'] === 'Bộ') {
            $episodes = $this->model->getEpisodes($id);
            if (isset($_GET['tap'])) {
                $tapId = intval($_GET['tap']);
                foreach ($episodes as $ep) {
                    if ($ep['MaTap'] == $tapId) {
                        $episodeLink = $ep['Link'];
                        break;
                    }
                }
            }
            // Nếu không có tap hoặc tap không hợp lệ, lấy tập đầu tiên
            if (empty($episodeLink) && !empty($episodes)) {
                $tapId = $episodes[0]['MaTap'];
                $episodeLink = $episodes[0]['Link'];
            }
        } else {
            // Phim lẻ: luôn lấy link từ bảng phim
            $movieLink = $movie['Link'];
        }

        // Truyền $movie, $movieLink, $episodeLink, $episodes, $tapId sang view
        include __DIR__ . '/../Views/movies/Watch.php';
    }

    public function watchEpisode()
    {
        // Lấy id tập phim từ URL
        $tapId = isset($_GET['tap']) ? intval($_GET['tap']) : 0;
        if ($tapId <= 0) {
            die("Tập phim không hợp lệ.");
        }

        // Lấy thông tin tập phim
        $episode = $this->model->getEpisodeById($tapId);
        if (!$episode) {
            die("Không tìm thấy tập phim.");
        }

        // Lấy thông tin phim bộ
        $movieId = $episode['MaPhim'];
        $movie = $this->model->getMovieDetails($movieId);
        if (!$movie) {
            die("Không tìm thấy phim.");
        }

        // Lấy danh sách tập phim
        $episodes = $this->model->getEpisodes($movieId);

        // Link tập phim hiện tại
        $episodeLink = $episode['Link'];

        // Truyền sang view
        include __DIR__ . '/../Views/movies/WatchEpisode.php';
    }
}
