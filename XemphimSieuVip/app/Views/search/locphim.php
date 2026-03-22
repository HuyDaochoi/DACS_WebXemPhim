<?php require_once 'app/Views/shares/header.php'; ?>
<div class="container mt-4">
    <div class="card bg-dark text-white mb-4">
        <div class="card-body">
            <form method="get" action="/search/locphim" class="row g-3 align-items-end">
                <div class="col-md-6">
                    <label class="form-label text-white">Chọn thể loại:</label>
                    <select name="genres[]" multiple class="form-control">
                        <?php foreach ($genres as $g): ?>
                            <option value="<?php echo $g['MaTheLoai']; ?>"
                                <?php echo (isset($_GET['genres']) && in_array($g['MaTheLoai'], (array)$_GET['genres'])) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($g['TenTheLoai']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label text-white">Chọn quốc gia:</label>
                    <select name="country" class="form-control">
                        <option value="">-- Tất cả --</option>
                        <?php foreach ($countries as $c): ?>
                            <option value="<?php echo $c['MaQuocGia']; ?>"
                                <?php echo (isset($_GET['country']) && $_GET['country'] == $c['MaQuocGia']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($c['TenQuocGia']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-danger w-100">Lọc phim</button>
                </div>
            </form>
        </div>
    </div>

    <div class="row">
        <?php if (empty($movies)): ?>
            <div class="alert alert-info text-center">Không tìm thấy phim phù hợp.</div>
        <?php else: ?>
            <?php foreach ($movies as $movie): ?>
                <div class="col-md-3 mb-4">
                    <div class="movie-card h-100">
                        <a href="/movie/view/<?php echo $movie['MaPhim']; ?>">
                            <img src="/<?php echo $movie['HinhAnh']; ?>" alt="<?php echo htmlspecialchars($movie['TenPhim']); ?>" class="movie-card__image w-100">
                            <div class="movie-card__content p-2">
                                <div class="movie-card__title text-white mb-1"><?php echo htmlspecialchars($movie['TenPhim']); ?></div>
                                <div class="movie-card__info"><?php echo $movie['NamPhatHanh']; ?> • <?php echo $movie['ThoiLuong']; ?> phút</div>
                            </div>
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        $('select[name="genres[]"]').select2({
            placeholder: "Chọn thể loại",
            width: '100%'
        });
        $('select[name="country"]').select2({
            placeholder: "Chọn quốc gia",
            width: '100%'
        });
    });
</script>

<style>
    .card.bg-dark {
        background: #181818 !important;
        border: 1px solid #232323;
        color: #fff;
    }

    .card-header,
    .card-body {
        background: transparent !important;
        color: #fff;
    }

    .form-label,
    .select2-container--default .select2-selection--multiple,
    .select2-container--default .select2-selection--single {
        color: #fff !important;
        background: #232323 !important;
        border: 1px solid #333 !important;
    }

    .select2-container--default .select2-selection--multiple {
        min-height: 38px;
    }

    .select2-container--default .select2-selection--multiple .select2-selection__choice {
        background-color: #ff4757 !important;
        color: #fff !important;
        border: none;
    }

    .select2-container--default .select2-selection--single .select2-selection__rendered {
        color: #fff !important;
    }

    .select2-dropdown {
        background: #232323 !important;
        color: #fff !important;
    }

    .select2-results__option {
        color: #fff !important;
        background: #232323 !important;
    }

    .select2-results__option--highlighted {
        background: #ff4757 !important;
        color: #fff !important;
    }

    .btn-danger,
    .btn-danger:focus {
        background: #ff4757 !important;
        border: none;
        color: #fff;
    }

    .btn-danger:hover {
        background: #ff6b81 !important;
        color: #fff;
    }

    .movie-card {
        background: #1a1a1a;
        border-radius: 8px;
        overflow: hidden;
        transition: transform 0.3s;
    }

    .movie-card:hover {
        transform: translateY(-5px);
    }

    .movie-card__image {
        height: 300px;
        object-fit: cover;
    }

    .movie-card__title {
        font-size: 1rem;
        font-weight: 600;
        line-height: 1.4;
        height: 2.8em;
        overflow: hidden;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
    }
</style>
<?php require_once 'app/Views/shares/footer.php'; ?>