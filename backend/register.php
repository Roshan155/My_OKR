<?php
header("Content-Type: application/json");
include("db.php");

$data = json_decode(file_get_contents("php://input"), true); // <-- fix applied here

// Extract & sanitize input
$name     = isset($data['name']) ? trim($data['name']) : '';
$email    = isset($data['email']) ? trim($data['email']) : '';
$password = isset($data['password']) ? trim($data['password']) : '';
$team_id  = isset($data['team_id']) && $data['team_id'] !== '' ? intval($data['team_id']) : "NULL";

// Validate required fields
if (empty($name) || empty($email) || empty($password)) {
    echo json_encode(["status" => "error", "message" => "Name, email, and password are required."]);
    exit;
}

// Check for duplicate email
$checkEmail = $conn->prepare("SELECT id FROM users WHERE email = ?");
$checkEmail->bind_param("s", $email);
$checkEmail->execute();
$checkResult = $checkEmail->get_result();

if ($checkResult->num_rows > 0) {
    echo json_encode(["status" => "error", "message" => "Email already registered."]);
    exit;
}

// Check if team_id exists if provided
if ($team_id !== "NULL") {
    $checkTeam = $conn->prepare("SELECT id FROM teams WHERE id = ?");
    $checkTeam->bind_param("i", $team_id);
    $checkTeam->execute();
    $teamResult = $checkTeam->get_result();
    if ($teamResult->num_rows === 0) {
        echo json_encode(["status" => "error", "message" => "Invalid team ID."]);
        exit;
    }
}

// Hash password
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

// Insert user
$sql = "INSERT INTO users (name, email, password, role, team_id)
        VALUES (?, ?, ?, 'member', $team_id)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sss", $name, $email, $hashedPassword);

if ($stmt->execute()) {
    echo json_encode(["status" => "success", "message" => "User registered successfully."]);
} else {
    echo json_encode(["status" => "error", "message" => $conn->error]);
}
?>
