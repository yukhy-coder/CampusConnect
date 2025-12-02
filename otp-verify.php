<?php
// Omit closing tag 

// 1. DATABASE CONNECTION DETAILS
$servername = "localhost";
$username = "root";       
$password = "";           
$dbname = "Campus_Connect"; 

// Redirect URLs
$verification_page = "otp-verify.html";
$reset_page = "reset-password.html"; 


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // IMPORTANT: The email and OTP code are sent from the client-side form submission.
    $submitted_email = trim($_POST['email'] ?? ''); // Hidden field from JS
    $submitted_otp = trim($_POST['otp_code'] ?? ''); // Combined OTP input

    if (empty($submitted_email) || empty($submitted_otp)) {
        header("Location: " . $verification_page . "?error=invalid");
        exit();
    }

    $conn = new mysqli($servername, $username, $password, $dbname);
    
    if ($conn->connect_error) {
        header("Location: " . $verification_page . "?error=db_connect");
        exit();
    }
    
    // --- STEP 1: Fetch Stored OTP and Expiry by Email ---
    $sql = "SELECT otp_code, expires_at FROM PasswordReset WHERE user_email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $submitted_email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $data = $result->fetch_assoc();
        $stored_otp = $data['otp_code'];
        $expiry_time = strtotime($data['expires_at']);

        // --- STEP 2: Perform Secure Verification ---
        
        // A. Check if code has expired
        if (time() > $expiry_time) {
            $error_status = "expired";
        } 
        // B. Check if code matches
        else if ($submitted_otp !== $stored_otp) {
            $error_status = "invalid";
        } 
        
        // --- SUCCESS ---
        else {
            // OPTIONAL: Delete the used OTP record immediately for security
            $delete_sql = "DELETE FROM PasswordReset WHERE user_email = ?";
            $delete_stmt = $conn->prepare($delete_sql);
            $delete_stmt->bind_param("s", $submitted_email);
            $delete_stmt->execute();
            
            // Redirect to the final reset page
            header("Location: " . $reset_page . "?email=" . urlencode($submitted_email));
            exit();
        }

    } else {
        // No record found for the email (or multiple records exist)
        $error_status = "invalid";
    }

    // Redirect on failure
    $conn->close();
    header("Location: " . $verification_page . "?error=" . ($error_status ?? 'invalid'));
    exit();
} else {
    // If accessed directly without POST data
    header("Location: " . $verification_page);
    exit();
}
// Omit closing tag