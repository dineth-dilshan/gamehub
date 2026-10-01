<?php
include 'config.php';

echo "<!DOCTYPE html>
<html>
<head>
    <title>Test Add to Cart</title>
    <style>
        body { font-family: Arial; padding: 20px; background: #0a0e27; color: white; }
        .box { background: #1e293b; padding: 20px; margin: 15px 0; border-radius: 8px; border-left: 4px solid #26b6ff; }
        .success { color: #00ff00; }
        .error { color: #ff0000; }
        .warning { color: #ffaa00; }
        button { background: #26b6ff; color: white; padding: 10px 20px; border: none; cursor: pointer; border-radius: 5px; }
        pre { background: #0f1724; padding: 10px; border-radius: 5px; overflow: auto; }
    </style>
</head>
<body>
    <h1>🔧 Add to Cart Diagnostic</h1>";

// Check database connection
echo "<div class='box'>";
echo "<h2>1. Database Connection</h2>";
if ($conn->ping()) {
    echo "<p class='success'>✓ Database connected!</p>";
} else {
    echo "<p class='error'>✗ Database connection failed!</p>";
}
echo "</div>";

// Check cart table structure
echo "<div class='box'>";
echo "<h2>2. Cart Table Structure</h2>";
$result = $conn->query("DESCRIBE cart");
if ($result) {
    echo "<table border='1' style='width:100%; border-collapse: collapse;'>";
    echo "<tr style='background: #2d3748;'><th>Field</th><th>Type</th><th>Null</th><th>Key</th></tr>";
    while ($row = $result->fetch_assoc()) {
        $productIdIssue = $row['Field'] === 'product_id' && strpos($row['Type'], 'INT') !== false;
        $style = $productIdIssue ? "style='background: #ff6b6b;'" : '';
        echo "<tr $style>";
        echo "<td>{$row['Field']}</td>";
        echo "<td>{$row['Type']}</td>";
        echo "<td>{$row['Null']}</td>";
        echo "<td>{$row['Key']}</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // Check if product_id is INT
    $typeResult = $conn->query("SELECT COLUMN_TYPE FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='cart' AND COLUMN_NAME='product_id' AND TABLE_SCHEMA=DATABASE()");
    $typeRow = $typeResult->fetch_assoc();
    if (strpos($typeRow['COLUMN_TYPE'], 'INT') !== false) {
        echo "<p class='error'>⚠ PROBLEM: product_id is INT but should be VARCHAR!</p>";
        echo "<p>Run this SQL to fix:</p>";
        echo "<pre>ALTER TABLE cart MODIFY COLUMN product_id VARCHAR(255) NOT NULL;
ALTER TABLE wishlist MODIFY COLUMN product_id VARCHAR(255) NOT NULL;</pre>";
        echo "<p><a href='fix_database.php' style='color: #26b6ff; text-decoration: underline;'>Click here to auto-fix</a></p>";
    } else {
        echo "<p class='success'>✓ product_id is VARCHAR (correct!)</p>";
    }
} else {
    echo "<p class='error'>✗ Error checking table structure: " . $conn->error . "</p>";
}
echo "</div>";

// Check session
echo "<div class='box'>";
echo "<h2>3. User Session</h2>";
if (isset($_SESSION['user_id'])) {
    echo "<p class='success'>✓ User logged in (ID: {$_SESSION['user_id']})</p>";
} else {
    echo "<p class='warning'>⚠ Not logged in - you need to login to test</p>";
}
echo "</div>";

// Test add to cart
echo "<div class='box'>";
echo "<h2>4. Manual Test</h2>";

if (isset($_SESSION['user_id'])) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['test_add'])) {
        $user_id = $_SESSION['user_id'];
        $product_id = 'test_' . time();
        $product_name = 'Test Product';
        $product_price = 99.99;
        $quantity = 1;

        $stmt = $conn->prepare("INSERT INTO cart (user_id, product_id, product_name, product_price, quantity) VALUES (?, ?, ?, ?, ?)");
        
        if ($stmt) {
            $stmt->bind_param("issdi", $user_id, $product_id, $product_name, $product_price, $quantity);
            
            if ($stmt->execute()) {
                echo "<p class='success'>✓ Test item added successfully!</p>";
                echo "<p>Product ID: $product_id</p>";
                
                // Show cart contents
                $checkStmt = $conn->prepare("SELECT * FROM cart WHERE user_id = ? AND product_id = ?");
                $checkStmt->bind_param("is", $user_id, $product_id);
                $checkStmt->execute();
                $result = $checkStmt->get_result();
                $item = $result->fetch_assoc();
                
                echo "<p><strong>Item in database:</strong></p>";
                echo "<pre>";
                print_r($item);
                echo "</pre>";
                
                $checkStmt->close();
            } else {
                echo "<p class='error'>✗ Error: " . $stmt->error . "</p>";
            }
            $stmt->close();
        } else {
            echo "<p class='error'>✗ Prepare error: " . $conn->error . "</p>";
        }
    } else {
        echo "<form method='POST'>";
        echo "<button type='submit' name='test_add' value='1'>Test Add Item to Cart</button>";
        echo "</form>";
    }
} else {
    echo "<p class='warning'>⚠ <a href='8.Logintab.html' style='color: #26b6ff;'>Login first</a> to test</p>";
}
echo "</div>";

// Show your cart
if (isset($_SESSION['user_id'])) {
    echo "<div class='box'>";
    echo "<h2>5. Your Current Cart</h2>";
    $stmt = $conn->prepare("SELECT id, product_id, product_name, product_price, quantity FROM cart WHERE user_id = ? LIMIT 5");
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        echo "<table border='1' style='width:100%; border-collapse: collapse;'>";
        echo "<tr style='background: #2d3748;'><th>Product ID</th><th>Name</th><th>Price</th><th>Qty</th></tr>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row['product_id']) . "</td>";
            echo "<td>" . htmlspecialchars($row['product_name']) . "</td>";
            echo "<td>Rs. " . $row['product_price'] . "</td>";
            echo "<td>" . $row['quantity'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p class='warning'>Your cart is empty</p>";
    }
    $stmt->close();
    echo "</div>";
}

echo "</body></html>";
?>
