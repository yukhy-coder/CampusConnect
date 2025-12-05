<?php
header('Content-Type: application/json');
require 'db_connect.php';

$filter = isset($_GET['filter']) ? $_GET['filter'] : 'unread';

if ($filter == 'reviewed') {
    $sql = "SELECT * FROM anonymousmessages WHERE is_reviewed = 1 ORDER BY created_at DESC";

} elseif ($filter == 'archive') {
    $sql = "SELECT * FROM anonymousmessages WHERE is_archived = 1 ORDER BY created_at DESC";

} elseif ($filter == 'favorite') {
    $sql = "SELECT * FROM anonymousmessages WHERE is_favorite = 1 ORDER BY created_at DESC";

} elseif ($filter == 'read') {
    $sql = "SELECT * FROM anonymousmessages WHERE is_read = 1 ORDER BY created_at DESC";

} else {
    // Default: Dashboard (Everything needs to be 0)
    $sql = "SELECT * FROM anonymousmessages WHERE is_read = 0 AND is_archived = 0 AND is_reviewed = 0 AND is_favorite = 0 ORDER BY created_at DESC";
}

$result = $conn->query($sql);

$messages = [];
while ($row = $result->fetch_assoc()) {
    $messages[] = $row;
}

echo json_encode($messages);
$conn->close();
?>