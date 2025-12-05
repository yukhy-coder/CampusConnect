<?php
session_start();
header('Content-Type: application/json');
require 'db_connect.php';

$data = json_decode(file_get_contents("php://input"), true);

$title = $data['title'];
$date = $data['date'];
$desc = isset($data['description']) ? $data['description'] : '';

if(empty($title) || empty($date)){
    echo json_encode(["success" => false, "error" => "Title/Date required"]);
    exit();
}

$stmt = $conn->prepare("INSERT INTO events (title, event_date, description) VALUES (?, ?, ?)");
$stmt->bind_param("sss", $title, $date, $desc);

if ($stmt->execute()) {
    // --- IMPROVED NOTIFICATION LOGIC ---
    
    // 1. Get User Info from Session
    // (If session missing, fallback to "Someone")
    $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
    $fullname = isset($_SESSION['fullname']) ? $_SESSION['fullname'] : "Someone";

    // 2. Create Specific Text: "Khyan added event: CTU Nite"
    $notif_text = "$fullname added event: $title";

    // 3. Insert into DB with user_id
    $notif_stmt = $conn->prepare("INSERT INTO notifications (type, text, user_id) VALUES ('event', ?, ?)");
    $notif_stmt->bind_param("si", $notif_text, $user_id);
    $notif_stmt->execute();
    // -----------------------------------

    echo json_encode(["success" => true]);
} else {
    echo json_encode(["success" => false, "error" => $conn->error]);
}
$stmt->close();
$conn->close();
?>