<?php
require_once 'app/Models/MovieModel.php';
require_once 'app/Models/SearchModel.php';

class SearchController
{
    private $movieModel;
    private $searchModel;
    private $db;

    public function __construct($db)
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $this->db = $db;
        $this->movieModel = new MovieModel($this->db);
        $this->searchModel = new SearchModel($this->db);
    }

    // Hiển thị trang tìm kiếm
    public function index()
    {
        try {
            $keyword = $_GET['keyword'] ?? '';
            $genre = $_GET['genre'] ?? '';
            $country = $_GET['country'] ?? '';
            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            $perPage = 12;

            // Lấy danh sách thể loại và quốc gia cho filter
            $genres = $this->movieModel->getAllGenres();
            $countries = $this->movieModel->getAllCountries();

            // Tìm kiếm phim
            $result = $this->movieModel->searchMovies($keyword, $genre, $country, $page, $perPage);
            $movies = $result['movies'];
            $total = $result['total'];
            $totalPages = ceil($total / $perPage);

            require_once 'app/Views/search/timkiem.php';
        } catch (PDOException $e) {
            $_SESSION['error'] = 'Có lỗi xảy ra: ' . $e->getMessage();
            header('Location: /');
            exit;
        }
    }

    // API tìm kiếm nhanh (cho search box)
    public function quick()
    {
        try {
            $keyword = $_GET['keyword'] ?? '';
            if (strlen($keyword) < 2) {
                echo json_encode(['success' => false, 'message' => 'Từ khóa quá ngắn']);
                exit;
            }

            $movies = $this->movieModel->quickSearch($keyword);
            echo json_encode(['success' => true, 'movies' => $movies]);
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'message' => 'Có lỗi xảy ra']);
        }
        exit;
    }

    // Hiển thị danh sách thể loại
 

    // Hiển thị phim theo thể loại
    public function theloai($id)
    {
        $movies = [];
        $total = 0;
        $totalPages = 1;
        $genre = null;
        try {
            $keyword = $_GET['keyword'] ?? '';
            $country = $_GET['country'] ?? '';
            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            $perPage = 12;

            // Lấy lại thông tin thể loại từ DB mỗi lần truy cập
            $genre = $this->movieModel->getGenreById($id);
            $countries = $this->movieModel->getAllCountries();
            $genres = $this->movieModel->getAllGenres();

            if ($genre) {
                $result = $this->searchModel->searchMoviesByGenre($genre['MaTheLoai'], $keyword, $country, $page, $perPage);
                $movies = $result['movies'];
                $total = $result['total'];
                $totalPages = max(1, ceil($total / $perPage));

                // Xử lý dữ liệu thể loại cho mỗi phim
                foreach ($movies as &$movie) {
                    if (!empty($movie['TheLoai'])) {
                        $movie['TheLoaiArray'] = explode(',', $movie['TheLoai']);
                    } else {
                        $movie['TheLoaiArray'] = [];
                    }
                }
            }

            require_once 'app/Views/search/theloai.php';
        } catch (PDOException $e) {
            $_SESSION['error'] = 'Có lỗi xảy ra: ' . $e->getMessage();
            require_once 'app/Views/search/theloai.php';
        }
    }

    // Hiển thị danh sách quốc gia
 

    // Hiển thị phim theo quốc gia
    public function quocgia($id)
    {
        $movies = [];
        $total = 0;
        $totalPages = 1;
        $country = null;
        try {
            $keyword = $_GET['keyword'] ?? '';
            $genre = $_GET['genre'] ?? '';
            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            $perPage = 12;

            // Lấy lại thông tin quốc gia từ DB mỗi lần truy cập
            $country = $this->movieModel->getCountryById($id);
            $genres = $this->movieModel->getAllGenres();
            $countries = $this->movieModel->getAllCountries();

            if ($country) {
                $result = $this->movieModel->searchMoviesByCountry($country['MaQuocGia'], $keyword, $genre, $page, $perPage);
                $movies = $result['movies'];
                $total = $result['total'];
                $totalPages = max(1, ceil($total / $perPage));

                // Xử lý dữ liệu thể loại cho mỗi phim
                foreach ($movies as &$movie) {
                    if (!empty($movie['TheLoai'])) {
                        $movie['TheLoaiArray'] = explode(',', $movie['TheLoai']);
                    } else {
                        $movie['TheLoaiArray'] = [];
                    }
                }
            }

            require_once 'app/Views/search/quocgia.php';
        } catch (PDOException $e) {
            $_SESSION['error'] = 'Có lỗi xảy ra: ' . $e->getMessage();
            require_once 'app/Views/search/quocgia.php';
        }
    }

    // Lọc phim theo thể loại và quốc gia
    public function locphim()
    {
        $genres = $this->movieModel->getAllGenres();
        $countries = $this->movieModel->getAllCountries();

        $selectedGenres = isset($_GET['genres']) ? $_GET['genres'] : [];
        $selectedCountry = isset($_GET['country']) ? $_GET['country'] : '';

        $movies = [];
        if (!empty($selectedGenres) || !empty($selectedCountry)) {
            $movies = $this->searchModel->filterMovies($selectedGenres, $selectedCountry);
        }

        require_once 'app/Views/search/locphim.php';
    }
}
