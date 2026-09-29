<?php
include 'config.php';
header('Content-Type: application/json');

$current_user_id = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 0;

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

$sql = "SELECT id, user_id, username, message, image_data, created_at
        FROM community_posts
        ORDER BY created_at DESC";
$result = $conn->query($sql);

$posts = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $posts[] = [
            'id' => (int)$row['id'],
            'username' => $row['username'],
            'message' => $row['message'],
            'image' => $row['image_data'],
            'timestamp' => date('Y-m-d H:i', strtotime($row['created_at'])),
            'can_delete' => $current_user_id > 0 && (int)$row['user_id'] === $current_user_id
        ];
    }
}

echo json_encode(['success' => true, 'posts' => $posts]);
?>