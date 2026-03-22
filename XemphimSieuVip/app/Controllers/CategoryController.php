<?php
require_once __DIR__ . '/../Models/CategoryModel.php';
require_once __DIR__ . '/../configs/database.php';

class CategoryController
{
    private $db;
    private $categoryModel;

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $this->db = (new Database())->getConnection();
        $this->categoryModel = new CategoryModel($this->db);
    }

    // Hiển thị form + danh sách thể loại
    public function themtheloaimoi()
    {
        $genres = $this->categoryModel->getAllCategories();
        require_once 'app/Views/admin/genres/themtheloaimoi.php';
    }

    // Thêm thể loại mới
    public function storeTheLoai()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim($_POST['name']);

            // Kiểm tra trùng tên
            $existingGenre = $this->categoryModel->getCategoryById($id);
            if ($existingGenre) {
                $_SESSION['error'] = 'Tên thể loại đã tồn tại!';
                header('Location: /category/themtheloaimoi');
                exit;
            }

            $result = $this->categoryModel->addCategory($name, '');
            if ($result['success']) {
                $_SESSION['success'] = $result['message'];
            } else {
                $_SESSION['error'] = $result['message'];
            }

            header('Location: /category/themtheloaimoi');
            exit;
        }
    }

    // Cập nhật thể loại
    public function updateTheLoai()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'];
            $name = trim($_POST['name']);

            $result = $this->categoryModel->updateCategory($id, $name, '');
            if ($result['success']) {
                $_SESSION['success'] = $result['message'];
            } else {
                $_SESSION['error'] = $result['message'];
            }

            header('Location: /category/themtheloaimoi');
            exit;
        }
    }

    // Xóa thể loại
    public function deleteTheLoai($id)
    {
        $result = $this->categoryModel->deleteCategory($id);

        if ($result['success']) {
            $_SESSION['success'] = $result['message'];
        } else {
            $_SESSION['error'] = $result['message'];
        }

        header('Location: /category/themtheloaimoi');
        exit;
    }

     //QUỐC GIA

     // Hiển thị form + danh sách thể loại
     public function themquocgiamoi()
     {
         $genres = $this->categoryModel->getAllCountry();
         require_once 'app/Views/admin/countries/themquocgiamoi.php';
     }
 
     // Thêm thể loại mới
     public function storeQuocGia()
     {
         if ($_SERVER['REQUEST_METHOD'] === 'POST') {
             $name = trim($_POST['name']);
 
             // Kiểm tra trùng tên
             $existingGenre = $this->categoryModel->getCountryId($id);
             if ($existingGenre) {
                 $_SESSION['error'] = 'Tên thể loại đã tồn tại!';
                 header('Location: /category/themquocgiamoi');
                 exit;
             }
 
             $result = $this->categoryModel->addCountry($name, '');
             if ($result['success']) {
                 $_SESSION['success'] = $result['message'];
             } else {
                 $_SESSION['error'] = $result['message'];
             }
 
             header('Location: /category/themquocgiamoi');
             exit;
         }
     }
 
     // Cập nhật thể loại
     public function updateQuocGia()
     {
         if ($_SERVER['REQUEST_METHOD'] === 'POST') {
             $id = $_POST['id'];
             $name = trim($_POST['name']);
 
             $result = $this->categoryModel->updateCountry($id, $name, '');
             if ($result['success']) {
                 $_SESSION['success'] = $result['message'];
             } else {
                 $_SESSION['error'] = $result['message'];
             }
 
             header('Location: /category/themquocgiamoi');
             exit;
         }
     }
 
     // Xóa thể loại
     public function deleteQuocGia($id)
     {
         $result = $this->categoryModel->deleteCountry($id);
 
         if ($result['success']) {
             $_SESSION['success'] = $result['message'];
         } else {
             $_SESSION['error'] = $result['message'];
         }
 
         header('Location: /category/themquocgiamoi');
         exit;
     }

     public function manage_account()
    {
        $accounts = $this->categoryModel->getAllAccounts();
        require_once 'app/Views/admin/manage_account.php';
    }

    
   

}