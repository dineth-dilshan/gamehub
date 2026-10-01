<?php
include 'config.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$createTableSql = "CREATE TABLE IF NOT EXISTS community_posts (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NULL,
    username VARCHAR(100) NOT NULL,
    message TEXT NOT NULL,
    image_data LONGTEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_created_at (created_at),
    INDEX idx_user_id (user_id),
    CONSTRAINT fk_community_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
)";

if (!$conn->query($createTableSql)) {
    echo json_encode(['success' => false, 'message' => 'Failed to initialize posts table']);
    exit;
}

$username = trim($_POST['username'] ?? '');
$message = trim($_POST['message'] ?? '');
$image = $_POST['image'] ?? null;

if ($username === '' || $message === '') {
    echo json_encode(['success' => false, 'message' => 'Username and message are required']);
    exit;
}

if (strlen($username) > 100) {
    echo json_encode(['success' => false, 'message' => 'Username is too long']);
    exit;
}

if (strlen($message) > 10000) {
    echo json_encode(['success' => false, 'message' => 'Message is too long']);
    exit;
}

if ($image !== null && $image !== '' && strlen($image) > 6 * 1024 * 1024) {
    echo json_encode(['success' => false, 'message' => 'Image is too large']);
    exit;
}

$user_id = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : null;

$stmt = $conn->prepare("INSERT INTO community_posts (user_id, username, message, image_data) VALUES (?, ?, ?, ?)");
if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $conn->error]);
    exit;
}

$stmt->bind_param('isss', $user_id, $username, $message, $image);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Post created successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to save post']);
}

$stmt->close();
?>