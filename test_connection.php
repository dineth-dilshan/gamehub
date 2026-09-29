<?php
echo "<h2>🔧 Database Connection Test</h2>";

// Include config
include 'config.php';

// Test 1: Check if connection exists
echo "<h3>1. Connection Status:</h3>";
if ($conn->connect_error) {
    echo "<span style='color: red;'>❌ Connection Failed: " . $conn->connect_error . "</span>";
    exit;
} else {
    echo "<span style='color: green;'>✅ Connected to MySQL successfully!</span>";
}

// Test 2: Check if database exists
echo "<h3>2. Database Check:</h3>";
$databases = $conn->query("SHOW DATABASES LIKE 'gamers_hub'");
if ($databases->num_rows > 0) {
    echo "<span style='color: green;'>✅ Database 'gamers_hub' exists!</span>";
} else {
    echo "<span style='color: red;'>❌ Database 'gamers_hub' NOT found!</span>";
    echo "<p>Run this query in phpMyAdmin: <code>CREATE DATABASE IF NOT EXISTS gamers_hub;</code></p>";
}

// Test 3: Check tables
echo "<h3>3. Tables Check:</h3>";
$tables = ['users', 'cart', 'wishlist'];
foreach ($tables as $table) {
    $result = $conn->query("SHOW TABLES FROM gamers_hub LIKE '$table'");
    if ($result && $result->num_rows > 0) {
        echo "<span style='color: green;'>✅ Table '$table' exists</span><br>";
    } else {
        echo "<span style='color: red;'>❌ Table '$table' NOT found</span><br>";
    }
}

// Test 4: Test a simple query
echo "<h3>4. Query Test:</h3>";
$result = $conn->query("SELECT COUNT(*) as user_count FROM gamers_hub.users");
if ($result) {
    $row = $result->fetch_assoc();
    echo "<span style='color: green;'>✅ Can query users table. Total users: " . $row['user_count'] . "</span>";
} else {
    echo "<span style='color: red;'>❌ Query failed: " . $conn->error . "</span>";
}

// Test 5: Session test
echo "<h3>5. Session Status:</h3>";
if (session_status() == PHP_SESSION_ACTIVE) {
    echo "<span style='color: green;'>✅ Session is active</span>";
} else {
    echo "<span style='color: orange;'>⚠️ Session is not active (will start on next page load)</span>";
}

// Display config
echo "<h3>6. Current Config:</h3>";
echo "Host: " . DB_HOST . "<br>";
echo "User: " . DB_USER . "<br>";
echo "Database: " . DB_NAME . "<br>";

echo "<hr><p><strong>💡 Next Steps:</strong></p>";
echo "<ul>";
echo "<li>If database doesn't exist, import <code>database_setup.sql</code> in phpMyAdmin</li>";
echo "<li>If tables don't exist, import the SQL file</li>";
echo "<li>If connection fails, check MySQL is running and credentials in config.php are correct</li>";
echo "</ul>";
?>
