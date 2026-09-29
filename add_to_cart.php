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
    $quantity = (int)($_POST['quantity'] ?? 1);

    if (empty($product_id) || empty($product_name)) {
        echo json_encode(['success' => false, 'message' => 'Invalid product data']);
        exit;
    }

    // Check if product already in cart
    $stmt = $conn->prepare("SELECT id, quantity FROM cart WHERE user_id = ? AND product_id = ?");
    if (!$stmt) {
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $conn->error]);
        exit;
    }
    
    $stmt->bind_param("is", $user_id, $product_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Update quantity
        $row = $result->fetch_assoc();
        $new_quantity = $row['quantity'] + $quantity;
        $stmt = $conn->prepare("UPDATE cart SET quantity = ? WHERE user_id = ? AND product_id = ?");
        if (!$stmt) {
            echo json_encode(['success' => false, 'message' => 'Database error: ' . $conn->error]);
            exit;
        }
        $stmt->bind_param("iis", $new_quantity, $user_id, $product_id);
    } else {
        // Insert new item
        $stmt = $conn->prepare("INSERT INTO cart (user_id, product_id, product_name, product_price, quantity) 
                                VALUES (?, ?, ?, ?, ?)");
        if (!$stmt) {
            echo json_encode(['success' => false, 'message' => 'Database error: ' . $conn->error]);
            exit;
        }
        $stmt->bind_param("issdi", $user_id, $product_id, $product_name, $product_price, $quantity);
    }

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Item added to cart']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to add item to cart: ' . $stmt->error]);
    }
    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
}
?>
