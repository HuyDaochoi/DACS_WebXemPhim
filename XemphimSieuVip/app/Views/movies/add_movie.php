<?php
require_once 'app/Views/shares/header.php';
?>

<style>
body {
    background-color: #141414;
    color: #ffffff;
    font-family: 'Roboto', sans-serif;
}

.container {
    max-width: 1200px;
    padding: 20px;
}

.card {
    background-color: #181818;
    border-radius: 12px;
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.3);
    padding: 20px;
}

.card-header {
    background: transparent;
    border-bottom: 1px solid #333;
    padding: 20px;
}

.card-header h4 {
    color: #E50914;
    font-weight: 600;
    margin: 0;
}

.form-control, .select2-container--default .select2-selection--multiple {
    background-color: #333;
    border: 1px solid #444;
    color: #ffffff;
    border-radius: 6px;
    transition: border-color 0.2s;
}

.form-control:focus, .select2-container--default .select2-selection--multiple:focus {
    border-color: #E50914;
    box-shadow: none;
}

.form-control::placeholder {
    color: #999;
}

.btn-netflix {
    background-color: #E50914;
    color: #ffffff;
    border: none;
    border-radius: 6px;
    padding: 10px 20px;
    font-weight: 500;
    transition: background-color 0.2s;
}

.btn-netflix:hover {
    background-color: #f40612;
}

.btn-secondary {
    background-color: #333;
    border: 1px solid #444;
    color: #ffffff;
    border-radius: 6px;
}

.btn-secondary:hover {
    background-color: #444;
}

.select2-container--default .select2-selection--multiple .select2-selection__choice {
    background-color: #E50914;
    border: 1px solid #f40612;
    color: #ffffff;
}

.select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
    color: #ffffff;
}

.alert {
    border-radius: 6px;
    margin-bottom: 20px;
}

.form-label {
    color: #ffffff;
    font-weight: 500;
    margin-bottom: 8px;
}

.invalid-feedback {
    color: #ff6b6b;
    font-size: 0.85rem;
}

.preview-img {
    max-width: 200px;
    border-radius: 6px;
    margin-top: 10px;
}

@media (max-width: 768px) {
    .container {
        padding: 10px;
    }
    .card {
        padding: 15px;
    }
}
</style>

