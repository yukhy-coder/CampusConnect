<?php
header('Content-Type: application/json');
require 'db_connect.php';

// Get JSON input
$data = json_decode(file_get_contents("php://input"), true);
$id = $data['id'];
$new_role = $data['role']; // 'admin' or 'officer'

if ($id && $new_role) {
    // Security check: Ensure role is valid
    if (!in_array($new_role, ['admin', 'officer'])) {
        echo json_encode(["success" => false, "error" => "Invalid role"]);
        exit();
    }

    $stmt = $conn->prepare("UPDATE users SET role = ? WHERE user_id = ?");
    $stmt->bind_param("si", $new_role, $id);
    
    if ($stmt->execute()) {
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["success" => false, "error" => $conn->error]);
    }
    $stmt->close();
}
$conn->close();
?>