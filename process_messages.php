<?php
require 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $message_text = $_POST['message_text'];
    
    // Auto-Detect Tags
    $detectedTags = [];
    if (stripos($message_text, '#Concern') !== false) { $detectedTags[] = 'Concern'; }
    if (stripos($message_text, '#Complaint') !== false) { $detectedTags[] = 'Complaint'; }
    if (stripos($message_text, '#Query') !== false) { $detectedTags[] = 'Query'; }
    $tags_string = implode(',', $detectedTags);

    // --- MULTIPLE FILE UPLOAD LOGIC ---
    $uploaded_filenames = []; // Array to store success names
    $target_dir = __DIR__ . "/uploads/";

    // Check if files exist
    if (isset($_FILES['attachment']) && count($_FILES['attachment']['name']) > 0) {
        
        // Ensure folder exists
        if (!is_dir($target_dir)) { mkdir($target_dir, 0777, true); }

        // Loop through each file
        $total_files = count($_FILES['attachment']['name']);
        
        for ($i = 0; $i < $total_files; $i++) {
            // Check for errors
            if ($_FILES['attachment']['error'][$i] == 0) {
                
                $original_name = basename($_FILES["attachment"]["name"][$i]);
                // Check if empty (sometimes happens with empty array index)
                if(!empty($original_name)){
                    $new_filename = time() . "_" . $i . "_" . $original_name; // Add index $i to ensure uniqueness
                    $target_file = $target_dir . $new_filename;
    
                    if (move_uploaded_file($_FILES["attachment"]["tmp_name"][$i], $target_file)) {
                        $uploaded_filenames[] = $new_filename;
                    }
                }
            }
        }
    }

    // Convert array ["img1.jpg", "img2.jpg"] -> string "img1.jpg,img2.jpg"
    // If no files, this becomes null or empty string
    $attachment_link = !empty($uploaded_filenames) ? implode(',', $uploaded_filenames) : null;

    $ip_address = $_SERVER['REMOTE_ADDR'];

    // Insert into DB
    $sql = "INSERT INTO anonymousmessages (message_text, tags, attachment_link, ip_address) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $message_text, $tags_string, $attachment_link, $ip_address);

    // ... inside process_messages.php success block ...
    if ($stmt->execute()) {
        
        
        header("Location: message-sent.html");
        exit();
    } else {
        echo "Database Error: " . $conn->error;
    }

    $stmt->close();
    $conn->close();
}
?>