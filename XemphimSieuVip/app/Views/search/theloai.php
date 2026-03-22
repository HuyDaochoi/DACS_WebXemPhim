<?php
if (!isset($movies) || !is_array($movies)) $movies = [];
if (!isset($total)) $total = 0;
if (!isset($totalPages)) $totalPages = 1;
?>
<?php require_once 'app/Views/shares/header.php'; ?>

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<style>
    :root {
        --primary-color: #E50914;
        /* Netflix red */
        --secondary-color: #B20710;
        /* Darker red for hover */
        --bg-color: #141414;
        /* Netflix dark background */
        --text-color: #FFFFFF;
        /* White text */
        --text-muted: #B3B3B3;
        /* Muted gray text */
        --card-bg: #1F1F1F;
        /* Slightly lighter dark for cards */
        --border-color: #333333;
        /* Dark border */
    }

    body {
        background-color: var(--bg-color);
        color: var(--text-color);
        font-family: 'Roboto', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
        margin: 0;
    }

    .container {
        max-width: 1400px;
        padding: 20px 15px;
    }

    /* Filter Form */
    .filter-card {
        background-color: var(--card-bg);
        border: none;
        border-radius: 6px;
        padding: 15px;
        margin-bottom: 20px;
    }

    .filter-form {
        display: flex;
        gap: 15px;
        align-items: flex-end;
    }

    .filter-form .form-group {
        flex: 1;
    }

    .filter-form label {
        font-size: 13px;
        color: var(--text-color);
        margin-bottom: 5px;
        display: block;
    }

    .filter-form select {
        background-color: #2A2A2A;
        border: 1px solid var(--border-color);
        color: var(--text-color);
        font-size: 13px;
        padding: 8px;
        border-radius: 4px;
        width: 100%;
    }

    .filter-form select:focus {
        border-color: var(--primary-color);
        outline: none;
        box-shadow: 0 0 0 2px rgba(229, 9, 20, 0.2);
    }

    .filter-form .btn {
        background-color: var(--primary-color);
        border: none;
        padding: 8px 16px;
        font-size: 13px;
        font-weight: 500;
        border-radius: 4px;
        color: var(--text-color);
        transition: background-color 0.2s;
    }

    .filter-form .btn:hover {
        background-color: var(--secondary-color);
    }

    /* Select2 Styling */
    .select2-container--default .select2-selection--multiple,
    .select2-container--default .select2-selection--single {
        background-color: #2A2A2A;
        border: 1px solid var(--border-color);
        border-radius: 4px;
        min-height: 34px;
    }

    .select2-container--default .select2-selection--single .select2-selection__rendered {
        color: var(--text-color);
        line-height: 34px;
        padding-left: 10px;
    }

    .select2-container--default .select2-selection--multiple .select2-selection__choice {
        background-color: var(--primary-color);
        border: none;
        color: var(--text-color);
        font-size: 12px;
        padding: 2px 8px;
        margin: 4px 2px;
    }

    .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
        color: var(--text-color);
        margin-right: 5px;
    }

    .select2-dropdown {
        background-color: #2A2A2A;
        border: 1px solid var(--border-color);
        border-radius: 4px;
    }

    .select2-results__option {
        color: var(--text-color);
        font-size: 13px;
        padding: 6px 10px;
    }

    .select2-results__option--highlighted {
        background-color: var(--primary-color);
        color: var(--text-color);
    }

    /* Header Section */
    .header-section {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 15px;
    }

    .header-section h2 {
        font-size: 20px;
        font-weight: 600;
        color: var(--text-color);
        margin: 0;
    }

    .sort-dropdown .btn {
        background-color: #2A2A2A;
        border: 1px solid var(--border-color);
        color: var(--text-color);
        font-size: 13px;
        padding: 6px 12px;
        border-radius: 4px;
    }

    .sort-dropdown .btn:hover {
        background-color: #333333;
    }

    .sort-dropdown .dropdown-menu {
        background-color: #2A2A2A;
        border: 1px solid var(--border-color);
        border-radius: 4px;
        min-width: 120px;
    }

    .sort-dropdown .dropdown-item {
        color: var(--text-color);
        font-size: 13px;
        padding: 6px 12px;
    }

    .sort-dropdown .dropdown-item:hover {
        background-color: var(--primary-color);
        color: var(--text-color);
    }

    /* Movie Card */
    .movie-card {
        background-color: var(--card-bg);
        border-radius: 6px;
        overflow: hidden;
        transition: transform 0.2s ease;
    }

    .movie-card:hover {
        transform: scale(1.05);
    }

    .movie-card__image {
        width: 100%;
        height: 220px;
        object-fit: cover;
        display: block;
    }

    .movie-card__overlay {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        background: linear-gradient(to top, rgba(0, 0, 0, 0.7), transparent);
        padding: 10px;
        opacity: 0;
        transition: opacity 0.2s ease;
    }

    .movie-card:hover .movie-card__overlay {
        opacity: 1;
    }

    .movie-card__content {
        padding: 8px;
    }

    .movie-card__title {
        font-size: 14px;
        font-weight: 600;
        color: var(--text-color);
        margin-bottom: 4px;
        line-height: 1.3;
        max-height: 2.6em;
        overflow: hidden;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        line-clamp: 2;
        -webkit-box-orient: vertical;
    }

    .movie-card__info .badge {
        font-size: 11px;
        padding: 4px 8px;
        margin-right: 4px;
        background-color: rgba(255, 255, 255, 0.15);
        color: var(--text-color);
    }

    .movie-card__tags .badge {
        font-size: 11px;
        padding: 4px 8px;
        margin: 2px 4px 2px 0;
        background-color: rgba(255, 255, 255, 0.15);
        color: var(--text-color);
    }

    .movie-badge {
        position: absolute;
        top: 6px;
        left: 6px;
        background: var(--primary-color);
        color: var(--text-color);
        padding: 3px 6px;
        border-radius: 3px;
        font-size: 9px;
        font-weight: 700;
        text-transform: uppercase;
    }

    /* Pagination */
    .pagination {
        margin-top: 20px;
    }

    .page-link {
        background-color: #2A2A2A;
        border: 1px solid var(--border-color);
        color: var(--text-color);
        font-size: 13px;
        padding: 6px 12px;
        border-radius: 4px;
        margin: 0 2px;
        transition: background-color 0.2s, color 0.2s;
    }

    .page-link:hover {
        background-color: var(--primary-color);
        color: var(--text-color);
        border-color: var(--primary-color);
    }

    .page-item.active .page-link {
        background-color: var(--primary-color);
        border-color: var(--primary-color);
        color: var(--text-color);
    }

    /* No Results */
    .alert {
        background-color: var(--card-bg);
        border: 1px solid var(--border-color);
        color: var(--text-color);
        font-size: 14px;
        text-align: center;
        padding: 15px;
        border-radius: 6px;
    }

    /* Responsive Design */
    @media (max-width: 992px) {
        .filter-form {
            flex-direction: column;
            align-items: stretch;
            gap: 10px;
        }

        .filter-form .form-group {
            flex: none;
        }

        .movie-card__image {
            height: 180px;
        }
    }

    @media (max-width: 576px) {
        .container {
            padding: 15px 10px;
        }

        .header-section h2 {
            font-size: 18px;
        }

        .movie-card__image {
            height: 150px;
        }

        .movie-card__title {
            font-size: 13px;
        }

        .movie-card__info .badge,
        .movie-card__tags .badge {
            font-size: 10px;
            padding: 3px 6px;
        }

        .filter-form label {
            font-size: 12px;
        }

        .filter-form select,
        .filter-form .btn {
            font-size: 12px;
            padding: 6px;
        }

        .sort-dropdown .btn {
            font-size: 12px;
            padding: 5px 10px;
        }
    }
