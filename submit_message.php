<?php
header('Content-Type: application/json');
require_once 'db_config.php';

// Handle POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$conn = getDBConnection();

// Get message data
$message = isset($_POST['message']) ? trim($_POST['message']) : '';
$category = isset($_POST['category']) ? trim($_POST['category']) : '';

// Validation
if (empty($message) && !isset($_FILES['file'])) {
    echo json_encode(['success' => false, 'message' => 'Message or file required']);
    exit;
}

// Determine category_id based on hashtag
$category_id = null;
if (strpos($message, '#Concern') !== false) {
    $category_id = 'concern';
} elseif (strpos($message, '#Complaint') !== false) {
    $category_id = 'complaint';
} elseif (strpos($message, '#Query') !== false) {
    $category_id = 'query';
} else {
    $category_id = 'general'; // Default category
}

// Make sure category exists in database
$stmt = $conn->prepare("INSERT IGNORE INTO categories (category_id, category_name) VALUES (?, ?)");
$category_name = ucfirst($category_id);
$stmt->bind_param("ss", $category_id, $category_name);
$stmt->execute();
$stmt->close();

// Handle file upload
$file_path = '';
if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
    $upload_dir = 'uploads/';
    
    // Create uploads directory if it doesn't exist
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }
    
    $file_name = time() . '_' . basename($_FILES['file']['name']);
    $target_path = $upload_dir . $file_name;
    
    if (move_uploaded_file($_FILES['file']['tmp_name'], $target_path)) {
        $file_path = $target_path;
    }
}

// If no file was uploaded, set a default value
if (empty($file_path)) {
    $file_path = 'none';
}

// Insert message into database
$letter_id = generateUniqueId('letter_');
$sender_name = 'Anonymous';
$is_anonymous = 1;

$stmt = $conn->prepare("INSERT INTO letters (letter_id, category_id, sender_name, is_anonymous, message, file_path, submitted_at, uploaded_at) VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW())");
$stmt->bind_param("sssiss", $letter_id, $category_id, $sender_name, $is_anonymous, $message, $file_path);

if ($stmt->execute()) {
    echo json_encode([
        'success' => true,
        'message' => 'Message sent successfully',
        'letter_id' => $letter_id
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Failed to send message: ' . $stmt->error
    ]);
}

$stmt->close();
$conn->close();
?>