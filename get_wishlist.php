<?php
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['items' => []]);
    exit;
}

$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT id, product_id, product_name, product_price FROM wishlist WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$items = [];

while ($row = $result->fetch_assoc()) {
    $items[] = $row;
}

echo json_encode(['success' => true, 'items' => $items]);
$stmt->close();
?>
