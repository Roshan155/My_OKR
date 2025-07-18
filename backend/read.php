<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

$host = "localhost";
$db_name = "web_dev";  // Replace with your DB
$username = "root";
$password = "rose123";

$conn = new mysqli($host, $username, $password, $db_name);

if ($conn->connect_error) {
    echo json_encode(["status" => "error", "message" => "Database connection failed"]);
    exit;
}

$sql = "SELECT * FROM okrs ORDER BY id DESC";
$result = $conn->query($sql);

$data = [];

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
}

echo json_encode([
    "status" => "success",
    "data" => $data
]);
?>
