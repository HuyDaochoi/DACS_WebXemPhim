<!DOCTYPE html>
<html lang="Vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/app/public/style.css">
</head>


<footer class=" class=" fooster_main"">
    <div class="container">
        <div class="row">
            <div class=".fooster_main">
                <h5 class="mb-3 text-danger">Về XEMP</h5>
                <p class=".footer_text">XEMP là nền tảng xem phim trực tuyến hàng đầu với kho phim đa dạng và phong phú. Chúng tôi cập nhật liên tục các bộ phim mới nhất từ nhiều quốc gia với chất lượng cao nhất.</p>
            </div>
            <div class="col-md-2 mb-3">
                <h5 class="mb-3 text-danger">Phim</h5>
                <ul class="list-unstyled">
                    <li class=".footer_text"><a href="/movie/show" class="text-light text-decoration-none hover-text-danger">Phim mới</a></li>
                    <li class=".footer_text"><a href="/movie/popular" class="text-light text-decoration-none hover-text-danger">Phim hot</a></li>
                    <li class=".footer_text"><a href="/movie/featured" class="text-light text-decoration-none hover-text-danger">Phim đề cử</a></li>
                    <li class=".footer_text"><a href="/movie/recent" class="text-light text-decoration-none hover-text-danger">Phim bộ</a></li>
                    <li class=".footer_text"><a href="/movie/recent" class="text-light text-decoration-none hover-text-danger">Phim lẻ</a></li>
                </ul>
            </div>
            <div class="col-md-2 mb-3">
                <h5 class="mb-3 text-danger">Thể loại</h5>
                <ul class="list-unstyled">
                    <li class="mb-2"><a href="#" class="text-light text-decoration-none hover-text-danger">Hành động</a></li>
                    <li class="mb-2"><a href="#" class="text-light text-decoration-none hover-text-danger">Tình cảm</a></li>
                    <li class="mb-2"><a href="#" class="text-light text-decoration-none hover-text-danger">Viễn tưởng</a></li>
                    <li class="mb-2"><a href="#" class="text-light text-decoration-none hover-text-danger">Hoạt hình</a></li>
                    <li class="mb-2"><a href="#" class="text-light text-decoration-none hover-text-danger">Kinh dị</a></li>
                </ul>
            </div>
            <div class="col-md-4 mb-3">
                <h5 class="mb-3 text-danger">Liên hệ với chúng tôi</h5>
                <p class="text-light">Theo dõi chúng tôi trên mạng xã hội để cập nhật những bộ phim mới nhất.</p>
                <div class="d-flex gap-3 mt-3">
                    <a href="https://www.facebook.com/jack.phuongtuan1204?rdid=wJWK0OLz3Pvbe7cv&share_url=https%3A%2F%2Fwww.facebook.com%2Fshare%2F1EgpKowJon%2F#" class="text-primary hover-text-danger" target="_blank"><i class="fab fa-facebook fa-lg"></i></a>
                    <a href="#" class="text-primary hover-text-danger"><i class="fab fa-twitter fa-lg"></i></a>
                    <a href="#" class=" insta hover-text-danger"><i class="fab fa-instagram fa-lg"></i></a>
                    <a href="https://www.youtube.com/watch?v=xvFZjo5PgG0" class="text-danger hover-text-danger" target="_blank"><i class="fab fa-youtube fa-lg"></i></a>
                    <a href="#" class="text-light  hover-text-danger"><i class="fab fa-tiktok fa-lg"></i></a>
                </div>
                <div class="mt-3">

                    <small class="text-light">Nhận thông báo về phim mới và khuyến mãi</small>
                </div>
            </div>
        </div>
        <hr class="my-4 border-secondary">
        <div class="row">
            <div class="col-md-6 text-center text-md-start">
                <p class="mb-0 text-danger">&copy; <?php echo date('Y'); ?> XEMP. Tất cả quyền được bảo lưu.</p>
            </div>
            <div class="col-md-6 text-center text-md-end">
                <a href="/privacy" class="CDF text-decoration-none me-3 hover-text-danger">Chính sách bảo mật</a>
                <a href="/terms" class="CDF text-decoration-none me-3 hover-text-danger">Điều khoản sử dụng</a>
                <a href="/faq" class="CDF text-decoration-none hover-text-danger">FAQ</a>
            </div>
        </div>
    </div>
</footer>

<style>
    footer {
        background-color: #161616;
        /* Nền đen */
        color: #ffffff;
        /* Chữ trắng */
        padding: 20px 0;
    }

    footer a {
        color: #ffffff;
        /* Chữ trắng */
        text-decoration: none;
        transition: color 0.3s, transform 0.3s;
    }

    footer a:hover {
        color: var(--primary-color);
        /* Màu chính khi hover */
        transform: translateX(3px);
    }

    footer .text-danger {
        color: var(--primary-color);
        /* Màu chính */
    }

    footer .text-light {
        color: #b3b3b3;
        /* Chữ xám nhạt */
    }

    footer hr {
        border-color: #333333;
        /* Viền đen đậm */
    }

    footer .social-icons a {
        color: #ffffff;
        /* Màu trắng */
        font-size: 20px;
        margin-right: 15px;
        transition: color 0.3s;
    }

    footer .social-icons a:hover {
        color: var(--primary-color);
        /* Màu chính khi hover */
    }

    .insta {
        color: rgb(239, 0, 235);
    }

    .CDF {
        color: rgb(142, 136, 142);
    }

    .hover-text-danger:hover {
        color: var(--primary-color) !important;
        transition: color 0.3s;
    }

    footer a:hover {
        transform: translateX(3px);
        display: inline-block;
    }

    footer a {
        transition: transform 0.3s, color 0.3s;
    }
</style>

<script>
    // Hiển thị thông báo lỗi/thành công
    <?php if (isset($_SESSION['error'])): ?>
        alert('<?php echo $_SESSION['error']; ?>');
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['success'])): ?>
        alert('<?php echo $_SESSION['success']; ?>');
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>
</script>
</body>

</html>