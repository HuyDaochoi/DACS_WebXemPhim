<?php require_once 'app/Views/shares/header.php'; ?>

<div class="container mt-4">
    <div class="row">
        <!-- Sidebar Filter -->
        <div class="col-md-3">
            <div class="card bg-dark">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0">Bộ lọc</h5>
                </div>
                <div class="card-body">
                    <form action="/timkiem" method="GET" id="filterForm">
                        <input type="hidden" name="keyword" value="<?php echo htmlspecialchars($keyword); ?>">
                        
                        <div class="mb-3">
                            <label class="form-label text-white">Thể loại</label>
                            <select name="genre" class="form-select bg-dark text-white" id="genreSelect">
                                <option value="">Tất cả thể loại</option>
                                <?php foreach ($genres as $g): ?>
                                    <option value="<?php echo $g['MaTheLoai']; ?>" <?php echo isset($_GET['genre']) && $_GET['genre'] == $g['MaTheLoai'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($g['TenTheLoai']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-white">Quốc gia</label>
                            <select name="country" class="form-select bg-dark text-white" id="countrySelect">
                                <option value="">Tất cả quốc gia</option>
                                <?php foreach ($countries as $c): ?>
                                    <option value="<?php echo $c['MaQuocGia']; ?>" <?php echo isset($_GET['country']) && $_GET['country'] == $c['MaQuocGia'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($c['TenQuocGia']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-danger w-100">Lọc kết quả</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Movie List -->
        <div class="col-md-9">
            <h2 class="text-white mb-4">
                <?php if (!empty($keyword)): ?>
                    Kết quả tìm kiếm cho "<?php echo htmlspecialchars($keyword); ?>"
                <?php else: ?>
                    Tất cả phim
                <?php endif; ?>
            </h2>

            <?php if (empty($movies)): ?>
                <div class="alert alert-info">
                    Không tìm thấy phim nào phù hợp với tiêu chí tìm kiếm.
                </div>
            <?php else: ?>
                <div class="row">
                    <?php foreach ($movies as $movie): ?>
                        <div class="col-md-4 col-sm-6 mb-4">
                            <div class="movie-card">
                                <a href="/movie/view/<?php echo $movie['MaPhim']; ?>">
                                    <img src="<?php echo !empty($movie['HinhAnh']) ? '/' . $movie['HinhAnh'] : 'https://via.placeholder.com/300x450?text=No+Image'; ?>" 
                                         alt="<?php echo $movie['TenPhim']; ?>" class="movie-card__image">
                                    <div class="movie-card__content">
                                        <h3 class="movie-card__title"><?php echo $movie['TenPhim']; ?></h3>
                                        <div class="movie-card__info">
                                            <?php echo $movie['NamPhatHanh']; ?> • <?php echo $movie['ThoiLuong']; ?> phút
                                        </div>
                                    </div>
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Pagination -->
                <?php if ($totalPages > 1): ?>
                    <nav aria-label="Page navigation" class="mt-4">
                        <ul class="pagination justify-content-center">
                            <?php if ($page > 1): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?page=<?php echo $page - 1; ?>&keyword=<?php echo urlencode($keyword); ?>&genre=<?php echo $_GET['genre'] ?? ''; ?>&country=<?php echo $_GET['country'] ?? ''; ?>">Trước</a>
                                </li>
                            <?php endif; ?>

                            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                                    <a class="page-link" href="?page=<?php echo $i; ?>&keyword=<?php echo urlencode($keyword); ?>&genre=<?php echo $_GET['genre'] ?? ''; ?>&country=<?php echo $_GET['country'] ?? ''; ?>"><?php echo $i; ?></a>
                                </li>
                            <?php endfor; ?>

                            <?php if ($page < $totalPages): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?page=<?php echo $page + 1; ?>&keyword=<?php echo urlencode($keyword); ?>&genre=<?php echo $_GET['genre'] ?? ''; ?>&country=<?php echo $_GET['country'] ?? ''; ?>">Sau</a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </nav>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize select2 for better dropdown experience
    $('#genreSelect, #countrySelect').select2({
        theme: 'dark',
        width: '100%'
    });

    // Auto submit form when filter changes
    $('#genreSelect, #countrySelect').on('change', function() {
        $('#filterForm').submit();
    });
});
</script>

<?php require_once 'app/Views/shares/footer.php'; ?> 