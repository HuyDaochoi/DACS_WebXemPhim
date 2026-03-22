<?php include 'app/Views/shares/header.php'; ?>
<style>
    .movie-card {
        position: relative;
        background-color: #1F1F1F;
        border-radius: 6px;
        overflow: hidden;
        margin-bottom: 20px;
        transition: transform 0.2s;
        height: 100%;
    }

    .movie-card:hover {
        transform: scale(1.03);
        z-index: 1;
    }

    .movie-card__image {
        width: 100%;
        height: 220px;
        object-fit: cover;
        display: block;
    }

    .movie-card__content {
        padding: 10px;
    }

    .movie-card__title {
        font-size: 15px;
        font-weight: 600;
        margin-bottom: 4px;
        color: #fff;
        text-decoration: none;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        min-height: 40px;
    }

    .movie-card__info {
        font-size: 12px;
        color: #B3B3B3;
        margin-bottom: 6px;
    }

    .movie-card__time {
        font-size: 11px;
        color: #aaa;
        margin-bottom: 6px;
    }

    .movie-card__actions {
        display: flex;
        justify-content: flex-end;
    }

    .movie-card__actions .btn {
        font-size: 12px;
        padding: 3px 10px;
    }

    .section-title {
        font-size: 20px;
        font-weight: 600;
        margin: 20px 0 15px;
        color: #fff;
    }
</style>
<div class="container mt-4">
    <h2 class="section-title">Lịch sử xem phim</h2>
    <?php if (empty($history)): ?>
        <div>Bạn chưa xem phim nào.</div>
    <?php else: ?>
        <div class="row">
            <?php foreach ($history as $item): ?>
                <div class="col-6 col-sm-4 col-md-3 col-lg-2 d-flex">
                    <div class="movie-card w-100 d-flex flex-column">
                        <a href="/movie/view/<?php echo $item['MaPhim']; ?>">
                            <img src="/<?php echo htmlspecialchars($item['HinhAnh']); ?>" alt="<?php echo htmlspecialchars($item['TenPhim']); ?>" class="movie-card__image">
                        </a>
                        <div class="movie-card__content flex-grow-1 d-flex flex-column">
                            <a href="/movie/view/<?php echo $item['MaPhim']; ?>" class="movie-card__title">
                                <?php echo htmlspecialchars($item['TenPhim']); ?>
                            </a>
                            <div class="movie-card__info">
                                <?php echo htmlspecialchars($item['NamPhatHanh']); ?>
                            </div>
                            <div class="movie-card__time">
                                Xem lúc: <?php echo htmlspecialchars($item['ThoiGian']); ?>
                            </div>
                            <div class="movie-card__actions mt-auto">
                                <a href="/LichSu/delete?maPhim=<?php echo $item['MaPhim']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Xóa phim này khỏi lịch sử?')">Xóa</a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="mt-3">
            <a href="/LichSu/delete" class="btn btn-warning" onclick="return confirm('Xóa toàn bộ lịch sử?')">Xóa tất cả</a>
        </div>
    <?php endif; ?>
</div>
<?php include 'app/Views/shares/footer.php'; ?>