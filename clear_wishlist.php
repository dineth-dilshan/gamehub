<?php
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Please login first']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_SESSION['user_id'];
    
    if ($conn->query("DELETE FROM wishlist WHERE user_id = $user_id")) {
        echo json_encode(['success' => true, 'message' => 'Wishlist cleared']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to clear wishlist: ' . $conn->error]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
}
?>
