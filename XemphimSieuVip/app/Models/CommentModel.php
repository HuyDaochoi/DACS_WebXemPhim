<?php
require_once 'app/configs/database.php';

class CommentModel {
    private $db;

    public function __construct(Database $db) {
        $this->db = $db;
    }

    /**
     * Add a new comment
     * @param int $movieId Movie ID
     * @param int $userId User ID
     * @param string $content Comment content
     * @return bool True on success, false on failure
     */
    public function addComment($movieId, $userId, $content) {
        try {
            $query = "INSERT INTO BinhLuan (MaPhim, MaNguoiDung, NoiDung, ThoiGian) 
                      VALUES (:movieId, :userId, :content, NOW())";
            $stmt = $this->db->prepare($query);
            return $stmt->execute([
                ':movieId' => $movieId,
                ':userId' => $userId,
                ':content' => $content
            ]);
        } catch (PDOException $e) {
            error_log("Error adding comment: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get comments for a specific movie
     * @param int $movieId Movie ID
     * @return array Array of comments
     */
    public function getCommentsByMovie($movieId) {
        try {
            $query = "SELECT bl.*, tk.ten_dang_nhap as username 
                      FROM BinhLuan bl 
                      JOIN tai_khoan tk ON bl.MaNguoiDung = tk.id 
                      WHERE bl.MaPhim = :movieId 
                      ORDER BY bl.ThoiGian DESC";
            $stmt = $this->db->prepare($query);
            $stmt->execute([':movieId' => $movieId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching comments: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get paginated comments with search for admin
     * @param int $page Page number
     * @param int $perPage Items per page
     * @param string $search Search term
     * @return array Array containing comments and total count
     */
    public function getCommentsForAdmin($page, $perPage, $search = '') {
        try {
            $offset = ($page - 1) * $perPage;
            $params = [];
            $query = "SELECT bl.*, tk.ten_dang_nhap as username, p.TenPhim 
                      FROM BinhLuan bl 
                      JOIN tai_khoan tk ON bl.MaNguoiDung = tk.id 
                      JOIN Phim p ON bl.MaPhim = p.MaPhim 
                      WHERE 1=1";
            if ($search) {
                $query .= " AND (tk.ten_dang_nhap LIKE :search OR p.TenPhim LIKE :search)";
                $params[':search'] = '%' . $search . '%';
            }
            $query .= " ORDER BY bl.ThoiGian DESC LIMIT :limit OFFSET :offset";
            $stmt = $this->db->prepare($query);
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }
            $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Count total comments
            $countQuery = "SELECT COUNT(*) as total 
                           FROM BinhLuan bl 
                           JOIN tai_khoan tk ON bl.MaNguoiDung = tk.id 
                           JOIN Phim p ON bl.MaPhim = p.MaPhim 
                           WHERE 1=1";
            if ($search) {
                $countQuery .= " AND (tk.ten_dang_nhap LIKE :search OR p.TenPhim LIKE :search)";
            }
            $stmt = $this->db->prepare($countQuery);
            if ($search) {
                $stmt->bindValue(':search', '%' . $search . '%');
            }
            $stmt->execute();
            $total = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

            return ['comments' => $comments, 'total' => $total];
        } catch (PDOException $e) {
            error_log("Error fetching comments for admin: " . $e->getMessage());
            return ['comments' => [], 'total' => 0];
        }
    }

    /**
     * Update a comment
     * @param int $commentId Comment ID
     * @param string $content New content
     * @return bool True on success, false on failure
     */
    public function updateComment($commentId, $content) {
        try {
            $query = "UPDATE BinhLuan SET NoiDung = :content WHERE MaBinhLuan = :commentId";
            $stmt = $this->db->prepare($query);
            return $stmt->execute([':content' => $content, ':commentId' => $commentId]);
        } catch (PDOException $e) {
            error_log("Error updating comment: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Delete a comment
     * @param int $commentId Comment ID
     * @return bool True on success, false on failure
     */
    public function deleteComment($commentId) {
        try {
            $query = "DELETE FROM BinhLuan WHERE MaBinhLuan = :commentId";
            $stmt = $this->db->prepare($query);
            return $stmt->execute([':commentId' => $commentId]);
        } catch (PDOException $e) {
            error_log("Error deleting comment: " . $e->getMessage());
            return false;
        }
    }
}