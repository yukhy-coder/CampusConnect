<?php
header('Content-Type: application/json');
require 'db_connect.php';

$data = json_decode(file_get_contents("php://input"), true);
$id = $data['id'];

if ($id) {
    // Set Reviewed = 1, RESET others to 0
    $stmt = $conn->prepare("UPDATE anonymousmessages SET is_reviewed = 1, is_read = 0, is_favorite = 0, is_archived = 0 WHERE message_id = ?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["success" => false, "error" => $conn->error]);
    }
    $stmt->close();
}
$conn->close();
?>