<?php
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    exit("Please login first");
}

$user_id = $_SESSION['user_id'];

echo "<h2>Database Debug - Full Diagnostic</h2>";

// 1. Check table structure
echo "<h3>1. Cart Table Structure:</h3>";
$result = $conn->query("DESCRIBE cart");
if ($result) {
    echo "<table border='1' cellpadding='5'>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td><strong>" . $row['Field'] . "</strong></td>";
        echo "<td>" . $row['Type'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
}

// 2. Show current cart items
echo "<h3>2. Your Cart Items in Database:</h3>";
$result = $conn->query("SELECT * FROM cart WHERE user_id = $user_id");
if ($result && $result->num_rows > 0) {
    echo "<table border='1' cellpadding='10'>";
    echo "<tr>";
    while ($field = $result->fetch_field()) {
        echo "<th>" . $field->name . "</th>";
    }
    echo "</tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        foreach ($row as $cell) {
            echo "<td>" . htmlspecialchars($cell) . "</td>";
        }
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p style='color: red;'><strong>❌ No items in cart</strong></p>";
}

// 3. Test get_cart.php
echo "<h3>3. What get_cart.php Returns:</h3>";
ob_start();
include 'get_cart.php';
$output = ob_get_clean();
echo "<pre>" . htmlspecialchars($output) . "</pre>";

// 4. Show SQL query
echo "<h3>4. Manual SQL Query Test:</h3>";
$query = "SELECT id, product_id, product_name, product_price, quantity FROM cart WHERE user_id = $user_id";
echo "<p>Query: <code>$query</code></p>";
$result = $conn->query($query);
if ($result) {
    echo "<p style='color: green;'><strong>✓ Query executed successfully</strong></p>";
    echo "<p>Rows returned: " . $result->num_rows . "</p>";
} else {
    echo "<p style='color: red;'><strong>✗ Query failed: " . $conn->error . "</strong></p>";
}

// 5. Test adding item directly
echo "<h3>5. Test Add Item via PHP:</h3>";
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $test_product_id = "test_" . time();
    $test_product_name = "Test Product";
    $test_product_price = 5000;
    
    $insert_query = "INSERT INTO cart (user_id, product_id, product_name, product_price, quantity) VALUES ($user_id, '$test_product_id', '$test_product_name', $test_product_price, 1)";
    
    echo "<p>Query: <code>" . htmlspecialchars($insert_query) . "</code></p>";
    
    if ($conn->query($insert_query)) {
        echo "<p style='color: green;'><strong>✓ Item added successfully!</strong></p>";
        echo "<meta http-equiv='refresh' content='1'>";
    } else {
        echo "<p style='color: red;'><strong>✗ Insert failed: " . $conn->error . "</strong></p>";
    }
}
?>

<form method="POST">
    <button type="submit" style="padding: 10px 20px; background: #26b6ff; color: white; border: none; cursor: pointer; border-radius: 5px;">
        Test Add Item to Cart
    </button>
</form>

<h3>6. Browser Console Issues:</h3>
<p>Open your browser's Developer Tools (F12) and check:</p>
<ol>
    <li>Go to <a href="storehome.html" target="_blank">storehome.html</a></li>
    <li>Press <strong>F12</strong> to open Developer Tools</li>
    <li>Click the <strong>"Console"</strong> tab</li>
    <li>Try adding an item to cart</li>
    <li>Look for any red error messages</li>
    <li>Screenshot and share the console errors</li>
</ol>
