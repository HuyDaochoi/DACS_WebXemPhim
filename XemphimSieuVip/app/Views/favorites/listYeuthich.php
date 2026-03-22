<?php require_once 'app/Views/shares/header.php'; ?>

<style>
    body {
        background-color: #161616; /* Nền đen */
        color: #ffffff; /* Chữ trắng */
    }

    .card {
        background-color: #1e1e1e; /* Nền thẻ đen nhạt */
        border: 1px solid #333333; /* Viền đen đậm */
    }

    .card-title {
        color: #ffffff; /* Chữ trắng */
    }

    .btn {
        color: #ffffff; /* Chữ trắng */
        background-color: var(--primary-color); /* Màu chính */
        border-color: var(--primary-color);
    }

    .btn:hover {
        background-color: #c11119; /* Màu khi hover */
        border-color: #c11119;
    }
</style>

<div class="container mt-4">
    <h3 class="text-warning">🎬 Danh sách phim yêu thích</h3>
    <?php if (empty($movies)): ?>
        <p class="text-light">Bạn chưa thêm phim nào vào danh sách yêu thích.</p>
    <?php else: ?>
        <div class="row">
            <?php foreach ($movies as $movie): ?>
                <div class="col-md-3 mb-4">
                    <div class="card bg-dark text-white">
                        <img src="/<?php echo $movie['HinhAnh']; ?>" class="card-img-top" alt="<?php echo $movie['TenPhim']; ?>">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo $movie['TenPhim']; ?></h5>
                            <a href="/Movie/view/<?php echo $movie['MaPhim']; ?>" class="btn btn-warning btn-sm">Xem</a>
                            <a href="/YeuThich/toggleYeuThich?id=<?php echo $movie['MaPhim']; ?>" class="btn btn-outline-light btn-sm">Bỏ thích</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php require_once 'app/Views/shares/footer.php'; ?>