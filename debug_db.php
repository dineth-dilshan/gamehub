<?php
include 'config.php';

echo "<h2>Database Debug Info</h2>";

// Check table structure
echo "<h3>Users Table Structure:</h3>";
$result = $conn->query("DESCRIBE users");
if ($result) {
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['Field'] . "</td>";
        echo "<td>" . $row['Type'] . "</td>";
        echo "<td>" . $row['Null'] . "</td>";
        echo "<td>" . $row['Key'] . "</td>";
        echo "<td>" . $row['Default'] . "</td>";
        echo "<td>" . $row['Extra'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<span style='color: red;'>❌ Error: " . $conn->error . "</span>";
}

// Count existing users
echo "<h3>Current Users in Database:</h3>";
$result = $conn->query("SELECT id, first_name, last_name, email, username FROM users");
if ($result) {
    if ($result->num_rows == 0) {
        echo "<span style='color: orange;'>⚠️ No users registered yet</span>";
    } else {
        echo "<table border='1' cellpadding='5'>";
        echo "<tr><th>ID</th><th>First Name</th><th>Last Name</th><th>Email</th><th>Username</th></tr>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $row['id'] . "</td>";
            echo "<td>" . $row['first_name'] . "</td>";
            echo "<td>" . $row['last_name'] . "</td>";
            echo "<td>" . $row['email'] . "</td>";
            echo "<td>" . $row['username'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
} else {
    echo "<span style='color: red;'>❌ Error: " . $conn->error . "</span>";
}

// Test INSERT MANUALLY
echo "<h3>Manual INSERT Test:</h3>";
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $first_name = "Test";
    $last_name = "User";
    $email = "test@example.com";
    $username = "testuser123";
    $password = password_hash("testpass123", PASSWORD_DEFAULT);
    $avatar = "https://ui-avatars.com/api/?name=Test%20User&background=a855f7&color=fff";
    
    $stmt = $conn->prepare("INSERT INTO users (first_name, last_name, email, username, password, avatar) VALUES (?, ?, ?, ?, ?, ?)");
    
    if (!$stmt) {
        echo "<span style='color: red;'>❌ Prepare failed: " . $conn->error . "</span>";
    } else {
        $stmt->bind_param("ssssss", $first_name, $last_name, $email, $username, $password, $avatar);
        
        if ($stmt->execute()) {
            echo "<span style='color: green;'>✅ Test INSERT successful! User added.</span>";
        } else {
            echo "<span style='color: red;'>❌ Execute failed: " . $stmt->error . "</span>";
        }
        $stmt->close();
    }
}
?>
<form method="POST">
    <button type="submit" style="padding: 10px 20px; background: #a855f7; color: white; border: none; cursor: pointer;">
        Test INSERT
    </button>
</form>
