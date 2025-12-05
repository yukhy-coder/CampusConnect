<?php
session_start();
header('Content-Type: application/json');
require 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["success" => false, "error" => "Not logged in"]);
    exit();
}

$user_id = $_SESSION['user_id'];

// 1. Get Text Data (Using $_POST now, not JSON)
$fullname = $_POST['fullname'];
$position = $_POST['position'];
$email = $_POST['email'];
$birthday = $_POST['birthday'];

// 2. Handle Profile Picture Upload
$profile_pic_sql = ""; // Helper string for SQL
$params = [$fullname, $position, $email, $birthday];
$types = "ssss";

if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] == 0) {
    $target_dir = __DIR__ . "/uploads/";
    if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);

    $filename = time() . "_pfp_" . basename($_FILES["profile_pic"]["name"]);
    $target_file = $target_dir . $filename;

    if (move_uploaded_file($_FILES["profile_pic"]["tmp_name"], $target_file)) {
        // If upload success, we update the profile_pic column too
        $profile_pic_sql = ", profile_pic=?";
        $params[] = $filename; // Add filename to params list
        $types .= "s";         // Add string type
    }
}

// 3. Update Database
// We define the base query
$sql = "UPDATE users SET fullname=?, position=?, email=?, birthday=? $profile_pic_sql WHERE user_id=?";

// Add user_id to the end of params
$params[] = $user_id;
$types .= "i";

$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params); // Unpack array into arguments

if ($stmt->execute()) {
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["success" => false, "error" => $conn->error]);
}

$stmt->close();
$conn->close();
?>