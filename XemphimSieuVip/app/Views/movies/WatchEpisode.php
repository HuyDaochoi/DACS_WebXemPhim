<?php
require_once 'app/Views/shares/header.php';
if (!isset($movie) || !is_array($movie) || !isset($episode)) {
    echo "Dữ liệu không hợp lệ.";
    exit;
}
$tenPhim = htmlspecialchars($movie['TenPhim']);
$tenTap = htmlspecialchars($episode['TenTap']);
$luotXem = $movie['LuotXem'] ?? 0;
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title><?= $tenPhim ?> - <?= $tenTap ?></title>
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

        .episode-list {
            margin-top: 24px;
        }

        .episode-list a {
            margin: 0 4px 8px 0;
            padding: 8px 16px;
            border-radius: 6px;
            border: 1px solid #888;
            color: #eee;
            background: #222;
            text-decoration: none;
            display: inline-block;
        }

        .episode-list a.active,
        .episode-list a:focus {
            background: #e50914;
            color: #fff;
            border-color: #e50914;
            font-weight: bold;
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
        <div class="movie-info">
            <h1><?= $tenPhim ?> - <?= $tenTap ?></h1>
            <p><strong>Lượt xem:</strong> <span><?= $luotXem ?></span></p>
        </div>
        <div class="box-player">
            <?php if (empty($episodeLink)): ?>
                <div class="alert" style="border-color: yellow; color: yellow;">
                    Không có trailer khả dụng cho tập này.
                </div>
            <?php elseif (preg_match('/\.mp4$/i', $episodeLink)): ?>
                <video controls>
                    <source src="<?= htmlspecialchars($episodeLink) ?>" type="video/mp4">
                    Trình duyệt của bạn không hỗ trợ video HTML5.
                </video>
            <?php elseif (strpos($episodeLink, 'drive.google.com') !== false): ?>
                <?php
                if (preg_match('/\/file\/d\/([a-zA-Z0-9_-]+)/', $episodeLink, $matches)) {
                    $embed = "https://drive.google.com/file/d/{$matches[1]}/preview";
                    echo '<iframe src="' . htmlspecialchars($embed) . '" allowfullscreen></iframe>';
                } else {
                    echo '<div class="alert" style="border-color: red; color: red;">Link Google Drive không hợp lệ.</div>';
                }
                ?>
            <?php else: ?>
                <iframe src="<?= htmlspecialchars($episodeLink) ?>" allowfullscreen></iframe>
            <?php endif; ?>
        </div>

        <?php if (!empty($episodes)): ?>
            <div class="episode-list">
                <h5 style="color: #ffb700;">Danh sách tập</h5>
                <?php foreach ($episodes as $ep): ?>
                    <a href="/watch/watchEpisode?tap=<?= $ep['MaTap'] ?>"
                        class="<?= ($ep['MaTap'] == $episode['MaTap']) ? 'active' : '' ?>">
                        <?= htmlspecialchars($ep['TenTap']) ?>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
    <?php require_once 'app/Views/shares/footer.php'; ?>
</body>

</html>