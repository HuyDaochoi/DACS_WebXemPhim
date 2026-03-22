<?php
require_once 'app/Models/MovieModel.php';
require_once 'app/Models/AccountModel.php';
require_once 'app/configs/database.php';
require_once 'app/configs/config.php';
require_once 'app/Core/AdminMiddleware.php';

class MovieController
{
    private $movieModel;
    private $accountModel;
    private $db;

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $this->db = new Database();
        $this->movieModel = new MovieModel($this->db);
        $this->accountModel = new AccountModel($this->db);
    }

    // Hiển thị trang chủ với danh sách phim
    public function index()
    {
        try {
            // Fetch movies from database
            $query = "SELECT p.*, GROUP_CONCAT(t.TenTheLoai) as TheLoai, qg.TenQuocGia as TenQuocGia 
                     FROM Phim p 
                     LEFT JOIN Phim_TheLoai pt ON p.MaPhim = pt.MaPhim 
                     LEFT JOIN TheLoai t ON pt.MaTheLoai = t.MaTheLoai 
                     LEFT JOIN QuocGia qg ON p.MaQuocGia = qg.MaQuocGia
                     GROUP BY p.MaPhim 
                     ORDER BY p.NgayTao DESC 
                     LIMIT 10";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            $new_movies = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $query = "SELECT p.*, GROUP_CONCAT(t.TenTheLoai) as TheLoai, qg.TenQuocGia as TenQuocGia 
                     FROM Phim p 
                     LEFT JOIN Phim_TheLoai pt ON p.MaPhim = pt.MaPhim 
                     LEFT JOIN TheLoai t ON pt.MaTheLoai = t.MaTheLoai 
                     LEFT JOIN QuocGia qg ON p.MaQuocGia = qg.MaQuocGia
                     GROUP BY p.MaPhim 
                     ORDER BY p.LuotXem DESC 
                     LIMIT 10";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            $popular_movies = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $query = "SELECT p.*, GROUP_CONCAT(t.TenTheLoai) as TheLoai, qg.TenQuocGia as TenQuocGia, 
                            COUNT(ls.MaPhim) as LuotXemTuan
                     FROM Phim p 
                     LEFT JOIN Phim_TheLoai pt ON p.MaPhim = pt.MaPhim 
                     LEFT JOIN TheLoai t ON pt.MaTheLoai = t.MaTheLoai 
                     LEFT JOIN QuocGia qg ON p.MaQuocGia = qg.MaQuocGia
                     LEFT JOIN LichSu ls ON p.MaPhim = ls.MaPhim 
                     WHERE ls.ThoiGian >= DATE_SUB(NOW(), INTERVAL 7 DAY)
                     GROUP BY p.MaPhim
                     ORDER BY LuotXemTuan DESC
                     LIMIT 10";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            $trending_movies = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Fetch genres for sidebar
            $genres = $this->movieModel->getAllGenres();

            // Fetch trending movies from TMDb API (default: day)
            $tmdb_trending = $this->fetchTmdbTrendingMovies('day');

            require_once 'app/Views/shares/show.php';
        } catch (PDOException $e) {
            $_SESSION['error'] = 'Có lỗi xảy ra: ' . $e->getMessage();
            $new_movies = [];
            $popular_movies = [];
            $trending_movies = [];
            $genres = [];
            $tmdb_trending = [];
            require_once 'app/Views/shares/show.php';
        }
    }

    // Fetch trending movies from TMDb API
    private function fetchTmdbTrendingMovies($time_window = 'day')
    {
        // Validate time_window
        $time_window = in_array($time_window, ['day', 'week']) ? $time_window : 'day';

        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => "https://api.themoviedb.org/3/trending/movie/$time_window?language=en-US",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => [
                "Authorization: Bearer " . TMDB_API_KEY,
                "accept: application/json"
            ],
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);

        if ($err) {
            return [];
        }

        $data = json_decode($response, true);
        if (!$data || !isset($data['results'])) {
            return [];
        }

        // Fetch genre list for mapping genre_ids
        $genre_map = $this->fetchTmdbGenres();

        // Map TMDb movies to app format
        $movies = [];
        foreach (array_slice($data['results'], 0, 10) as $movie) {
            $genre_names = [];
            foreach ($movie['genre_ids'] as $genre_id) {
                if (isset($genre_map[$genre_id])) {
                    $genre_names[] = $genre_map[$genre_id];
                }
            }

            $movies[] = [
                'TmdbId' => $movie['id'],
                'TenPhim' => $movie['title'] ?? 'Unknown',
                'HinhAnh' => $movie['poster_path'] ? TMDB_IMAGE_BASE_URL . $movie['poster_path'] : 'https://via.placeholder.com/300x450?text=No+Image',
                'TheLoai' => implode(', ', $genre_names) ?: 'N/A',
                'TenQuocGia' => 'N/A', // Could fetch via /movie/{id}
                'NamPhatHanh' => $movie['release_date'] ? substr($movie['release_date'], 0, 4) : 'N/A',
                'TinhTrang' => $this->inferMovieStatus($movie['release_date'])
            ];
        }

        return $movies;
    }

    // Fetch TMDb genre list (with caching)
    private function fetchTmdbGenres()
    {
        $cache_file = 'app/cache/tmdb_genres.json';
        $cache_duration = 24 * 60 * 60; // 24 hours

        // Check cache
        if (file_exists($cache_file) && (time() - filemtime($cache_file)) < $cache_duration) {
            $genre_map = json_decode(file_get_contents($cache_file), true);
            if ($genre_map) {
                return $genre_map;
            }
        }

        // Fetch from API
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => "https://api.themoviedb.org/3/genre/movie/list?language=en-US",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => [
                "Authorization: Bearer " . TMDB_API_KEY,
                "accept: application/json"
            ],
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);

        if ($err) {
            return [];
        }

        $data = json_decode($response, true);
        if (!$data || !isset($data['genres'])) {
            return [];
        }

        $genre_map = [];
        foreach ($data['genres'] as $genre) {
            $genre_map[$genre['id']] = $genre['name'];
        }

        // Save to cache
        if (!is_dir('app/cache')) {
            mkdir('app/cache', 0755, true);
        }
        file_put_contents($cache_file, json_encode($genre_map));

        return $genre_map;
    }

    // Infer movie status based on release date
    private function inferMovieStatus($release_date)
    {
        if (!$release_date) {
            return 'N/A';
        }
        $release_year = (int)substr($release_date, 0, 4);
        $current_year = (int)date('Y');
        return $release_year >= $current_year ? 'Đang chiếu' : 'Hoàn thành';
    }

    // Hiển thị chi tiết phim
    public function view($id)
    {
        try {
            $movie = $this->movieModel->getMovieById($id);
            if (!$movie) {
                $_SESSION['error'] = 'Không tìm thấy phim!';
                header('Location: /');
                exit;
            }

            $query = "UPDATE Phim SET LuotXem = LuotXem + 1 WHERE MaPhim = :id";
            $stmt = $this->db->prepare($query);
            $stmt->execute([':id' => $id]);

            if (isset($_SESSION['user_id'])) {
                $query = "INSERT INTO LichSu (TenDN, MaPhim, ThoiGian) 
                         VALUES (:username, :movie_id, NOW())
                         ON DUPLICATE KEY UPDATE ThoiGian = NOW()";
                $stmt = $this->db->prepare($query);
                $stmt->execute([
                    ':username' => $_SESSION['username'],
                    ':movie_id' => $id
                ]);
            }        


            $episodes = [];
            if ($movie['PhanLoai'] === 'Bộ') {
                $episodes = $this->movieModel->getMovieEpisodes($id);
            }

            $videos = $this->movieModel->getMovieVideos($id);

            // Fetch actors and directors
            $actors = $this->movieModel->getMovieActors($id);
            $directors = $this->movieModel->getMovieDirectors($id);

            $isFavorite = false;
        if (isset($_SESSION['user_id'])) {
            require_once 'app/Models/YeuThichModel.php';
            $favModel = new YeuThichModel($this->db);
            $isFavorite = $favModel->isFavorite($id, $_SESSION['user_id']);
        }
            require_once 'app/Views/movies/view.php';
        } catch (PDOException $e) {
            $_SESSION['error'] = 'Có lỗi xảy ra: ' . $e->getMessage();
            header('Location: /');
            exit;
        }
    }

    // Hiển thị trailer phim
    public function watch($id)
    {
        try {
            $movie = $this->movieModel->getMovieById($id);
            if (!$movie) {
                $_SESSION['error'] = 'Không tìm thấy phim!';
                header('Location: /');
                exit;
            }

            // Increment view count
            $query = "UPDATE Phim SET LuotXem = LuotXem + 1 WHERE MaPhim = :id";
            $stmt = $this->db->prepare($query);
            $stmt->execute([':id' => $id]);

            require_once 'app/Views/movies/watch.php';
        } catch (PDOException $e) {
            $_SESSION['error'] = 'Có lỗi xảy ra: ' . $e->getMessage();
            header('Location: /');
            exit;
        }
    }

    // Hiển thị form thêm phim mới (chỉ admin)
    public function add()
    {
        if (!isset($_SESSION['vai_tro']) || $_SESSION['vai_tro'] !== 'admin') {
            $_SESSION['error'] = 'Bạn không có quyền truy cập trang này!';
            header('Location: /');
            exit;
        }

        $genres = $this->movieModel->getAllGenres();
        $countries = $this->movieModel->getAllCountries();
        require_once 'app/Views/movies/add_movie.php';
    }

    // Xử lý thêm phim mới (chỉ admin)
    public function save()
    {
        if (!isset($_SESSION['vai_tro']) || $_SESSION['vai_tro'] !== 'admin') {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => 'Bạn không có quyền truy cập trang này!'
            ]);
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $result = $this->movieModel->addMovie($_POST, $_FILES);

            header('Content-Type: application/json');
            echo json_encode($result);
            exit;
        }

        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'message' => 'Phương thức không hợp lệ'
        ]);
        exit;
    }

    // Hiển thị danh sách phim (chỉ admin)
    public function list()
    {
        if (!isset($_SESSION['vai_tro']) || $_SESSION['vai_tro'] !== 'admin') {
            $_SESSION['error'] = 'Bạn không có quyền truy cập trang này!';
            header('Location: /');
            exit;
        }

        $search = isset($_GET['search']) ? trim($_GET['search']) : '';
        $category = isset($_GET['category']) ? (int)$_GET['category'] : '';
        $status = isset($_GET['status']) ? trim($_GET['status']) : '';
        $page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
        $perPage = 10;

        $categories = $this->movieModel->getAllGenres();

        $result = $this->movieModel->filterMovies($search, $category, $status, $page, $perPage);
        $movies = $result['movies'];
        $total = $result['total'];

        $total_pages = ceil($total / $perPage);

        $queryParams = [];
        if ($search) $queryParams['search'] = $search;
        if ($category) $queryParams['category'] = $category;
        if ($status) $queryParams['status'] = $status;
        $query_string = $queryParams ? '&' . http_build_query($queryParams) : '';

        require_once 'app/Views/movies/list.php';
    }

    // Thêm tập phim mới
    public function addEpisode($movie_id)
    {
        if (!isset($_SESSION['vai_tro']) || $_SESSION['vai_tro'] !== 'admin') {
            $_SESSION['error'] = 'Bạn không có quyền truy cập trang này!';
            header('Location: /');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $ten_tap = trim($_POST['ten_tap']);
            $link = trim($_POST['link']);

            if (empty($ten_tap) || empty($link)) {
                $_SESSION['error'] = 'Tên tập và link không được để trống!';
                header('Location: /movie/manageEpisodes/' . $movie_id);
                exit;
            }

            $result = $this->movieModel->addEpisode($movie_id, $ten_tap, $link);
            if ($result['success']) {
                $_SESSION['success'] = $result['message'];
            } else {
                $_SESSION['error'] = $result['message'];
            }
            header('Location: /movie/manageEpisodes/' . $movie_id);
            exit;
        } else {
            $_SESSION['error'] = 'Phương thức không hợp lệ!';
            header('Location: /movie/manageEpisodes/' . $movie_id);
            exit;
        }
    }

    // Thêm video mới
    public function addVideo($movie_id)
    {
        if (!isset($_SESSION['vai_tro']) || $_SESSION['vai_tro'] !== 'admin') {
            $_SESSION['error'] = 'Bạn không có quyền truy cập trang này!';
            header('Location: /');
            exit;
        }

        $movie = $this->movieModel->getMovieById($movie_id);
        if (!$movie) {
            $_SESSION['error'] = 'Không tìm thấy phim!';
            header('Location: /');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            require_once 'app/Views/movies/add_video.php';
        } else {
            $ten_video = $_POST['ten_video'] ?? '';
            $chat_luong = $_POST['chat_luong'] ?? '';
            $ngon_ngu = $_POST['ngon_ngu'] ?? '';
            $thoi_luong = $_POST['thoi_luong'] ?? '';
            $link = $_POST['link'] ?? '';

            $result = $this->movieModel->addVideo($movie_id, $ten_video, $chat_luong, $ngon_ngu, $thoi_luong, $link);

            if ($result['success']) {
                $_SESSION['success'] = $result['message'];
                header('Location: /movie/view/' . $movie_id);
            } else {
                $_SESSION['error'] = $result['message'];
                header('Location: /movie/video/' . $movie_id);
            }
            exit;
        }
    }

    // Hiển thị form sửa phim (chỉ admin)
    public function edit($id)
    {
        if (!isset($_SESSION['vai_tro']) || $_SESSION['vai_tro'] !== 'admin') {
            $_SESSION['error'] = 'Bạn không có quyền truy cập trang này!';
            header('Location: /');
            exit;
        }

        $movie = $this->movieModel->getMovieById($id);
        if (!$movie) {
            $_SESSION['error'] = 'Không tìm thấy phim!';
            header('Location: /movie/list');
            exit;
        }
        $movie['actors'] = $this->movieModel->getMovieActors($id);
        $movie['directors'] = $this->movieModel->getMovieDirectors($id);
        $genres = $this->movieModel->getAllGenres();
        $countries = $this->movieModel->getAllCountries();
        require_once 'app/Views/movies/edit.php';
    }

    // Xử lý cập nhật phim (chỉ admin)
    public function update($id) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $_SESSION['error'] = 'Phương thức không hợp lệ!';
            header('Location: /movie/edit/' . $id);
            exit;
        }

        // Verify CSRF token
        $csrf_token = $_POST['csrf_token'] ?? '';
        if (!verifyCsrfToken($csrf_token)) {
            $_SESSION['error'] = 'CSRF token không hợp lệ!';
            header('Location: /movie/edit/' . $id);
            exit;
        }

        // Collect and validate form data
        $data = [
            'title' => trim($_POST['title'] ?? ''),
            'subtitle' => trim($_POST['subtitle'] ?? ''),
            'description' => trim($_POST['description'] ?? ''),
            'content' => trim($_POST['content'] ?? ''),
            'release_year' => !empty($_POST['release_year']) ? (int)$_POST['release_year'] : null,
            'duration' => (int)($_POST['duration'] ?? 0),
            'country_id' => trim($_POST['country_id'] ?? ''),
            'status' => trim($_POST['status'] ?? ''),
            'type' => trim($_POST['type'] ?? ''),
            'trailer_url' => trim($_POST['trailer_url'] ?? '')
        ];
        $poster = $_FILES['poster'] ?? null;
        $genres = $_POST['genres'] ?? [];
        $actors = trim($_POST['actors'] ?? '');
        $directors = trim($_POST['directors'] ?? '');
        $current_poster = trim($_POST['current_poster'] ?? '');

        // Log input data for debugging
        error_log('Input data: ' . print_r($data, true));
        error_log('Genres: ' . print_r($genres, true));
        error_log('Poster: ' . print_r($poster, true));

        // Server-side validation
        if (empty($data['title']) || strlen($data['title']) < 3 || strlen($data['title']) > 100) {
            $_SESSION['error'] = 'Tên phim phải từ 3-100 ký tự!';
            header('Location: /movie/edit/' . $id);
            exit;
        }
        if ($data['duration'] < 1 || $data['duration'] > 999) {
            $_SESSION['error'] = 'Thời lượng phải từ 1-999 phút!';
            header('Location: /movie/edit/' . $id);
            exit;
        }
        if (empty($data['country_id'])) {
            $_SESSION['error'] = 'Vui lòng chọn quốc gia!';
            header('Location: /movie/edit/' . $id);
            exit;
        }
        if (empty($genres)) {
            $_SESSION['error'] = 'Vui lòng chọn ít nhất một thể loại!';
            header('Location: /movie/edit/' . $id);
            exit;
        }
        if (empty($data['status']) || !in_array($data['status'], ['Đang chiếu', 'Sắp chiếu', 'Đã kết thúc'])) {
            $_SESSION['error'] = 'Trạng thái không hợp lệ!';
            header('Location: /movie/edit/' . $id);
            exit;
        }
        if (empty($data['type']) || !in_array($data['type'], ['Lẻ', 'Bộ'])) {
            $_SESSION['error'] = 'Phân loại không hợp lệ!';
            header('Location: /movie/edit/' . $id);
            exit;
        }
        if (empty($data['trailer_url']) || !filter_var($data['trailer_url'], FILTER_VALIDATE_URL)) {
            $_SESSION['error'] = 'Link trailer không hợp lệ!';
            header('Location: /movie/edit/' . $id);
            exit;
        }
        if ($poster && $poster['error'] === UPLOAD_ERR_OK) {
            $validTypes = ['image/jpeg', 'image/png', 'image/gif'];
            if (!in_array($poster['type'], $validTypes)) {
                $_SESSION['error'] = 'Poster phải là file JPEG, PNG hoặc GIF!';
                header('Location: /movie/edit/' . $id);
                exit;
            }
            if ($poster['size'] > 5 * 1024 * 1024) {
                $_SESSION['error'] = 'Poster không được vượt quá 5MB!';
                header('Location: /movie/edit/' . $id);
                exit;
            }
        }

        // Update movie
        try {
            $result = $this->movieModel->updateMovie($id, $data, $poster, $genres, $actors, $directors, $current_poster);
            if ($result) {
                $_SESSION['success'] = 'Cập nhật phim thành công!';
                header('Location: /movie/list');
            } else {
                $_SESSION['error'] = 'Cập nhật phim thất bại! Kiểm tra log để biết chi tiết.';
                header('Location: /movie/edit/' . $id);
            }
        } catch (Exception $e) {
            error_log('Update movie error: ' . $e->getMessage());
            $_SESSION['error'] = 'Lỗi hệ thống khi cập nhật phim: ' . $e->getMessage();
            header('Location: /movie/edit/' . $id);
        }
        exit;
    }
    // Xóa phim (chỉ admin)
    public function delete($id)
    {
        if (!isset($_SESSION['vai_tro']) || $_SESSION['vai_tro'] !== 'admin') {
            $_SESSION['error'] = 'Bạn không có quyền truy cập trang này!';
            header('Location: /');
            exit;
        }

        $result = $this->movieModel->deleteMovie($id);
        if ($result['success']) {
            $_SESSION['success'] = $result['message'];
        } else {
            $_SESSION['error'] = $result['message'];
        }
        header('Location: /movie/list');
        exit;
    }

    // Tìm kiếm phim
    public function search()
    {
        $keyword = $_GET['keyword'] ?? '';
        $movies = $this->movieModel->searchMovies($keyword);
        require_once 'app/Views/movies/search.php';
    }

    // Lọc phim theo thể loại
    public function category($id)
    {
        $movies = $this->movieModel->getMoviesByCategory($id);
        require_once 'app/Views/movies/category.php';
    }

    // Hiển thị trang quản lý phim
    public function manage()
    {
        if (!isset($_SESSION['vai_tro']) || $_SESSION['vai_tro'] !== 'admin') {
            $_SESSION['error'] = 'Bạn không có quyền truy cập trang này!';
            header('Location: /');
            exit;
        }

        $movies = $this->movieModel->getAllMovies();
        $genres = $this->movieModel->getAllGenres();
        require_once 'app/Views/movies/manage.php';
    }

    // Thêm phim bộ (chỉ admin)
    public function addSeries()
    {
        if (!isset($_SESSION['vai_tro']) || $_SESSION['vai_tro'] !== 'admin') {
            $_SESSION['error'] = 'Bạn không có quyền truy cập trang này!';
            header('Location: /');
            exit;
        }

        require_once 'app/Views/movies/add_series.php';
    }

    // Xử lý thêm phim bộ mới (chỉ admin)
    public function saveSeries()
    {
        try {
            // Lấy dữ liệu từ form
            $title = $_POST['title'] ?? '';
            $subtitle = $_POST['subtitle'] ?? '';
            $description = $_POST['description'] ?? '';
            $releaseYear = $_POST['release_year'] ?? null;
            $duration = $_POST['duration'] ?? null;
            $countryId = $_POST['country_id'] ?? null;
            $genres = $_POST['genres'] ?? []; // Danh sách thể loại
            $poster = $_FILES['poster'] ?? null;
            $episodes = $_POST['episodes'] ?? '';
            $link = $_POST['trailer_url'] ?? ''; // Lấy link phim từ form

            // Kiểm tra dữ liệu hợp lệ
            if (empty($title) || empty($genres) || !$poster || empty($episodes)) {
                $_SESSION['error'] = 'Vui lòng điền đầy đủ thông tin!';
                header('Location: /movie/addSeries');
                exit;
            }

            // Upload poster
            $posterPath = 'uploads/' . basename($poster['name']);
            move_uploaded_file($poster['tmp_name'], $posterPath);

            // Lưu phim vào bảng `Phim`
            $query = "INSERT INTO Phim (TenPhim, TieuDe, MoTa, NamPhatHanh, ThoiLuong, MaQuocGia, HinhAnh, PhanLoai, Link)
                      VALUES (:title, :subtitle, :description, :releaseYear, :duration, :countryId, :poster, 'Bộ', :link)";
            $stmt = $this->db->prepare($query);
            $stmt->execute([
                ':title' => $title,
                ':subtitle' => $subtitle,
                ':description' => $description,
                ':releaseYear' => $releaseYear,
                ':duration' => $duration,
                ':countryId' => $countryId,
                ':poster' => $posterPath,
                ':link' => $link
            ]);

            // Lấy ID của phim vừa thêm
            $movieId = $this->db->lastInsertId();

            // Lưu thể loại vào bảng liên kết `Phim_TheLoai`
            $query = "INSERT INTO Phim_TheLoai (MaPhim, MaTheLoai) VALUES (:movieId, :genreId)";
            $stmt = $this->db->prepare($query);
            foreach ($genres as $genreId) {
                $stmt->execute([
                    ':movieId' => $movieId,
                    ':genreId' => $genreId
                ]);
            }

            // Lưu danh sách tập phim vào bảng `TapPhim`
            $query = "INSERT INTO TapPhim (MaPhim, TenTap, Link) VALUES (:movieId, :episodeName, :episodeLink)";
            $stmt = $this->db->prepare($query);
            $episodeList = explode("\n", $episodes);
            foreach ($episodeList as $episode) {
                list($episodeName, $episodeLink) = explode('|', $episode);
                $stmt->execute([
                    ':movieId' => $movieId,
                    ':episodeName' => trim($episodeName),
                    ':episodeLink' => trim($episodeLink)
                ]);
            }

            $_SESSION['success'] = 'Thêm phim bộ thành công!';
            header('Location: /movie/list');
            exit;
        } catch (PDOException $e) {
            $_SESSION['error'] = 'Có lỗi xảy ra: ' . $e->getMessage();
            header('Location: /movie/addSeries');
            exit;
        }
    }

    public function manageEpisodes($movie_id)
    {
        if (!isset($_SESSION['vai_tro']) || $_SESSION['vai_tro'] !== 'admin') {
            $_SESSION['error'] = 'Bạn không có quyền truy cập trang này!';
            header('Location: /');
            exit;
        }

        $movie = $this->movieModel->getMovieById($movie_id);
        if (!$movie) {
            $_SESSION['error'] = 'Không tìm thấy phim!';
            header('Location: /movie/list');
            exit;
        }

        $episodes = $this->movieModel->getMovieEpisodes($movie_id);
        require_once 'app/Views/movies/manage_episodes.php';
    }
}