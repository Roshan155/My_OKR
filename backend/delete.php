<?php
include 'db.php';
header("Content-Type: application/json");
file_put_contents("delete_log.txt", file_get_contents("php://input")); // Log input


$data = json_decode(file_get_contents("php://input"), true);

if (isset($data['id'])) {
    $id = intval($data['id']);

    $sql = "DELETE FROM okrs WHERE id = $id";
    $result = mysqli_query($conn, $sql);

    if ($result) {
        echo json_encode([
            "status" => "success",
            "message" => "OKR deleted"
        ]);
    } else {
        echo json_encode([
            "status" => "error",
            "message" => "Failed to delete OKR",
            "sql_error" => mysqli_error($conn)
        ]);
    }
} else {
    echo json_encode([
        "status" => "error",
        "message" => "No ID received"
    ]);
}
?>
