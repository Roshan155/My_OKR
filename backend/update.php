<?php
include 'db.php';
header("Content-Type: application/json");

$data = json_decode(file_get_contents("php://input"), true);

if (
    isset($data['id']) &&
    (isset($data['objective']) || isset($data['key_results']) || isset($data['progress']))
) {
    $id = intval($data['id']);
    $updates = [];

    if (isset($data['objective'])) {
        $objective = mysqli_real_escape_string($conn, $data['objective']);
        $updates[] = "objective = '$objective'";
    }

    if (isset($data['key_results'])) {
        $key_results = mysqli_real_escape_string($conn, $data['key_results']);
        $updates[] = "key_results = '$key_results'";
    }

    if (isset($data['progress'])) {
        $progress = intval($data['progress']);
        $updates[] = "progress = $progress";
    }

    $update_query = implode(", ", $updates);
    $sql = "UPDATE okrs SET $update_query WHERE id = $id";
    $result = mysqli_query($conn, $sql);

    if ($result) {
        echo json_encode([
            "status" => "success",
            "message" => "OKR updated successfully"
        ]);
    } else {
        echo json_encode([
            "status" => "error",
            "message" => "Failed to update OKR",
            "error" => mysqli_error($conn)
        ]);
    }
} else {
    echo json_encode([
        "status" => "error",
        "message" => "Missing OKR ID or fields to update"
    ]);
}
?>
