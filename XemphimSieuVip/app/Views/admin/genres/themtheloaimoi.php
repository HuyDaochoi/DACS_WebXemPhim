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
    color: #FFFFFF;
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
    color: #FFFFFF;
    margin: 0;
}

.card-body {
    padding: 20px;
}

/* Form Styling */
.form-label {
    font-size: 13px;
    font-weight: 500;
    color: #FFFFFF;
    margin-bottom: 6px;
}

.form-control {
    background-color: #2A2A2A;
    border: 1px solid var(--border-color);
    color: #FFFFFF;
    font-size: 13px;
    padding: 8px;
    border-radius: 4px;
    transition: border-color 0.2s;
}

.form-control:focus {
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
    color: #FFFFFF;
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
    color: #FFFFFF  ;
    font-size: 13px;
}

.table th {
    background-color: #2A2A2A;
    color: #FFFFFF;
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
        <!-- Form thêm thể loại -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h3>Thêm Thể Loại</h3>
                </div>
                <div class="card-body">
                    <?php if (isset($_SESSION['error'])): ?>
                        <div class="alert alert-danger">
                            <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                        </div>
                    <?php endif; ?>

                    <?php if (isset($_SESSION['success'])): ?>
                        <div class="alert alert-success">
                            <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
                        </div>
                    <?php endif; ?>

                    <form action="/category/storeTheLoai" method="POST" id="addGenreForm">
                        <div class="mb-3">
                            <label for="name" class="form-label">Tên thể loại <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="name" name="name" required minlength="2" maxlength="50">
                            <div class="invalid-feedback">Tên thể loại phải từ 2-50 ký tự</div>
                        </div>
                        <div class="text-end">
                            <button type="submit" class="btn btn-primary">Thêm</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Danh sách thể loại -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3>Danh Sách Thể Loại</h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Tên thể loại</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($genres as $genre): ?>
                                <tr>
                                    <td><?php echo $genre['MaTheLoai']; ?></td>
                                    <td><?php echo htmlspecialchars($genre['TenTheLoai']); ?></td>
                                    <td>
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-warning" 
                                                    onclick="editGenre(<?php echo $genre['MaTheLoai']; ?>, '<?php echo htmlspecialchars(addslashes($genre['TenTheLoai'])); ?>')"
                                                    title="Sửa">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button type="button" class="btn btn-danger" 
                                                    onclick="deleteGenre(<?php echo $genre['MaTheLoai']; ?>)"
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
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal sửa thể loại -->
<div class="modal fade" id="editGenreModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Sửa Thể Loại</h3>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="editGenreForm" action="/category/updateTheLoai" method="POST">
                    <input type="hidden" name="id" id="editGenreId">
                    <div class="mb-3">
                        <label for="editGenreName" class="form-label">Tên thể loại <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="editGenreName" name="name" required minlength="2" maxlength="50">
                        <div class="invalid-feedback">Tên thể loại phải từ 2-50 ký tự</div>
                    </div>
                </form>
            </div>
           ？”

<div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <button type="button" class="btn btn-primary" onclick="submitEditGenre()">Lưu</button>
            </div>
        </div>
    </div>
</div>

<script>
function editGenre(id, name) {
    document.getElementById('editGenreId').value = id;
    document.getElementById('editGenreName').value = name;
    new bootstrap.Modal(document.getElementById('editGenreModal')).show();
}

function submitEditGenre() {
    const form = document.getElementById('editGenreForm');
    if (form.checkValidity()) {
        form.submit();
    } else {
        form.reportValidity();
    }
}

function deleteGenre(id) {
    if (confirm('Bạn có chắc chắn muốn xóa thể loại này?')) {
        window.location.href = '/category/deleteTheLoai/' + id;
    }
}

document.getElementById('addGenreForm').addEventListener('submit', function(e) {
    if (!this.checkValidity()) {
        e.preventDefault();
        this.reportValidity();
    }
});
</script>

<?php require_once 'app/Views/shares/footer.php'; ?>