<?php
header('Content-Type: application/json');
require 'db_connect.php';

// Fetch logs
$sql = "SELECT l.timestamp as login_time, l.logout_time, u.fullname, u.position, u.email 
        FROM activity_logs l 
        JOIN users u ON l.user_id = u.user_id 
        ORDER BY l.timestamp DESC LIMIT 50";

$result = $conn->query($sql);

$logs = [];
while ($row = $result->fetch_assoc()) {
    // Format Login Time
    $dt = new DateTime($row['login_time']);
    $row['date'] = $dt->format('Y-m-d');
    $row['time'] = $dt->format('h:i:s A');
    
    // Format Logout Time
    if ($row['logout_time']) {
        $dt_out = new DateTime($row['logout_time']);
        // Display the Exact Time
        $row['logout_time'] = $dt_out->format('h:i:s A');
    } else {
        // Still logged in
        $row['logout_time'] = '<span style="color:green; font-weight:bold;">Active</span>';
    }
    
    $logs[] = $row;
}

echo json_encode($logs);
$conn->close();
?>