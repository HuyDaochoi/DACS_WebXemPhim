<?php
// Lấy danh sách thể loại và quốc gia
require_once 'app/Models/MovieModel.php';
require_once 'app/configs/database.php';

$db = new Database();
$movieModel = new MovieModel($db);

try {
  $genres = $movieModel->getAllGenres();
  $countries = $movieModel->getAllCountries();
} catch (PDOException $e) {
  $genres = [];
  $countries = [];
}
?>
<!DOCTYPE html>
<html lang="vi">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Xemp - Xem phim HD Online</title>
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <!-- Select2 CSS -->
  <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
  <!-- Swiper CSS -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.css" />
  <!-- Google Fonts (Netflix Sans alternative: Roboto) -->
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
  <!-- jQuery -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <!-- Select2 JS -->
  <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
  <!-- Bootstrap JS Bundle -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <!-- Swiper JS -->
  <script src="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.js"></script>

  <style>
    :root {
      --primary-color: #E50914;
      /* Netflix red */
      --secondary-color: #B20710;
      --header-bg: #141414;
      /* Netflix dark background */
      --text-color: #FFFFFF;
      /* White text */
      --text-muted: #B3B3B3;
      /* Light gray for muted text */
      --card-bg: #1F1F1F;
      /* Slightly lighter dark for cards */
      --border-color: #333333;
      /* Dark border */
    }

    body {
      background-color: #000000;
      color: var(--text-color);
      font-family: 'Roboto', sans-serif;
      margin: 0;
    }

    /* Header Styles */
    .header {
      background-color: var(--header-bg);
      padding: 15px 0;
      position: fixed;
      width: 100%;
      top: 0;
      z-index: 1000;
      border-bottom: 1px solid var(--border-color);
      transition: background-color 0.3s;
    }

    .header__nav {
      display: flex;
      align-items: center;
      justify-content: space-between;
      max-width: 1400px;
      margin: 0 auto;
      padding: 0 20px;
    }

    .header__logo {
      color: var(--primary-color);
      font-size: 32px;
      font-weight: 700;
      text-decoration: none;
      letter-spacing: 2px;
      transition: transform 0.2s;
    }

    .header__logo:hover {
      transform: scale(1.05);
    }

    /* Navigation Menu */
    .header__menu {
      display: flex;
      gap: 20px;
      list-style: none;
      margin: 0;
      padding: 0;
    }

    .header__menu-item {
      color: var(--text-color);
      text-decoration: none;
      font-size: 16px;
      font-weight: 500;
      transition: color 0.2s;
    }

    .header__menu-item:hover {
      color: var(--primary-color);
    }

    /* Search Form */
    .search-form {
      position: relative;
      flex: 1;
      max-width: 400px;
      margin: 0 20px;
    }

    .search-form .form-control {
      background-color: #333333;
      border: none;
      color: var(--text-color);
      padding: 8px 40px 8px 15px;
      border-radius: 4px;
      font-size: 14px;
      transition: background-color 0.3s;
    }

    .search-form .form-control:focus {
      background-color: #444444;
      box-shadow: none;
      border: 1px solid var(--primary-color);
    }

    .search-form .btn {
      position: absolute;
      right: 10px;
      top: 50%;
      transform: translateY(-50%);
      background: transparent;
      border: none;
      color: var(--text-muted);
    }

    .search-form .btn:hover {
      color: var(--primary-color);
    }

    .search-results {
      position: absolute;
      top: 100%;
      left: 0;
      right: 0;
      background: var(--header-bg);
      border: 1px solid var(--border-color);
      border-radius: 4px;
      margin-top: 5px;
      max-height: 400px;
      overflow-y: auto;
      display: none;
      z-index: 1000;
    }

    .search-results.active {
      display: block;
    }

    .search-result-item {
      padding: 10px;
      border-bottom: 1px solid var(--border-color);
      display: flex;
      align-items: center;
      gap: 10px;
      text-decoration: none;
      color: var(--text-color);
      transition: background-color 0.2s;
    }

    .search-result-item:hover {
      background-color: #2A2A2A;
    }

    .search-result-item img {
      width: 50px;
      height: 75px;
      object-fit: cover;
      border-radius: 4px;
    }

    .search-result-title {
      font-weight: 500;
      font-size: 14px;
      margin-bottom: 3px;
    }

    .search-result-meta {
      font-size: 12px;
      color: var(--text-muted);
    }

    /* Dropdown Styles */
    .dropdown-menu {
      background: #1A1A1A;
      border: 1px solid var(--border-color);
      border-radius: 4px;
      padding: 10px;
      min-width: 200px;
    }

    .dropdown-item {
      color: var(--text-color);
      padding: 8px 15px;
      border-radius: 4px;
      font-size: 14px;
      transition: background-color 0.2s, color 0.2s;
    }

    .dropdown-item:hover {
      background-color: #2A2A2A;
      color: var(--primary-color);
    }

    .dropdown-item i {
      margin-right: 8px;
    }

    /* Auth Buttons */
    .auth-buttons {
      display: flex;
      gap: 10px;
      align-items: center;
    }

    .auth-buttons__login,
    .auth-buttons__register {
      padding: 6px 16px;
      border-radius: 4px;
      text-decoration: none;
      font-size: 14px;
      font-weight: 500;
      transition: background-color 0.2s, color 0.2s;
    }

    .auth-buttons__login {
      background-color: var(--primary-color);
      color: #FFFFFF;
    }

    .auth-buttons__login:hover {
      background-color: var(--secondary-color);
      color: #FFFFFF;
    }

    .auth-buttons__register {
      background-color: transparent;
      color: var(--text-color);
      border: 1px solid var(--text-muted);
    }

    .auth-buttons__register:hover {
      border-color: var(--primary-color);
      color: var(--primary-color);
    }

    /* User Dropdown */
    .user-dropdown {
      display: flex;
      align-items: center;
      gap: 8px;
      color: var(--text-color);
      font-size: 14px;
      font-weight: 500;
      text-decoration: none;
      padding: 6px 12px;
      border-radius: 4px;
      transition: background-color 0.2s;
    }

    .user-dropdown:hover {
      background-color: #2A2A2A;
    }

    .user-dropdown img {
      width: 32px;
      height: 32px;
      object-fit: cover;
      border-radius: 50%;
    }

    /* Admin Dropdown */
    .admin-dropdown .btn {
      background-color: var(--primary-color);
      border: none;
      padding: 6px 12px;
      font-size: 14px;
      font-weight: 500;
      border-radius: 4px;
      transition: background-color 0.2s;
    }

    .admin-dropdown .btn:hover {
      background-color: var(--secondary-color);
    }

    /* Main Content Spacing */
    .main-content {
      margin-top: 80px;
      padding: 20px 0;
    }

    /* Responsive Design */
    @media (max-width: 992px) {
      .header__nav {
        flex-wrap: wrap;
        gap: 10px;
      }

      .search-form {
        order: 3;
        width: 100%;
        max-width: none;
        margin: 10px 0;
      }

      .header__menu {
        gap: 15px;
      }
    }

    @media (max-width: 576px) {
      .header__logo {
        font-size: 24px;
      }

      .header__menu {
        flex-direction: column;
        align-items: flex-start;
      }

      .auth-buttons {
        flex-direction: column;
        gap: 5px;
      }
    }
    .genre-dropdown .dropdown-menu,
    .country-dropdown .dropdown-menu {
      background-color: #141414;
      border: 1px solid #333;
      border-radius: 8px;
      width: 500px;
    }

    .genre-dropdown .dropdown-item,
    .country-dropdown .dropdown-item {
      color: #fff;
      font-size: 14px;
      padding: 6px 12px;
      border-radius: 4px;
      transition: background-color 0.2s ease;
    }

    .genre-dropdown .dropdown-item:hover,
    .country-dropdown .dropdown-item:hover {
      background-color: #2a2a2a;
      color: #ff3c00;
    }
  </style>
