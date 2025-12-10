<?php
session_start();
header('Content-Type: application/json');
require 'db_connect.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(["success" => false, "error" => "Unauthorized"]);
    exit();
}

$data = json_decode(file_get_contents("php://input"), true);

$title = $data['title'];
$date = $data['date'];
$desc = isset($data['description']) ? $data['description'] : '';
$user_id = $_SESSION['user_id']; // <--- GET USER ID FROM SESSION

if(empty($title) || empty($date)){
    echo json_encode(["success" => false, "error" => "Title/Date required"]);
    exit();
}

// UPDATE SQL: Added user_id
$stmt = $conn->prepare("INSERT INTO events (title, event_date, description, user_id) VALUES (?, ?, ?, ?)");
$stmt->bind_param("sssi", $title, $date, $desc, $user_id);

if ($stmt->execute()) {
    
    // --- NOTIFICATION LOGIC (Keep this) ---
    $fullname = isset($_SESSION['fullname']) ? $_SESSION['fullname'] : "Someone";
    $notif_text = "$fullname added event: $title";
    
    // Insert into notifications
    $notif_stmt = $conn->prepare("INSERT INTO notifications (type, text, user_id) VALUES ('event', ?, ?)");
    $notif_stmt->bind_param("si", $notif_text, $user_id);
    $notif_stmt->execute();
    // --------------------------------------

    echo json_encode(["success" => true]);
} else {
    echo json_encode(["success" => false, "error" => $conn->error]);
}
$stmt->close();
$conn->close();
?>