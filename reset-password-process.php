<?php
// Omit closing tag 

// =================================================================
// 0. DEBUGGING BLOCK (REMOVE AFTER SUCCESSFUL TESTING!)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// =================================================================

// 1. DATABASE CONNECTION DETAILS
$servername = "localhost";
$username = "root";       
$password = "";           
$dbname = "CampusConnect"; // VERIFY THIS NAME

// Redirect URLs
$login_page = "password-changed.html";
$reset_page = "reset-password.html";


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $email = trim($_POST['userEmail'] ?? '');      
    $new_password = $_POST['newPass'] ?? '';      
    $confirm_password = $_POST['confirmPass'] ?? ''; 

    // --- STEP 0: Basic Validation ---
    if (empty($email) || empty($new_password) || $new_password !== $confirm_password) {
        // Should not happen if JS is working, but necessary safety check
        header("Location: " . $reset_page . "?status=invalid_input");
        exit();
    }
    
    // --- STEP 1: HASH THE NEW PASSWORD ---
    // This is the secure string that will be saved to the DB
    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

    // --- STEP 2: Connect to Database ---
    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        header("Location: " . $reset_page . "?status=update_failed");
        exit();
    }
    
    // --- STEP 3: Update the User's Password in UsersInfo Table ---
    $sql = "UPDATE users SET password = ? WHERE email = ?";
    $stmt = $conn->prepare($sql);
    
    if ($stmt) {
        // 'ss' means two string parameters: $hashed_password and $email
        $stmt->bind_param("ss", $hashed_password, $email);
        
        if ($stmt->execute()) {
            // SUCCESS: Password updated!
            $stmt->close();
            $conn->close();
            
            // --- FINAL ACTION: ALERT AND REDIRECT ---
            // Clear the reset email from the browser's local storage upon success
            echo '<script>localStorage.removeItem("resetEmail");</script>';
            
            // Redirect to login page with a success flag
            header("Location: " . $login_page . "?status=reset_success");
            exit();
        } else {
            // Execution Error
            $stmt->close();
            $conn->close();
            header("Location: " . $reset_page . "?status=update_failed");
            exit();
        }
    } else {
        // Prepare statement error (check table structure)
        $conn->close();
        header("Location: " . $reset_page . "?status=update_failed");
        exit();
    }

} else {
    // Access directly (not via POST)
    header("Location: " . $login_page);
    exit();
}
// Omit closing tag