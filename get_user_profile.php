<?php
session_start();
header('Content-Type: application/json');
require 'db_connect.php';

// REAL SECURITY CHECK
if (!isset($_SESSION['user_id'])) {
    echo json_encode(["success" => false, "error" => "Not logged in"]);
    exit();
}

$user_id = $_SESSION['user_id'];

// Select using your exact column names
$sql = "SELECT fullname, position, email, birthday, profile_pic FROM users WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($user = $result->fetch_assoc()) {
    echo json_encode(["success" => true, "data" => $user]);
} else {
    echo json_encode(["success" => false, "error" => "User not found"]);
}

$stmt->close();
$conn->close();
?>