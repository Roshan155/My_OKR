<?php
$servername = "localhost";
$username = "root";
$password = "rose123"; // Use your actual password if you have one
$dbname = "web_dev";    

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die(json_encode([
        "status" => "error",
        "message" => "Connection failed: " . $conn->connect_error
    ]));
}
?>
