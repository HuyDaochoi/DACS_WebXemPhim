<?php require_once 'app/Views/shares/header.php'; ?>
<?php

$selected_genres = array_column($movie['genres'] ?? [], 'MaTheLoai');
?>
<style>
    .edit-movie-page {
        background-color: #141414;
        color: #ffffff;
        font-family: 'Roboto', sans-serif;
    }

    .edit-movie-page .container {
        max-width: 900px;
        margin: 40px auto;
        padding: 20px;
    }

    .edit-movie-page .form-container {
        background-color: #181818;
        border-radius: 12px;
        padding: 30px;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.3);
    }

    .edit-movie-page .form-title {
        color: #E50914;
        font-weight: 600;
        margin-bottom: 20px;
    }

    .edit-movie-page .form-label {
        color: #ffffff;
        font-weight: 500;
        margin-bottom: 8px;
    }

    .edit-movie-page .form-control,
    .edit-movie-page .form-select {
        background-color: #333;
        border: 1px solid #444;
        color: #ffffff;
        border-radius: 6px;
    }

    .edit-movie-page .form-control:focus,
    .edit-movie-page .form-select:focus {
        border-color: #E50914;
        background-color: #333;
        color: #fff;
        box-shadow: none;
    }

    .edit-movie-page select option {
        background-color: #222;
        color: #fff;
    }

    .edit-movie-page select option:checked {
        background-color: #E50914;
        color: #fff;
    }

    .edit-movie-page .form-control::placeholder {
        color: #999;
    }

    .edit-movie-page .btn-netflix {
        background-color: #E50914;
        color: #ffffff;
        border: none;
        border-radius: 6px;
        padding: 10px 20px;
        font-weight: 500;
    }

    .edit-movie-page .btn-netflix:hover {
        background-color: #f40612;
    }

    .edit-movie-page .btn-cancel {
        background-color: #555;
        color: #ffffff;
        border: none;
        border-radius: 6px;
        padding: 10px 20px;
    }

    .edit-movie-page .btn-cancel:hover {
        background-color: #666;
    }

    .edit-movie-page .poster-preview img {
        max-width: 200px;
        border-radius: 8px;
        margin-top: 10px;
    }

    .edit-movie-page .alert {
        border-radius: 6px;
        margin-bottom: 20px;
    }

    .edit-movie-page .invalid-feedback {
        color: #ff6b6b;
        font-size: 0.85rem;
    }

    @media (max-width: 576px) {
        .edit-movie-page .form-container {
            padding: 20px;
        }

        .edit-movie-page .btn-netflix,
        .edit-movie-page .btn-cancel {
            width: 100%;
            margin-bottom: 10px;
        }
    }

    /* Select2 specific text color */
    .select2-container--default .select2-selection--multiple .select2-selection__choice {
        color: #000;
    }

    .select2-container--default .select2-results__option {
        color: #000;
    }

    .select2-container--default .select2-selection--multiple {
        color: #000;
    }
</style>

