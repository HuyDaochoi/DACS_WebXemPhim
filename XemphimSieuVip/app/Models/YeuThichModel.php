<?php
class YeuThichModel
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    // Kiểm tra xem phim đã được yêu thích chưa
    public function isFavorite($movieId, $userId)
    {
        $stmt = $this->db->prepare("SELECT 1 FROM phim_yeuthich WHERE MaPhim = ? AND UserID = ?");
        $stmt->execute([$movieId, $userId]);
        return $stmt->fetch() !== false;
    }

    // Thêm phim vào danh sách yêu thích
    public function addFavorite($movieId, $userId)
    {
        $stmt = $this->db->prepare("INSERT INTO phim_yeuthich (MaPhim, UserID) VALUES (?, ?)");
        return $stmt->execute([$movieId, $userId]);
    }

    // Xóa phim khỏi danh sách yêu thích
    public function removeFavorite($movieId, $userId)
    {
        $stmt = $this->db->prepare("DELETE FROM phim_yeuthich WHERE MaPhim = ? AND UserID = ?");
        return $stmt->execute([$movieId, $userId]);
    }

    // Lấy danh sách phim yêu thích của người dùng
    public function getFavoritesByUser($userId)
    {
        $stmt = $this->db->prepare("SELECT p.* FROM Phim p 
            JOIN phim_yeuthich f ON p.MaPhim = f.MaPhim
            WHERE f.UserID = ?");
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
