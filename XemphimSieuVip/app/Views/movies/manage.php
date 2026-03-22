<!DOCTYPE html>
<html lang="Vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý phim</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
    <?php include 'app/Views/shares/header.php'; ?>

    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Quản lý phim</h2>
            <a href="/movie/addmovie" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Thêm phim mới
            </a>
        </div>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success">
                <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger">
                <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Poster</th>
                                <th>Tên phim</th>
                                <th>Thể loại</th>
                                <th>Quốc gia</th>
                                <th>Năm phát hành</th>
                                <th>Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($movies as $movie): ?>
                                <tr>
                                    <td><?php echo $movie['MaPhim']; ?></td>
                                    <td>
                                        <img src="<?php echo $movie['HinhAnh']; ?>" 
                                             alt="<?php echo $movie['TenPhim']; ?>"
                                             style="width: 50px; height: 70px; object-fit: cover;">
                                    </td>
                                    <td><?php echo $movie['TenPhim']; ?></td>
                                    <td><?php echo $movie['TheLoai']; ?></td>
                                    <td><?php echo $movie['TenQuocGia']; ?></td>
                                    <td><?php echo $movie['NamPhatHanh']; ?></td>
                                    <td>
                                        <a href="/movie/edit/<?php echo $movie['MaPhim']; ?>" 
                                           class="btn btn-sm btn-warning">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <a href="/movie/delete/<?php echo $movie['MaPhim']; ?>" 
                                           class="btn btn-sm btn-danger"
                                           onclick="return confirm('Bạn có chắc chắn muốn xóa phim này?')">
                                            <i class="bi bi-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 