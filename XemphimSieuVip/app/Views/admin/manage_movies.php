<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý phim - Xemp Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .movie-table img {
            width: 50px;
            height: 75px;
            object-fit: cover;
        }
        .action-buttons {
            display: flex;
            gap: 10px;
        }
        .movie-title {
            max-width: 200px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
    </style>
</head>
<body>
    <?php require_once 'app/Views/shares/header.php'; ?>

    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Quản lý phim</h2>
            <a href="/admin/movie" class="btn btn-primary">
                <i class="fas fa-plus"></i> Thêm phim mới
            </a>
        </div>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success">
                <?php 
                echo $_SESSION['success'];
                unset($_SESSION['success']);
                ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger">
                <?php 
                echo $_SESSION['error'];
                unset($_SESSION['error']);
                ?>
            </div>
        <?php endif; ?>

        <div class="table-responsive">
            <table class="table table-striped movie-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Poster</th>
                        <th>Tên phim</th>
                        <th>Năm</th>
                        <th>Thời lượng</th>
                        <th>Thể loại</th>
                        <th>Lượt xem</th>
                        <th>Trạng thái</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($movies as $movie): ?>
                    <tr>
                        <td><?php echo $movie['MaPhim']; ?></td>
                        <td>
                            <img src="<?php echo $movie['HinhAnh']; ?>" alt="<?php echo $movie['TenPhim']; ?>">
                        </td>
                        <td class="movie-title"><?php echo $movie['TenPhim']; ?></td>
                        <td><?php echo $movie['NamPhatHanh']; ?></td>
                        <td><?php echo $movie['ThoiLuong']; ?> phút</td>
                        <td><?php echo $movie['TheLoai']; ?></td>
                        <td><?php echo $movie['LuotXem']; ?></td>
                        <td>
                            <span class="badge bg-<?php echo $movie['TinhTrang'] === 'Đang chiếu' ? 'success' : ($movie['TinhTrang'] === 'Sắp chiếu' ? 'warning' : 'secondary'); ?>">
                                <?php echo $movie['TinhTrang']; ?>
                            </span>
                        </td>
                        <td>
                            <div class="action-buttons">
                                <a href="/admin/edit-movie/<?php echo $movie['MaPhim']; ?>" class="btn btn-sm btn-warning">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button type="button" class="btn btn-sm btn-danger" onclick="confirmDelete(<?php echo $movie['MaPhim']; ?>)">
                                    <i class="fas fa-trash"></i>
                                </button>
                                <?php if ($movie['PhanLoai'] === 'Bộ'): ?>
                                <a href="/admin/episode/<?php echo $movie['MaPhim']; ?>" class="btn btn-sm btn-info">
                                    <i class="fas fa-list-ol"></i>
                                </a>
                                <?php endif; ?>
                                <a href="/admin/video/<?php echo $movie['MaPhim']; ?>" class="btn btn-sm btn-success">
                                    <i class="fas fa-video"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <?php require_once 'app/Views/shares/footer.php'; ?>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function confirmDelete(movieId) {
            if (confirm('Bạn có chắc chắn muốn xóa phim này không?')) {
                window.location.href = '/admin/delete-movie/' + movieId;
            }
        }
    </script>
</body>
</html> 