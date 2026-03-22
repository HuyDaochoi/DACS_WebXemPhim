<?php
require_once __DIR__ . '/../configs/database.php';

class WatchModel
{
    private $conn;

    public function __construct()
    {
        $this->conn = new Database();
    }

    public function getMovieDetails($id)
    {
        $sql = "SELECT p.*, q.TenQuocGia 
                FROM Phim p
                LEFT JOIN QuocGia q ON p.MaQuocGia = q.MaQuocGia
                WHERE p.MaPhim = :id";

        $stmt = $this->conn->getConnection()->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }
        return null;
    }

    public function incrementViewCount($id)
    {
        $sql = "UPDATE Phim SET LuotXem = LuotXem + 1 WHERE MaPhim = :id";
        $stmt = $this->conn->getConnection()->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
    }

    // Lấy danh sách tập phim theo id phim
    public function getEpisodes($movieId)
    {
        $sql = "SELECT * FROM TapPhim WHERE MaPhim = :movieId ORDER BY MaTap ASC";
        $stmt = $this->conn->getConnection()->prepare($sql);
        $stmt->bindParam(':movieId', $movieId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Lấy id phim từ id tập phim
    public function getMovieIdByEpisodeId($tapId)
    {
        $sql = "SELECT MaPhim FROM TapPhim WHERE MaTap = :tapId LIMIT 1";
        $stmt = $this->conn->getConnection()->prepare($sql);
        $stmt->bindParam(':tapId', $tapId, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? $row['MaPhim'] : null;
    }

    public function getEpisodeById($tapId)
    {
        $sql = "SELECT * FROM TapPhim WHERE MaTap = :tapId LIMIT 1";
        $stmt = $this->conn->getConnection()->prepare($sql);
        $stmt->bindParam(':tapId', $tapId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
