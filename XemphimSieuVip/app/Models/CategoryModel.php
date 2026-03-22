<?php

class CategoryModel
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    // Lấy tất cả thể loại
    public function getAllCategories()
    {
        try {
            $query = "SELECT * FROM TheLoai ORDER BY MaTheLoai ASC";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    // Lấy thể loại theo ID
    public function getCategoryById($id)
    {
        try {
            $query = "SELECT * FROM TheLoai WHERE MaTheLoai = :id";
            $stmt = $this->db->prepare($query);
            $stmt->execute([':id' => $id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return null;
        }
    }

    // Thêm thể loại mới
    public function addCategory($name)
    {
        try {
            // Kiểm tra thể loại đã tồn tại chưa
            $checkQuery = "SELECT COUNT(*) FROM TheLoai WHERE TenTheLoai = :name";
            $checkStmt = $this->db->prepare($checkQuery);
            $checkStmt->execute([':name' => $name]);
            $count = $checkStmt->fetchColumn();
    
            if ($count > 0) {
                return [
                    'success' => false,
                    'message' => 'Thể loại đã tồn tại!'
                ];
            }
    
            // Thêm mới nếu chưa tồn tại
            $query = "INSERT INTO TheLoai (TenTheLoai) VALUES (:name)";
            $stmt = $this->db->prepare($query);
            $stmt->execute([':name' => $name]);
    
            return [
                'success' => true,
                'message' => 'Thêm Thể Loại thành công!'
            ];
        } catch (PDOException $e) {
            return [
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ];
        }
    }

    // Cập nhật thể loại
    public function updateCategory($id, $name)
    {
        try {
            $query = "UPDATE TheLoai SET TenTheLoai = :name WHERE MaTheLoai = :id";
            $stmt = $this->db->prepare($query);
            $stmt->execute([
                ':id' => $id,
                ':name' => $name
            ]);

            return [
                'success' => true,
                'message' => 'Cập nhật thể loại thành công!'
            ];
        } catch (PDOException $e) {
            return [
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ];
        }
    }

    // Xóa thể loại
    public function deleteCategory($id)
    {
        try {
            $this->db->beginTransaction();

            $query = "DELETE FROM Phim_TheLoai WHERE MaTheLoai = :id";
            $stmt = $this->db->prepare($query);
            $stmt->execute([':id' => $id]);

            $query = "DELETE FROM TheLoai WHERE MaTheLoai = :id";
            $stmt = $this->db->prepare($query);
            $stmt->execute([':id' => $id]);

            $this->db->commit();
            return [
                'success' => true,
                'message' => 'Xóa thể loại thành công!'
            ];
        } catch (PDOException $e) {
            $this->db->rollBack();
            return [
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ];
        }
    }


    //Quốc Gia

    public function getAllCountry()
    {
        try {         
            $query = "SELECT * FROM QuocGia ORDER BY MaQuocGia ASC";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    // Lấy thể loại theo ID
    public function getCountryId($id)
    {
        try {
            $query = "SELECT * FROM QuocGia WHERE MaQuocGia = :id";
            $stmt = $this->db->prepare($query);
            $stmt->execute([':id' => $id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return null;
        }
    }

    // Thêm thể loại mới
    public function addCountry($name)
    {
        try {
            // Kiểm tra quốc gia đã tồn tại chưa
            $checkQuery = "SELECT COUNT(*) FROM QuocGia WHERE TenQuocGia = :name";
            $checkStmt = $this->db->prepare($checkQuery);
            $checkStmt->execute([':name' => $name]);
            $count = $checkStmt->fetchColumn();
    
            if ($count > 0) {
                return [
                    'success' => false,
                    'message' => 'Quốc gia đã tồn tại!'
                ];
            }
    
            // Thêm mới nếu chưa tồn tại
            $query = "INSERT INTO QuocGia (TenQuocGia) VALUES (:name)";
            $stmt = $this->db->prepare($query);
            $stmt->execute([':name' => $name]);
    
            return [
                'success' => true,
                'message' => 'Thêm Quốc Gia thành công!'
            ];
        } catch (PDOException $e) {
            return [
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ];
        }
    }

    // Cập nhật thể loại
    public function updateCountry($id, $name)
    {
        try {
            $query = "UPDATE QuocGia SET TenQuocGia = :name WHERE MaQuocGia = :id";
            $stmt = $this->db->prepare($query);
            $stmt->execute([
                ':id' => $id,
                ':name' => $name
            ]);

            return [
                'success' => true,
                'message' => 'Cập nhật quốc gia thành công!'
            ];
        } catch (PDOException $e) {
            return [
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ];
        }
    }

    // Xóa thể loại
    public function deleteCountry($id)
    {
        try {
            $this->db->beginTransaction();
        
            $query = "DELETE FROM QuocGia WHERE MaQuocGia = :id";
            $stmt = $this->db->prepare($query);
            $stmt->execute([':id' => $id]);

            $this->db->commit();
            return [
                'success' => true,
                'message' => 'Xóa quốc gia thành công!'
            ];
        } catch (PDOException $e) {
            $this->db->rollBack();
            return [
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ];
        }
    }
    

    // TÀI KHOẢN
    public function getAllAccounts()
    {
        try {
            $query = "SELECT * FROM Tai_Khoan ORDER BY id ASC";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    
    public function getUserById($id)
    {
        $sql = "SELECT * FROM tai_khoan WHERE id = :id LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    

}
