<?php
header('Content-Type: application/json');
require 'db_connect.php';

// Order by date ascending (soonest events first)
$sql = "SELECT * FROM events ORDER BY event_date ASC";
$result = $conn->query($sql);

$events = [];
while ($row = $result->fetch_assoc()) {
    $events[] = $row;
}

echo json_encode($events);
$conn->close();
?>