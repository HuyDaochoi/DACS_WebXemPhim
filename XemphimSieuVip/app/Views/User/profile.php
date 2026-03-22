<?php
require_once 'app/Views/shares/header.php';
?>
<?php

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$db = new Database();
$conn = $db->getConnection();

// Lấy thông tin người dùng
$userId = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT ten_dang_nhap, email, ho_ten, hinh_dai_dien, vai_tro, trang_thai, ngay_tao, ngay_cap_nhat FROM tai_khoan WHERE id = :id LIMIT 1");
$stmt->bindValue(':id', $userId, PDO::PARAM_INT);
$stmt->execute();

$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    die("Không tìm thấy thông tin tài khoản.");
}
?>

<style>
:root {
    --primary-color: #E50914; /* Netflix red */
    --secondary-color: #B20710; /* Darker red for hover */
    --bg-color: #141414; /* Netflix dark background */
    --text-color: #FFFFFF; /* White text */
    --text-muted: #B3B3B3; /* Muted gray text */
    --card-bg: #1F1F1F; /* Card background */
    --border-color: #333333; /* Dark border */
}

body {
    background-color: var(--bg-color);
    color: var(--text-color);
    font-family: 'Roboto', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
    margin: 0;
    padding: 20px 15px;
}

.container {
    max-width: 1200px;
    margin: 0 auto;
    display: grid;
    grid-template-columns: 1fr;
    gap: 20px;
    justify-items: center;
}

@media (min-width: 768px) {
    .container {
        grid-template-columns: 1fr 1fr;
        align-items: start;
    }
    .profile-info-card {
        position: sticky;
        top: 20px;
    }
}

.card {
    background-color: var(--card-bg);
    border-radius: 6px;
    border: none;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
    width: 100%;
    max-width: 500px;
}

.card-header {
    background: transparent;
    border-bottom: 1px solid var(--border-color);
    padding: 15px 20px;
}

.card-header h2 {
    font-size: 18px;
    font-weight: 600;
    color: var(--text-color);
    margin: 0;
}

.card-body {
    padding: 20px;
}

/* Profile Info */
.profile-info {
    text-align: center;
}

.profile-info .avatar {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid var(--primary-color);
    margin-bottom: 15px;
}

.profile-info dl {
    margin: 0;
}

.profile-info dt {
    font-size: 13px;
    font-weight: 600;
    color: var(--text-muted);
    margin-bottom: 5px;
}

.profile-info dd {
    font-size: 14px;
    color: var(--text-color);
    margin-bottom: 15px;
}

/* Form Styling */
.form-label {
    font-size: 13px;
    font-weight: 500;
    color: var(--text-color);
    margin-bottom: 6px;
}

.form-control {
    background-color: #2A2A2A;
    border: 1px solid var(--border-color);
    color: var(--text-color);
    font-size: 13px;
    padding: 8px;
    border-radius: 4px;
    transition: border-color 0.2s;
    width: 100%;
}

.form-control:focus {
    border-color: var(--primary-color);
    box-shadow: 0 0 0 2px rgba(229, 9, 20, 0.2);
    outline: none;
}

.form-control::placeholder {
    color: var(--text-muted);
}

.form-control[type="file"] {
    padding: 6px;
}

.btn-primary {
    background-color: var(--primary-color);
    border: none;
    padding: 8px 16px;
    font-size: 13px;
    font-weight: 500;
    border-radius: 4px;
    color: var(--text-color);
    transition: background-color 0.2s;
}

.btn-primary:hover {
    background-color: var(--secondary-color);
}

.btn-secondary {
    background-color: #2A2A2A;
    border: 1px solid var(--border-color);
    color: var(--text-color);
    padding: 8px 16px;
    font-size: 13px;
    border-radius: 4px;
}

.btn-secondary:hover {
    background-color: #333333;
}

/* Alert Styling */
.alert {
    border-radius: 4px;
    font-size: 13px;
    padding: 10px;
    margin-bottom: 15px;
}

.alert-success {
    background-color: #2A2A2A;
    border: 1px solid #28A745;
    color: #28A745;
}

.alert-danger {
    background-color: #2A2A2A;
    border: 1px solid #DC3545;
    color: #DC3545;
}

/* Navigation */
.nav-link {
    display: inline-block;
    padding: 8px 16px;
    background-color: #2A2A2A;
    color: var(--text-color);
    text-decoration: none;
    border-radius: 4px;
    font-size: 13px;
    margin-bottom: 20px;
    transition: background-color 0.2s;
}

.nav-link:hover {
    background-color: var(--primary-color);
}

/* Validation Feedback */
.invalid-feedback {
    font-size: 12px;
    color: #DC3545;
}

/* Separator */
hr {
    border: none;
    border-top: 1px solid var(--border-color);
    margin: 20px 0;
}

/* Responsive Design */
@media (max-width: 576px) {
    body {
        padding: 15px 10px;
    }

    .card-header h2 {
        font-size: 16px;
    }

    .form-label,
    .form-control,
    .btn,
    .alert,
    .nav-link,
    .profile-info dt {
        font-size: 12px;
    }

    .profile-info dd {
        font-size: 13px;
    }

    .profile-info .avatar {
        width: 100px;
        height: 100px;
    }

    .card {
        max-width: 100%;
    }
}
</style>

