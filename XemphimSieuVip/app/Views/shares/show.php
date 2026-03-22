<?php include 'app/Views/shares/header.php'; ?>

<style>
:root {
    --primary-color: #E50914; /* Netflix red */
    --secondary-color: #B20710; /* Darker red for hover */
    --bg-color: #141414; /* Netflix dark background */
    --text-color: #FFFFFF; /* White text */
    --text-muted: #B3B3B3; /* Muted gray text */
    --card-bg: #1F1F1F; /* Slightly lighter dark for cards */
    --border-color: #333333; /* Dark border */
}

body {
    background-color: var(--bg-color);
    color: var(--text-color);
    font-family: 'Roboto', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
    margin: 0;
}

/* Container */
.container-fluid {
    padding: 0 30px;
    max-width: 1800px;
}

/* Banner Slider */
.banner-swiper {
    margin: 0 0 20px;
    position: relative;
}

.banner-slide {
    position: relative;
    height: 400px;
    overflow: hidden;
}

.banner-slide img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    filter: brightness(0.65);
}

.banner-slide__content {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    padding: 20px;
    background: linear-gradient(to top, rgba(0, 0, 0, 0.8), transparent);
}

.banner-slide__title {
    font-size: 28px;
    font-weight: 700;
    margin-bottom: 10px;
    line-height: 1.2;
    max-width: 60%;
}

.banner-slide__description {
    font-size: 14px;
    color: var(--text-muted);
    max-width: 40%;
    margin-bottom: 15px;
    line-height: 1.4;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.banner-slide__meta .badge {
    background-color: rgba(255, 255, 255, 0.15);
    color: var(--text-color);
    font-size: 11px;
    padding: 4px 8px;
    margin-right: 6px;
}

.banner-slide .btn {
    background-color: var(--primary-color);
    border: none;
    padding: 8px 16px;
    font-size: 14px;
    font-weight: 500;
    border-radius: 4px;
    transition: background-color 0.2s;
}

.banner-slide .btn:hover {
    background-color: var(--secondary-color);
}

.swiper-pagination-bullet {
    background: var(--text-muted);
    opacity: 0.7;
}

.swiper-pagination-bullet-active {
    background: var(--primary-color);
    opacity: 1;
}

.swiper-button-next,
.swiper-button-prev {
    color: var(--text-color);
    background: rgba(0, 0, 0, 0.5);
    width: 32px;
    height: 32px;
    border-radius: 50%;
    transition: background-color 0.2s;
}

.swiper-button-next:hover,
.swiper-button-prev:hover {
    background: var(--primary-color);
}

.swiper-button-next:after,
.swiper-button-prev:after {
    font-size: 14px;
}

/* Movie Card */
.movie-card {
    position: relative;
    background-color: var(--card-bg);
    border-radius: 6px;
    overflow: hidden;
    transition: transform 0.2s ease;
}

.movie-card:hover {
    transform: scale(1.05);
    z-index: 1;
}

.movie-card__image {
    width: 100%;
    height: 220px;
    object-fit: cover;
    display: block;
}

.movie-card__overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(to bottom, rgba(0, 0, 0, 0.1), rgba(0, 0, 0, 0.5));
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
    margin-bottom: 4px;
    line-height: 1.3;
    max-height: 2.6em;
    overflow: hidden;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
}

.movie-card__info {
    font-size: 11px;
    color: var(--text-muted);
    line-height: 1.3;
    max-height: 2.6em;
    overflow: hidden;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
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
    z-index: 2;
}

