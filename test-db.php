<?php
$conn = new mysqli('localhost', 'root', 'root', 'local');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
echo "Connected successfully";
$conn->close();
