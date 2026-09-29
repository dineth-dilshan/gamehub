<?php
include 'config.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Please login first']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_SESSION['user_id'];
    $product_id = $_POST['product_id'] ?? '';
    $product_name = $_POST['product_name'] ?? '';
    $product_price = (float)($_POST['product_price'] ?? 0);

    if (empty($product_id) || empty($product_name)) {
        echo json_encode(['success' => false, 'message' => 'Invalid product data']);
        exit;
    }

    // Check if product already in wishlist
    $stmt = $conn->prepare("SELECT id FROM wishlist WHERE user_id = ? AND product_id = ?");
    if (!$stmt) {
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $conn->error]);
        exit;
    }
    
    $stmt->bind_param("is", $user_id, $product_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo json_encode(['success' => false, 'message' => 'Item already in wishlist']);
        exit;
    }

    // Insert item to wishlist
    $stmt = $conn->prepare("INSERT INTO wishlist (user_id, product_id, product_name, product_price) 
                            VALUES (?, ?, ?, ?)");
    
    if (!$stmt) {
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $conn->error]);
        exit;
    }
    
    $stmt->bind_param("issd", $user_id, $product_id, $product_name, $product_price);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Item added to wishlist']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to add item to wishlist: ' . $stmt->error]);
    }
    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
}
?>
