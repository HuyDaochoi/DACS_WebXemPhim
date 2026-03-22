<?php

class SearchModel {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    // Lấy phim theo thể loại
    public function searchMoviesByGenre($genreId, $keyword = '', $country = '', $page = 1, $perPage = 12) {
        $offset = ($page - 1) * $perPage;
        $params = [':genreId' => $genreId];
        $where = "pt.MaTheLoai = :genreId";

        if ($keyword) {
            $where .= " AND p.TenPhim LIKE :keyword";
            $params[':keyword'] = '%' . $keyword . '%';
        }
        if ($country) {
            $where .= " AND p.MaQuocGia = :country";
            $params[':country'] = $country;
        }

        $query = "SELECT p.*, GROUP_CONCAT(t.TenTheLoai) as TheLoai, qg.TenQuocGia
                  FROM Phim p
                  LEFT JOIN Phim_TheLoai pt ON p.MaPhim = pt.MaPhim
                  LEFT JOIN TheLoai t ON pt.MaTheLoai = t.MaTheLoai
                  LEFT JOIN QuocGia qg ON p.MaQuocGia = qg.MaQuocGia
                  WHERE $where
                  GROUP BY p.MaPhim
                  ORDER BY p.NgayTao DESC
                  LIMIT $offset, $perPage";
        $stmt = $this->db->prepare($query);
        $stmt->execute($params);
        $movies = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Đếm tổng số phim
        $countQuery = "SELECT COUNT(DISTINCT p.MaPhim) as total
                       FROM Phim p
                       LEFT JOIN Phim_TheLoai pt ON p.MaPhim = pt.MaPhim
                       WHERE $where";
        $stmt = $this->db->prepare($countQuery);
        $stmt->execute($params);
        $total = $stmt->fetchColumn();

        return ['movies' => $movies, 'total' => $total];
    }

    // Lấy phim theo quốc gia
    public function searchMoviesByCountry($countryId, $keyword = '', $genre = '', $page = 1, $perPage = 12) {
        $offset = ($page - 1) * $perPage;
        $params = [':countryId' => $countryId];
        $where = "p.MaQuocGia = :countryId";

        if ($keyword) {
            $where .= " AND p.TenPhim LIKE :keyword";
            $params[':keyword'] = '%' . $keyword . '%';
        }
        if ($genre) {
            $where .= " AND pt.MaTheLoai = :genre";
            $params[':genre'] = $genre;
        }

        $query = "SELECT p.*, GROUP_CONCAT(t.TenTheLoai) as TheLoai, qg.TenQuocGia
                  FROM Phim p
                  LEFT JOIN Phim_TheLoai pt ON p.MaPhim = pt.MaPhim
                  LEFT JOIN TheLoai t ON pt.MaTheLoai = t.MaTheLoai
                  LEFT JOIN QuocGia qg ON p.MaQuocGia = qg.MaQuocGia
                  WHERE $where
                  GROUP BY p.MaPhim
                  ORDER BY p.NgayTao DESC
                  LIMIT $offset, $perPage";
        $stmt = $this->db->prepare($query);
        $stmt->execute($params);
        $movies = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Đếm tổng số phim
        $countQuery = "SELECT COUNT(DISTINCT p.MaPhim) as total
                       FROM Phim p
                       LEFT JOIN Phim_TheLoai pt ON p.MaPhim = pt.MaPhim
                       WHERE $where";
        $stmt = $this->db->prepare($countQuery);
        $stmt->execute($params);
        $total = $stmt->fetchColumn();

        return ['movies' => $movies, 'total' => $total];
    }

    // Lấy thông tin thể loại
    public function getGenreById($id) {
        $stmt = $this->db->prepare("SELECT * FROM TheLoai WHERE MaTheLoai = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Lấy thông tin quốc gia
    public function getCountryById($id) {
        $stmt = $this->db->prepare("SELECT * FROM QuocGia WHERE MaQuocGia = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function filterMovies($genreIds = [], $countryId = '')
    {
        $params = [];
        $where = [];

        if (!empty($genreIds)) {
            $in = implode(',', array_fill(0, count($genreIds), '?'));
            $where[] = "pt.MaTheLoai IN ($in)";
            $params = array_merge($params, $genreIds);
        }
        if (!empty($countryId)) {
            $where[] = "p.MaQuocGia = ?";
            $params[] = $countryId;
        }

        $whereSql = $where ? 'WHERE ' . implode(' AND ', $where) : '';
        $query = "SELECT p.*, GROUP_CONCAT(t.TenTheLoai) as TheLoai
                  FROM Phim p
                  LEFT JOIN Phim_TheLoai pt ON p.MaPhim = pt.MaPhim
                  LEFT JOIN TheLoai t ON pt.MaTheLoai = t.MaTheLoai
                  $whereSql
                  GROUP BY p.MaPhim
                  ORDER BY p.NgayTao DESC";
        $stmt = $this->db->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}