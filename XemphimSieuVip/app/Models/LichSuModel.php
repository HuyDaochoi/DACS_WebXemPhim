<?php
class LichSuModel
{
    private $db;
    public function __construct($db)
    {
        $this->db = $db;
    }

    // Lấy lịch sử xem của user
    public function getHistoryByUser($username)
    {
        $sql = "SELECT ls.*, p.TenPhim, p.HinhAnh, p.NamPhatHanh
                FROM LichSu ls
                JOIN Phim p ON ls.MaPhim = p.MaPhim
                WHERE ls.TenDN = :username
                ORDER BY ls.ThoiGian DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':username' => $username]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Thêm/cập nhật lịch sử xem
    public function addOrUpdateHistory($username, $maPhim)
    {
        $sql = "INSERT INTO LichSu (TenDN, MaPhim, ThoiGian)
                VALUES (:username, :maPhim, NOW())
                ON DUPLICATE KEY UPDATE ThoiGian = NOW()";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':username' => $username, ':maPhim' => $maPhim]);
    }

    // Xóa lịch sử xem của user (toàn bộ hoặc 1 phim)
    public function deleteHistory($username, $maPhim = null)
    {
        if ($maPhim) {
            $sql = "DELETE FROM LichSu WHERE TenDN = :username AND MaPhim = :maPhim";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([':username' => $username, ':maPhim' => $maPhim]);
        } else {
            $sql = "DELETE FROM LichSu WHERE TenDN = :username";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([':username' => $username]);
        }
    }
}
