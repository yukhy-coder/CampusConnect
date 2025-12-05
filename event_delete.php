<?php
header('Content-Type: application/json');
require 'db_connect.php';

$data = json_decode(file_get_contents("php://input"), true);
$id = $data['id'];

if ($id) {
    $stmt = $conn->prepare("DELETE FROM events WHERE event_id = ?");
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