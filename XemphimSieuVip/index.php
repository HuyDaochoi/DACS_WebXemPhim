<?php
session_start();

// CSRF Token Functions
/**
 * Generate a CSRF token and store it in session
 * @return string The CSRF token
 */
function generateCsrfToken() {
    if (empty($_SESSION['csrf_token']) || empty($_SESSION['csrf_token_time']) || time() - $_SESSION['csrf_token_time'] > 3600) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        $_SESSION['csrf_token_time'] = time();
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verify a CSRF token
 * @param string $token The token to verify
 * @return bool True if valid, false otherwise
 */
function verifyCsrfToken($token) {
    if (!isset($_SESSION['csrf_token']) || !isset($_SESSION['csrf_token_time'])) {
        return false;
    }
    if (time() - $_SESSION['csrf_token_time'] > 3600) {
        unset($_SESSION['csrf_token'], $_SESSION['csrf_token_time']);
        return false;
    }
    $isValid = hash_equals($_SESSION['csrf_token'], $token);
    if ($isValid) {
        unset($_SESSION['csrf_token'], $_SESSION['csrf_token_time']);
        generateCsrfToken(); // Regenerate for next request
    }
    return $isValid;
}

// Database Connection
require_once 'app/configs/database.php';
$db = new Database();

// URL Processing
$url = $_GET['url'] ?? '';
$url = rtrim($url, '/');
$url = filter_var($url, FILTER_SANITIZE_URL);
$url = explode('/', $url);

// Handle Specific Routes
$uri = $_SERVER['REQUEST_URI'];
$method = $_SERVER['REQUEST_METHOD'];

// Manage Comment Page
if ($uri === '/manage_comment' || preg_match('/^\/manage_comment\?/', $uri)) {
    require_once 'app/Views/manage_comment.php';
    exit;
}

// Watch Page
if ($uri === '/watch' || preg_match('/^\/watch\?id=\d+$/', $uri)) {
    require_once 'app/Views/watch.php';
    exit;
}

// API Routes for Comments
require_once 'app/Controllers/CommentController.php';
$commentController = new CommentController();
if ($method === 'POST' && $uri === '/api/comments') {
    $commentController->createComment();
    exit;
}
if ($method === 'GET' && preg_match('/^\/api\/comments\?movie_id=(\d+)$/', $uri, $matches)) {
    $commentController->getComments($matches[1]);
    exit;
}
if ($method === 'GET' && $uri === '/api/comments/admin') {
    $commentController->getCommentsForAdmin();
    exit;
}
if ($method === 'PUT' && preg_match('/^\/api\/comments\/(\d+)$/', $uri, $matches)) {
    $commentController->updateComment($matches[1]);
    exit;
}
if ($method === 'DELETE' && preg_match('/^\/api\/comments\/(\d+)$/', $uri, $matches)) {
    $commentController->deleteComment($matches[1]);
    exit;
}



// Default Controller Handling
$controllerName = isset($url[0]) && $url[0] != '' ? ucfirst($url[0]) . 'Controller' : 'MovieController';
$action = isset($url[1]) && $url[1] != '' ? $url[1] : 'index';

if (!file_exists('app/Controllers/' . $controllerName . '.php')) {
    $_SESSION['error'] = 'Không tìm thấy controller!';
    header('Location: /');
    exit;
}

require_once 'app/Controllers/' . $controllerName . '.php';
$controller = new $controllerName($db);

if (!method_exists($controller, $action)) {
    $_SESSION['error'] = 'Không tìm thấy action!';
    header('Location: /');
    exit;
}

call_user_func_array([$controller, $action], array_slice($url, 2));
?>
