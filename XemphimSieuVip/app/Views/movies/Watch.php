<?php
require_once 'app/Views/shares/header.php';

// Kiểm tra nếu biến $movie tồn tại và là mảng
if (!isset($movie) || !is_array($movie)) {
    $_SESSION['error'] = 'Dữ liệu phim không hợp lệ.';
    header('Location: /');
    exit;
}

$link = $movie['Link'] ?? '';
$tenPhim = htmlspecialchars($movie['TenPhim']);
$luotXem = $movie['LuotXem'] ?? 0;
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>XEM TRAILER - Xemp</title>
    <style>
        body {
            background: #111;
            color: #eee;
            font-family: Arial, sans-serif;
            padding: 20px;
            margin: 0;
        }

        .container {
            max-width: 1500px;
            margin: auto;
        }

        .movie-info {
            margin-top: 20px;
            text-align: left;
        }

        .movie-info h1 {
            font-size: 32px;
            margin-bottom: 10px;
            color: red;
        }

        .movie-info p {
            font-size: 18px;
            color: white;
        }

        .movie-info strong {
            color: lightblue;
        }

        .box-player {
            display: flex;
            justify-content: center;
            margin-bottom: 20px;
        }

        video,
        iframe {
            width: 80%;
            max-width: 1200px;
            height: 650px;
            border-radius: 10px;
            padding: 20px;
            border: none;
        }

        .alert {
            text-align: center;
            padding: 10px;
            border: 1px dashed #e25ddb;
            margin-bottom: 20px;
        }

        @media (max-width: 768px) {

            video,
            iframe {
                width: 100%;
                height: 225px;
                padding: 5px;
            }
        }
    </style>
</head>

<body>

    <div class="container">
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert" style="border-color: red; color: red;">
                <?php echo $_SESSION['error'];
                unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <div class="movie-info">
            <h1><?= $tenPhim ?></h1>
            <p><strong>Lượt xem:</strong> <span><?= $luotXem ?></span></p>
        </div>

        <div class="box-player">
            <?php if (empty($link)): ?>
                <div class="alert" style="border-color: yellow; color: yellow;">
                    Không có trailer khả dụng cho phim này.
                </div>
            <?php elseif (preg_match('/\.mp4$/i', $link)): ?>
                <video controls>
                    <source src="<?= htmlspecialchars($link) ?>" type="video/mp4">
                    Trình duyệt của bạn không hỗ trợ video HTML5.
                </video>
            <?php elseif (strpos($link, 'drive.google.com') !== false): ?>
                <?php
                if (preg_match('/\/file\/d\/([a-zA-Z0-9_-]+)/', $link, $matches)) {
                    $embed = "https://drive.google.com/file/d/{$matches[1]}/preview";
                    echo '<iframe src="' . htmlspecialchars($embed) . '" allowfullscreen></iframe>';
                } else {
                    echo '<div class="alert" style="border-color: red; color: red;">Link Google Drive không hợp lệ.</div>';
                }
                ?>
            <?php else: ?>
                <iframe src="<?= htmlspecialchars($link) ?>" allowfullscreen></iframe>
            <?php endif; ?>
        </div>

        <div class="comments-section mt-4">
            <h5 class="section-title">Bình luận</h5>

            <!-- Form thêm bình luận -->
            <?php if (isset($_SESSION['user_id'])): ?>
                <form id="comment-form">
                    <input type="hidden" id="movie-id" value="<?php echo $movie['MaPhim']; ?>">
                    <textarea id="comment-content" class="form-control mb-2" rows="3" placeholder="Viết bình luận..."></textarea>
                    <button type="button" id="submit-comment" class="btn btn-primary">Gửi</button>
                </form>
            <?php else: ?>
                <p>Vui lòng <a href="/account/login">đăng nhập</a> để bình luận.</p>
            <?php endif; ?>

            <!-- Danh sách bình luận -->
            <div id="comments-list" class="mt-3"></div>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const movieId = document.getElementById('movie-id').value;

                // Lấy danh sách bình luận
                function loadComments() {
                    fetch(`/comment/getComments?movie_id=${movieId}`)
                        .then(response => response.json())
                        .then(data => {
                            const commentsList = document.getElementById('comments-list');
                            commentsList.innerHTML = '';
                            data.forEach(comment => {
                                commentsList.innerHTML += `
                                <div class="comment-item">
                                    <strong>${comment.username}</strong> - <small>${comment.ThoiGian}</small>
                                    <p>${comment.NoiDung}</p>
                                    <?php if (isset($_SESSION['vai_tro']) && $_SESSION['vai_tro'] === 'admin'): ?>
                                        <button class="btn btn-danger btn-sm" onclick="deleteComment(${comment.MaBinhLuan})">Xóa</button>
                                    <?php endif; ?>
                                </div>
                            `;
                            });
                        });
                }

                loadComments();

                // Thêm bình luận
                document.getElementById('submit-comment').addEventListener('click', function() {
                    const content = document.getElementById('comment-content').value.trim();
                    if (!content) {
                        alert('Nội dung bình luận không được để trống.');
                        return;
                    }

                    fetch('/comment/addComment', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded'
                            },
                            body: `movie_id=${movieId}&content=${encodeURIComponent(content)}`
                        })
                        .then(response => response.json())
                        .then(data => {
                            alert(data.message);
                            if (data.success) {
                                document.getElementById('comment-content').value = '';
                                loadComments();
                            }
                        });
                });

                // Xóa bình luận
                window.deleteComment = function(commentId) {
                    if (!confirm('Bạn có chắc chắn muốn xóa bình luận này?')) return;

                    fetch('/comment/deleteComment', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded'
                            },
                            body: `comment_id=${commentId}`
                        })
                        .then(response => response.json())
                        .then(data => {
                            alert(data.message);
                            if (data.success) {
                                loadComments();
                            }
                        });
                };
            });
        </script>
    </div>

    <?php require_once 'app/Views/shares/footer.php'; ?>
</body>

</html>