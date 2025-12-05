<?php
header('Content-Type: application/json');
require 'db_connect.php';

$tab = isset($_GET['tab']) ? $_GET['tab'] : 'users';

if ($tab == 'pending') {
    // Fetch Unapproved Users
    $sql = "SELECT * FROM users WHERE is_approved = 0 ORDER BY created_at DESC";
} else {
    // Fetch Approved Users (Officers/Admins)
    // You can adjust this WHERE clause if you only want 'officer' roles shown
    $sql = "SELECT * FROM users WHERE is_approved = 1 ORDER BY fullname ASC";
}

$result = $conn->query($sql);

$users = [];
while ($row = $result->fetch_assoc()) {
    // Format Dates
    $row['last_login_text'] = $row['last_login'] ? time_elapsed_string($row['last_login']) : "Never";
    $row['created_at_text'] = date("M d, Y", strtotime($row['created_at']));
    $users[] = $row;
}

echo json_encode($users);
$conn->close();

// Helper for "1 min ago"
function time_elapsed_string($datetime, $full = false) {
    $now = new DateTime;
    $ago = new DateTime($datetime);
    $diff = $now->diff($ago);
    $diff->w = floor($diff->d / 7);
    $diff->d -= $diff->w * 7;
    $string = array('y' => 'year', 'm' => 'month', 'w' => 'week', 'd' => 'day', 'h' => 'hour', 'i' => 'minute', 's' => 'second');
    foreach ($string as $k => &$v) {
        if ($diff->$k) {
            $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
        } else {
            unset($string[$k]);
        }
    }
    if (!$full) $string = array_slice($string, 0, 1);
    return $string ? implode(', ', $string) . ' ago' : 'just now';
}
?>