<?php
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    exit("<h2 style='color: red;'>Please login first</h2>");
}

$user_id = $_SESSION['user_id'];
echo "<h1>Complete System Diagnosis</h1>";

// 1. Check cart table structure
echo "<h2>1. Cart Table Column Types:</h2>";
$result = $conn->query("DESCRIBE cart");
$cart_structure = [];
while ($row = $result->fetch_assoc()) {
    $cart_structure[$row['Field']] = $row['Type'];
    echo "<p><strong>{$row['Field']}:</strong> {$row['Type']}</p>";
}

if (strpos($cart_structure['product_id'], 'INT') !== false) {
    echo "<p style='color: red; background: #fee2e2; padding: 10px; border-radius: 5px;'><strong>❌ ERROR:</strong> product_id is still INT! It should be VARCHAR(255)</p>";
} else if (strpos($cart_structure['product_id'], 'VARCHAR') !== false) {
    echo "<p style='color: green; background: #d4edda; padding: 10px; border-radius: 5px;'><strong>✓ OK:</strong> product_id is VARCHAR</p>";
}

// 2. Check database directly
echo "<h2>2. Raw Data in Cart Table:</h2>";
$result = $conn->query("SELECT * FROM cart WHERE user_id = $user_id");
if ($result && $result->num_rows > 0) {
    echo "<p style='color: green;'><strong>Found " . $result->num_rows . " items</strong></p>";
    echo "<table border='1' cellpadding='10'>";
    while ($field = $result->fetch_field()) {
        echo "<th>" . $field->name . "</th>";
    }
    $result->data_seek(0);
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        foreach ($row as $cell) {
            echo "<td>" . htmlspecialchars($cell ?? 'NULL') . "</td>";
        }
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p style='color: red;'><strong>No items in database</strong></p>";
}

// 3. Test insert directly
echo "<h2>3. Test Direct INSERT:</h2>";
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['test_insert'])) {
    $test_id = "direct_test_" . time();
    $test_name = "Direct Test Item";
    $test_price = 5000;
    $test_qty = 1;
    
    echo "<p>Attempting INSERT with:</p>";
    echo "<ul>";
    echo "<li>user_id: $user_id (INT)</li>";
    echo "<li>product_id: $test_id (VARCHAR)</li>";
    echo "<li>product_name: $test_name (VARCHAR)</li>";
    echo "<li>product_price: $test_price (DECIMAL)</li>";
    echo "<li>quantity: $test_qty (INT)</li>";
    echo "</ul>";
    
    // Try prepared statement
    $stmt = $conn->prepare("INSERT INTO cart (user_id, product_id, product_name, product_price, quantity) VALUES (?, ?, ?, ?, ?)");
    if (!$stmt) {
        echo "<p style='color: red;'><strong>Error preparing statement:</strong> " . $conn->error . "</p>";
    } else {
        echo "<p>Prepared statement OK</p>";
        $stmt->bind_param("issdi", $user_id, $test_id, $test_name, $test_price, $test_qty);
        
        if ($stmt->execute()) {
            echo "<p style='color: green; background: #d4edda; padding: 10px; border-radius: 5px;'><strong>✓ INSERT successful!</strong></p>";
            echo "<meta http-equiv='refresh' content='1'>";
        } else {
            echo "<p style='color: red; background: #fee2e2; padding: 10px; border-radius: 5px;'><strong>✗ INSERT failed:</strong> " . $stmt->error . "</p>";
        }
        $stmt->close();
    }
}

// 4. Test get_cart.php
echo "<h2>4. What get_cart.php Returns:</h2>";
$response = file_get_contents('get_cart.php');
echo "<pre style='background: #0f1724; padding: 10px; border-radius: 5px; overflow-x: auto;'>";
echo htmlspecialchars($response);
echo "</pre>";

// 5. Check session
echo "<h2>5. Session Data:</h2>";
echo "<p><strong>user_id:</strong> " . ($_SESSION['user_id'] ?? 'NOT SET') . "</p>";
echo "<p><strong>username:</strong> " . ($_SESSION['username'] ?? 'NOT SET') . "</p>";

// 6. Test add_to_cart.php with form
echo "<h2>6. Test add_to_cart.php via Form:</h2>";
?>

<form method="POST" style="background: #1e293b; padding: 20px; border-radius: 8px; max-width: 400px;">
    <h3>Manual Test Form</h3>
    <div style="margin: 10px 0;">
        <label>Product Name:</label><br>
        <input type="text" name="product_name" value="Manual Test Product" style="width: 100%; padding: 8px; background: #0f172a; color: white; border: 1px solid #2d3748;">
    </div>
    <div style="margin: 10px 0;">
        <label>Product Price:</label><br>
        <input type="text" name="product_price" value="9999" style="width: 100%; padding: 8px; background: #0f172a; color: white; border: 1px solid #2d3748;">
    </div>
    <div style="margin: 10px 0;">
        <label>Quantity:</label><br>
        <input type="text" name="quantity" value="1" style="width: 100%; padding: 8px; background: #0f172a; color: white; border: 1px solid #2d3748;">
    </div>
    <button type="submit" name="test_insert" style="background: #26b6ff; color: white; padding: 10px 20px; border: none; cursor: pointer; border-radius: 5px; width: 100%;">Test Direct INSERT</button>
</form>

<?php
echo "<hr style='border: 1px solid #2d3748; margin: 20px 0;'>";

// 7. Show SQL for fixing column type
echo "<h2>7. SQL Commands to Fix Database:</h2>";
echo "<p>If product_id is still INT, run these in phpMyAdmin:</p>";
echo "<pre style='background: #0f1724; padding: 10px; border-radius: 5px;'>";
echo "ALTER TABLE cart MODIFY COLUMN product_id VARCHAR(255) NOT NULL;
ALTER TABLE wishlist MODIFY COLUMN product_id VARCHAR(255) NOT NULL;
DELETE FROM cart;
DELETE FROM wishlist;";
echo "</pre>";
?>
