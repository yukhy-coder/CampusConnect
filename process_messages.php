<?php
// Omit closing tag 

// =================================================================
// 0. TEMPORARY DEBUGGING BLOCK (REMOVE AFTER SUCCESSFUL TESTING!)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// =================================================================


// 1. DATABASE CONNECTION DETAILS
$servername = "localhost";
$username = "root";       
$password = "";           
$dbname = "Campus_Connect"; // Ensure this matches your database name

// Redirect URLs
$success_redirect = "message-sent.html";
$error_page = "anonymous-message.html"; 

// Directory where uploaded files will be stored (MUST BE CREATED AND WRITABLE)
// Example: Create a folder named 'uploads' next to your PHP file.
$upload_dir = "uploads/";


// 2. CHECK FOR POST DATA
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $message_text = trim($_POST['message_text'] ?? '');
    $attachment_link = null; // Changed from has_attachment to store the link/path
    $tags_found = [];
    $ip_address = $_SERVER['REMOTE_ADDR'] ?? null; 

    // --- FILE HANDLING AND LINK DETERMINATION ---
    // Check if a file was uploaded and handle the upload path
    if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] === UPLOAD_ERR_OK) {
        
        $file_info = $_FILES['attachment'];
        $file_ext = pathinfo($file_info['name'], PATHINFO_EXTENSION);
        // Create a unique filename to prevent overwrites
        $file_name = uniqid('anon_') . '.' . $file_ext;
        $target_file = $upload_dir . $file_name;

        // Ensure the uploads directory exists
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        // Attempt to move the uploaded file
        if (move_uploaded_file($file_info['tmp_name'], $target_file)) {
            // Store the path/link to the file in the variable
            $attachment_link = $target_file;
        } else {
            // File upload failed (e.g., permissions or size limit)
            header("Location: " . $error_page . "?status=upload_failed");
            exit();
        }
    }

    // Dynamic Hashtag Detection 
    if (strpos($message_text, '#Concern') !== false) { $tags_found[] = 'Concern'; }
    if (strpos($message_text, '#Complaint') !== false) { $tags_found[] = 'Complaint'; }
    if (strpos($message_text, '#Query') !== false) { $tags_found[] = 'Query'; }
    $tags_string = implode(',', $tags_found); 

    // Basic validation check (must have text OR an attachment)
    if (empty($message_text) && $attachment_link === null) {
        header("Location: " . $error_page . "?status=empty");
        exit();
    }
    
    // 3. CONNECT TO THE DATABASE
    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        error_log("DB Connection Failed: " . $conn->connect_error);
        header("Location: " . $error_page . "?status=db_error");
        exit();
    }
    
    // 4. PREPARE SQL INSERT STATEMENT (UPDATED for 'attachment_link')
    // Columns: message_text, tags, attachment_link, ip_address
    $sql = "INSERT INTO AnonymousMessages (message_text, tags, attachment_link, ip_address) 
            VALUES (?, ?, ?, ?)";
            
    $stmt = $conn->prepare($sql);
    
    if ($stmt === false) {
        error_log("SQL Prepare failed: " . $conn->error);
        $conn->close();
        header("Location: " . $error_page . "?status=db_error");
        exit();
    }
    
    // Bind parameters: "sssi" changed to "ssss" for four strings (message, tags, link, ip)
    // The attachment_link is now a string (VARCHAR) that can be NULL (which mysqli handles as a string).
    $stmt->bind_param("ssss", $message_text, $tags_string, $attachment_link, $ip_address); 
    
    // 5. EXECUTE AND REDIRECT
    if ($stmt->execute()) {
        $stmt->close();
        $conn->close();
        // SUCCESS: Redirect to message-sent.html
        header("Location: " . $success_redirect); 
        exit();
    } else {
        error_log("SQL Insert Error: " . $stmt->error);
        $stmt->close();
        $conn->close();
        header("Location: " . $error_page . "?status=insert_failed");
        exit();
    }

} else {
    // If accessed directly without POST data
    header("Location: " . $error_page);
    exit();
}
// Omit closing tag