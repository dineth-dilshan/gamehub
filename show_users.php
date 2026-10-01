<?php
include 'config.php';

echo "<h2>All Registered Users</h2>";

$result = $conn->query("SELECT id, first_name, last_name, email, username, created_at FROM users ORDER BY created_at DESC");

if ($result && $result->num_rows > 0) {
    echo "<table border='1' cellpadding='10' style='width:100%; border-collapse: collapse;'>";
    echo "<tr style='background: #a855f7; color: white;'>";
    echo "<th>ID</th><th>First Name</th><th>Last Name</th><th>Email</th><th>Username</th><th>Created</th>";
    echo "</tr>";
    
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['id'] . "</td>";
        echo "<td>" . $row['first_name'] . "</td>";
        echo "<td>" . $row['last_name'] . "</td>";
        echo "<td>" . $row['email'] . "</td>";
        echo "<td>" . $row['username'] . "</td>";
        echo "<td>" . $row['created_at'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    echo "<p><strong>Total users: " . $result->num_rows . "</strong></p>";
} else {
    echo "<p style='color: orange;'>No users found</p>";
}

// Option to clear all users for testing
echo "<hr>";
echo "<h3>For Testing - Clear Database</h3>";
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] == 'clear') {
        $conn->query("DELETE FROM cart");
        $conn->query("DELETE FROM wishlist");
        $conn->query("DELETE FROM users");
        echo "<p style='background: #d4edda; padding: 10px; border-radius: 5px; color: green;'><strong>✅ Database cleared!</strong> All users, carts, and wishlists deleted.</p>";
    }
}
?>
<form method="POST">
    <button type="submit" name="action" value="clear" style="padding: 10px 20px; background: #dc2626; color: white; border: none; cursor: pointer; border-radius: 5px;">
        Clear ALL Users (Reset Database)
    </button>
</form>
<?php
?>
