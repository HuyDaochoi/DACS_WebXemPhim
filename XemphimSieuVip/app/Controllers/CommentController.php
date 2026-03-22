<?php
require_once 'app/configs/database.php';
require_once 'app/Models/CommentModel.php';

class CommentController {
    private $commentModel;

    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $db = new Database();
        $this->commentModel = new CommentModel($db);
    }

    // POST /api/comments
    public function createComment() {
        if (!isset($_SESSION['user_id'])) {
            $this->sendResponse(401, ['success' => false, 'message' => 'Bạn cần đăng nhập để bình luận.']);
        }

        $data = json_decode(file_get_contents('php://input'), true);
        $movieId = $data['movie_id'] ?? null;
        $content = trim($data['content'] ?? '');

        if (empty($movieId) || empty($content)) {
            $this->sendResponse(400, ['success' => false, 'message' => 'Nội dung và ID phim là bắt buộc.']);
        }

        $userId = $_SESSION['user_id'];
        if ($this->commentModel->addComment($movieId, $userId, $content)) {
            $this->sendResponse(201, ['success' => true, 'message' => 'Bình luận đã được thêm.']);
        } else {
            $this->sendResponse(500, ['success' => false, 'message' => 'Không thể thêm bình luận.']);
        }
    }

    // GET /api/comments?movie_id={id}
    public function getComments() {
        $movieId = $_GET['movie_id'] ?? null;
        if (empty($movieId)) {
            $this->sendResponse(400, ['success' => false, 'message' => 'ID phim là bắt buộc.']);
        }

        $comments = $this->commentModel->getCommentsByMovie($movieId);
        $this->sendResponse(200, ['success' => true, 'data' => $comments]);
    }

    // GET /api/comments/admin
    public function getCommentsForAdmin() {
        if (!isset($_SESSION['vai_tro']) || $_SESSION['vai_tro'] !== 'admin') {
            $this->sendResponse(403, ['success' => false, 'message' => 'Bạn không có quyền truy cập.']);
        }

        $page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
        $perPage = isset($_GET['per_page']) && is_numeric($_GET['per_page']) ? (int)$_GET['per_page'] : 10;
        $search = isset($_GET['search']) ? trim($_GET['search']) : '';

        $result = $this->commentModel->getCommentsForAdmin($page, $perPage, $search);
        $this->sendResponse(200, [
            'success' => true,
            'data' => $result['comments'],
            'total' => $result['total'],
            'page' => $page,
            'per_page' => $perPage
        ]);
    }

    // PUT /api/comments/{id}
    public function updateComment($commentId) {
        if (!isset($_SESSION['vai_tro']) || $_SESSION['vai_tro'] !== 'admin') {
            $this->sendResponse(403, ['success' => false, 'message' => 'Bạn không có quyền sửa bình luận.']);
        }

        $data = json_decode(file_get_contents('php://input'), true);
        $content = trim($data['content'] ?? '');

        if (empty($content)) {
            $this->sendResponse(400, ['success' => false, 'message' => 'Nội dung bình luận không hợp lệ.']);
        }

        if ($this->commentModel->updateComment($commentId, $content)) {
            $this->sendResponse(200, ['success' => true, 'message' => 'Cập nhật bình luận thành công.']);
        } else {
            $this->sendResponse(404, ['success' => false, 'message' => 'Không tìm thấy bình luận.']);
        }
    }

    // DELETE /api/comments/{id}
    public function deleteComment($commentId) {
        if (!isset($_SESSION['vai_tro']) || $_SESSION['vai_tro'] !== 'admin') {
            $this->sendResponse(403, ['success' => false, 'message' => 'Bạn không có quyền xóa bình luận.']);
        }

        if ($this->commentModel->deleteComment($commentId)) {
            $this->sendResponse(204, null);
        } else {
            $this->sendResponse(404, ['success' => false, 'message' => 'Không tìm thấy bình luận.']);
        }
    }

    private function sendResponse($statusCode, $data) {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        if ($data !== null) {
            echo json_encode($data);
        }
        exit;
    }
}