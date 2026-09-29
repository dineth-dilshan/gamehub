<?php
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['items' => [], 'total' => 0]);
    exit;
}

$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT id, product_id, product_name, product_price, quantity FROM cart WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$items = [];
$total = 0;

while ($row = $result->fetch_assoc()) {
    $item_total = $row['product_price'] * $row['quantity'];
    $total += $item_total;
    $items[] = [
        'id' => $row['id'],
        'product_id' => $row['product_id'],
        'product_name' => $row['product_name'],
        'product_price' => $row['product_price'],
        'quantity' => $row['quantity'],
        'item_total' => $item_total
    ];
}

echo json_encode(['success' => true, 'items' => $items, 'total' => $total]);
$stmt->close();
?>
