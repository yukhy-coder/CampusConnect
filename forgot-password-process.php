<?php
// Omit closing tag 

// =================================================================
// 0. DEBUGGING BLOCK (KEEP THIS WHILE TESTING!)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// =================================================================


// 1. DATABASE CONNECTION DETAILS (CRITICAL: VERIFY THESE!)
$servername = "localhost";
$username = "root";       
$password = "";           
$dbname = "CampusConnect"; // VERIFY THIS NAME

// 2. EMAIL/OTP CONFIGURATION
$verification_page = 'otp-verify.html';
$forgot_page = 'forgot-password.html'; // Redirect back to this page on error

// 3. GMAIL SENDER DETAILS (Used by the mail() function and sendmail.ini)
$sender_email = 'laxusmachica@gmail.com'; 


// 4. FUNCTION TO SEND MAIL (Uses built-in PHP mail() which relies on XAMPP config)
function sendOtpEmail($recipient_email, $otp_code, $sender_email) {
    $subject = 'Password Reset Code: ' . $otp_code;
    $message = "Your 4-digit password reset code is: $otp_code. It expires in 5 minutes.\r\n\r\nPlease verify your identity on the website.";
    
    $headers = "From: Student Government <{$sender_email}>\r\n";
    $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
    
    // Attempt to send the email using PHP's built-in mail function (requires XAMPP sendmail config)
    return mail($recipient_email, $subject, $message, $headers);
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['emailInput'] ?? ''); 

    // --- STEP 0: Initial Validation ---
    if (empty($email)) {
        header("Location: " . $forgot_page . "?error=empty_email");
        exit();
    }

    // --- STEP 1: Database Verification ---
    $conn = new mysqli($servername, $username, $password, $dbname);
    
    if ($conn->connect_error) {
        header("Location: " . $forgot_page . "?error=db_connect");
        exit();
    }
    
    // Check if the email exists in UsersInfo
    $sql = "SELECT email FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    
    if ($stmt) {
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            $conn->close();
            header("Location: " . $forgot_page . "?error=not_registered");
            exit();
        } 
        
        // --- STEP 2: Generate OTP and Attempt Send ---
        $otp_code = random_int(1000, 9999);
        $otp_expiry = date('Y-m-d H:i:s', time() + $otp_expiry_seconds);
        
        $mail_sent = sendOtpEmail($email, $otp_code, $sender_email);

        if ($mail_sent) {
            // --- STEP 3: Store OTP in Database ---
            // Assuming 'PasswordResets' table exists for OTP storage
            $insert_sql = "REPLACE INTO PasswordReset (user_email, otp_code, expires_at) 
                           VALUES (?, ?, ?)";
            
            $insert_stmt = $conn->prepare($insert_sql);
            $insert_stmt->bind_param("sss", $email, $otp_code, $otp_expiry);
            $insert_stmt->execute();

            // --- STEP 4: Redirect to Verification Page ---
            $conn->close();
            echo '<script>
                // Store email and OTP securely for the next page (DEMO ONLY - INSECURE)
                localStorage.setItem("otpCode", "' . $otp_code . '");
                localStorage.setItem("resetEmail", "' . $email . '");
                window.location.href = "' . $verification_page . '";
            </script>';
            exit();

        } else {
            // Email failed (sendmail.ini/App Password issue)
            $conn->close();
            header("Location: " . $forgot_page . "?error=mail_fail");
            exit();
        }
        
    } else {
        $conn->close();
        header("Location: " . $forgot_page . "?error=db_query");
        exit();
    }
    
} else {
    // If accessed directly without POST data
    header("Location: " . $forgot_page);
    exit();
}
// Omit closing tag