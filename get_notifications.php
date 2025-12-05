<?php
header('Content-Type: application/json');
require 'db_connect.php';

$notifs = [];

// 1. COUNT UNREAD ANONYMOUS MESSAGES
// We check the actual messages table for anything not read yet
// Fix: Only count items that are strictly in the Dashboard (Unread, Unfavorited, Unarchived, Unreviewed)
$count_sql = "SELECT COUNT(*) as count FROM anonymousmessages WHERE is_read = 0 AND is_favorite = 0 AND is_archived = 0 AND is_reviewed = 0";
$count_result = $conn->query($count_sql);
$count_row = $count_result->fetch_assoc();
$unread_count = $count_row['count'];

// If there are unread messages, create a "Smart Notification" at the top
if ($unread_count > 0) {
    $s = ($unread_count > 1) ? 's' : ''; // Plural check
    $notifs[] = [
        'type' => 'message', // Keeps the Shield Icon logic
        'text' => "You have $unread_count unread post$s. Tap to navigate!",
        'time_ago' => 'Live',
        'is_read' => 0
    ];
}

// 2. FETCH EVENT NOTIFICATIONS (Standard)
// We only fetch 'event' type from the notifications table now
$sql = "SELECT n.*, u.fullname, u.profile_pic 
        FROM notifications n 
        LEFT JOIN users u ON n.user_id = u.user_id 
        WHERE n.type = 'event'
        ORDER BY n.created_at DESC LIMIT 10";

$result = $conn->query($sql);

while ($row = $result->fetch_assoc()) {
    $row['time_ago'] = humanTiming(strtotime($row['created_at'])) . ' ago';
    $notifs[] = $row;
}

echo json_encode($notifs);

// Time Helper
function humanTiming ($time) {
    $time = time() - $time;
    $tokens = array (31536000=>'year', 2592000=>'month', 604800=>'week', 86400=>'day', 3600=>'hour', 60=>'minute', 1=>'second');
    foreach ($tokens as $unit => $text) {
        if ($time < $unit) continue;
        $numberOfUnits = floor($time / $unit);
        return $numberOfUnits.' '.$text.(($numberOfUnits>1)?'s':'');
    }
    return 'Just now';
}
?>