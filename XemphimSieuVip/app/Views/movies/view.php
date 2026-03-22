<?php require_once 'app/Views/shares/header.php'; ?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">

<style>
    body {
        background-color: #141414;
        color: #fff;
        font-family: 'Roboto', sans-serif;
    }

    .hero {
        position: relative;
        background-image: url('/<?php echo $movie['HinhAnh']; ?>');
        background-size: cover;
        background-position: center;
        padding: 80px 0;
        color: white;
    }

    .hero-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.7);
        z-index: 1;
    }

    .hero-content {
        position: relative;
        z-index: 2;
        display: flex;
        flex-direction: column;
        padding: 0 40px;
    }

    .poster-and-info {
        display: flex;
        flex-wrap: wrap;
        gap: 30px;
        align-items: flex-start;
    }

    .poster {
        width: 220px;
        height: 320px;
        object-fit: cover;
        border-radius: 10px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.8);
    }

    .poster-wrapper {
        position: relative;
    }

    .play-button {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        font-size: 64px;
        color: rgba(255, 255, 255, 0.9);
        text-decoration: none;
        transition: transform 0.3s ease, color 0.3s ease;
    }

    .play-button:hover {
        transform: translate(-50%, -50%) scale(1.1);
        color: #E50914;
    }

    .movie-title {
        font-size: 2.5rem;
        font-weight: bold;
        color: #E50914;
    }

    .info-label {
        color: #ffb700;
        font-weight: 500;
    }

    .badge-genre {
        background-color: #ff3c3c;
        color: white;
        margin-right: 6px;
        margin-bottom: 4px;
    }

    .btn-netflix {
        background-color: #E50914;
        color: #fff;
        font-weight: bold;
        border: none;
        border-radius: 6px;
        padding: 10px 20px;
        margin-right: 10px;
    }

    .btn-netflix:hover {
        background-color: #f40612;
    }

    .star {
        color: #ffb700;
    }

    .content-section .btn-outline-secondary {
        background-color: #222;
        /* nền đậm, đồng bộ với nền tổng thể */
        color: #E50914;
        /* màu đỏ Netflix */
        border: 1.5px solid #E50914;
        /* viền đỏ nổi bật */
        font-weight: 600;
        transition: background-color 0.3s ease, color 0.3s ease;
        border-radius: 6px;
        padding: 8px 16px;
        text-align: center;
        min-width: 80px;
    }

    .content-section .btn-outline-secondary:hover,
    .content-section .btn-outline-secondary.active {
        background-color: #E50914;
        /* nền đỏ khi hover hoặc active */
        color: white;
        border-color: #E50914;
        text-decoration: none;
    }

    .episode-item,
    .video-item {
        background-color: #222;
        padding: 10px;
        border-radius: 6px;
        margin-bottom: 10px;
    }

    .episode-item a,
    .video-item a {
        color: #E50914;
        text-decoration: none;
    }

    .episode-item a:hover,
    .video-item a:hover {
        text-decoration: underline;
    }

    /* Nút yêu thích */
    .btn-favorite {
        background-color: white;
        color: black;
        border: 1px solidrgb(0, 0, 0);
        ;
    }

    .btn-not-favorite {
        background-color: transparent;
        color: #ff5722;
        /* màu cam */
        border: 1px solid #ff5722;
    }

    .btn-favorite:hover {
        background-color: rgba(255, 0, 0, 0.86);
        color: white;
    }

    .btn-not-favorite:hover {
        background-color: rgb(255, 0, 0);
        color: white;
    }
</style>

<nav style="padding: 10px;">
    <a href="/" style="padding: 8px 16px; background-color: white; color: black; text-decoration: none; border-radius: 4px;">Quay lại</a>
</nav>

