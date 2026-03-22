<?php
require_once 'app/Views/shares/header.php';

if (!isset($_SESSION['vai_tro']) || $_SESSION['vai_tro'] !== 'admin') {
    $_SESSION['error'] = 'Bạn không có quyền truy cập trang này!';
    header('Location: /');
    exit;
}

// Get search and page from query params
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$perPage = 10;
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý bình luận</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #f4f4f4;
            font-family: Arial, sans-serif;
        }
        .container {
            max-width: 1200px;
            margin: 20px auto;
        }
        .table {
            background: white;
            border-radius: 8px;
            overflow: hidden;
        }
        .search-form {
            margin-bottom: 20px;
        }
        .pagination {
            justify-content: center;
            margin-top: 20px;
        }
        .modal-content {
            border-radius: 8px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="mb-4">Quản lý bình luận</h1>

        <div id="alert-container"></div>

        <!-- Search Form -->
        <form class="search-form" id="search-form">
            <div class="input-group">
                <input type="text" name="search" class="form-control" placeholder="Tìm theo tên người dùng hoặc phim" value="<?php echo htmlspecialchars($search); ?>">
                <button type="submit" class="btn btn-primary">Tìm kiếm</button>
            </div>
        </form>

        <!-- Comments Table -->
        <table class="table table-bordered table-hover">
            <thead class="table-dark">
                <tr>
                    <th>Người dùng</th>
                    <th>Phim</th>
                    <th>Nội dung</th>
                    <th>Thời gian</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody id="comments-table-body">
                <!-- Comments will be populated by JavaScript -->
            </tbody>
        </table>

        <!-- Pagination -->
        <nav>
            <ul class="pagination" id="pagination"></ul>
        </nav>

        <!-- Edit Modal -->
        <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editModalLabel">Sửa bình luận</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="edit-comment-form">
                            <input type="hidden" id="edit-comment-id">
                            <div class="mb-3">
                                <label for="edit-comment-content" class="form-label">Nội dung</label>
                                <textarea class="form-control" id="edit-comment-content" rows="4" required></textarea>
                            </div>
                            <button type="button" class="btn btn-primary" onclick="updateComment()">Cập nhật</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const perPage = <?php echo $perPage; ?>;
        let currentPage = <?php echo $page; ?>;
        let searchQuery = '<?php echo addslashes($search); ?>';

        // Fetch comments on page load
        fetchComments(currentPage, searchQuery);

        // Handle search form submission
        document.getElementById('search-form').addEventListener('submit', function(e) {
            e.preventDefault();
            searchQuery = this.querySelector('input[name="search"]').value;
            currentPage = 1;
            fetchComments(currentPage, searchQuery);
            updateUrl();
        });

        function fetchComments(page, search) {
            const url = `/api/comments/admin?page=${page}&per_page=${perPage}&search=${encodeURIComponent(search)}`;
            fetch(url)
                .then(response => response.json())
                .then(data => {
                    if (!data.success) {
                        showAlert('danger', data.message);
                        return;
                    }
                    renderComments(data.data);
                    renderPagination(data.total, data.page, data.per_page);
                })
                .catch(error => showAlert('danger', 'Lỗi khi tải bình luận: ' + error.message));
        }

        function renderComments(comments) {
            const tbody = document.getElementById('comments-table-body');
            tbody.innerHTML = '';
            if (comments.length === 0) {
                tbody.innerHTML = '<tr><td colspan="5" class="text-center">Không có bình luận nào.</td></tr>';
                return;
            }
            comments.forEach(comment => {
                const row = `
                    <tr>
                        <td>${escapeHtml(comment.username)}</td>
                        <td>${escapeHtml(comment.TenPhim)}</td>
                        <td>${escapeHtml(comment.NoiDung)}</td>
                        <td>${formatDate(comment.ThoiGian)}</td>
                        <td>
                            <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editModal" 
                                    onclick="setEditModal(${comment.MaBinhLuan}, '${escapeHtml(comment.NoiDung).replace(/'/g, "\\'")}')">Sửa</button>
                            <button class="btn btn-danger btn-sm" onclick="deleteComment(${comment.MaBinhLuan})">Xóa</button>
                        </td>
                    </tr>
                `;
                tbody.innerHTML += row;
            });
        }

        function renderPagination(total, page, perPage) {
            const totalPages = Math.ceil(total / perPage);
            const pagination = document.getElementById('pagination');
            pagination.innerHTML = '';
            if (totalPages <= 1) return;

            for (let i = 1; i <= totalPages; i++) {
                const li = document.createElement('li');
                li.className = `page-item ${i === page ? 'active' : ''}`;
                li.innerHTML = `<a class="page-link" href="#" onclick="changePage(${i})">${i}</a>`;
                pagination.appendChild(li);
            }
        }

        function changePage(page) {
            currentPage = page;
            fetchComments(currentPage, searchQuery);
            updateUrl();
        }

        function updateUrl() {
            const params = new URLSearchParams();
            if (currentPage !== 1) params.set('page', currentPage);
            if (searchQuery) params.set('search', searchQuery);
            const newUrl = params.toString() ? `?${params.toString()}` : '/manage_comment';
            history.pushState(null, '', newUrl);
        }

        function setEditModal(commentId, content) {
            document.getElementById('edit-comment-id').value = commentId;
            document.getElementById('edit-comment-content').value = content;
        }

        function updateComment() {
            const commentId = document.getElementById('edit-comment-id').value;
            const content = document.getElementById('edit-comment-content').value.trim();

            if (!content) {
                showAlert('danger', 'Nội dung bình luận không được để trống.');
                return;
            }

            fetch(`/api/comments/${commentId}`, {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ content })
            })
                .then(response => response.json())
                .then(data => {
                    showAlert(data.success ? 'success' : 'danger', data.message);
                    if (data.success) {
                        fetchComments(currentPage, searchQuery);
                        bootstrap.Modal.getInstance(document.getElementById('editModal')).hide();
                    }
                })
                .catch(error => showAlert('danger', 'Lỗi: ' + error.message));
        }

        function deleteComment(commentId) {
            if (!confirm('Bạn có chắc chắn muốn xóa bình luận này?')) return;

            fetch(`/api/comments/${commentId}`, {
                method: 'DELETE'
            })
                .then(response => {
                    if (response.status === 204) {
                        showAlert('success', 'Bình luận đã được xóa.');
                        fetchComments(currentPage, searchQuery);
                    } else {
                        return response.json().then(data => {
                            throw new Error(data.message);
                        });
                    }
                })
                .catch(error => showAlert('danger', 'Lỗi: ' + error.message));
        }

        function showAlert(type, message) {
            const alertContainer = document.getElementById('alert-container');
            alertContainer.innerHTML = `
                <div class="alert alert-${type} alert-dismissible">
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            `;
            setTimeout(() => {
                alertContainer.innerHTML = '';
            }, 5000);
        }

        function escapeHtml(text) {
            const map = {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            };
            return text.replace(/[&<>"']/g, m => map[m]);
        }

        function formatDate(dateStr) {
            const date = new Date(dateStr);
            return date.toLocaleString('vi-VN', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
        }
    </script>

    <?php require_once 'app/Views/shares/footer.php'; ?>
</body>
</html>