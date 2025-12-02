<?php
header('Content-Type: application/json');
require_once 'db_config.php';

$conn = getDBConnection();

// Get all messages ordered by newest first
$sql = "SELECT l.letter_id, l.message, l.file_path, l.submitted_at, c.category_name 
        FROM letters l 
        LEFT JOIN categories c ON l.category_id = c.category_id 
        ORDER BY l.submitted_at DESC";

$result = $conn->query($sql);

$messages = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Extract hashtags from message
        $tags = [];
        if (strpos($row['message'], '#Concern') !== false) {
            $tags[] = 'Concern';
        }
        if (strpos($row['message'], '#Complaint') !== false) {
            $tags[] = 'Complaint';
        }
        if (strpos($row['message'], '#Query') !== false) {
            $tags[] = 'Query';
        }
        
        $messages[] = [
            'letter_id' => $row['letter_id'],
            'text' => $row['message'],
            'tags' => $tags,
            'hasImage' => ($row['file_path'] !== 'none' && !empty($row['file_path'])),
            'file_path' => $row['file_path'],
            'submitted_at' => $row['submitted_at'],
            'category' => $row['category_name']
        ];
    }
}

echo json_encode([
    'success' => true,
    'messages' => $messages
]);

$conn->close();
?>