</style>

<div class="container">
    <!-- Filter Form -->
    <div class="card filter-card">
        <div class="card-body">
            <form method="get" action="/search/locphim" class="filter-form">
                <div class="form-group">
                    <label>Thể loại</label>
                    <select name="genres[]" multiple class="form-control">
                        <?php foreach ($genres as $g): ?>
                            <option value="<?php echo $g['MaTheLoai']; ?>"
                                <?php echo (isset($_GET['genres']) && in_array($g['MaTheLoai'], (array)$_GET['genres'])) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($g['TenTheLoai']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Quốc gia</label>
                    <select name="country" class="form-control">
                        <option value="">Tất cả</option>
                        <?php foreach ($countries as $c): ?>
                            <option value="<?php echo $c['MaQuocGia']; ?>"
                                <?php echo (isset($_GET['country']) && $_GET['country'] == $c['MaQuocGia']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($c['TenQuocGia']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="submit" class="btn">Tìm</button>
            </form>
        </div>
    </div>

    <!-- Main Content -->
    <div class="header-section">
        <h2>
            <?php echo htmlspecialchars($genre['TenTheLoai']); ?>
            <?php if (isset($_GET['country'])): ?>
                - <?php echo htmlspecialchars($countries[array_search($_GET['country'], array_column($countries, 'MaQuocGia'))]['TenQuocGia']); ?>
            <?php endif; ?>
        </h2>
        <div class="sort-dropdown btn-group">
            <button type="button" class="btn dropdown-toggle" data-bs-toggle="dropdown">
                Sắp xếp
            </button>
            <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="?sort=newest">Mới nhất</a></li>
                <li><a class="dropdown-item" href="?sort=oldest">Cũ nhất</a></li>
                <li><a class="dropdown-item" href="?sort=name_asc">Tên A-Z</a></li>
                <li><a class="dropdown-item" href="?sort=name_desc">Tên Z-A</a></li>
                <li><a class="dropdown-item" href="?sort=views">Lượt xem</a></li>
            </ul>
        </div>
    </div>

    <?php if (empty($movies)): ?>
        <div class="alert">
            Không tìm thấy phim trong thể loại này.
        </div>
    <?php else: ?>
        <div class="row row-cols-2 row-cols-md-3 row-cols-lg-5 g-3">
            <?php foreach ($movies as $movie): ?>
                <div class="col">
                    <div class="movie-card">
                        <?php if (isset($movie['TinhTrang']) && $movie['TinhTrang'] === 'Đang chiếu'): ?>
                            <div class="movie-badge">Hot</div>
                        <?php endif; ?>
                        <a href="/movie/view/<?php echo $movie['MaPhim']; ?>" class="text-decoration-none">
                            <div class="position-relative">
                                <img src="<?php echo !empty($movie['HinhAnh']) ? '/' . $movie['HinhAnh'] : 'https://via.placeholder.com/160x220?text=No+Image'; ?>"
                                    alt="<?php echo htmlspecialchars($movie['TenPhim']); ?>"
                                    class="movie-card__image">
                                <div class="movie-card__overlay">
                                    <div class="movie-card__info">
                                        <span class="badge"><?php echo $movie['NamPhatHanh']; ?></span>
                                        <span class="badge"><?php echo $movie['ThoiLuong']; ?> phút</span>
                                    </div>
                                </div>
                            </div>
                            <div class="movie-card__content">
                                <h3 class="movie-card__title"><?php echo htmlspecialchars($movie['TenPhim']); ?></h3>
                                <div class="movie-card__tags">
                                    <?php if (!empty($movie['TheLoaiArray'])): ?>
                                        <?php foreach (array_slice($movie['TheLoaiArray'], 0, 2) as $tenTheLoai): ?>
                                            <span class="badge"><?php echo htmlspecialchars($tenTheLoai); ?></span>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Pagination -->
        <?php if ($totalPages > 1): ?>
            <nav aria-label="Page navigation" class="pagination">
                <ul class="pagination justify-content-center">
                    <?php if ($page > 1): ?>
                        <li class="page-item">
                            <a class="page-link" href="?page=<?php echo $page - 1; ?><?php echo isset($_GET['country']) ? '&country=' . $_GET['country'] : ''; ?>">Trước</a>
                        </li>
                    <?php endif; ?>

                    <?php
                    $startPage = max(1, $page - 2);
                    $endPage = min($totalPages, $page + 2);
                    if ($startPage > 1): ?>
                        <li class="page-item"><a class="page-link" href="?page=1<?php echo isset($_GET['country']) ? '&country=' . $_GET['country'] : ''; ?>">1</a></li>
                        <?php if ($startPage > 2): ?>
                            <li class="page-item disabled"><span class="page-link">...</span></li>
                        <?php endif; ?>
                    <?php endif; ?>

                    <?php for ($i = $startPage; $i <= $endPage; $i++): ?>
                        <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                            <a class="page-link" href="?page=<?php echo $i; ?><?php echo isset($_GET['country']) ? '&country=' . $_GET['country'] : ''; ?>"><?php echo $i; ?></a>
                        </li>
                    <?php endfor; ?>

                    <?php if ($endPage < $totalPages): ?>
                        <?php if ($endPage < $totalPages - 1): ?>
                            <li class="page-item disabled"><span class="page-link">...</span></li>
                        <?php endif; ?>
                        <li class="page-item"><a class="page-link" href="?page=<?php echo $totalPages; ?><?php echo isset($_GET['country']) ? '&country=' . $_GET['country'] : ''; ?>"><?php echo $totalPages; ?></a></li>
                    <?php endif; ?>

                    <?php if ($page < $totalPages): ?>
                        <li class="page-item">
                            <a class="page-link" href="?page=<?php echo $page + 1; ?><?php echo isset($_GET['country']) ? '&country=' . $_GET['country'] : ''; ?>">Sau</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </nav>
        <?php endif; ?>
    <?php endif; ?>
</div>

<script>
    $(document).ready(function() {
        $('select[name="genres[]"]').select2({
            placeholder: "Chọn thể loại",
            width: '100%',
            theme: 'default',
            dropdownCssClass: 'select2-dark'
        });
        $('select[name="country"]').select2({
            placeholder: "Tất cả",
            width: '100%',
            theme: 'default',
            dropdownCssClass: 'select2-dark'
        });
    });
</script>

<?php require_once 'app/Views/shares/footer.php'; ?>