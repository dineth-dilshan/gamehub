<?php
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Please login first']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_SESSION['user_id'];
    $wishlist_id = $_POST['wishlist_id'] ?? '';

    if (empty($wishlist_id)) {
        echo json_encode(['success' => false, 'message' => 'Invalid wishlist item']);
        exit;
    }

    $stmt = $conn->prepare("DELETE FROM wishlist WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $wishlist_id, $user_id);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Item removed from wishlist']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to remove item']);
    }
    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
}
?>
