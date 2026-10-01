<?php
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    echo "<h2 style='color: red;'>Please login first to test cart</h2>";
    echo "<a href='8.Logintab.html'>Go to Login</a>";
    exit;
}

$user_id = $_SESSION['user_id'];
$userName = $_SESSION['user_name'] ?? 'User';

echo "<h2>Cart & Wishlist Debug Test</h2>";
echo "<p>Logged in as: <strong>$userName</strong> (ID: $user_id)</p>";

// Show current cart
echo "<h3>Current Cart Items:</h3>";
$result = $conn->query("SELECT * FROM cart WHERE user_id = $user_id");
if ($result && $result->num_rows > 0) {
    echo "<table border='1' cellpadding='10'>";
    echo "<tr><th>ID</th><th>Product ID</th><th>Product Name</th><th>Price</th><th>Qty</th><th>Total</th></tr>";
    while ($row = $result->fetch_assoc()) {
        $total = $row['product_price'] * $row['quantity'];
        echo "<tr><td>{$row['id']}</td><td>{$row['product_id']}</td><td>{$row['product_name']}</td><td>Rs. {$row['product_price']}</td><td>{$row['quantity']}</td><td>Rs. $total</td></tr>";
    }
    echo "</table>";
} else {
    echo "<p style='color: orange;'>Cart is empty</p>";
}

// Show current wishlist
echo "<h3>Current Wishlist Items:</h3>";
$result = $conn->query("SELECT * FROM wishlist WHERE user_id = $user_id");
if ($result && $result->num_rows > 0) {
    echo "<table border='1' cellpadding='10'>";
    echo "<tr><th>ID</th><th>Product ID</th><th>Product Name</th><th>Price</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr><td>{$row['id']}</td><td>{$row['product_id']}</td><td>{$row['product_name']}</td><td>Rs. {$row['product_price']}</td></tr>";
    }
    echo "</table>";
} else {
    echo "<p style='color: orange;'>Wishlist is empty</p>";
}

echo "<hr>";
echo "<h3>Test Add to Cart:</h3>";
echo "<form method='POST'>";
echo "<input type='hidden' name='test' value='add_cart'>";
echo "<button type='submit'>Add Test Item to Cart</button>";
echo "</form>";

echo "<h3>Test Add to Wishlist:</h3>";
echo "<form method='POST'>";
echo "<input type='hidden' name='test' value='add_wishlist'>";
echo "<button type='submit'>Add Test Item to Wishlist</button>";
echo "</form>";

echo "<h3>Clear All:</h3>";
echo "<form method='POST'>";
echo "<input type='hidden' name='test' value='clear'>";
echo "<button type='submit' onclick='return confirm(\"Clear all cart and wishlist?\")'>Clear Cart & Wishlist</button>";
echo "</form>";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $test = $_POST['test'] ?? '';
    
    if ($test == 'add_cart') {
        $product_id = "test_" . time();
        $product_name = "Test Item " . date('H:i:s');
        $product_price = 5000;
        $quantity = 1;
        
        $stmt = $conn->prepare("INSERT INTO cart (user_id, product_id, product_name, product_price, quantity) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("issdi", $user_id, $product_id, $product_name, $product_price, $quantity);
        
        if ($stmt->execute()) {
            echo "<p style='color: green; background: #d4edda; padding: 10px; border-radius: 5px;'><strong>✓ Successfully added test item to cart!</strong></p>";
        } else {
            echo "<p style='color: red; background: #f8d7da; padding: 10px; border-radius: 5px;'><strong>✗ Error: " . $stmt->error . "</strong></p>";
        }
        $stmt->close();
        echo "<meta http-equiv='refresh' content='2'>";
    }
    
    if ($test == 'add_wishlist') {
        $product_id = "test_wish_" . time();
        $product_name = "Test Wishlist Item " . date('H:i:s');
        $product_price = 7500;
        
        $stmt = $conn->prepare("INSERT INTO wishlist (user_id, product_id, product_name, product_price) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("issd", $user_id, $product_id, $product_name, $product_price);
        
        if ($stmt->execute()) {
            echo "<p style='color: green; background: #d4edda; padding: 10px; border-radius: 5px;'><strong>✓ Successfully added test item to wishlist!</strong></p>";
        } else {
            echo "<p style='color: red; background: #f8d7da; padding: 10px; border-radius: 5px;'><strong>✗ Error: " . $stmt->error . "</strong></p>";
        }
        $stmt->close();
        echo "<meta http-equiv='refresh' content='2'>";
    }
    
    if ($test == 'clear') {
        $conn->query("DELETE FROM cart WHERE user_id = $user_id");
        $conn->query("DELETE FROM wishlist WHERE user_id = $user_id");
        echo "<p style='color: blue; background: #d1ecf1; padding: 10px; border-radius: 5px;'><strong>✓ Cart and Wishlist cleared!</strong></p>";
        echo "<meta http-equiv='refresh' content='1'>";
    }
}
?>