<div class="container">
    <nav style="grid-column: 1 / -1;">
        
    </nav>

    <!-- Thông tin cá nhân -->
    <div class="card profile-info-card">        
        <div class="card-header">
            <a href="/" class="nav-link"><i class="bi bi-arrow-left"></i> Quay lại</a>
            <h2>Thông Tin Cá Nhân</h2>
        </div>
        <div class="card-body profile-info">
            <img src="<?php echo !empty($user['hinh_dai_dien']) ? '/' . $user['hinh_dai_dien'] : '/assets/images/default-avatar.png'; ?>"
                 alt="Avatar" class="avatar">
            <dl>
                <dt>Tên đăng nhập</dt>
                <dd><?= htmlspecialchars($user['ten_dang_nhap']) ?></dd>
                <dt>Họ tên</dt>
                <dd><?= htmlspecialchars($user['ho_ten'] ?: 'Chưa cập nhật') ?></dd>
                <dt>Email</dt>
                <dd><?= htmlspecialchars($user['email'] ?: 'Chưa cập nhật') ?></dd>
                <dt>Vai trò</dt>
                <dd><?= htmlspecialchars(ucfirst($user['vai_tro'])) ?></dd>
            </dl>
        </div>
    </div>

    <!-- Form container -->
    <div class="form-container">
        <!-- Cập nhật ảnh đại diện -->
        <div class="card">
            <div class="card-header">
                <h2>Cập Nhật Ảnh Đại Diện</h2>
            </div>
            <div class="card-body">
                <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($_SESSION['error']) ?></div>
                    <?php unset($_SESSION['error']); ?>
                <?php endif; ?>
                <?php if (isset($_SESSION['success'])): ?>
                    <div class="alert alert-success"><?= htmlspecialchars($_SESSION['success']) ?></div>
                    <?php unset($_SESSION['success']); ?>
                <?php endif; ?>
                <form action="/account/updateAvatar" method="post" enctype="multipart/form-data" id="avatarForm">
                    <div class="mb-3">
                        <label for="avatar" class="form-label">Chọn ảnh đại diện</label>
                        <input type="file" class="form-control" id="avatar" name="avatar" accept="image/*" required>
                        <div class="invalid-feedback">Vui lòng chọn một tệp hình ảnh</div>
                    </div>
                    <div class="text-end">
                        <button type="submit" class="btn btn-primary">Cập nhật</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Chỉnh sửa thông tin -->
        <div class="card">
            <div class="card-header">
                <h2>Chỉnh Sửa Thông Tin</h2>
            </div>
            <div class="card-body">
                <form action="/account/editProfile" method="POST" id="editProfileForm">
                    <input type="hidden" name="id" value="<?= $userId ?>">
                    <div class="mb-3">
                        <label for="editHoTen" class="form-label">Họ tên</label>
                        <input type="text" class="form-control" id="editHoTen" name="ho_ten" value="<?= htmlspecialchars($user['ho_ten'] ?? '') ?>" maxlength="100">
                        <div class="invalid-feedback">Họ tên tối đa 100 ký tự</div>
                    </div>
                    <div class="mb-3">
                        <label for="editEmail" class="form-label">Email</label>
                        <input type="email" class="form-control" id="editEmail" name="email" value="<?= htmlspecialchars($user['email'] ?? '') ?>" maxlength="100">
                        <div class="invalid-feedback">Vui lòng nhập email hợp lệ</div>
                    </div>
                    <div class="text-end">
                        <button type="button" class="btn btn-secondary" onclick="resetForm()">Hủy</button>
                        <button type="submit" class="btn btn-primary">Lưu</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Đổi mật khẩu -->
        <div class="card">
            <div class="card-header">
                <h2>Đổi Mật Khẩu</h2>
            </div>
            <div class="card-body">
                <?php if (!empty($errorMessage)): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($errorMessage) ?></div>
                <?php endif; ?>
                <?php if (!empty($successMessage)): ?>
                    <div class="alert alert-success"><?= htmlspecialchars($successMessage) ?></div>
                <?php endif; ?>
                <form action="/account/updatePassword" method="POST" id="passwordForm">
                    <div class="mb-3">
                        <label for="old_password" class="form-label">Mật khẩu cũ</label>
                        <input type="password" class="form-control" id="old_password" name="old_password" required minlength="6" maxlength="100">
                        <div class="invalid-feedback">Mật khẩu cũ phải từ 6-100 ký tự</div>
                    </div>
                    <div class="mb-3">
                        <label for="new_password" class="form-label">Mật khẩu mới</label>
                        <input type="password" class="form-control" id="new_password" name="new_password" required minlength="6" maxlength="100">
                        <div class="invalid-feedback">Mật khẩu mới phải từ 6-100 ký tự</div>
                    </div>
                    <div class="mb-3">
                        <label for="confirm_password" class="form-label">Xác nhận mật khẩu mới</label>
                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" required minlength="6" maxlength="100">
                        <div class="invalid-feedback">Xác nhận mật khẩu không khớp</div>
                    </div>
                    <div class="text-end">
                        <button type="button" class="btn btn-secondary" onclick="resetForm()">Hủy</button>
                        <button type="submit" class="btn btn-primary">Đổi</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>



<?php require_once 'app/Views/shares/footer.php'; ?>