<div class="container edit-movie-page">
    <div class="form-container">
        <h2 class="form-title">Chỉnh sửa phim: <?php echo htmlspecialchars($movie['TenPhim']); ?></h2>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger"><?php echo $_SESSION['error'];
                                            unset($_SESSION['error']); ?></div>
        <?php endif; ?>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success"><?php echo $_SESSION['success'];
                                                unset($_SESSION['success']); ?></div>
        <?php endif; ?>

        <form action="/movie/update/<?php echo $movie['MaPhim']; ?>" method="POST" enctype="multipart/form-data" id="editMovieForm">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(generateCsrfToken()); ?>">

            <div class="row">
                <!-- Left -->
                <div class="col-md-6">
                    <!-- Tên phim -->
                    <div class="mb-3">
                        <label for="title" class="form-label">Tên phim *</label>
                        <input type="text" class="form-control" id="title" name="title" value="<?php echo htmlspecialchars($movie['TenPhim']); ?>" required minlength="3" maxlength="100">
                        <div class="invalid-feedback">Tên phim phải từ 3-100 ký tự</div>
                    </div>
                    <!-- Tiêu đề phụ -->
                    <div class="mb-3">
                        <label for="subtitle" class="form-label">Tiêu đề phụ</label>
                        <input type="text" class="form-control" id="subtitle" name="subtitle" value="<?php echo htmlspecialchars($movie['TieuDe'] ?? ''); ?>" maxlength="255">
                    </div>
                    <!-- Mô tả -->
                    <div class="mb-3">
                        <label for="description" class="form-label">Mô tả</label>
                        <textarea class="form-control" id="description" name="description" rows="4"><?php echo htmlspecialchars($movie['MoTa'] ?? ''); ?></textarea>
                    </div>
                    <!-- Nội dung -->
                    <div class="mb-3">
                        <label for="content" class="form-label">Nội dung</label>
                        <textarea class="form-control" id="content" name="content" rows="4"><?php echo htmlspecialchars($movie['NoiDung'] ?? ''); ?></textarea>
                    </div>
                    <!-- Năm phát hành -->
                    <div class="mb-3">
                        <label for="release_year" class="form-label">Năm phát hành</label>
                        <input type="number" class="form-control" id="release_year" name="release_year" value="<?php echo htmlspecialchars($movie['NamPhatHanh'] ?? ''); ?>" min="1900" max="<?php echo date('Y'); ?>">
                    </div>
                    <!-- Thời lượng -->
                    <div class="mb-3">
                        <label for="duration" class="form-label">Thời lượng (phút) *</label>
                        <input type="number" class="form-control" id="duration" name="duration" value="<?php echo htmlspecialchars($movie['ThoiLuong'] ?? ''); ?>" min="1" max="999" required>
                        <div class="invalid-feedback">Thời lượng phải từ 1-999 phút</div>
                    </div>
                </div>
                <!-- Right -->
                <div class="col-md-6">
                    <!-- Quốc gia -->
                    <div class="mb-3">
                        <label for="country_id" class="form-label">Quốc gia *</label>
                        <select class="form-select" id="country_id" name="country_id" required>
                            <option value="">Chọn quốc gia</option>
                            <?php foreach ($countries as $country): ?>
                                <option value="<?php echo $country['MaQuocGia']; ?>" <?php echo $country['MaQuocGia'] == $movie['MaQuocGia'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($country['TenQuocGia']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="invalid-feedback">Vui lòng chọn quốc gia</div>
                    </div>
                    <!-- Thể loại -->
                    <div class="mb-3">
                        <label for="genres" class="form-label">Thể loại *</label>
                        <select class="form-select" id="genres" name="genres[]" multiple required>
                            <?php foreach ($genres as $genre): ?>
                                <option value="<?php echo $genre['MaTheLoai']; ?>" <?php echo in_array($genre['MaTheLoai'], $selected_genres) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($genre['TenTheLoai']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="invalid-feedback">Vui lòng chọn ít nhất một thể loại</div>
                    </div>
                    <script>
                        $(document).ready(function() {
                            $('#genres').select2({
                                placeholder: "Chọn thể loại",
                                width: '100%'
                            });
                        });
                    </script>
                    <!-- Diễn viên, đạo diễn, trạng thái, phân loại -->
                    <div class="mb-3"><label class="form-label">Diễn viên</label><input type="text" class="form-control" name="actors" value="<?php echo htmlspecialchars($movie['actors'] ?? ''); ?>"></div>
                    <div class="mb-3"><label class="form-label">Đạo diễn</label><input type="text" class="form-control" name="directors" value="<?php echo htmlspecialchars($movie['directors'] ?? ''); ?>"></div>
                    <div class="mb-3"><label class="form-label">Trạng thái *</label><select class="form-select" name="status" required>
                            <option value="Đang chiếu" <?php echo $movie['TinhTrang'] == 'Đang chiếu' ? 'selected' : ''; ?>>Đang chiếu</option>
                            <option value="Sắp chiếu" <?php echo $movie['TinhTrang'] == 'Sắp chiếu' ? 'selected' : ''; ?>>Sắp chiếu</option>
                            <option value="Đã kết thúc" <?php echo $movie['TinhTrang'] == 'Đã kết thúc' ? 'selected' : ''; ?>>Đã kết thúc</option>
                        </select></div>
                    <div class="mb-3"><label class="form-label">Phân loại *</label><select class="form-select" name="type" required>
                            <option value="Lẻ" <?php echo $movie['PhanLoai'] == 'Lẻ' ? 'selected' : ''; ?>>Phim lẻ</option>
                            <option value="Bộ" <?php echo $movie['PhanLoai'] == 'Bộ' ? 'selected' : ''; ?>>Phim bộ</option>
                        </select></div>
                    <!-- Poster -->
                    <div class="mb-3">
                        <label class="form-label">Poster hiện tại</label>
                        <div class="poster-preview"><img src="/<?php echo htmlspecialchars($movie['HinhAnh']); ?>" alt="Poster hiện tại"></div>
                        <input type="hidden" name="current_poster" value="<?php echo htmlspecialchars($movie['HinhAnh']); ?>">
                    </div>
                    <div class="mb-3">
                        <label for="poster" class="form-label">Thay đổi poster</label>
                        <input type="file" class="form-control" id="poster" name="poster" accept="image/jpeg,image/png,image/gif">
                        <div class="invalid-feedback">Vui lòng chọn file ảnh (jpg, png, gif) dưới 5MB</div>
                    </div>
                    <!-- Trailer -->
                    <div class="mb-3">
                        <label for="trailer_url" class="form-label">Link trailer *</label>
                        <input type="url" class="form-control" id="trailer_url" name="trailer_url" value="<?php echo htmlspecialchars($movie['Link'] ?? ''); ?>" required pattern="https?://.+">
                        <div class="invalid-feedback">Vui lòng nhập URL hợp lệ</div>
                    </div>
                </div>
            </div>
            <div class="d-flex gap-2 mt-4">
                <button type="submit" class="btn btn-netflix">Cập nhật</button>
                <a href="/movie/list" class="btn btn-cancel">Hủy</a>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('editMovieForm');
        const posterInput = document.getElementById('poster');
        const preview = document.querySelector('.poster-preview img');

        form.addEventListener('submit', function(e) {
            const title = document.getElementById('title').value.trim();
            const genres = document.getElementById('genres').selectedOptions;
            const country = document.getElementById('country_id').value;
            const trailerUrl = document.getElementById('trailer_url').value.trim();

            if (!title || genres.length === 0 || !country || !trailerUrl) {
                e.preventDefault();
                alert('Vui lòng điền đầy đủ thông tin bắt buộc!');
            }

            const poster = posterInput.files[0];
            if (poster) {
                const validTypes = ['image/jpeg', 'image/png', 'image/gif'];
                if (!validTypes.includes(poster.type)) {
                    e.preventDefault();
                    alert('Poster phải là ảnh (JPEG, PNG, GIF)');
                }
                if (poster.size > 5 * 1024 * 1024) {
                    e.preventDefault();
                    alert('Poster vượt quá 5MB');
                }
            }
        });

        posterInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(event) {
                    preview.src = event.target.result;
                };
                reader.readAsDataURL(file);
            }
        });
    });
</script>

<?php require_once 'app/Views/shares/footer.php'; ?>