</head>

<body>
  <header class="header">
    <nav class="header__nav">
      <a href="/" class="header__logo">XEMP</a>

      <!-- Navigation Menu -->    
      <ul class="header__menu">
        <li class="nav-item dropdown genre-dropdown">
        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
          <i class="fas fa-tags"></i> Thể loại
        </a>
        <div class="dropdown-menu mega-menu p-3">
          <div class="row row-cols-3 g-2">
            <?php foreach ($genres as $genre): ?>
              <div class="col">
                <a class="dropdown-item" href="/search/theloai/<?php echo $genre['MaTheLoai']; ?>">
                  <?php echo $genre['TenTheLoai']; ?>
                </a>
              </div>
            <?php endforeach; ?>
          </div>
        </div>
      </li>

        <li class="nav-item dropdown country-dropdown">
          <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
            <i class="fas fa-globe-asia"></i> Quốc gia
          </a>
          <div class="dropdown-menu mega-menu p-3">
            <div class="row row-cols-3 g-2">
              <?php foreach ($countries as $country): ?>
                <div class="col">
                  <a class="dropdown-item" href="/search/quocgia/<?php echo $country['MaQuocGia']; ?>">
                    <?php echo $country['TenQuocGia']; ?>
                  </a>
                </div>
              <?php endforeach; ?>
            </div>
          </div>
        </li>



        <li><a href="/YeuThich/listYeuthich" class="header__menu-item">Phim yêu thích</a></li>
        <li><a href="/LichSu/index" class="header__menu-item">Lịch sử xem</a></li> <!-- Thêm dòng này -->
      </ul>

      <!-- Search Form -->
      <div class="search-form">
        <input type="text" class="form-control" id="searchInput" placeholder="Tìm kiếm phim..." autocomplete="off">
        <button type="button" class="btn">
          <i class="fas fa-search"></i>
        </button>
        <div class="search-results" id="searchResults"></div>
      </div>

      <!-- Auth Section -->
      <div class="auth-buttons">
        <?php if (isset($_SESSION['user_id'])): ?>
          <?php if ($_SESSION['vai_tro'] === 'admin'): ?>
            <div class="admin-dropdown dropdown">
              <button class="btn dropdown-toggle" type="button" data-bs-toggle="dropdown">
                <i class="fas fa-cog"></i> Quản lý
              </button>
              <ul class="dropdown-menu">
                <li>
                  <a class="dropdown-item" href="/movie/list">
                    <i class="fas fa-film"></i> Quản lý phim
                  </a>
                </li>
                <li>
                  <a class="dropdown-item" href="/category/themtheloaimoi">
                    <i class="fas fa-tags"></i> Quản lý thể loại
                  </a>
                </li>
                <li>
                  <a class="dropdown-item" href="/category/themquocgiamoi">
                    <i class="fas fa-globe"></i> Quản lý quốc gia
                  </a>
                </li>
                <li>
                  <a class="dropdown-item" href="/category/manage_account">
                    <i class="fas fa-user-cog"></i> Quản lý tài khoản
                  </a>
                </li>

              </ul>
            </div>
          <?php endif; ?>
          <div class="dropdown">
            <a class="user-dropdown dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
              <img src="<?php echo !empty($_SESSION['hinh_dai_dien']) ? '/' . $_SESSION['hinh_dai_dien'] : '/assets/images/default-avatar.png'; ?>"
                alt="Avatar">
              <span><?php echo htmlspecialchars($_SESSION['username'] ?? ''); ?></span>
            </a>
            <ul class="dropdown-menu dropdown-menu-end">
              <li><a class="dropdown-item" href="/account/profile"><i class="fas fa-user-edit"></i> Hồ sơ</a></li>
              <li>
                <hr class="dropdown-divider">
              </li>
              <li><a class="dropdown-item text-danger" href="/account/logout"><i class="fas fa-sign-out-alt"></i> Đăng xuất</a></li>
            </ul>
          </div>
        <?php else: ?>
          <a href="/account/login" class="auth-buttons__login">Đăng nhập</a>
          <a href="/account/register" class="auth-buttons__register">Đăng ký</a>
        <?php endif; ?>
      </div>
    </nav>
  </header>
  <div class="main-content">

    <script>
      document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('searchInput');
        const searchResults = document.getElementById('searchResults');
        let searchTimeout;

        searchInput.addEventListener('input', function() {
          clearTimeout(searchTimeout);
          const keyword = this.value.trim();

          if (keyword.length < 2) {
            searchResults.classList.remove('active');
            return;
          }

          searchTimeout = setTimeout(() => {
            fetch(`/search/quick?keyword=${encodeURIComponent(keyword)}`)
              .then(response => response.json())
              .then(data => {
                if (data.success && data.movies.length > 0) {
                  searchResults.innerHTML = data.movies.map(movie => `
                    <a href="/movie/view/${movie.MaPhim}" class="search-result-item">
                      <img src="${movie.HinhAnh ? '/' + movie.HinhAnh : 'https://via.placeholder.com/50x75?text=No+Image'}" 
                           alt="${movie.TenPhim}">
                      <div class="search-result-info">
                        <div class="search-result-title">${movie.TenPhim}</div>
                        <div class="search-result-meta">
                          ${movie.NamPhatHanh} • ${movie.ThoiLuong} phút
                        </div>
                      </div>
                    </a>
                  `).join('');
                  searchResults.classList.add('active');
                } else {
                  searchResults.innerHTML = '<div class="p-3 text-center text-muted">Không tìm thấy kết quả</div>';
                  searchResults.classList.add('active');
                }
              })
              .catch(error => {
                console.error('Error:', error);
                searchResults.classList.remove('active');
              });
          }, 300);
        });

        // Close search results when clicking outside
        document.addEventListener('click', function(e) {
          if (!searchInput.contains(e.target) && !searchResults.contains(e.target)) {
            searchResults.classList.remove('active');
          }
        });

        // Handle Enter key for search
        searchInput.addEventListener('keypress', function(e) {
          if (e.key === 'Enter') {
            const keyword = this.value.trim();
            if (keyword.length >= 2) {
              window.location.href = `/search?keyword=${encodeURIComponent(keyword)}`;
            }
          }
        });
      });
    </script>
</body>

</html>