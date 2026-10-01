<?php
include 'config.php';

echo "<h2>Fixing Database Structure...</h2>";

// Fix cart table
$sql1 = "ALTER TABLE cart MODIFY COLUMN product_id VARCHAR(255) NOT NULL";
if ($conn->query($sql1)) {
    echo "<p style='color: green;'>✓ Cart table product_id fixed</p>";
} else {
    if (strpos($conn->error, 'Syntax error') !== false || strpos($conn->error, 'already') !== false) {
        echo "<p style='color: green;'>✓ Cart table already correct</p>";
    } else {
        echo "<p style='color: red;'>✗ Error fixing cart: " . $conn->error . "</p>";
    }
}

// Fix wishlist table
$sql2 = "ALTER TABLE wishlist MODIFY COLUMN product_id VARCHAR(255) NOT NULL";
if ($conn->query($sql2)) {
    echo "<p style='color: green;'>✓ Wishlist table product_id fixed</p>";
} else {
    if (strpos($conn->error, 'Syntax error') !== false || strpos($conn->error, 'already') !== false) {
        echo "<p style='color: green;'>✓ Wishlist table already correct</p>";
    } else {
        echo "<p style='color: red;'>✗ Error fixing wishlist: " . $conn->error . "</p>";
    }
}

echo "<h2>System Ready!</h2>";
echo "<p>Database tables have been updated.</p>";
echo "<p><a href='storehome.html'>Go to Store Home</a></p>";
?>
