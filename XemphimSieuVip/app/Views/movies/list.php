<?php require_once 'app/Views/shares/header.php'; ?>

<style>
    /* Netflix-inspired dark theme */
    body {
        background-color: #141414;
        /* Netflix black */
        color: #ffffff;
        font-family: 'Roboto', sans-serif;
    }

    .container {
        max-width: 1400px;
    }

    .card {
        background-color: #181818;
        /* Dark gray card */
        border: none;
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
    }

    .card-header {
        background-color: transparent;
        border-bottom: 1px solid #333;
        padding: 1.5rem;
    }

    .card-header h4 {
        margin: 0;
        font-weight: 600;
        color: #ffffff;
    }

    .btn-netflix {
        background-color: #E50914;
        /* Netflix red */
        color: #ffffff;
        border: none;
        padding: 0.75rem 1.5rem;
        border-radius: 4px;
        font-weight: 600;
        transition: background-color 0.2s;
    }

    .btn-netflix:hover {
        background-color: #f40612;
        color: #ffffff;
    }

    .btn-netflix i {
        margin-right: 0.5rem;
    }

    .form-control,
    .select2-container--default .select2-selection--single {
        background-color: #333;
        border: 1px solid #444;
        color: #ffffff;
        border-radius: 4px;
        height: 40px;
    }

    .form-control:focus,
    .select2-container--default .select2-selection--single:focus {
        border-color: #E50914;
        box-shadow: none;
        background-color: #333;
    }

    .form-control::placeholder {
        color: #999;
    }

    .select2-container--default .select2-selection--single .select2-selection__rendered {
        color: #ffffff;
        line-height: 38px;
    }

    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 38px;
    }

    .select2-dropdown {
        background-color: #333;
        border: 1px solid #444;
    }

    .select2-results__option {
        color: #ffffff;
    }

    .select2-results__option--highlighted {
        background-color: #E50914 !important;
    }

    .movie-card {
        background-color: #222;
        border-radius: 8px;
        overflow: hidden;
        transition: transform 0.3s, box-shadow 0.3s;
        position: relative;
    }

    .movie-card:hover {
        transform: scale(1.05);
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.5);
    }

    .movie-card img {
        width: 100%;
        height: 200px;
        object-fit: cover;
    }

    .movie-info {
        padding: 1rem;
    }

    .movie-info h5 {
        font-size: 1.1rem;
        margin: 0 0 0.5rem;
        color: #ffffff;
    }

    .movie-info p {
        font-size: 0.9rem;
        color: #b3b3b3;
        margin: 0.2rem 0;
    }

    .movie-actions {
        position: absolute;
        top: 10px;
        right: 10px;
        opacity: 0;
        transition: opacity 0.3s;
        display: flex;
        gap: 0.5rem;
    }

    .movie-card:hover .movie-actions {
        opacity: 1;
    }

    .movie-actions .btn {
        padding: 0.75rem;
        font-size: 1rem;
        border-radius: 50%;
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: transform 0.2s, background-color 0.2s;
    }

    .movie-actions .btn:hover {
        transform: scale(1.1);
    }

    .movie-actions .btn-view {
        background-color: #ffffff;
        color: #141414;
    }

    .movie-actions .btn-view:hover {
        background-color: #e0e0e0;
    }

    .movie-actions .btn-edit {
        background-color: #FFC107;
        /* Yellow for Edit */
        color: #141414;
    }

    .movie-actions .btn-edit:hover {
        background-color: #ffca28;
    }

    .movie-actions .btn-delete {
        background-color: #E50914;
        /* Netflix red for Delete */
        color: #ffffff;
    }

    .movie-actions .btn-delete:hover {
        background-color: #f40612;
    }

    .badge-status {
        font-size: 0.8rem;
        padding: 0.4rem 0.8rem;
        border-radius: 12px;
    }

    .pagination .page-link {
        background-color: #333;
        border: 1px solid #444;
        color: #ffffff;
        margin: 0 0.2rem;
        border-radius: 4px;
    }

    .pagination .page-link:hover {
        background-color: #E50914;
        color: #ffffff;
    }

    .pagination .page-item.active .page-link {
        background-color: #E50914;
        border-color: #E50914;
    }

    .pagination .page-item.disabled .page-link {
        background-color: #222;
        color: #666;
        border-color: #444;
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .movie-card {
            margin-bottom: 1.5rem;
        }

        .movie-actions {
            opacity: 1;
        }

        .movie-actions .btn {
            width: 36px;
            height: 36px;
            padding: 0.6rem;
        }
    }
</style>

