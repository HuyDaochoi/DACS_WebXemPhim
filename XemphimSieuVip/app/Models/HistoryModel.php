<?php
class HistoryModel {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    // Lấy lịch sử xem phim của người dùng
    public function getUserHistory($user_id) {
        try {
            $query = "SELECT ls.*, p.TenPhim, p.HinhAnh, p.NamPhatHanh 
                     FROM LichSu ls 
                     JOIN Phim p ON ls.MaPhim = p.MaPhim 
                     WHERE ls.MaND = :user_id 
                     ORDER BY ls.NgayXem DESC";
            $stmt = $this->db->prepare($query);
            $stmt->execute([':user_id' => $user_id]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    // Thêm lịch sử xem phim
    public function addHistory($user_id, $movie_id, $episode = null) {
        try {
            // Kiểm tra xem đã có lịch sử xem chưa
            $query = "SELECT * FROM LichSu 
                     WHERE MaND = :user_id AND MaPhim = :movie_id";
            if ($episode) {
                $query .= " AND TapPhim = :episode";
            }
            $stmt = $this->db->prepare($query);
            $params = [
                ':user_id' => $user_id,
                ':movie_id' => $movie_id
            ];
            if ($episode) {
                $params[':episode'] = $episode;
            }
            $stmt->execute($params);

            if ($stmt->rowCount() > 0) {
                // Nếu đã có thì cập nhật thời gian xem
                $query = "UPDATE LichSu 
                         SET NgayXem = NOW() 
                         WHERE MaND = :user_id AND MaPhim = :movie_id";
                if ($episode) {
                    $query .= " AND TapPhim = :episode";
                }
            } else {
                // Nếu chưa có thì thêm mới
                $query = "INSERT INTO LichSu (MaND, MaPhim, TapPhim, NgayXem) 
                         VALUES (:user_id, :movie_id, :episode, NOW())";
            }

            $stmt = $this->db->prepare($query);
            $stmt->execute($params);

            return [
                'success' => true,
                'message' => 'Cập nhật lịch sử xem thành công!'
            ];
        } catch (PDOException $e) {
            return [
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ];
        }
    }

    // Xóa lịch sử xem phim
    public function deleteHistory($user_id, $movie_id = null) {
        try {
            if ($movie_id) {
                // Xóa lịch sử xem của một phim cụ thể
                $query = "DELETE FROM LichSu WHERE MaND = :user_id AND MaPhim = :movie_id";
                $params = [
                    ':user_id' => $user_id,
                    ':movie_id' => $movie_id
                ];
            } else {
                // Xóa toàn bộ lịch sử xem
                $query = "DELETE FROM LichSu WHERE MaND = :user_id";
                $params = [':user_id' => $user_id];
            }

            $stmt = $this->db->prepare($query);
            $stmt->execute($params);

            return [
                'success' => true,
                'message' => 'Xóa lịch sử xem thành công!'
            ];
        } catch (PDOException $e) {
            return [
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ];
        }
    }
} 