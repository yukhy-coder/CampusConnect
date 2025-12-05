<?php
session_start();
require 'db_connect.php';

// Set Timezone
date_default_timezone_set('Asia/Manila'); 

// 1. Check if we have a tracked session ID
if (isset($_SESSION['current_log_id'])) {
    $log_id = $_SESSION['current_log_id'];
    
    // 2. Update the database: Set logout_time to NOW
    $sql = "UPDATE activity_logs SET logout_time = NOW() WHERE log_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $log_id);
    $stmt->execute();
    $stmt->close();
}

// 3. Destroy Session
session_unset();
session_destroy();
$conn->close();

// 4. Redirect to Login Page
header("Location: login.html");
exit();
?>