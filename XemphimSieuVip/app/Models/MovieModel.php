<?php
class MovieModel
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    // Lấy chi tiết phim theo ID, bao gồm đánh giá trung bình và số lượt đánh giá
    public function getMovieById($id)
    {
        try {
            $query = "SELECT p.*, GROUP_CONCAT(t.TenTheLoai) as TheLoai, qg.TenQuocGia,
                            COALESCE(AVG(dg.SoDiem), 0) as DanhGia,
                            COUNT(dg.MaPhim) as LuotDanhGia
                     FROM Phim p 
                     LEFT JOIN Phim_TheLoai pt ON p.MaPhim = pt.MaPhim 
                     LEFT JOIN TheLoai t ON pt.MaTheLoai = t.MaTheLoai 
                     LEFT JOIN QuocGia qg ON p.MaQuocGia = qg.MaQuocGia
                     LEFT JOIN DanhGia dg ON p.MaPhim = dg.MaPhim
                     WHERE p.MaPhim = :id 
                     GROUP BY p.MaPhim";
            $stmt = $this->db->prepare($query);
            $stmt->execute([':id' => $id]);
            $movie = $stmt->fetch(PDO::FETCH_ASSOC);

            // Fetch selected genres for the movie
            $query = "SELECT MaTheLoai FROM Phim_TheLoai WHERE MaPhim = :id";
            $stmt = $this->db->prepare($query);
            $stmt->execute([':id' => $id]);
            $movie['genres'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return $movie;
        } catch (PDOException $e) {
            return null;
        }
    }

    // Lấy danh sách diễn viên của phim
    public function getMovieActors($movie_id)
    {
        try {
            $query = "SELECT GROUP_CONCAT(dv.TenDienVien) as actors 
                      FROM Phim_DienVien pd 
                      JOIN DienVien dv ON pd.MaDienVien = dv.MaDienVien 
                      WHERE pd.MaPhim = :movie_id";
            $stmt = $this->db->prepare($query);
            $stmt->execute([':movie_id' => $movie_id]);
            return $stmt->fetch(PDO::FETCH_ASSOC)['actors'] ?? 'Chưa có thông tin';
        } catch (PDOException $e) {
            return 'Chưa có thông tin';
        }
    }

    // Lấy danh sách đạo diễn của phim
    public function getMovieDirectors($movie_id)
    {
        try {
            $query = "SELECT GROUP_CONCAT(dd.TenDaoDien) as directors 
                      FROM Phim_DaoDien pd 
                      JOIN DaoDien dd ON pd.MaDaoDien = dd.MaDaoDien 
                      WHERE pd.MaPhim = :movie_id";
            $stmt = $this->db->prepare($query);
            $stmt->execute([':movie_id' => $movie_id]);
            return $stmt->fetch(PDO::FETCH_ASSOC)['directors'] ?? 'Chưa có thông tin';
        } catch (PDOException $e) {
            return 'Chưa có thông tin';
        }
    }

    // Thêm hoặc cập nhật đánh giá của người dùng
    public function addRating($username, $movieId, $rating, $comment = '')
    {
        try {
            $query = "INSERT INTO DanhGia (TenDN, MaPhim, SoDiem, BinhLuan, ThoiGian) 
                     VALUES (:username, :movieId, :rating, :comment, NOW())
                     ON DUPLICATE KEY UPDATE SoDiem = :rating, BinhLuan = :comment, ThoiGian = NOW()";
            $stmt = $this->db->prepare($query);
            $stmt->execute([
                ':username' => $username,
                ':movieId' => $movieId,
                ':rating' => $rating,
                ':comment' => $comment
            ]);
            return [
                'success' => true,
                'message' => 'Đánh giá thành công!'
            ];
        } catch (PDOException $e) {
            return [
                'success' => false,
                'message' => 'Lỗi khi đánh giá: ' . $e->getMessage()
            ];
        }
    }

    // Lấy tất cả phim
    public function getAllMovies()
    {
        try {
            $query = "SELECT p.*, GROUP_CONCAT(t.TenTheLoai) as TheLoai, qg.TenQuocGia as TenQuocGia, 
                            p.TinhTrang as TinhTrang
                     FROM Phim p 
                     LEFT JOIN Phim_TheLoai pt ON p.MaPhim = pt.MaPhim 
                     LEFT JOIN TheLoai t ON pt.MaTheLoai = t.MaTheLoai 
                     LEFT JOIN QuocGia qg ON p.MaQuocGia = qg.MaQuocGia
                     GROUP BY p.MaPhim 
                     ORDER BY p.NgayTao DESC";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    // Lọc phim theo tiêu chí tìm kiếm
    public function filterMovies($search, $category, $status, $page, $perPage)
    {
        try {
            $offset = ($page - 1) * $perPage;
            $params = [];
            $query = "SELECT p.*, GROUP_CONCAT(t.TenTheLoai) as TheLoai, qg.TenQuocGia as TenQuocGia, 
                            p.TinhTrang as TinhTrang
                     FROM Phim p 
                     LEFT JOIN Phim_TheLoai pt ON p.MaPhim = pt.MaPhim 
                     LEFT JOIN TheLoai t ON pt.MaTheLoai = t.MaTheLoai 
                     LEFT JOIN QuocGia qg ON p.MaQuocGia = qg.MaQuocGia
                     WHERE 1=1";

            if ($search) {
                $query .= " AND p.TenPhim LIKE :search";
                $params[':search'] = '%' . $search . '%';
            }

            if ($category) {
                $query .= " AND pt.MaTheLoai = :category";
                $params[':category'] = $category;
            }

            if ($status) {
                $query .= " AND p.TinhTrang = :status";
                $params[':status'] = $status;
            }

            $query .= " GROUP BY p.MaPhim ORDER BY p.NgayTao DESC LIMIT :limit OFFSET :offset";
            $stmt = $this->db->prepare($query);

            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }
            $stmt->bindValue(':limit', (int)$perPage, PDO::PARAM_INT);
            $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
            $stmt->execute();
            $movies = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $countQuery = "SELECT COUNT(DISTINCT p.MaPhim) as total
                          FROM Phim p 
                          LEFT JOIN Phim_TheLoai pt ON p.MaPhim = pt.MaPhim 
                          LEFT JOIN TheLoai t ON pt.MaTheLoai = t.MaTheLoai 
                          LEFT JOIN QuocGia qg ON p.MaQuocGia = qg.MaQuocGia
                          WHERE 1=1";
            $countParams = [];

            if ($search) {
                $countQuery .= " AND p.TenPhim LIKE :search";
                $countParams[':search'] = '%' . $search . '%';
            }

            if ($category) {
                $countQuery .= " AND pt.MaTheLoai = :category";
                $countParams[':category'] = $category;
            }

            if ($status) {
                $countQuery .= " AND p.TinhTrang = :status";
                $countParams[':status'] = $status;
            }

            $stmt = $this->db->prepare($countQuery);
            foreach ($countParams as $key => $value) {
                $stmt->bindValue($key, $value);
            }
            $stmt->execute();
            $total = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

            return [
                'movies' => $movies,
                'total' => $total
            ];
        } catch (PDOException $e) {
            return [
                'movies' => [],
                'total' => 0
            ];
        }
    }

    // Thêm phim mới
    public function addMovie($data, $files)
    {
        try {
            // Kiểm tra phim đã tồn tại
            $query = "SELECT COUNT(*) FROM Phim WHERE TenPhim = :title AND NamPhatHanh = :release_year";
            $stmt = $this->db->prepare($query);
            $stmt->execute([
                ':title' => $data['title'],
                ':release_year' => $data['release_year']
            ]);

            if ($stmt->fetchColumn() > 0) {
                return [
                    'success' => false,
                    'message' => 'Phim này đã tồn tại trong cơ sở dữ liệu!'
                ];
            }

            $this->db->beginTransaction();

            // Trích xuất dữ liệu từ form
            $title = trim($data['title'] ?? '');
            $subtitle = trim($data['subtitle'] ?? '');
            $description = trim($data['description'] ?? '');
            $content = trim($data['content'] ?? '');
            $release_year = !empty($data['release_year']) ? (int)$data['release_year'] : null;
            $duration = !empty($data['duration']) ? (int)$data['duration'] : null;
            $country_id = !empty($data['country_id']) ? (int)$data['country_id'] : null;
            $genres = $data['genres'] ?? [];
            $actors = !empty($data['actors']) ? array_map('trim', explode(',', $data['actors'])) : [];
            $directors = !empty($data['directors']) ? array_map('trim', explode(',', $data['directors'])) : [];
            $status = $data['status'] ?? 'Đang chiếu';
            $type = $data['type'] ?? 'Lẻ';
            $trailer_url = trim($data['trailer_url'] ?? '');

            // Xử lý upload hình ảnh
            $poster_url = '';
            if (isset($files['poster']) && $files['poster']['error'] === UPLOAD_ERR_OK) {
                $target_dir = "Uploads/";

                if (!is_dir($target_dir)) {
                    mkdir($target_dir, 0777, true);
                }

                $filename = time() . '_' . basename($files["poster"]["name"]);
                $target_file = $target_dir . $filename;

                if (move_uploaded_file($files["poster"]["tmp_name"], $target_file)) {
                    $poster_url = $target_file;
                } else {
                    throw new Exception("Không thể upload hình ảnh");
                }
            } else {
                throw new Exception("Poster phim là bắt buộc");
            }

            // Thêm phim vào bảng Phim
            $query = "INSERT INTO Phim (TenPhim, TieuDe, MoTa, NoiDung, NamPhatHanh, ThoiLuong, MaQuocGia, HinhAnh, Link, TinhTrang, PhanLoai) 
                     VALUES (:title, :subtitle, :description, :content, :release_year, :duration, :country_id, :poster_url, :trailer_url, :status, :type)";

            $stmt = $this->db->prepare($query);
            $params = [
                ':title' => $title,
                ':subtitle' => $subtitle ?: null,
                ':description' => $description ?: null,
                ':content' => $content ?: null,
                ':release_year' => $release_year,
                ':duration' => $duration,
                ':country_id' => $country_id,
                ':poster_url' => $poster_url,
                ':trailer_url' => $trailer_url ?: null,
                ':status' => $status,
                ':type' => $type
            ];

            if (!$stmt->execute($params)) {
                throw new Exception("Không thể thêm phim vào cơ sở dữ liệu");
            }

            $movie_id = $this->db->lastInsertId();

            // Thêm thể loại cho phim
            if (!empty($genres)) {
                $query = "INSERT INTO Phim_TheLoai (MaPhim, MaTheLoai) VALUES (:movie_id, :genre_id)";
                $stmt = $this->db->prepare($query);
                foreach ($genres as $genre_id) {
                    if (!$stmt->execute([
                        ':movie_id' => $movie_id,
                        ':genre_id' => (int)$genre_id
                    ])) {
                        throw new Exception("Không thể thêm thể loại cho phim");
                    }
                }
            }

            // Thêm diễn viên vào DienVien và Phim_DienVien
            if (!empty($actors)) {
                $query = "INSERT INTO DienVien (TenDienVien) VALUES (:actor_name) 
                          ON DUPLICATE KEY UPDATE MaDienVien = LAST_INSERT_ID(MaDienVien)";
                $stmt_actor = $this->db->prepare($query);

                $query_assoc = "INSERT INTO Phim_DienVien (MaPhim, MaDienVien) VALUES (:movie_id, :actor_id)";
                $stmt_assoc = $this->db->prepare($query_assoc);

                foreach ($actors as $actor_name) {
                    if (empty($actor_name)) continue;

                    // Thêm hoặc lấy ID diễn viên
                    $stmt_actor->execute([':actor_name' => $actor_name]);
                    $actor_id = $this->db->lastInsertId();

                    // Liên kết với phim
                    $stmt_assoc->execute([
                        ':movie_id' => $movie_id,
                        ':actor_id' => $actor_id
                    ]);
                }
            }

            // Thêm đạo diễn vào DaoDien và Phim_DaoDien
            if (!empty($directors)) {
                $query = "INSERT INTO DaoDien (TenDaoDien) VALUES (:director_name) 
                          ON DUPLICATE KEY UPDATE MaDaoDien = LAST_INSERT_ID(MaDaoDien)";
                $stmt_director = $this->db->prepare($query);

                $query_assoc = "INSERT INTO Phim_DaoDien (MaPhim, MaDaoDien) VALUES (:movie_id, :director_id)";
                $stmt_assoc = $this->db->prepare($query_assoc);

                foreach ($directors as $director_name) {
                    if (empty($director_name)) continue;

                    // Thêm hoặc lấy ID đạo diễn
                    $stmt_director->execute([':director_name' => $director_name]);
                    $director_id = $this->db->lastInsertId();

                    // Liên kết với phim
                    $stmt_assoc->execute([
                        ':movie_id' => $movie_id,
                        ':director_id' => $director_id
                    ]);
                }
            }

            $this->db->commit();
            return [
                'success' => true,
                'message' => 'Thêm phim thành công!',
                'movie_id' => $movie_id
            ];
        } catch (PDOException $e) {
            $this->db->rollBack();
            return [
                'success' => false,
                'message' => 'Có lỗi xảy ra khi thêm phim: ' . $e->getMessage()
            ];
        } catch (Exception $e) {
            $this->db->rollBack();
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    // Cập nhật phim
    public function updateMovie($id, $data, $poster, $genres, $actors, $directors, $current_poster)
    {
        try {
            $this->db->beginTransaction();

            // Trích xuất dữ liệu từ form
            $title = trim($data['title'] ?? '');
            $subtitle = trim($data['subtitle'] ?? '');
            $description = trim($data['description'] ?? '');
            $content = trim($data['content'] ?? '');
            $release_year = !empty($data['release_year']) ? (int)$data['release_year'] : null;
            $duration = !empty($data['duration']) ? (int)$data['duration'] : null;
            $country_id = !empty($data['country_id']) ? (int)$data['country_id'] : null;
            $status = trim($data['status'] ?? '');
            $type = trim($data['type'] ?? '');
            $trailer_url = trim($data['trailer_url'] ?? '');

            // Validate required fields
            if (empty($title)) {
                throw new Exception('Tên phim là bắt buộc');
            }
            if (empty($genres)) {
                throw new Exception('Phải chọn ít nhất một thể loại');
            }
            if (empty($country_id)) {
                throw new Exception('Quốc gia là bắt buộc');
            }
            if (empty($status)) {
                throw new Exception('Trạng thái là bắt buộc');
            }
            if (empty($type)) {
                throw new Exception('Phân loại là bắt buộc');
            }
            if (empty($trailer_url)) {
                throw new Exception('Link trailer là bắt buộc');
            }

            // Xử lý upload hình ảnh
            $poster_url = $current_poster;
            if ($poster && $poster['error'] === UPLOAD_ERR_OK) {
                $target_dir = 'Uploads/';
                if (!is_dir($target_dir) || !is_writable($target_dir)) {
                    throw new Exception('Thư mục Uploads/ không tồn tại hoặc không có quyền ghi');
                }
                $filename = time() . '_' . basename($poster['name']);
                $target_file = $target_dir . $filename;
                if (!move_uploaded_file($poster['tmp_name'], $target_file)) {
                    throw new Exception('Không thể upload hình ảnh');
                }
                $poster_url = $target_file;
            }

            // Cập nhật thông tin phim
            $query = "UPDATE Phim 
                     SET TenPhim = :title,
                         TieuDe = :subtitle,
                         MoTa = :description,
                         NoiDung = :content,
                         NamPhatHanh = :release_year,
                         ThoiLuong = :duration,
                         MaQuocGia = :country_id,
                         HinhAnh = :poster_url,
                         Link = :trailer_url,
                         TinhTrang = :status,
                         PhanLoai = :type,
                         NgayCapNhat = NOW()
                     WHERE MaPhim = :id";
            $stmt = $this->db->prepare($query);
            $params = [
                ':id' => $id,
                ':title' => $title,
                ':subtitle' => $subtitle ?: null,
                ':description' => $description ?: null,
                ':content' => $content ?: null,
                ':release_year' => $release_year,
                ':duration' => $duration,
                ':country_id' => $country_id,
                ':poster_url' => $poster_url,
                ':trailer_url' => $trailer_url,
                ':status' => $status,
                ':type' => $type
            ];
            $stmt->execute($params);

            // Kiểm tra xem có bản ghi nào được cập nhật không
            if ($stmt->rowCount() === 0) {
                throw new Exception('Không tìm thấy phim với MaPhim = ' . $id);
            }

            // Xóa các quan hệ cũ
            $queries = [
                "DELETE FROM Phim_TheLoai WHERE MaPhim = :movie_id",
                "DELETE FROM Phim_DienVien WHERE MaPhim = :movie_id",
                "DELETE FROM Phim_DaoDien WHERE MaPhim = :movie_id"
            ];
            foreach ($queries as $query) {
                $stmt = $this->db->prepare($query);
                $stmt->execute([':movie_id' => $id]);
            }

            // Thêm thể loại mới
            if (!empty($genres)) {
                $query = "INSERT INTO Phim_TheLoai (MaPhim, MaTheLoai) VALUES (:movie_id, :genre_id)";
                $stmt = $this->db->prepare($query);
                foreach ($genres as $genre_id) {
                    $stmt->execute([
                        ':movie_id' => $id,
                        ':genre_id' => (int)$genre_id
                    ]);
                    if ($stmt->rowCount() === 0) {
                        throw new Exception('Không thể thêm thể loại MaTheLoai = ' . $genre_id);
                    }
                }
            }

            // Thêm diễn viên vào DienVien và Phim_DienVien
            if (!empty($actors)) {
                $query = "INSERT INTO DienVien (TenDienVien) VALUES (:actor_name) 
                          ON DUPLICATE KEY UPDATE MaDienVien = LAST_INSERT_ID(MaDienVien)";
                $stmt_actor = $this->db->prepare($query);
                $query_assoc = "INSERT INTO Phim_DienVien (MaPhim, MaDienVien) VALUES (:movie_id, :actor_id)";
                $stmt_assoc = $this->db->prepare($query_assoc);

                foreach (array_map('trim', explode(',', $actors)) as $actor_name) {
                    if (empty($actor_name)) continue;
                    $stmt_actor->execute([':actor_name' => $actor_name]);
                    $actor_id = $this->db->lastInsertId();
                    if (!$actor_id) {
                        $stmt = $this->db->prepare("SELECT MaDienVien FROM DienVien WHERE TenDienVien = :actor_name");
                        $stmt->execute([':actor_name' => $actor_name]);
                        $actor_id = $stmt->fetchColumn();
                    }
                    $stmt_assoc->execute([':movie_id' => $id, ':actor_id' => $actor_id]);
                    if ($stmt_assoc->rowCount() === 0) {
                        throw new Exception('Không thể thêm diễn viên: ' . $actor_name);
                    }
                }
            }

            // Thêm đạo diễn vào DaoDien và Phim_DaoDien
            if (!empty($directors)) {
                $query = "INSERT INTO DaoDien (TenDaoDien) VALUES (:director_name) 
                          ON DUPLICATE KEY UPDATE MaDaoDien = LAST_INSERT_ID(MaDaoDien)";
                $stmt_director = $this->db->prepare($query);
                $query_assoc = "INSERT INTO Phim_DaoDien (MaPhim, MaDaoDien) VALUES (:movie_id, :director_id)";
                $stmt_assoc = $this->db->prepare($query_assoc);

                foreach (array_map('trim', explode(',', $directors)) as $director_name) {
                    if (empty($director_name)) continue;
                    $stmt_director->execute([':director_name' => $director_name]);
                    $director_id = $this->db->lastInsertId();
                    if (!$director_id) {
                        $stmt = $this->db->prepare("SELECT MaDaoDien FROM DaoDien WHERE TenDaoDien = :director_name");
                        $stmt->execute([':director_name' => $director_name]);
                        $director_id = $stmt->fetchColumn();
                    }
                    $stmt_assoc->execute([':movie_id' => $id, ':director_id' => $director_id]);
                    if ($stmt_assoc->rowCount() === 0) {
                        throw new Exception('Không thể thêm đạo diễn: ' . $director_name);
                    }
                }
            }

            $this->db->commit();
            error_log("Movie updated successfully: MaPhim = $id");
            return [
                'success' => true,
                'message' => 'Cập nhật phim thành công!'
            ];
        } catch (PDOException $e) {
            $this->db->rollBack();
            error_log("MovieModel::updateMovie PDO error: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Lỗi cơ sở dữ liệu: ' . $e->getMessage()
            ];
        } catch (Exception $e) {
            $this->db->rollBack();
            error_log("MovieModel::updateMovie error: " . $e->getMessage());
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    // Xóa phim
    public function deleteMovie($id)
    {
        try {
            $this->db->beginTransaction();

            $queries = [
                "DELETE FROM Phim_TheLoai WHERE MaPhim = :id",
                "DELETE FROM Phim_DienVien WHERE MaPhim = :id",
                "DELETE FROM Phim_DaoDien WHERE MaPhim = :id",
                "DELETE FROM LichSu WHERE MaPhim = :id",
                "DELETE FROM Phim WHERE MaPhim = :id"
            ];

            foreach ($queries as $query) {
                $stmt = $this->db->prepare($query);
                $stmt->execute([':id' => $id]);
            }

            $this->db->commit();
            return [
                'success' => true,
                'message' => 'Xóa phim thành công!'
            ];
        } catch (PDOException $e) {
            $this->db->rollBack();
            return [
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ];
        }
    }

    // Lấy danh sách tập phim
    public function getMovieEpisodes($movie_id)
    {
        try {
            $query = "SELECT * FROM TapPhim WHERE MaPhim = :movie_id ORDER BY MaTap ASC";
            $stmt = $this->db->prepare($query);
            $stmt->execute([':movie_id' => $movie_id]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    // Lấy danh sách video của phim
    public function getMovieVideos($movie_id)
    {
        try {
            $query = "SELECT * FROM Video WHERE MaPhim = :movie_id ORDER BY NgayTao DESC";
            $stmt = $this->db->prepare($query);
            $stmt->execute([':movie_id' => $movie_id]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    // Thêm tập phim mới
    public function addEpisode($movie_id, $ten_tap, $link) {
        try {
            $query = "INSERT INTO TapPhim (MaPhim, TenTap, Link) VALUES (:movie_id, :ten_tap, :link)";
            $stmt = $this->db->prepare($query);
            $stmt->execute([
                ':movie_id' => $movie_id,
                ':ten_tap' => $ten_tap,
                ':link' => $link
            ]);

            return [
                'success' => true,
                'message' => 'Thêm tập phim thành công!'
            ];
        } catch (PDOException $e) {
            return [
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ];
        }
    }

    // Thêm video mới
    public function addVideo($movie_id, $ten_video, $chat_luong, $ngon_ngu, $thoi_luong, $link)
    {
        try {
            $query = "INSERT INTO Video (MaPhim, TenVideo, ChatLuong, NgonNgu, Link, NgayTao) 
                     VALUES (:movie_id, :ten_video, :chat_luong, :ngon_ngu, :link, CURRENT_TIMESTAMP)";

            $stmt = $this->db->prepare($query);
            $stmt->execute([
                ':movie_id' => $movie_id,
                ':ten_video' => $ten_video,
                ':chat_luong' => $chat_luong,
                ':ngon_ngu' => $ngon_ngu,
                ':link' => $link
            ]);

            return [
                'success' => true,
                'message' => 'Thêm video thành công!'
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ];
        }
    }

    // Tìm kiếm phim
    public function searchMovies($keyword, $genre = '', $country = '', $page = 1, $perPage = 12)
    {
        try {
            $offset = ($page - 1) * $perPage;
            $params = [];
            $conditions = [];

            $sql = "SELECT DISTINCT p.*, GROUP_CONCAT(t.TenTheLoai) as TheLoai, qg.TenQuocGia as TenQuocGia 
                   FROM Phim p 
                   LEFT JOIN Phim_TheLoai pt ON p.MaPhim = pt.MaPhim 
                   LEFT JOIN TheLoai t ON pt.MaTheLoai = t.MaTheLoai 
                   LEFT JOIN QuocGia qg ON p.MaQuocGia = qg.MaQuocGia
                   WHERE 1=1";

            if (!empty($keyword)) {
                $conditions[] = "(p.TenPhim LIKE :keyword OR p.MoTa LIKE :keyword)";
                $params[':keyword'] = '%' . $keyword . '%';
            }

            if (!empty($genre)) {
                $conditions[] = "pt.MaTheLoai = :genre";
                $params[':genre'] = $genre;
            }

            if (!empty($country)) {
                $conditions[] = "p.MaQuocGia = :country";
                $params[':country'] = $country;
            }

            if (!empty($conditions)) {
                $sql .= " AND " . implode(" AND ", $conditions);
            }

            $sql .= " GROUP BY p.MaPhim";

            $countSql = str_replace("SELECT DISTINCT p.*, GROUP_CONCAT(t.TenTheLoai) as TheLoai, qg.TenQuocGia as TenQuocGia", "SELECT COUNT(DISTINCT p.MaPhim) as total", $sql);
            $stmt = $this->db->prepare($countSql);
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }
            $stmt->execute();
            $total = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

            $sql .= " ORDER BY p.NgayTao DESC LIMIT :limit OFFSET :offset";
            $stmt = $this->db->prepare($sql);
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }
            $stmt->bindValue(':limit', (int)$perPage, PDO::PARAM_INT);
            $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
            $stmt->execute();
            $movies = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return [
                'movies' => $movies,
                'total' => $total
            ];
        } catch (PDOException $e) {
            return [
                'movies' => [],
                'total' => 0
            ];
        }
    }

    // Tìm kiếm nhanh (cho search box)
    public function quickSearch($keyword)
    {
        try {
            $sql = "SELECT MaPhim, TenPhim, HinhAnh, NamPhatHanh, ThoiLuong 
                   FROM Phim 
                   WHERE TenPhim LIKE :keyword 
                   ORDER BY NgayTao DESC 
                   LIMIT 5";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([':keyword' => '%' . $keyword . '%']);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    // Tìm kiếm theo thể loại
    public function searchMoviesByGenre($genreId, $keyword = '', $country = '', $year = '', $page = 1, $perPage = 12)
    {
        try {
            $offset = ($page - 1) * $perPage;
            $params = [];
            $sql = "SELECT DISTINCT p.*, GROUP_CONCAT(DISTINCT t.TenTheLoai) as TheLoai, qg.TenQuocGia
                    FROM Phim p
                    INNER JOIN Phim_TheLoai pt ON p.MaPhim = pt.MaPhim
                    LEFT JOIN TheLoai t ON pt.MaTheLoai = t.MaTheLoai
                    LEFT JOIN QuocGia qg ON p.MaQuocGia = qg.MaQuocGia
                    WHERE pt.MaTheLoai = :genreId";

            $params[':genreId'] = (int)$genreId;

            if (!empty($keyword)) {
                $sql .= " AND (p.TenPhim LIKE :keyword OR p.MoTa LIKE :keyword)";
                $params[':keyword'] = "%$keyword%";
            }
            if (!empty($country)) {
                $sql .= " AND p.MaQuocGia = :country";
                $params[':country'] = (int)$country;
            }
            if (!empty($year)) {
                if ($year === 'older') {
                    $sql .= " AND p.NamPhatHanh < :year";
                    $params[':year'] = date('Y') - 10;
                } else {
                    $sql .= " AND p.NamPhatHanh = :year";
                    $params[':year'] = (int)$year;
                }
            }
            $sql .= " GROUP BY p.MaPhim ORDER BY p.NgayTao DESC LIMIT :limit OFFSET :offset";
            $params[':limit'] = (int)$perPage;
            $params[':offset'] = (int)$offset;

            $stmt = $this->db->prepare($sql);
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value, is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR);
            }
            $stmt->execute();
            $movies = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $countSql = "SELECT COUNT(DISTINCT p.MaPhim) as total
                        FROM Phim p
                        INNER JOIN Phim_TheLoai pt ON p.MaPhim = pt.MaPhim
                        WHERE pt.MaTheLoai = :genreId";
            $countParams = [':genreId' => (int)$genreId];

            if (!empty($keyword)) {
                $countSql .= " AND (p.TenPhim LIKE :keyword OR p.MoTa LIKE :keyword)";
                $countParams[':keyword'] = "%$keyword%";
            }
            if (!empty($country)) {
                $countSql .= " AND p.MaQuocGia = :country";
                $countParams[':country'] = (int)$country;
            }
            if (!empty($year)) {
                if ($year === 'older') {
                    $countSql .= " AND p.NamPhatHanh < :year";
                    $countParams[':year'] = date('Y') - 10;
                } else {
                    $countSql .= " AND p.NamPhatHanh = :year";
                    $countParams[':year'] = (int)$year;
                }
            }

            $stmt = $this->db->prepare($countSql);
            foreach ($countParams as $key => $value) {
                $stmt->bindValue($key, $value, is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR);
            }
            $stmt->execute();
            $total = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

            return [
                'movies' => $movies,
                'total' => $total
            ];
        } catch (PDOException $e) {
            return [
                'movies' => [],
                'total' => 0
            ];
        }
    }

    // Tìm kiếm theo quốc gia
    public function searchMoviesByCountry($countryId, $keyword = '', $genre = '', $page = 1, $perPage = 12)
    {
        try {
            $offset = ($page - 1) * $perPage;
            $params = [];
            $sql = "SELECT DISTINCT p.*, GROUP_CONCAT(DISTINCT t.TenTheLoai) as TheLoai, qg.TenQuocGia
                    FROM Phim p
                    INNER JOIN QuocGia qg ON p.MaQuocGia = qg.MaQuocGia
                    LEFT JOIN Phim_TheLoai pt ON p.MaPhim = pt.MaPhim
                    LEFT JOIN TheLoai t ON pt.MaTheLoai = t.MaTheLoai
                    WHERE p.MaQuocGia = :countryId";

            $params[':countryId'] = (int)$countryId;

            if (!empty($keyword)) {
                $sql .= " AND (p.TenPhim LIKE :keyword OR p.MoTa LIKE :keyword)";
                $params[':keyword'] = "%$keyword%";
            }
            if (!empty($genre)) {
                $sql .= " AND pt.MaTheLoai = :genre";
                $params[':genre'] = (int)$genre;
            }
            $sql .= " GROUP BY p.MaPhim ORDER BY p.NgayTao DESC LIMIT :limit OFFSET :offset";
            $params[':limit'] = (int)$perPage;
            $params[':offset'] = (int)$offset;

            $stmt = $this->db->prepare($sql);
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value, is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR);
            }
            $stmt->execute();
            $movies = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $countSql = "SELECT COUNT(DISTINCT p.MaPhim) as total
                        FROM Phim p
                        INNER JOIN QuocGia qg ON p.MaQuocGia = qg.MaQuocGia
                        WHERE p.MaQuocGia = :countryId";
            $countParams = [':countryId' => (int)$countryId];

            if (!empty($keyword)) {
                $countSql .= " AND (p.TenPhim LIKE :keyword OR p.MoTa LIKE :keyword)";
                $countParams[':keyword'] = "%$keyword%";
            }
            if (!empty($genre)) {
                $countSql .= " AND EXISTS (
                    SELECT 1 FROM Phim_TheLoai pt 
                    WHERE pt.MaPhim = p.MaPhim AND pt.MaTheLoai = :genre
                )";
                $countParams[':genre'] = (int)$genre;
            }

            $stmt = $this->db->prepare($countSql);
            foreach ($countParams as $key => $value) {
                $stmt->bindValue($key, $value, is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR);
            }
            $stmt->execute();
            $total = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

            return [
                'movies' => $movies,
                'total' => $total
            ];
        } catch (PDOException $e) {
            return [
                'movies' => [],
                'total' => 0
            ];
        }
    }

    // Lấy thể loại theo ID
    public function getGenreById($id)
    {
        try {
            $sql = "SELECT * FROM TheLoai WHERE MaTheLoai = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':id' => $id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return null;
        }
    }

    // Lấy thông tin quốc gia theo ID
    public function getCountryById($id)
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

    // Lấy phim theo thể loại
    public function getMoviesByCategory($category_id)
    {
        try {
            $query = "SELECT p.*, GROUP_CONCAT(t.TenTheLoai) as TheLoai, qg.TenQuocGia as TenQuocGia
                     FROM Phim p 
                     LEFT JOIN Phim_TheLoai pt ON p.MaPhim = pt.MaPhim 
                     LEFT JOIN TheLoai t ON pt.MaTheLoai = t.MaTheLoai 
                     LEFT JOIN QuocGia qg ON p.MaQuocGia = qg.MaQuocGia
                     WHERE pt.MaTheLoai = :category_id
                     GROUP BY p.MaPhim 
                     ORDER BY p.NgayTao DESC";

            $stmt = $this->db->prepare($query);
            $stmt->execute([':category_id' => $category_id]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }


    // Lấy danh sách quốc gia
    public function getAllCountries()
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

    // Lấy danh sách thể loại
    public function getAllGenres()
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

    // Thêm phim bộ mới
    public function addSeries($data, $files) {
        try {
            $this->db->beginTransaction();

            // Xử lý upload poster
            $posterPath = '';
            if (isset($files['poster']) && $files['poster']['error'] === UPLOAD_ERR_OK) {
                $targetDir = "Uploads/";
                if (!is_dir($targetDir)) {
                    mkdir($targetDir, 0777, true);
                }
                $posterPath = $targetDir . time() . '_' . basename($files['poster']['name']);
                move_uploaded_file($files['poster']['tmp_name'], $posterPath);
            }

            // Thêm phim vào bảng Phim
            $query = "INSERT INTO Phim (TenPhim, MoTa, HinhAnh, PhanLoai) 
                      VALUES (:title, :description, :poster, 'Bộ')";
            $stmt = $this->db->prepare($query);
            $stmt->execute([
                ':title' => $data['title'],
                ':description' => $data['description'] ?? '',
                ':poster' => $posterPath
            ]);

            $movieId = $this->db->lastInsertId();

            // Thêm thể loại vào bảng liên kết `Phim_TheLoai`
            $query = "INSERT INTO Phim_TheLoai (MaPhim, MaTheLoai) VALUES (:movieId, :genreId)";
            $stmt = $this->db->prepare($query);
            foreach ($data['genres'] as $genreId) {
                $stmt->execute([
                    ':movieId' => $movieId,
                    ':genreId' => $genreId
                ]);
            }

            // Lưu danh sách tập phim vào bảng `TapPhim`
            $query = "INSERT INTO TapPhim (MaPhim, TenTap, Link) VALUES (:movieId, :episodeName, :episodeLink)";
            $stmt = $this->db->prepare($query);
            $episodeList = explode("\n", $data['episodes']);
            foreach ($episodeList as $episode) {
                list($episodeName, $episodeLink) = explode('|', $episode);
                $stmt->execute([
                    ':movieId' => $movieId,
                    ':episodeName' => trim($episodeName),
                    ':episodeLink' => trim($episodeLink)
                ]);
            }

            $this->db->commit();
            return [
                'success' => true,
                'message' => 'Thêm phim bộ thành công!'
            ];
        } catch (Exception $e) {
            $this->db->rollBack();
            return [
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ];
        }
    }
    
}
?>