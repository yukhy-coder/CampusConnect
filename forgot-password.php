<?php
// Omit closing tag 

// =================================================================
// 0. CONFIGURATION
// =================================================================

// Database Settings (CRITICAL: VERIFY THESE!)
$servername = "localhost";
$username = "root";       
$password = "";           
$dbname = "Campus_Connect"; 

// Redirect URLs
$verification_page = 'otp-verify.html';
$forgot_page = 'forgot-password.php';

// OTP Settings
$otp_expiry_seconds = 300; // 5 minutes validity
$sender_email = 'laxusmachica@gmail.com'; 


// 4. FUNCTION TO SEND MAIL (Uses built-in PHP mail() which relies on XAMPP config)
function sendOtpEmail($recipient_email, $otp_code, $sender_email) {
    $subject = 'Password Reset Code: ' . $otp_code;
    $message = "Your 4-digit password reset code is: $otp_code. It expires in 5 minutes.\r\n\r\nPlease verify your identity on the website.";
    
    $headers = "From: Student Government <{$sender_email}>\r\n";
    $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
    
    // Attempt to send the email using PHP's built-in mail function (Relies on sendmail.ini)
    return mail($recipient_email, $subject, $message, $headers);
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['emailInput'] ?? ''); 

    if (empty($email)) {
        header("Location: " . $forgot_page . "?error=empty_email");
        exit();
    }
    
    $conn = new mysqli($servername, $username, $password, $dbname);
    
    if ($conn->connect_error) {
        header("Location: " . $forgot_page . "?error=db_connect");
        exit();
    }
    
    // --- STEP 1: Verify Email Exists ---
    $sql = "SELECT email FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    
    if ($stmt) {
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            header("Location: " . $forgot_page . "?error=not_registered");
        } else {
            // --- STEP 2: Generate & Store OTP ---
            $otp_code = random_int(1000, 9999);
            $expiry_time = date('Y-m-d H:i:s', time() + $otp_expiry_seconds);

            // In a real system, you update the existing row for the user's email or insert a new one.
            // We use INSERT/REPLACE INTO here assuming you set up the PasswordResets table correctly.
            $insert_sql = "INSERT INTO PasswordReset (user_email, otp_code, expires_at) 
                           VALUES (?, ?, ?) 
                           ON DUPLICATE KEY UPDATE otp_code=VALUES(otp_code), expires_at=VALUES(expires_at)";
            
            $insert_stmt = $conn->prepare($insert_sql);
            $insert_stmt->bind_param("sss", $email, $otp_code, $expiry_time);
            $insert_stmt->execute();

            // --- STEP 3: Send Email ---
            $mail_sent = sendOtpEmail($email, $otp_code, $sender_email);

            if ($mail_sent) {
                // SUCCESS: Redirect to verification page
                header("Location: " . $verification_page);
                exit();
            } else {
                // Email failed (server configuration issue)
                header("Location: " . $forgot_page . "?error=mail_fail");
                exit();
            }
        }
        $stmt->close();
    } else {
        header("Location: " . $forgot_page . "?error=db_query");
    }
    $conn->close();
}
// Note: HTML output for the form remains in the same forgot_password.php file.
?>