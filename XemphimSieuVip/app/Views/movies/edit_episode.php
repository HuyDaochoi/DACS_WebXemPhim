<?php require_once 'app/Views/shares/header.php'; ?>
<div class="container mt-4">
    <h3>Sửa tập phim</h3>
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
    <?php endif; ?>
    <form method="POST">
        <div class="mb-3">
            <label class="form-label">Tên tập</label>
            <input type="text" class="form-control" name="ten_tap" value="<?= htmlspecialchars($episode['TenTap']) ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Link tập phim</label>
            <input type="url" class="form-control" name="link" value="<?= htmlspecialchars($episode['Link']) ?>" required>
        </div>
        <button type="submit" class="btn btn-primary">Lưu thay đổi</button>
        <a href="/movie/manageEpisodes/<?= $episode['MaPhim'] ?>" class="btn btn-secondary">Hủy</a>
    </form>
</div>
<?php require_once 'app/Views/shares/footer.php'; ?>