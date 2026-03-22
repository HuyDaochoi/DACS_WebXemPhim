<?php
require_once 'app/Models/AccountModel.php';
require_once 'app/configs/database.php';

class AccountController
{
    private $accountModel;
    private $db;

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $this->db = new Database();
        $this->accountModel = new AccountModel($this->db);
    }

    // Hiển thị trang đăng nhập
    public function login()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            require_once 'app/Views/accounts/login.php';
        } else {
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';
            $_SESSION['hinh_dai_dien'] = $user['hinh_dai_dien'];
            $result = $this->accountModel->login($username, $password);
            if ($result['success']) {
                $_SESSION['success'] = $result['message'];
                header('Location: /');
            } else {
                $_SESSION['error'] = $result['message'];
                header('Location: /account/login');
            }
            exit;
        }
    }

    // Hiển thị trang đăng ký
    public function register()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            require_once 'app/Views/accounts/register.php';
        } else {
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';
            $confirm_password = $_POST['confirm_password'] ?? '';
            $email = $_POST['email'] ?? '';
            $name = $_POST['name'] ?? '';

            // Kiểm tra mật khẩu và xác nhận mật khẩu
            if ($password !== $confirm_password) {
                $_SESSION['error'] = 'Mật khẩu và xác nhận mật khẩu không khớp';
                header('Location: /account/register');
                exit;
            }

            // Kiểm tra độ dài tên đăng nhập
            if (strlen($username) < 4 || strlen($username) > 20) {
                $_SESSION['error'] = 'Tên đăng nhập phải từ 4-20 ký tự';
                header('Location: /account/register');
                exit;
            }

            // Kiểm tra định dạng tên đăng nhập (chỉ chấp nhận chữ cái, số và dấu gạch dưới)
            if (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
                $_SESSION['error'] = 'Tên đăng nhập chỉ được chứa chữ cái, số và dấu gạch dưới';
                header('Location: /account/register');
                exit;
            }

            $result = $this->accountModel->register($username, $password, $email, $name);
            if ($result['success']) {
                $_SESSION['success'] = $result['message'];
                header('Location: /account/login');
            } else {
                $_SESSION['error'] = $result['message'];
                header('Location: /account/register');
            }
            exit;
        }
    }

    // Đăng xuất
    public function logout()
    {
        session_destroy();
        header('Location: /');
        exit;
    }





    // Hiển thị trang thông tin tài khoản
    public function profile()
    {
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error'] = 'Vui lòng đăng nhập để xem thông tin tài khoản!';
            header('Location: /account/login');
            exit;
        }

        $user = $this->accountModel->getUserById($_SESSION['user_id']);
        require_once 'app/Views/User/profile.php';
    }
    public function editProfile()
    {
        if (!isset($_SESSION['user_id'])) {
            header("Location: /account/editProfile");
            exit();
        }

        $userId = $_SESSION['user_id'];
        $userInfo = $this->accountModel->getUserInfo($userId);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'] ?? '';
            $fullname = $_POST['ho_ten'] ?? '';

            $result = $this->accountModel->updateUserInfo($userId, $email, $fullname);

            if ($result['success']) {
                $_SESSION['fullname'] = $fullname;
                $successMessage = $result['message'];
                $userInfo = $this->accountModel->getUserInfo($userId); // Load lại thông tin mới
            } else {
                $errorMessage = $result['message'];
            }
        }

        require_once 'app/Views/User/profile.php';
    }


    // Cập nhật thông tin tài khoản
    public function update()
    {
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error'] = 'Vui lòng đăng nhập để cập nhật thông tin tài khoản!';
            header('Location: /account/login');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'] ?? $_SESSION['user_id'];
            $username = $_POST['username'] ?? '';
            $email = $_POST['email'] ?? '';
            $role = $_POST['role'] ?? 'user';
            $current_password = $_POST['current_password'] ?? '';
            $new_password = $_POST['new_password'] ?? '';
            $confirm_password = $_POST['confirm_password'] ?? '';

            // Gọi model để xử lý cập nhật
            $result = $this->accountModel->updateAccount(
                $id,
                $username,
                $email,
                $role,
                $current_password,
                $new_password,
                $confirm_password
            );

            if ($result['success']) {
                $_SESSION['success'] = $result['message'];
            } else {
                $_SESSION['error'] = $result['message'];
            }

            header('Location: /category/manage_account');
            exit;
        }
    }



    // Hiển thị trang quản lý tài khoản (chỉ admin)
    public function manage()
    {
        if (!isset($_SESSION['vai_tro']) || $_SESSION['vai_tro'] !== 'admin') {
            $_SESSION['error'] = 'Bạn không có quyền truy cập trang này!';
            header('Location: /');
            exit;
        }

        $users = $this->accountModel->getAllUsers();
        require_once 'app/Views/accounts/manage.php';
    }

    // Xóa tài khoản (chỉ admin)
    public function delete($id)
    {
        if (!isset($_SESSION['vai_tro']) || $_SESSION['vai_tro'] !== 'admin') {
            $_SESSION['error'] = 'Bạn không có quyền truy cập trang này!';
            header('Location: /');
            exit;
        }

        $result = $this->accountModel->deleteAccount($id);
        if ($result['success']) {
            $_SESSION['success'] = $result['message'];
        } else {
            $_SESSION['error'] = $result['message'];
        }
        header('Location: /category/manage_account');
        exit;
    }

    // Cập nhật vai trò tài khoản (chỉ admin)
    public function updateRole($id)
    {
        if (!isset($_SESSION['vai_tro']) || $_SESSION['vai_tro'] !== 'admin') {
            $_SESSION['error'] = 'Bạn không có quyền truy cập trang này!';
            header('Location: /');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $role = $_POST['role'] ?? '';

            $result = $this->accountModel->updateUserRole($id, $role);
            if ($result['success']) {
                $_SESSION['success'] = $result['message'];
            } else {
                $_SESSION['error'] = $result['message'];
            }
            header('Location: /account/manage');
            exit;
        }
    }

    // Thêm tài khoản mới o admin 
    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';
            $email    = $_POST['email'] ?? '';
            $role     = $_POST['role'] ?? 'user';

            if (empty($username) || empty($password)) {
                $_SESSION['error'] = 'Tên đăng nhập và mật khẩu là bắt buộc.';
                header('Location: /category/manage_account');
                exit;
            }

            $result = $this->accountModel->createAccount($username, $password, $email, $role);

            if ($result['success']) {
                $_SESSION['success'] = 'Tạo tài khoản thành công!';
            } else {
                $_SESSION['error'] = $result['message'];
            }

            header('Location: /category/manage_account');
            exit;
        }
    }




    // Hiển thị form đổi mật khẩu
    public function changePassword()
    {
        if (!$this->accountModel->isLoggedIn()) {
            $_SESSION['error'] = 'Vui lòng đăng nhập để đổi mật khẩu';
            header('Location: /account/login');
            exit;
        }
        require_once 'app/Views/User/profile.php';
    }

    // Xử lý đổi mật khẩu
    public function updatePassword()
    {
        if (!isset($_SESSION['user_id'])) {
            header("Location: /account/login");
            exit();
        }

        $userId = $_SESSION['user_id'];
        $user = $this->accountModel->getUserById($userId);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $oldPassword = $_POST['old_password'] ?? '';
            $newPassword = $_POST['new_password'] ?? '';
            $confirmPassword = $_POST['confirm_password'] ?? '';

            // Kiểm tra xác nhận mật khẩu
            if ($newPassword !== $confirmPassword) {
                $errorMessage = "🔁 Mật khẩu xác nhận không khớp.";
            }
            // Kiểm tra mật khẩu cũ có đúng không
            elseif (!password_verify($oldPassword, $user['mat_khau'])) {
                $errorMessage = "❌ Mật khẩu cũ không đúng.";
            }
            // Kiểm tra mật khẩu mới có trùng mật khẩu cũ không
            elseif (password_verify($newPassword, $user['mat_khau'])) {
                $errorMessage = "⚠️ Mật khẩu mới không được trùng với mật khẩu cũ.";
            } else {
                $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
                $success = $this->accountModel->updatePasswordById($userId, $hashedPassword);

                if ($success) {
                    $successMessage = "✅ Đổi mật khẩu thành công.";
                } else {
                    $errorMessage = "❌ Đổi mật khẩu thất bại.";
                }
            }
        }

        require_once 'app/Views/User/profile.php';
    }

    public function updateAvatar()
    {
        if (!isset($_SESSION['user_id'])) {
            header("Location: /account/login");
            exit();
        }

        $userId = $_SESSION['user_id'];

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['avatar'])) {
            $file = $_FILES['avatar'];

            // Kiểm tra lỗi upload
            if ($file['error'] !== UPLOAD_ERR_OK) {
                $_SESSION['error'] = "Tải ảnh thất bại.";
                header("Location: /account/profile");
                exit();
            }

            // Kiểm tra định dạng file hợp lệ
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
            if (!in_array($file['type'], $allowedTypes)) {
                $_SESSION['error'] = "Chỉ cho phép ảnh JPG, PNG hoặc GIF.";
                header("Location: /account/profile");
                exit();
            }

            // Làm sạch tên file
            $filename = preg_replace("/[^A-Za-z0-9_\-\.]/", '_', basename($file['name']));
            $uploadDir = __DIR__ . '/../../uploads/';  // KHÔNG nằm trong public/
            $relativePath = 'uploads/' . $filename;    // Đường dẫn tương đối dùng để lưu trong DB
            $targetPath = $uploadDir . $filename;

            // Tạo thư mục nếu chưa có
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            // Nếu file đã tồn tại -> thêm timestamp
            if (file_exists($targetPath)) {
                $filename = time() . '_' . $filename;
                $relativePath = 'uploads/' . $filename;
                $targetPath = $uploadDir . $filename;
            }

            // Di chuyển file từ temp vào uploads
            if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
                $_SESSION['error'] = "Không thể lưu ảnh.";
                header("Location: /account/profile");
                exit();
            }

            // Lưu vào DB và session
            $this->accountModel->updateAvatar($userId, $relativePath);
            $_SESSION['hinh_dai_dien'] = $relativePath;
            $_SESSION['success'] = "Cập nhật ảnh đại diện thành công.";
        }

        header("Location: /account/profile");
        exit();
    }


    // Các phương thức quản lý phim (chỉ dành cho admin)
    public function movieManagement()
    {
        // Logic quản lý phim
        include 'app/Views/movies/list.php';
    }

    public function addMovie()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            include 'app/Views/movies/add.php';
        } else {
            // Xử lý thêm phim
        }
    }

    public function editMovie($id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            include 'app/Views/movies/edit.php';
        } else {
            // Xử lý sửa phim
        }
    }

    public function deleteMovie($id)
    {
        // Xử lý xóa phim
    }
}
