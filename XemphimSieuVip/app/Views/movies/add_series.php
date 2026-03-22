<?php require_once 'app/Views/shares/header.php'; ?>

<div class="container mt-4">
    <h3>Thêm Phim Bộ</h3>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger">
            <?php echo $_SESSION['error'];
            unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success">
            <?php echo $_SESSION['success'];
            unset($_SESSION['success']); ?>
        </div>
    <?php endif; ?>

    <form action="/movie/saveSeries" method="POST" enctype="multipart/form-data">
        <div class="mb-3">
            <label for="title" class="form-label">Tên Phim *</label>
            <input type="text" class="form-control" id="title" name="title" required>
        </div>

        <div class="mb-3">
            <label for="subtitle" class="form-label">Tiêu Đề Phụ</label>
            <input type="text" class="form-control" id="subtitle" name="subtitle">
        </div>

        <div class="mb-3">
            <label for="description" class="form-label">Mô Tả</label>
            <textarea class="form-control" id="description" name="description" rows="4"></textarea>
        </div>

        <div class="mb-3">
            <label for="poster" class="form-label">Poster *</label>
            <input type="file" class="form-control" id="poster" name="poster" accept="image/*" required>
        </div>

        <div class="mb-3">
            <label for="trailer_url" class="form-label">Link Phim</label>
            <input type="url" class="form-control" id="trailer_url" name="trailer_url">
        </div>

        <div class="mb-3">
            <label for="release_year" class="form-label">Năm Phát Hành</label>
            <input type="number" class="form-control" id="release_year" name="release_year" min="1900" max="<?php echo date('Y'); ?>">
        </div>

        <div class="mb-3">
            <label for="duration" class="form-label">Thời Lượng (phút)</label>
            <input type="number" class="form-control" id="duration" name="duration" min="1">
        </div>

        <div class="mb-3">
            <label for="country_id" class="form-label">Quốc Gia</label>
            <select class="form-control" id="country_id" name="country_id">
                <?php foreach ($countries as $country): ?>
                    <option value="<?php echo $country['MaQuocGia']; ?>"><?php echo htmlspecialchars($country['TenQuocGia']); ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-3">
            <label for="genres" class="form-label">Thể Loại *</label>
            <select class="form-control" id="genres" name="genres[]" multiple required>
                <?php foreach ($genres as $genre): ?>
                    <option value="<?php echo $genre['MaTheLoai']; ?>"><?php echo htmlspecialchars($genre['TenTheLoai']); ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-3">
            <label for="actors" class="form-label">Diễn Viên</label>
            <input type="text" class="form-control" id="actors" name="actors" placeholder="Nhập tên diễn viên, cách nhau bởi dấu phẩy">
        </div>

        <div class="mb-3">
            <label for="directors" class="form-label">Đạo Diễn</label>
            <input type="text" class="form-control" id="directors" name="directors" placeholder="Nhập tên đạo diễn, cách nhau bởi dấu phẩy">
        </div>

        <div class="mb-3">
            <label for="episodes" class="form-label">Danh Sách Tập Phim *</label>
            <textarea class="form-control" id="episodes" name="episodes" rows="5" placeholder="Nhập danh sách tập phim, mỗi dòng một tập. Ví dụ:
Tập 1|https://example.com/video1
Tập 2|https://example.com/video2" required></textarea>
        </div>

        <button type="submit" class="btn btn-primary">Thêm Phim Bộ</button>
        <a href="/movie/list" class="btn btn-secondary">Hủy</a>
    </form>
</div>

<?php require_once 'app/Views/shares/footer.php'; ?>