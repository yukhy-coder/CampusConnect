<?php
header('Content-Type: application/json');
require 'db_connect.php';

// Get the ID sent from JavaScript
$data = json_decode(file_get_contents("php://input"), true);
$id = $data['id'];

if ($id) {
    // Security: You might want to check if the user is an admin here
    
    // Delete the user
    $sql = "DELETE FROM users WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["success" => false, "error" => "Database error: " . $conn->error]);
    }
    
    $stmt->close();
} else {
    echo json_encode(["success" => false, "error" => "No ID provided"]);
}

$conn->close();
?>