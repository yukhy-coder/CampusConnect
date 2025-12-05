<?php
header('Content-Type: application/json');
require 'db_connect.php';

$response = [];

// 1. Total Officers (FIXED: Only count approved users)
$sql = "SELECT COUNT(*) as count FROM users WHERE (role = 'officer' OR role = 'admin') AND is_approved = 1";
$result = $conn->query($sql);
$response['total_officers'] = $result->fetch_assoc()['count'];

// 2. Logged-in Today (REAL DATA)
// We check if the DATE part of last_login matches today's date (CURDATE())
$sql = "SELECT COUNT(*) as count FROM users WHERE DATE(last_login) = CURDATE() AND (role = 'officer' OR role = 'admin') AND is_approved = 1";
$result = $conn->query($sql);
$response['logged_in_today'] = $result->fetch_assoc()['count'];

// 3. Total Anonymous Messages
$sql = "SELECT COUNT(*) as count FROM anonymousmessages";
$result = $conn->query($sql);
$response['total_messages'] = $result->fetch_assoc()['count'];

// 4. Total Events
$sql = "SELECT COUNT(*) as count FROM events";
$result = $conn->query($sql);
$response['total_events'] = $result->fetch_assoc()['count'];

// 5. Message Breakdown (For Donut Chart)
// We scan the 'tags' column for keywords
$complaints = 0;
$concerns = 0;
$queries = 0;

$sql = "SELECT tags FROM anonymousmessages";
$result = $conn->query($sql);
while($row = $result->fetch_assoc()) {
    if (strpos($row['tags'], 'Complaint') !== false) $complaints++;
    if (strpos($row['tags'], 'Concern') !== false) $concerns++;
    if (strpos($row['tags'], 'Query') !== false) $queries++;
}

$response['message_breakdown'] = [
    'Complaint' => $complaints,
    'Concern' => $concerns,
    'Query' => $queries
];

// 6. Event Stats (For Bar Chart)
// For now, let's just grab the last 3 events and give them dummy numbers
// In a real app, you would count RSVPs or Feedback linked to the event ID
// ... (Previous sections 1-5 remain the same) ...

// 6. Daily Message Volume (Last 7 Days)
// We group by DATE(created_at) to count how many messages arrived each day
$daily_stats = [];
$sql = "SELECT DATE(created_at) as msg_date, COUNT(*) as count 
        FROM anonymousmessages 
        GROUP BY DATE(created_at) 
        ORDER BY msg_date DESC LIMIT 7"; 

$result = $conn->query($sql);

while($row = $result->fetch_assoc()) {
    $daily_stats[] = [
        'date' => date('M d', strtotime($row['msg_date'])), // Format: "Sep 19"
        'count' => $row['count']
    ];
}

// The query gets newest first (DESC), but charts need oldest first (Left to Right)
// So we reverse the array
$response['daily_trend'] = array_reverse($daily_stats);

echo json_encode($response);
$conn->close();
?>