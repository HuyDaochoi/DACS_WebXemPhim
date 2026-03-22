<?php
require_once 'app/configs/database.php';
require_once 'app/Models/ProductModel.php';

class ProductController
{
    private $productModel;
    private $db;
    
    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        $database = new Database();
        $this->db = $database->getConnection();
        $this->productModel = new ProductModel($this->db);
    }

    public function index()
    {
        // Điều hướng đến trang show.php
        header('Location: /product/show');
        exit;
    }

    public function show()
    {
        // Kiểm tra và tạo thư mục uploads nếu chưa tồn tại
        $uploads_dir = 'uploads';
        if (!is_dir($uploads_dir)) {
            mkdir($uploads_dir, 0777, true);
        }
        
        // Khởi tạo các biến dữ liệu để truyền vào view
        try {
            // Lấy danh sách phim mới cập nhật
            $query = "SELECT p.*, GROUP_CONCAT(t.TenTheLoai) as TheLoai 
                     FROM Phim p 
                     LEFT JOIN Phim_TheLoai pt ON p.MaPhim = pt.MaPhim 
                     LEFT JOIN TheLoai t ON pt.MaTheLoai = t.MaTheLoai 
                     GROUP BY p.MaPhim 
                     ORDER BY p.NgayTao DESC 
                     LIMIT 10";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            $new_movies = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Lấy danh sách phim phổ biến (nhiều lượt xem nhất)
            $query = "SELECT p.*, GROUP_CONCAT(t.TenTheLoai) as TheLoai 
                     FROM Phim p 
                     LEFT JOIN Phim_TheLoai pt ON p.MaPhim = pt.MaPhim 
                     LEFT JOIN TheLoai t ON pt.MaTheLoai = t.MaTheLoai 
                     GROUP BY p.MaPhim 
                     ORDER BY p.LuotXem DESC 
                     LIMIT 10";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            $popular_movies = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Lấy danh sách phim trending (dựa trên lượt xem trong 7 ngày gần nhất)
            $query = "SELECT p.*, COUNT(ls.MaPhim) as LuotXemTuan, GROUP_CONCAT(t.TenTheLoai) as TheLoai
                     FROM Phim p 
                     LEFT JOIN LichSu ls ON p.MaPhim = ls.MaPhim
                     LEFT JOIN Phim_TheLoai pt ON p.MaPhim = pt.MaPhim 
                     LEFT JOIN TheLoai t ON pt.MaTheLoai = t.MaTheLoai  
                     WHERE ls.ThoiGian >= DATE_SUB(NOW(), INTERVAL 7 DAY) OR ls.ThoiGian IS NULL
                     GROUP BY p.MaPhim
                     ORDER BY LuotXemTuan DESC
                     LIMIT 10";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            $trending_movies = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            // Xử lý lỗi
            $_SESSION['error'] = 'Có lỗi xảy ra: ' . $e->getMessage();
            $new_movies = [];
            $popular_movies = [];
            $trending_movies = [];
        }
        
        // Hiển thị danh sách sản phẩm
        include 'app/Views/shares/show.php';
    }
    
    public function add()
    {
        // Hiển thị form thêm sản phẩm
        include 'app/Views/product/add.php';
    }

    public function save()
    {
        // Xử lý lưu sản phẩm
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Xử lý lưu sản phẩm
        }
    }

    public function edit()
    {
        // Hiển thị form sửa sản phẩm
        include 'app/Views/product/edit.php';
    }

    public function update()
    {
        // Xử lý cập nhật sản phẩm
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Xử lý cập nhật sản phẩm
        }
    }

    public function delete()
    {
        // Xử lý xóa sản phẩm
    }

   

    private function uploadImage($file)
    {
        $target_dir = "uploads/";
        // Kiểm tra và tạo thư mục nếu chưa tồn tại
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        $target_file = $target_dir . basename($file["name"]);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        // Kiểm tra xem file có phải là hình ảnh không
        $check = getimagesize($file["tmp_name"]);
        if ($check === false) {
            throw new Exception("File không phải là hình ảnh.");
        }
        // Kiểm tra kích thước file (10 MB = 10 * 1024 * 1024 bytes)
        if ($file["size"] > 10 * 1024 * 1024) {
            throw new Exception("Hình ảnh có kích thước quá lớn.");
        }
        // Chỉ cho phép một số định dạng hình ảnh nhất định
        if (
            $imageFileType != "jpg" && $imageFileType != "png" && $imageFileType !=
            "jpeg" && $imageFileType != "gif"
        ) {
            throw new Exception("Chỉ cho phép các định dạng JPG, JPEG, PNG và GIF.");
        }
        // Lưu file
        if (!move_uploaded_file($file["tmp_name"], $target_file)) {
            throw new Exception("Có lỗi xảy ra khi tải lên hình ảnh.");
        }
        return $target_file;
    }
}