<div class="container mt-4">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 style="color:red">Danh Sách Phim</h4>
                    <div>
                        <a href="/movie/add" class="btn btn-primary">
                            <i class="bi bi-plus-circle"></i> Thêm Phim Lẻ
                        </a>
                        <a href="/movie/addSeries" class="btn btn-primary">
                            <i class="bi bi-plus-circle"></i> Thêm Phim Bộ
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Bộ lọc -->
                    <form action="/movie/list" method="GET" class="mb-4" id="searchForm">
                        <div class="row">
                            <div class="col-md-3 col-sm-6 mb-3">
                                <div class="form-group">
                                    <label style="color:White" for="search">Tìm kiếm</label>
                                    <input type="text" class="form-control" id="search" name="search"
                                        value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>"
                                        placeholder="Tên phim...">
                                </div>
                                <div class="search-results" id="searchResults"></div>
                            </div>
                            <div class="col-md-3 col-sm-6 mb-3">
                                <div class="form-group">
                                    <label style="color:White" for="category">Thể loại</label>
                                    <select class="form-control" id="category" name="category">
                                        <option value="">Tất cả</option>
                                        <?php foreach ($categories as $category): ?>
                                            <option value="<?php echo $category['MaTheLoai']; ?>"
                                                <?php echo (isset($_GET['category']) && $_GET['category'] == $category['MaTheLoai']) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($category['TenTheLoai']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3 col-sm-6 mb-3">
                                <div class="form-group">
                                    <label style="color:White" for="status">Trạng thái</label>
                                    <select class="form-control" id="status" name="status">
                                        <option value="">Tất cả</option>
                                        <option value="Đang chiếu" <?php echo (isset($_GET['status']) && $_GET['status'] == 'Đang chiếu') ? 'selected' : ''; ?>>Đang chiếu</option>
                                        <option value="Sắp chiếu" <?php echo (isset($_GET['status']) && $_GET['status'] == 'Sắp chiếu') ? 'selected' : ''; ?>>Sắp chiếu</option>
                                        <option value="Đã kết thúc" <?php echo (isset($_GET['status']) && $_GET['status'] == 'Đã kết thúc') ? 'selected' : ''; ?>>Đã kết thúc</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3 col-sm-6 mb-3">
                                <div class="form-group">
                                    <label> </label>
                                    <button type="submit" class="btn btn-netflix w-100">
                                        <i class="bi bi-search"></i> Tìm kiếm
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>

                    <!-- Danh sách phim -->
                    <div class="row">
                        <?php if (empty($movies)): ?>
                            <div class="col-12 text-center">
                                <p class="text-muted">Không tìm thấy phim nào.</p>
                            </div>
                        <?php else: ?>
                            <?php foreach ($movies as $movie): ?>
                                <div class="col-md-3 col-sm-6 mb-4">
                                    <div class="movie-card">
                                        <img src="<?php echo !empty($movie['HinhAnh']) ? '/' . $movie['HinhAnh'] : 'https://via.placeholder.com/300x200'; ?>"
                                            alt="<?php echo htmlspecialchars($movie['TenPhim']); ?>">
                                        <div class="movie-info">
                                            <h5><?php echo htmlspecialchars($movie['TenPhim']); ?></h5>
                                            <p>
                                                <?php
                                                $genres = explode(',', $movie['TheLoai'] ?? '');
                                                foreach ($genres as $genre) {
                                                    echo '<span class="badge bg-secondary me-1">' . htmlspecialchars(trim($genre) ?? 'Không rõ') . '</span>';
                                                }
                                                ?>
                                            </p>
                                            <p><?php echo htmlspecialchars($movie['TenQuocGia'] ?? 'Không rõ'); ?> • <?php echo $movie['NamPhatHanh'] ?? 'Không rõ'; ?></p>
                                            <p>
                                                <span class="badge badge-status bg-<?php
                                                                                    echo $movie['TinhTrang'] == 'Đang chiếu' ? 'success' : ($movie['TinhTrang'] == 'Sắp chiếu' ? 'warning' : 'secondary');
                                                                                    ?>">
                                                    <?php echo htmlspecialchars($movie['TinhTrang']); ?>
                                                </span>
                                            </p>
                                        </div>
                                        <div class="movie-actions">
                                            <a href="/movie/view/<?php echo $movie['MaPhim']; ?>"
                                                class="btn btn-view" title="Xem chi tiết">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <a href="/movie/edit/<?php echo $movie['MaPhim']; ?>"
                                                class="btn btn-edit" title="Sửa">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <button type="button" class="btn btn-delete"
                                                onclick="confirmDelete(<?php echo $movie['MaPhim']; ?>)" title="Xóa">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                            <?php if ($movie['PhanLoai'] === 'Bộ'): ?>
                                                <a href="/movie/manageEpisodes/<?php echo $movie['MaPhim']; ?>" class="btn btn-info btn-sm" title="Quản lý tập phim">
                                                   
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>

                    <!-- Phân trang -->
                    <?php if ($total_pages > 1): ?>
                        <nav aria-label="Page navigation" class="mt-4">
                            <ul class="pagination justify-content-center">
                                <li class="page-item <?php echo $current_page <= 1 ? 'disabled' : ''; ?>">
                                    <a class="page-link" href="?page=<?php echo $current_page - 1; ?><?php echo htmlspecialchars($query_string); ?>" aria-label="Previous">
                                        <span aria-hidden="true">«</span>
                                    </a>
                                </li>
                                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                    <li class="page-item <?php echo $current_page == $i ? 'active' : ''; ?>">
                                        <a class="page-link" href="?page=<?php echo $i; ?><?php echo htmlspecialchars($query_string); ?>"><?php echo $i; ?></a>
                                    </li>
                                <?php endfor; ?>
                                <li class="page-item <?php echo $current_page >= $total_pages ? 'disabled' : ''; ?>">
                                    <a class="page-link" href="?page=<?php echo $current_page + 1; ?><?php echo htmlspecialchars($query_string); ?>" aria-label="Next">
                                        <span aria-hidden="true">»</span>
                                    </a>
                                </li>
                            </ul>
                        </nav>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function confirmDelete(movieId) {
        if (confirm('Bạn có chắc chắn muốn xóa phim này?')) {
            window.location.href = '/movie/delete/' + movieId;
        }
    }

    // Khởi tạo select2 và xử lý form
    $(document).ready(function() {
        $('#category, #status').select2({
            placeholder: 'Chọn...',
            allowClear: true,
            width: '100%',
            dropdownCssClass: 'netflix-select2'
        });

        // Loại bỏ tham số rỗng khi submit form
        $('#searchForm').on('submit', function(e) {
            $(this).find('input, select').each(function() {
                if (!$(this).val()) {
                    $(this).prop('disabled', true);
                }
            });
        });
    });
</script>

<?php require_once 'app/Views/shares/footer.php'; ?>