<div class="container mt-4">
    <div class="card">
        <div class="card-header">
            <h4>Thêm Phim Mới</h4>
        </div>
        <div class="card-body">
            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger">
                    <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                </div>
            <?php endif; ?>

            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success">
                    <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
                </div>
            <?php endif; ?>

            <form action="/movie/save" method="POST" enctype="multipart/form-data" id="addMovieForm">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="title" class="form-label">Tên Phim *</label>
                            <input type="text" class="form-control" id="title" name="title" required minlength="3" maxlength="100">
                            <div class="invalid-feedback">Tên phim phải từ 3-100 ký tự</div>
                        </div>

                        <div class="mb-3">
                            <label for="subtitle" class="form-label">Tiêu Đề (Tên Tiếng Anh)</label>
                            <input type="text" class="form-control" id="subtitle" name="subtitle" maxlength="255">
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Mô Tả</label>
                            <textarea class="form-control" id="description" name="description" rows="4"></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="content" class="form-label">Nội Dung</label>
                            <textarea class="form-control" id="content" name="content" rows="4"></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="release_year" class="form-label">Năm Phát Hành</label>
                            <input type="number" class="form-control" id="release_year" name="release_year" min="1900" max="<?php echo date('Y'); ?>">
                        </div>

                        <div class="mb-3">
                            <label for="duration" class="form-label">Thời Lượng (phút) *</label>
                            <input type="number" class="form-control" id="duration" name="duration" min="1" max="999" required>
                            <div class="invalid-feedback">Thời lượng phải từ 1-999 phút</div>
                        </div>

                        <div class="mb-3">
                            <label for="country_id" class="form-label">Quốc Gia *</label>
                            <select class="form-control" id="country_id" name="country_id" required>
                                <option value="">Chọn quốc gia</option>
                                <?php foreach ($countries as $country): ?>
                                    <option value="<?php echo $country['MaQuocGia']; ?>">
                                        <?php echo htmlspecialchars($country['TenQuocGia']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="invalid-feedback">Vui lòng chọn quốc gia</div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="genres" class="form-label">Thể Loại *</label>
                            <select class="form-control" id="genres" name="genres[]" multiple required>
                                <?php foreach ($genres as $genre): ?>
                                    <option value="<?php echo $genre['MaTheLoai']; ?>">
                                        <?php echo htmlspecialchars($genre['TenTheLoai']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <small class="form-text text-muted" >Giữ phím Ctrl để chọn nhiều thể loại</small>
                            <div class="invalid-feedback">Vui lòng chọn ít nhất một thể loại</div>
                        </div>

                        <div class="mb-3">
                            <label for="actors" class="form-label">Diễn Viên</label>
                            <input type="text" class="form-control" id="actors" name="actors" placeholder="Nhập tên diễn viên, cách nhau bởi dấu phẩy">
                            <small class="form-text text-muted">Ví dụ: Tom Hanks, Brad Pitt</small>
                        </div>

                        <div class="mb-3">
                            <label for="directors" class="form-label">Đạo Diễn</label>
                            <input type="text" class="form-control" id="directors" name="directors" placeholder="Nhập tên đạo diễn, cách nhau bởi dấu phẩy">
                            <small class="form-text text-muted">Ví dụ: Steven Spielberg, Christopher Nolan</small>
                        </div>

                        <div class="mb-3">
                            <label for="poster" class="form-label">Poster Phim *</label>
                            <input type="file" class="form-control" id="poster" name="poster" accept="image/*" required>
                            <div class="invalid-feedback">Vui lòng chọn file ảnh (jpg, png, gif) dưới 5MB</div>
                            <img id="poster-preview" class="preview-img d-none" src="" alt="Poster Preview">
                        </div>

                        <div class="mb-3">
                            <label for="trailer_url" class="form-label">Link Phim *</label>
                            <input type="url" class="form-control" id="trailer_url" name="trailer_url" required pattern="https?://.+">
                            <div class="invalid-feedback">Vui lòng nhập URL trailer hợp lệ</div>
                        </div>

                        <div class="mb-3">
                            <label for="status" class="form-label">Trạng Thái *</label>
                            <select class="form-control" id="status" name="status" required>
                                <option value="Đang chiếu">Đang chiếu</option>
                                <option value="Sắp chiếu">Sắp chiếu</option>
                                <option value="Đã kết thúc">Đã kết thúc</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="type" class="form-label">Phân Loại *</label>
                            <select class="form-control" id="type" name="type" required>
                                <option value="Lẻ">Phim lẻ</option>
                                
                            </select>
                        </div>
                    </div>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-netflix" id="submitBtn">
                        <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                        Thêm Phim
                    </button>
                    <a href="/movie/list" class="btn btn-secondary ms-2">Hủy</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Select2 for genres
    $('#genres').select2({
        placeholder: 'Chọn thể loại...',
        width: '100%',
        closeOnSelect: false,
        theme: 'bootstrap-5'
    });

    // Poster preview
    document.getElementById('poster').addEventListener('change', function(e) {
        const file = e.target.files[0];
        const preview = document.getElementById('poster-preview');
        if (file) {
            // Validate file size (5MB)
            if (file.size > 5 * 1024 * 1024) {
                this.setCustomValidity('File ảnh không được vượt quá 5MB');
                preview.classList.add('d-none');
                return;
            }

            // Validate file type
            const validTypes = ['image/jpeg', 'image/png', 'image/gif'];
            if (!validTypes.includes(file.type)) {
                this.setCustomValidity('Chỉ chấp nhận file ảnh (jpg, png, gif)');
                preview.classList.add('d-none');
                return;
            }

            this.setCustomValidity('');
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.classList.remove('d-none');
            };
            reader.readAsDataURL(file);
        } else {
            preview.classList.add('d-none');
        }
    });

    // Form submission handling
    document.getElementById('addMovieForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const submitBtn = document.getElementById('submitBtn');
        const spinner = submitBtn.querySelector('.spinner-border');
        spinner.classList.remove('d-none');
        submitBtn.disabled = true;

        const formData = new FormData(this);
        const selectedGenres = Array.from(document.getElementById('genres').selectedOptions).map(option => option.value);
        
        if (selectedGenres.length === 0) {
            alert('Vui lòng chọn ít nhất một thể loại');
            spinner.classList.add('d-none');
            submitBtn.disabled = false;
            return;
        }

        fetch('/movie/save', {
            method: 'POST',
            body: formData
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            const cardBody = document.querySelector('.card-body');
            const existingAlerts = cardBody.querySelectorAll('.alert');
            existingAlerts.forEach(alert => alert.remove());

            const alertDiv = document.createElement('div');
            alertDiv.className = `alert ${data.success ? 'alert-success' : 'alert-danger'}`;
            alertDiv.textContent = data.message || 'Thao tác thành công';
            cardBody.insertBefore(alertDiv, cardBody.firstChild);

            if (data.success) {
                setTimeout(() => {
                    window.location.href = '/movie/list';
                }, 2000);
            } else {
                spinner.classList.add('d-none');
                submitBtn.disabled = false;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            const cardBody = document.querySelector('.card-body');
            const existingAlerts = cardBody.querySelectorAll('.alert');
            existingAlerts.forEach(alert => alert.remove());

            const alertDiv = document.createElement('div');
            alertDiv.className = 'alert alert-danger';
            alertDiv.textContent = 'Có lỗi xảy ra. Vui lòng thử lại.';
            cardBody.insertBefore(alertDiv, cardBody.firstChild);

            spinner.classList.add('d-none');
            submitBtn.disabled = false;
        });
    });

    // Trailer URL validation
    document.getElementById('trailer_url').addEventListener('input', function(e) {
        const url = e.target.value;
        if (url && !url.match(/^https?:\/\/.+/)) {
            this.setCustomValidity('URL phải bắt đầu bằng http:// hoặc https://');
        } else {
            this.setCustomValidity('');
        }
    });
});
</script>

<?php require_once 'app/Views/shares/footer.php'; ?>