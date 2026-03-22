<?php require_once 'app/Views/shares/header.php'; ?>

<div class="container mt-4">
    <h3>Quản lý tập phim: <?php echo htmlspecialchars($movie['TenPhim']); ?></h3>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success">
            <?php echo $_SESSION['success'];
            unset($_SESSION['success']); ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger">
            <?php echo $_SESSION['error'];
            unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>

    <!-- Form thêm tập mới -->
    <form action="/movie/addEpisode/<?php echo $movie['MaPhim']; ?>" method="POST" class="mb-4">
        <div class="row">
            <div class="col-md-5">
                <input type="text" class="form-control" name="ten_tap" placeholder="Tên tập (VD: Tập 1)" required>
            </div>
            <div class="col-md-5">
                <input type="url" class="form-control" name="link" placeholder="Link tập phim" required>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">Thêm Tập</button>
            </div>
        </div>
    </form>

    <!-- Danh sách tập phim -->
    <table class="table table-striped">
        <thead>
            <tr>
                <th>#</th>
                <th>Tên tập</th>
                <th>Link</th>
                <th>Thao tác</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($episodes as $episode): ?>
                <tr>
                    <td><?php echo $episode['MaTap']; ?></td>
                    <td><?php echo htmlspecialchars($episode['TenTap']); ?></td>
                    <td>
                        <a href="<?php echo htmlspecialchars($episode['Link']); ?>" target="_blank">
                            <?php echo htmlspecialchars($episode['Link']); ?>
                        </a>
                    </td>
                    <td>
                        <a href="/editphimbo/editEpisode/<?php echo $episode['MaTap']; ?>" class="btn btn-warning btn-sm">Sửa</a>
                        <a href="/editphimbo/deleteEpisode/<?php echo $episode['MaTap']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Bạn có chắc chắn muốn xóa tập này?');">Xóa</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php require_once 'app/Views/shares/footer.php'; ?>