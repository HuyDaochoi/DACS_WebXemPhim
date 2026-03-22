<?php require_once 'app/Views/shares/header.php'; ?>

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
}

.container {
    max-width: 1200px;
    padding: 20px 15px;
}

.card {
    background-color: var(--card-bg);
    border-radius: 6px;
    border: none;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
}

.card-header {
    background: transparent;
    border-bottom: 1px solid var(--border-color);
    padding: 15px 20px;
}

.card-header h3 {
    font-size: 18px;
    font-weight: 600;
    color: var(--text-color);
    margin: 0;
}

.card-body {
    padding: 20px;
}

/* Form Styling */
.form-label {
    font-size: 13px;
    font-weight: 500;
    color: var(--text-color);
    margin-bottom: 6px;
}

.form-control, .form-select {
    background-color: #2A2A2A;
    border: 1px solid var(--border-color);
    color: var(--text-color);
    font-size: 13px;
    padding: 8px;
    border-radius: 4px;
    transition: border-color 0.2s;
}

.form-control:focus, .form-select:focus {
    border-color: var(--primary-color);
    box-shadow: 0 0 0 2px rgba(229, 9, 20, 0.2);
    outline: none;
}

.form-control::placeholder {
    color: var(--text-muted);
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

.btn-warning {
    background-color: #FFC107;
    color: #141414;
    border: none;
}

.btn-warning:hover {
    background-color: #FFD54F;
}

.btn-danger {
    background-color: var(--primary-color);
    color: var(--text-color);
    border: none;
}

.btn-danger:hover {
    background-color: var(--secondary-color);
}

/* Table Styling */
.table {
    color: var(--text-color);
    font-size: 13px;
}

.table th {
    background-color: #2A2A2A;
    color: var(--text-color);
    font-weight: 600;
    padding: 10px;
    border: none;
}

.table td {
    background-color: #FFFFFF;
    padding: 10px;
    border: none;
    vertical-align: middle;
}

.table-striped tbody tr:nth-of-type(odd) {
    background-color: #252525;
}

.table .btn {
    padding: 6px;
    font-size: 12px;
    width: 32px;
    height: 32px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: transform 0.2s ease;
}

.table .btn:hover {
    transform: scale(1.1);
}

.table .btn i {
    margin: 0;
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

.alert-warning {
    background-color: #2A2A2A;
    border: 1px solid #FFC107;
    color: #FFC107;
}

/* Modal Styling */
.modal-content {
    background-color: var(--card-bg);
    border: none;
    border-radius: 6px;
}

.modal-header {
    background: transparent;
    border-bottom: 1px solid var(--border-color);
    padding: 15px 20px;
}

.modal-header h3 {
    font-size: 18px;
    color: var(--text-color);
    margin: 0;
}

.modal-body {
    padding: 20px;
}

.modal-footer {
    border-top: 1px solid var(--border-color);
    padding: 15px 20px;
}

.modal .btn-secondary {
    background-color: #2A2A2A;
    border: 1px solid var(--border-color);
    color: var(--text-color);
    padding: 8px 16px;
    font-size: 13px;
    border-radius: 4px;
}

.modal .btn-secondary:hover {
    background-color: #333333;
}

.btn-close {
    filter: invert(1);
}

/* Validation Feedback */
.invalid-feedback {
    font-size: 12px;
    color: #DC3545;
}

/* Responsive Design */
@media (max-width: 768px) {
    .container {
        padding: 15px 10px;
    }

    .card-header h3,
    .modal-header h3 {
        font-size: 16px;
    }

    .form-label,
    .form-control,
    .form-select,
    .btn,
    .table,
    .alert {
        font-size: 12px;
    }

    .table .btn {
        width: 28px;
        height: 28px;
        font-size: 11px;
    }

    .modal-dialog {
        margin: 10px;
    }
}
</style>

<div class="container mt-4">
    <div class="row">
        <!-- Form thêm tài khoản -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h3>Thêm Tài Khoản</h3>
                </div>
                <div class="card-body">
                    <?php if (isset($_SESSION['error'])): ?>
                        <div class="alert alert-danger"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
                    <?php endif; ?>

                    <?php if (isset($_SESSION['success'])): ?>
                        <div class="alert alert-success"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
                    <?php endif; ?>

                    <form action="/account/store" method="POST" id="addAccountForm">
                        <div class="mb-3">
                            <label for="username" class="form-label">Tên đăng nhập <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="username" name="username" required minlength="3" maxlength="50" pattern="[A-Za-z0-9_]+">
                            <div class="invalid-feedback">Tên đăng nhập phải từ 3-50 ký tự, chỉ chứa chữ, số, hoặc dấu gạch dưới</div>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Mật khẩu <span class="text-danger">*</span></label>
                            <input type="password" class="form-control" id="password" name="password" required minlength="6" maxlength="100">
                            <div class="invalid-feedback">Mật khẩu phải từ 6-100 ký tự</div>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" maxlength="100">
                            <div class="invalid-feedback">Vui lòng nhập email hợp lệ</div>
                        </div>
                        <div class="mb-3">
                            <label for="role" class="form-label">Vai trò</label>
                            <select class="form-select" id="role" name="role">
                                <option value="admin">Admin</option>
                                <option value="user" selected>User</option>
                                <option value="moderator">Moderator</option>
                            </select>
                        </div>
                        <div class="text-end">
                            <button type="submit" class="btn btn-primary">Thêm</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Danh sách tài khoản -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3>Danh Sách Tài Khoản</h3>
                </div>
                <div class="card-body">
                    <?php if (empty($accounts)): ?>
                        <div class="alert alert-warning">Không có tài khoản nào.</div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Tên đăng nhập</th>
                                        <th>Email</th>
                                        <th>Vai trò</th>
                                        <th>Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($accounts as $account): ?>
                                    <tr>
                                        <td><?= $account['id'] ?></td>
                                        <td><?= htmlspecialchars($account['ten_dang_nhap']) ?></td>
                                        <td><?= htmlspecialchars($account['email']) ?></td>
                                        <td><?= htmlspecialchars($account['vai_tro']) ?></td>
                                        <td>
                                            <div class="btn-group">
                                                <button type="button" class="btn btn-warning"
                                                    onclick="editAccount(
                                                        <?= $account['id'] ?>,
                                                        '<?= htmlspecialchars(addslashes($account['ten_dang_nhap']), ENT_QUOTES) ?>',
                                                        '<?= htmlspecialchars(addslashes($account['email']), ENT_QUOTES) ?>',
                                                        '<?= htmlspecialchars($account['vai_tro'], ENT_QUOTES) ?>'
                                                    )"
                                                    title="Sửa">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button type="button" class="btn btn-danger"
                                                    onclick="deleteAccount(<?= $account['id'] ?>)"
                                                    title="Xóa">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal sửa tài khoản -->
<div class="modal fade" id="editAccountModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="editAccountForm" action="/account/update" method="POST">
                <div class="modal-header">
                    <h3>Sửa Tài Khoản</h3>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" id="editAccountId">
                    <div class="mb-3">
                        <label for="editUsername" class="form-label">Tên đăng nhập <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="editUsername" name="username" required minlength="3" maxlength="50" pattern="[A-Za-z0-9_]+">
                        <div class="invalid-feedback">Tên đăng nhập phải từ 3-50 ký tự, chỉ chứa chữ, số, hoặc dấu gạch dưới</div>
                    </div>
                    <div class="mb-3">
                        <label for="editEmail" class="form-label">Email</label>
                        <input type="email" class="form-control" id="editEmail" name="email" maxlength="100">
                        <div class="invalid-feedback">Vui lòng nhập email hợp lệ</div>
                    </div>
                    <div class="mb-3">
                        <label for="editRole" class="form-label">Vai trò</label>
                        <select class="form-select" id="editRole" name="role">
                            <option value="admin">Admin</option>
                            <option value="user">User</option>
                            <option value="moderator">Moderator</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary">Lưu</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function editAccount(id, username, email, role) {
    document.getElementById('editAccountId').value = id;
    document.getElementById('editUsername').value = username;
    document.getElementById('editEmail').value = email;
    document.getElementById('editRole').value = role;
    new bootstrap.Modal(document.getElementById('editAccountModal')).show();
}

function deleteAccount(id) {
    if (confirm('Bạn có chắc chắn muốn xóa tài khoản này?')) {
        window.location.href = '/account/delete/' + id;
    }
}

document.getElementById('addAccountForm').addEventListener('submit', function(e) {
    if (!this.checkValidity()) {
        e.preventDefault();
        this.reportValidity();
    }
});

document.getElementById('editAccountForm').addEventListener('submit', function(e) {
    if (!this.checkValidity()) {
        e.preventDefault();
        this.reportValidity();
    }
});
</script>

<?php require_once 'app/Views/shares/footer.php'; ?>