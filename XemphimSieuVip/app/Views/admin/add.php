<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thêm phim mới - Xemp</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .preview-image {
            max-width: 200px;
            max-height: 300px;
            object-fit: cover;
            margin-top: 1rem;
            display: none;
        }
    </style>
</head>
<body>
    <?php require_once 'app/Views/shares/header.php'; ?>

    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Thêm phim mới</h3>
                    </div>
                    <div class="card-body">
                        <?php if (isset($_SESSION['error'])): ?>
                            <div class="alert alert-danger">
                                <?php 
                                echo $_SESSION['error'];
                                unset($_SESSION['error']);
                                ?>
                            </div>
                        <?php endif; ?>

                        <form action="/movie/add" method="POST" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label for="title" class="form-label">Tên phim <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="title" name="title" required>
                            </div>

                            <div class="mb-3">
                                <label for="description" class="form-label">Mô tả</label>
                                <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                            </div>

                            <div class="mb-3">
                                <label for="content" class="form-label">Nội dung</label>
                                <textarea class="form-control" id="content" name="content" rows="5"></textarea>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="release_year" class="form-label">Năm phát hành <span class="text-danger">*</span></label>
                                        <input type="number" class="form-control" id="release_year" name="release_year" min="1900" max="<?php echo date('Y'); ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="duration" class="form-label">Thời lượng (phút) <span class="text-danger">*</span></label>
                                        <input type="number" class="form-control" id="duration" name="duration" min="1" required>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="country" class="form-label">Quốc gia <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="country" name="country" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="status" class="form-label">Trạng thái</label>
                                        <select class="form-select" id="status" name="status">
                                            <option value="Đang chiếu">Đang chiếu</option>
                                            <option value="Sắp chiếu">Sắp chiếu</option>
                                            <option value="Đã kết thúc">Đã kết thúc</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="type" class="form-label">Phân loại</label>
                                        <select class="form-select" id="type" name="type">
                                            <option value="Lẻ">Phim lẻ</option>
                                            <option value="Bộ">Phim bộ</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="genre" class="form-label">Thể loại <span class="text-danger">*</span></label>
                                        <select class="form-select" id="genre" name="genre[]" multiple required>
                                            <?php foreach ($genres as $genre): ?>
                                                <option value="<?php echo $genre['MaTheLoai']; ?>"><?php echo $genre['TenTheLoai']; ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="poster" class="form-label">Poster <span class="text-danger">*</span></label>
                                <input type="file" class="form-control" id="poster" name="poster" accept="image/*" required>
                                <img id="poster_preview" class="preview-image" src="#" alt="Preview">
                            </div>

                            <div class="mb-3">
                                <label for="trailer_url" class="form-label">Link trailer</label>
                                <input type="url" class="form-control" id="trailer_url" name="trailer_url">
                            </div>

                            <div class="text-end">
                                <a href="/movie/manage" class="btn btn-secondary">Hủy</a>
                                <button type="submit" class="btn btn-primary">Thêm phim</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php require_once 'app/Views/shares/footer.php'; ?>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Xem trước ảnh khi chọn file
        function readURL(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    $('#poster_preview').attr('src', e.target.result).show();
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        $("#poster").change(function() {
            readURL(this);
        });

        // Cho phép chọn nhiều thể loại
        $(document).ready(function() {
            $('#genre').select2({
                placeholder: 'Chọn thể loại',
                allowClear: true
            });
        });
    </script>
</body>
</html>