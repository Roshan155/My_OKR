<?php
session_start();
include 'db.php';

header("Content-Type: application/json");

// ✅ Check if session contains user_id
if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        "status" => "error",
        "message" => "User not logged in"
    ]);
    exit;
}


$user_id = $_SESSION['user_id'];

$sql = "SELECT * FROM okrs WHERE user_id = $user_id";
$result = mysqli_query($conn, $sql);

if (!$result) {
    echo json_encode([
        "status" => "error",
        "message" => "Database query failed",
        "error" => mysqli_error($conn)
    ]);
    exit;
}

$okrs = [];
while ($row = mysqli_fetch_assoc($result)) {
    $okrs[] = $row;
}

echo json_encode($okrs);