/* Section Title */
.section-title {
    font-size: 18px;
    font-weight: 600;
    margin: 15px 0 10px;
    color: var(--text-color);
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.section-title a {
    font-size: 12px;
    color: var(--primary-color);
    text-decoration: none;
    transition: color 0.2s;
}

.section-title a:hover {
    color: var(--secondary-color);
}

/* Movie Section */
.movie-section {
    margin-bottom: 20px;
}

.movie-swiper {
    padding-bottom: 20px;
}

/* Trending Section */
.trending {
    background-color: var(--card-bg);
    border-radius: 6px;
    padding: 15px;
    margin-bottom: 20px;
}

.trending__title {
    font-size: 16px;
    font-weight: 600;
    color: var(--primary-color);
    margin-bottom: 10px;
    padding-bottom: 8px;
    border-bottom: 1px solid var(--border-color);
}

.trending__list {
    list-style: none;
    padding: 0;
}

.trending__item {
    margin-bottom: 8px;
}

.trending__link {
    display: flex;
    align-items: center;
    color: var(--text-color);
    text-decoration: none;
    font-size: 13px;
    transition: color 0.2s;
}

.trending__link:hover {
    color: var(--primary-color);
}

.trending__number {
    font-weight: 700;
    color: var(--primary-color);
    margin-right: 8px;
    font-size: 14px;
}

/* Banner Ad */
.banner .card {
    background-color: var(--card-bg);
    border: none;
    border-radius: 6px;
    padding: 15px;
    text-align: center;
}

.banner .card-title {
    font-size: 16px;
    font-weight: 600;
    color: var(--text-color);
    margin-bottom: 8px;
}

.banner .card-text {
    font-size: 12px;
    color: var(--text-muted);
    margin-bottom: 10px;
}

.banner .BTN {
    background-color: var(--primary-color);
    border: none;
    padding: 6px 16px;
    font-size: 12px;
    border-radius: 4px;
    transition: background-color 0.2s;
}

.banner .BTN:hover {
    background-color: var(--secondary-color);
}

/* Responsive Design */
@media (max-width: 992px) {
    .container-fluid {
        padding: 0 20px;
    }

    .banner-slide {
        height: 300px;
    }

    .banner-slide__title {
        font-size: 22px;
        max-width: 70%;
    }

    .banner-slide__description {
        max-width: 50%;
        font-size: 12px;
    }

    .movie-card__image {
        height: 180px;
    }
}

@media (max-width: 576px) {
    .container-fluid {
        padding: 0 15px;
    }

    .banner-slide {
        height: 220px;
    }

    .banner-slide__title {
        font-size: 18px;
        max-width: 80%;
    }

    .banner-slide__description {
        max-width: 60%;
        font-size: 11px;
        -webkit-line-clamp: 1;
    }

    .banner-slide__meta .badge {
        font-size: 10px;
        padding: 3px 6px;
    }

    .banner-slide .btn {
        padding: 6px 12px;
        font-size: 12px;
    }

    .movie-card__image {
        height: 150px;
    }

    .movie-card__title {
        font-size: 13px;
    }

    .movie-card__info {
        font-size: 10px;
    }

    .section-title {
        font-size: 16px;
    }

    .section-title a {
        font-size: 11px;
    }

    .trending__title {
        font-size: 14px;
    }

    .trending__link {
        font-size: 12px;
    }

    .trending__number {
        font-size: 13px;
    }
}
</style>

<div class="container-fluid">
    <!-- Banner Slider -->
    <div class="swiper banner-swiper">
        <div class="swiper-wrapper">
            <?php for ($i = 0; $i < min(3, count($new_movies)); $i++): ?>
                <div class="swiper-slide">
                    <div class="banner-slide">
                        <a href="/movie/view/<?php echo $new_movies[$i]['MaPhim']; ?>">
                            <img src="<?php echo !empty($new_movies[$i]['HinhAnh']) ? '/' . $new_movies[$i]['HinhAnh'] : 'https://via.placeholder.com/1200x400?text=No+Image'; ?>" 
                                 alt="<?php  echo $new_movies[$i]['TenPhim']; ?>">
                            <div class="banner-slide__content">
                                <h2 class="banner-slide__title"><?php echo $new_movies[$i]['TenPhim']; ?></h2>
                                <p class="banner-slide__description">
                                    <?php echo substr($new_movies[$i]['MoTa'] ?? '', 0, 100); echo (strlen($new_movies[$i]['MoTa'] ?? '') > 100) ? '...' : ''; ?>
                                </p>
                                <div class="banner-slide__meta d-flex align-items-center">
                                    <button class="btn me-2">
                                        <i class="fas fa-play me-1"></i>Xem
                                    </button>
                                    <span class="badge"><?php echo $new_movies[$i]['NamPhatHanh']; ?></span>
                                    <span class="badge"><?php echo $new_movies[$i]['ThoiLuong']; ?> phút</span>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            <?php endfor; ?>
        </div>
        <div class="swiper-pagination"></div>
        <div class="swiper-button-prev"></div>
        <div class="swiper-button-next"></div>
    </div>

    <div class="row">
        <div class="col-lg-9 col-md-8">
            <!-- Phim Trending (TMDb) -->
            <section class="movie-section">
                <h2 class="section-title">
                    Trending
                    <a href="#">Xem tất cả</a>
                </h2>
                <div class="swiper movie-swiper">
                    <div class="swiper-wrapper">
                        <?php foreach ($tmdb_trending as $movie): ?>
                            <div class="swiper-slide" style="width: 160px;">
                                <div class="movie-card">
                                    <?php if ($movie['TinhTrang'] === 'Đang chiếu'): ?>
                                        <div class="movie-badge">Hot</div>
                                    <?php endif; ?>
                                    <a href="<?php echo TMDB_BASE_URL . $movie['TmdbId']; ?>" target="_blank">
                                        <img src="<?php echo htmlspecialchars($movie['HinhAnh']); ?>" 
                                             alt="<?php echo htmlspecialchars($movie['TenPhim']); ?>" class="movie-card__image">
                                        <div class="movie-card__overlay"></div>
                                        <div class="movie-card__content">
                                            <h3 class="movie-card__title"><?php echo htmlspecialchars($movie['TenPhim']); ?></h3>
                                            <div class="movie-card__info">
                                                <?php
                                                $genres = htmlspecialchars($movie['TheLoai'] ?? 'N/A');
                                                $year = htmlspecialchars($movie['NamPhatHanh'] ?? 'N/A');
                                                echo "$genres | $year";
                                                ?>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="swiper-pagination"></div>
                    <div class="swiper-button-prev"></div>
                    <div class="swiper-button-next"></div>
                </div>
            </section>

            <!-- Phim mới cập nhật -->
            <section class="movie-section">
                <h2 class="section-title">
                    Mới cập nhật
                    <a href="/movie/show">Xem tất cả</a>
                </h2>
                <div class="swiper movie-swiper">
                    <div class="swiper-wrapper">
                        <?php foreach ($new_movies as $movie): ?>
                            <div class="swiper-slide" style="width: 160px;">
                                <div class="movie-card">
                                    <?php if ($movie['TinhTrang'] === 'Đang chiếu'): ?>
                                        <div class="movie-badge">Hot</div>
                                    <?php endif; ?>
                                    <a href="/movie/view/<?php echo $movie['MaPhim']; ?>">
                                        <img src="<?php echo !empty($movie['HinhAnh']) ? '/' . $movie['HinhAnh'] : 'https://via.placeholder.com/160x220?text=No+Image'; ?>" 
                                             alt="<?php echo $movie['TenPhim']; ?>" class="movie-card__image">
                                        <div class="movie-card__overlay"></div>
                                        <div class="movie-card__content">
                                            <h3 class="movie-card__title"><?php echo $movie['TenPhim']; ?></h3>
                                            <div class="movie-card__info">
                                                <?php
                                                $genres = !empty($movie['TheLoai']) ? htmlspecialchars($movie['TheLoai']) : 'N/A';
                                                $year = !empty($movie['NamPhatHanh']) ? htmlspecialchars($movie['NamPhatHanh']) : 'N/A';
                                                echo "$genres | $year";
                                                ?>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="swiper-pagination"></div>
                    <div class="swiper-button-prev"></div>
                    <div class="swiper-button-next"></div>
                </div>
            </section>

            <!-- Phim phổ biến -->
            <section class="movie-section">
                <h2 class="section-title">
                    Phổ biến
                    <a href="/movie/popular">Xem tất cả</a>
                </h2>
                <div class="swiper movie-swiper">
                    <div class="swiper-wrapper">
                        <?php foreach ($popular_movies as $movie): ?>
                            <div class="swiper-slide" style="width: 160px;">
                                <div class="movie-card">
                                    <?php if ($movie['LuotXem'] > 100): ?>
                                        <div class="movie-badge">Hot</div>
                                    <?php endif; ?>
                                    <a href="/movie/view/<?php echo $movie['MaPhim']; ?>">
                                        <img src="<?php echo !empty($movie['HinhAnh']) ? '/' . $movie['HinhAnh'] : 'https://via.placeholder.com/160x220?text=No+Image'; ?>" 
                                             alt="<?php echo $movie['TenPhim']; ?>" class="movie-card__image">
                                        <div class="movie-card__overlay"></div>
                                        <div class="movie-card__content">
                                            <h3 class="movie-card__title"><?php echo $movie['TenPhim']; ?></h3>
                                            <div class="movie-card__info">
                                                <?php
                                                $genres = !empty($movie['TheLoai']) ? htmlspecialchars($movie['TheLoai']) : 'N/A';
                                                $year = !empty($movie['NamPhatHanh']) ? htmlspecialchars($movie['NamPhatHanh']) : 'N/A';
                                                echo "$genres | $year";
                                                ?>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="swiper-pagination"></div>
                    <div class="swiper-button-prev"></div>
                    <div class="swiper-button-next"></div>
                </div>
            </section>
        </div>

        <div class="col-lg-3 col-md-4">
            <!-- Trending -->
            <div class="trending">
                <h3 class="trending__title">Top Trending</h3>
                <ul class="trending__list">
                    <?php foreach (array_slice($tmdb_trending, 0, 5) as $index => $movie): ?>
                        <li class="trending__item">
                            <a href="<?php echo TMDB_BASE_URL . $movie['TmdbId']; ?>" class="trending__link" target="_blank">
                                <span class="trending__number"><?php echo $index + 1; ?></span>
                                <span><?php echo htmlspecialchars($movie['TenPhim']); ?></span>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <!-- Banner quảng cáo -->
            <div class="banner">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Xem không quảng cáo</h5>
                        <p class="card-text">Nâng cấp VIP để trải nghiệm tốt hơn!</p>
                        <a href="#" class="btn">Nâng cấp</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Banner swiper
    new Swiper('.banner-swiper', {
        slidesPerView: 1,
        spaceBetween: 20,
        loop: true,
        autoplay: {
            delay: 4000,
            disableOnInteraction: false,
        },
        pagination: {
            el: '.banner-swiper .swiper-pagination',
            clickable: true,
        },
        navigation: {
            nextEl: '.banner-swiper .swiper-button-next',
            prevEl: '.banner-swiper .swiper-button-prev',
        },
    });
    
    // Movie swipers
    const movieSwipers = document.querySelectorAll('.movie-swiper');
    movieSwipers.forEach(function(swiperContainer) {
        new Swiper(swiperContainer, {
            slidesPerView: 'auto',
            spaceBetween: 10,
            freeMode: true,
            pagination: {
                el: swiperContainer.querySelector('.swiper-pagination'),
                clickable: true,
            },
            navigation: {
                nextEl: swiperContainer.querySelector('.swiper-button-next'),
                prevEl: swiperContainer.querySelector('.swiper-button-prev'),
            },
            breakpoints: {
                320: {
                    slidesPerView: 2,
                    spaceBetween: 8
                },
                576: {
                    slidesPerView: 3,
                    spaceBetween: 10
                },
                768: {
                    slidesPerView: 4,
                    spaceBetween: 10
                },
                992: {
                    slidesPerView: 5,
                    spaceBetween: 12
                },
                1200: {
                    slidesPerView: 6,
                    spaceBetween: 12
                }
            }
        });
    });
});
</script>

<?php include 'app/Views/shares/footer.php'; ?>