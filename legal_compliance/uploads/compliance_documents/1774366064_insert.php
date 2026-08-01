<?php
include 'connect.php';

$username = $_POST['username'];

$sql = "INSERT INTO users (username) VALUES ('$username')";

if ($conn->query($sql) === TRUE) {
    echo "<h3>User added successfully</h3>";
} else {
    echo "<h3>Error: " . $conn->error . "</h3>";
}

$conn->close();
?>

<br>
<button onclick="window.location.href='display.php'">Go to display</button>