<?php
header('Content-Type: application/json');
require 'db_connect.php';

$data = json_decode(file_get_contents("php://input"), true);
$id = $data['id'];
$title = $data['title'];
$desc = $data['description'];
// Note: We usually don't update date in this simple flow, but you can add it if needed

if ($id) {
    $stmt = $conn->prepare("UPDATE events SET title = ?, description = ? WHERE event_id = ?");
    $stmt->bind_param("ssi", $title, $desc, $id);
    
    if ($stmt->execute()) {
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["success" => false, "error" => $conn->error]);
    }
    $stmt->close();
}
$conn->close();
?>