<?php
include 'config.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Login required to delete posts']);
    exit;
}

$post_id = (int)($_POST['post_id'] ?? 0);
if ($post_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid post id']);
    exit;
}

$user_id = (int)$_SESSION['user_id'];

$stmt = $conn->prepare("DELETE FROM community_posts WHERE id = ? AND user_id = ?");
if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $conn->error]);
    exit;
}

$stmt->bind_param('ii', $post_id, $user_id);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    echo json_encode(['success' => true, 'message' => 'Post deleted']);
} else {
    echo json_encode(['success' => false, 'message' => 'You can only delete your own posts']);
}

$stmt->close();
?>