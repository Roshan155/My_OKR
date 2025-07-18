<?php
include("db.php");

header('Content-Type: application/json');


$data = json_decode(file_get_contents("php://input"));

if (!$data) {
    echo json_encode(["status" => "error", "message" => "Invalid JSON input"]);
    exit;
}

$user_id = $data->user_id ?? null;
$team_id = $data->team_id ?? null;
$objective = $data->objective ?? '';
$key_results = $data->key_results ?? '';
$progress = $data->progress ?? 0;

if (!$user_id || !$team_id || !$objective || !$key_results) {
    echo json_encode(["status" => "error", "message" => "Missing required fields"]);
    exit;
}

$stmt = $conn->prepare("INSERT INTO okrs (user_id, team_id, objective, key_results, progress) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("iissi", $user_id, $team_id, $objective, $key_results, $progress);

if ($stmt->execute()) {
    echo json_encode(["status" => "success", "message" => "OKR created successfully"]);
} else {
    echo json_encode(["status" => "error", "message" => $stmt->error]);
}

$stmt->close();
$conn->close();
?>
