<?php
class EditPhimboModel
{
    private $db;
    public function __construct($db)
    {
        $this->db = $db;
    }

    public function getEpisodeById($tapId)
    {
        $sql = "SELECT * FROM TapPhim WHERE MaTap = :tapId LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':tapId' => $tapId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function deleteEpisode($tapId)
    {
        try {
            $sql = "DELETE FROM TapPhim WHERE MaTap = :tapId";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':tapId' => $tapId]);
            return ['success' => true, 'message' => 'Xóa tập phim thành công!'];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Lỗi khi xóa tập phim: ' . $e->getMessage()];
        }
    }

    public function updateEpisode($tapId, $tenTap, $link)
    {
        try {
            $sql = "UPDATE TapPhim SET TenTap = :tenTap, Link = :link WHERE MaTap = :tapId";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':tenTap' => $tenTap,
                ':link' => $link,
                ':tapId' => $tapId
            ]);
            return ['success' => true, 'message' => 'Cập nhật tập phim thành công!'];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Lỗi khi cập nhật tập phim: ' . $e->getMessage()];
        }
    }

    // Bạn có thể thêm các hàm thêm/sửa tập phim tại đây
}