<div class="hero">
    <div class="hero-overlay"></div>
    <div class="hero-content">
        <div class="poster-and-info">
            <div class="poster-wrapper">
                <img src="/<?php echo htmlspecialchars($movie['HinhAnh']); ?>" alt="Poster" class="poster">
                <a href="/movie/watch/<?php echo $movie['MaPhim']; ?>" class="play-button"><i class="bi bi-play-circle-fill"></i></a>
            </div>
            <div>
                <h1 class="movie-title"><?php echo htmlspecialchars($movie['TenPhim']); ?></h1>
                <?php if (!empty($movie['TieuDe'])): ?>
                    <p class="fst-italic text-muted"><?php echo htmlspecialchars($movie['TieuDe']); ?></p>
                <?php endif; ?>
                <p>
                    <?php echo htmlspecialchars($movie['NamPhatHanh']); ?> •
                    <?php echo htmlspecialchars($movie['ThoiLuong']); ?> phút •
                    <?php echo htmlspecialchars($movie['TenQuocGia'] ?? 'Chưa rõ'); ?>
                </p>
                <div class="mb-2">
                    <?php foreach (explode(',', $movie['TheLoai'] ?? '') as $genre): ?>
                        <span class="badge badge-genre"><?php echo htmlspecialchars(trim($genre)); ?></span>
                    <?php endforeach; ?>
                </div>
                <p><span class="info-label">Diễn viên:</span> <?php echo htmlspecialchars($actors); ?></p>
                <p><span class="info-label">Đạo diễn:</span> <?php echo htmlspecialchars($directors); ?></p>
                <p><span class="info-label">Phân loại:</span> <?php echo htmlspecialchars($movie['PhanLoai']); ?> |
                    <span class="info-label">Trạng thái:</span> <?php echo htmlspecialchars($movie['TinhTrang']); ?>
                </p>
                <div class="mb-3">
                    <span class="info-label">Đánh giá:</span>
                    <?php
                    $star = isset($movie['DanhGia']) ? round($movie['DanhGia'], 1) : 0;
                    $count = $movie['LuotDanhGia'] ?? 0;
                    $rounded = round($star);
                    for ($i = 1; $i <= 10; $i++) {
                        echo $i <= $rounded ? '<i class="bi bi-star-fill star"></i>' : '<i class="bi bi-star star"></i>';
                    }
                    ?>
                    <span class="ms-2">(<?php echo $star; ?> điểm / <?php echo $count; ?> lượt)</span>
                </div>
                <a href="/movie/watch/<?php echo $movie['MaPhim']; ?>" class="btn btn-netflix">▶ XEM PHIM</a>
                <a href="/YeuThich/toggleYeuThich?id=<?php echo $movie['MaPhim']; ?>"
                    class="btn <?php echo $isFavorite ? 'btn-favorite' : 'btn-not-favorite'; ?>">
                    <?php echo $isFavorite ? 'Đã Yêu Thích' : 'Yêu Thích'; ?>
                </a>
            </div>
        </div>

        <?php if (!empty($movie['MoTa'])): ?>
            <p class="mt-3"><span class="info-label">Mô tả:</span> <?php echo htmlspecialchars($movie['MoTa']); ?></p>
        <?php endif; ?>
        <?php if (!empty($movie['NoiDung'])): ?>
            <p><span class="info-label">Nội dung:</span> <?php echo htmlspecialchars($movie['NoiDung']); ?></p>
        <?php endif; ?>
        <!-- PHẦN BỔ SUNG: Danh sách tập hoặc Trailer -->
        <div class="content-section">
            <?php if ($movie['PhanLoai'] === 'Bộ' && !empty($episodes)): ?>
                <h5>Danh sách tập</h5>
                <div style="display: flex; flex-wrap: wrap; gap: 8px;">
                    <?php foreach ($episodes as $ep): ?>
                        <a href="/watch/watchEpisode?tap=<?= $ep['MaTap'] ?>"
                            class="btn btn-outline-secondary" style="color:#fff" <?= (isset($_GET['tap']) && $_GET['tap'] == $ep['MaTap']) ? ' active' : '' ?>">
                            <?= htmlspecialchars($ep['TenTap']) ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php elseif ($movie['PhanLoai'] === 'Lẻ' && !empty($movie['Link'])): ?>

            <?php endif; ?>

            <?php if (!empty($videos)): ?>
                <div class="mt-4">
                    <h5>Video khác:</h5>
                    <?php foreach ($videos as $video): ?>
                        <div class="video-item">
                            <a href="<?php echo htmlspecialchars($video['Link']); ?>" target="_blank">
                                <?php echo htmlspecialchars($video['TenVideo']); ?>
                                (<?php echo htmlspecialchars($video['ChatLuong']); ?>, <?php echo htmlspecialchars($video['NgonNgu']); ?>)
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>



<?php require_once 'app/Views/shares/footer.php'; ?>