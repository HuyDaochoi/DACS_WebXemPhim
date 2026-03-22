<?php


class AccountModel
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    /* Lấy thông tin tài khoản theo tên đăng nhập
   public function getAccountByUsername($username)
    {
        try {
            $query = "SELECT * FROM Tai_Khoan WHERE ten_dang_nhap = :username";
            $stmt = $this->db->prepare($query);
            $stmt->execute([':username' => $username]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return null;
        }
    }*/

    // Đăng ký tài khoản mới
    public function register($username, $password, $email, $fullname)
    {
        try {
            // Kiểm tra username đã tồn tại chưa
            $query = "SELECT COUNT(*) FROM tai_khoan WHERE ten_dang_nhap = :username";
            $stmt = $this->db->prepare($query);
            $stmt->execute([':username' => $username]);
            if ($stmt->fetchColumn() > 0) {
                return [
                    'success' => false,

                ];
            }

            // Kiểm tra email đã tồn tại chưa
            $query = "SELECT COUNT(*) FROM tai_khoan WHERE email = :email";
            $stmt = $this->db->prepare($query);
            $stmt->execute([':email' => $email]);
            if ($stmt->fetchColumn() > 0) {
                return [
                    'success' => false,
                    'message' => 'Email đã tồn tại!'
                ];
            }

            // Thêm người dùng mới
            $query = "INSERT INTO tai_khoan (ten_dang_nhap, mat_khau, email, ho_ten, vai_tro, ngay_tao) 
                     VALUES (:username, :password, :email, :fullname, 'user', NOW())";
            $stmt = $this->db->prepare($query);
            $stmt->execute([
                ':username' => $username,
                ':password' => password_hash($password, PASSWORD_DEFAULT),
                ':email' => $email,
                ':fullname' => $fullname
            ]);

            return [
                'success' => true,
                'message' => 'Đăng ký thành công!'
            ];
        } catch (PDOException $e) {
            return [
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ];
        }
    }

    // Đăng nhập
    public function login($username, $password)
    {
        try {
            $query = "SELECT * FROM tai_khoan WHERE ten_dang_nhap = :username LIMIT 1";
            $stmt = $this->db->prepare($query);
            $stmt->execute([':username' => $username]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$user) {
                return [
                    'success' => false,
                    'message' => 'Tên đăng nhập không tồn tại!'
                ];
            }

            if (!password_verify($password, $user['mat_khau'])) {
                return [
                    'success' => false,
                    'message' => 'Mật khẩu không chính xác!'
                ];
            }

            // Lưu thông tin người dùng vào session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['ten_dang_nhap'];
            $_SESSION['fullname'] = $user['ho_ten'];
            $_SESSION['vai_tro'] = $user['vai_tro'];
            $_SESSION['hinh_dai_dien'] = $user['hinh_dai_dien'];
            return [
                'success' => true,
                'message' => 'Đăng nhập thành công!',
                'role' => $user['vai_tro'],
                'user' => $userFromDatabase
            ];
        } catch (PDOException $e) {
            return [
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ];
        }
    }

    // Đăng xuất
    public function logout()
    {
        try {
            session_destroy();
            return [
                'success' => true,
                'message' => 'Đăng xuất thành công!'
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ];
        }
    }

    // Kiểm tra đăng nhập
    public function isLoggedIn()
    {
        return isset($_SESSION['user_id']);
    }

    // Kiểm tra quyền admin
    public function isAdmin()
    {
        return isset($_SESSION['vai_tro']) && $_SESSION['vai_tro'] === 'admin';
    }

    // Kiểm tra quyền truy cập
    public function checkPermission($requiredRole = 'admin')
    {
        if (!$this->isLoggedIn()) {
            return ['success' => false, 'message' => 'Vui lòng đăng nhập để tiếp tục'];
        }

        if ($_SESSION['vai_tro'] !== $requiredRole) {
            return ['success' => false, 'message' => 'Bạn không có quyền truy cập trang này'];
        }

        return ['success' => true];
    }

    // Lấy thông tin người dùng
    public function getUserInfo($user_id)
    {
        try {
            $query = "SELECT id, ten_dang_nhap, email, ho_ten, ngay_tao, vai_tro 
                     FROM tai_khoan WHERE id = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(":id", $user_id);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return false;
        }
    }

    // Cập nhật thông tin người dùng
    public function updateUserInfo($user_id, $email, $ho_ten)
    {
        try {
            $query = "UPDATE tai_khoan 
                     SET email = :email, ho_ten = :ho_ten 
                     WHERE id = :id";

            $stmt = $this->db->prepare($query);


            // Bind các tham số
            $stmt->bindParam(":email", $email);
            $stmt->bindParam(":ho_ten", $ho_ten);
            $stmt->bindParam(":id", $user_id);

            if ($stmt->execute()) {
                return ['success' => true, 'message' => 'Cập nhật thành công'];
            }
            return ['success' => false, 'message' => 'Cập nhật thất bại'];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()];
        }
    }







    public function updateAccount($id, $username, $email, $role)
    {
        try {
            // Danh sách vai trò hợp lệ
            $allowedRoles = ['user', 'admin', 'moderator'];
            if (!in_array($role, $allowedRoles)) {
                $role = 'user';
            }

            // Kiểm tra trùng email với tài khoản khác
            $checkQuery = "SELECT id FROM Tai_Khoan WHERE email = :email AND id != :id";
            $checkStmt = $this->db->prepare($checkQuery);
            $checkStmt->execute([
                ':email' => $email,
                ':id' => $id
            ]);

            if ($checkStmt->fetch()) {
                return [
                    'success' => false,
                    'message' => 'Email đã được sử dụng bởi tài khoản khác!'
                ];
            }

            // Tiến hành cập nhật
            $query = "UPDATE Tai_Khoan
                    SET ten_dang_nhap = :username, Email = :email, vai_tro = :role 
                    WHERE id = :id";
            $stmt = $this->db->prepare($query);
            $stmt->execute([
                ':id'       => $id,
                ':username' => $username,
                ':email'    => $email,
                ':role'     => $role
            ]);

            return [
                'success' => true,
                'message' => 'Cập nhật tài khoản thành công!'
            ];
        } catch (PDOException $e) {
            return [
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ];
        }
    }

    // Xóa tài khoản
    public function deleteAccount($id)
    {
        try {
            $query = "DELETE FROM Tai_Khoan WHERE id = :id";
            $stmt = $this->db->prepare($query);
            $stmt->execute([':id' => $id]);

            return [
                'success' => true,
                'message' => 'Xóa tài khoản thành công!'
            ];
        } catch (PDOException $e) {
            return [
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ];
        }
    }

    public function createAccount($username, $password, $email, $role = 'user')
    {
        try {
            $query = "INSERT INTO Tai_Khoan (ten_dang_nhap, mat_khau, email, vai_tro)
                    VALUES (:username, :password, :email, :role)";
            $stmt = $this->db->prepare($query);
            $stmt->execute([
                ':username' => $username,
                ':password' => password_hash($password, PASSWORD_DEFAULT),
                ':email'    => $email,
                ':role'     => $role
            ]);
            return ['success' => true];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Có lỗi xảy ra: ' . $e->getMessage()];
        }
    }

    public function getUserById($id)
    {
        $sql = "SELECT * FROM tai_khoan WHERE id = :id LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updatePasswordById($userId, $hashedPassword)
    {
        if (empty($userId) || empty($hashedPassword)) {
            return false;
        }

        $stmt = $this->db->prepare("UPDATE tai_khoan SET mat_khau = :mat_khau WHERE id = :id");
        $stmt->bindParam(':mat_khau', $hashedPassword, PDO::PARAM_STR);
        $stmt->bindParam(':id', $userId, PDO::PARAM_INT);

        return $stmt->execute();
    }

    public function updateAvatar($userId, $avatarPath)
    {
        $stmt = $this->db->prepare("UPDATE tai_khoan SET hinh_dai_dien = ? WHERE id = ?");
        return $stmt->execute([$avatarPath, $userId]);
        $_SESSION['hinh_dai_dien'] =  $filename;
    }
}
