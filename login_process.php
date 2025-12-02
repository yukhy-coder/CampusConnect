<?php
// Omit closing tag to prevent header output errors

// =================================================================
// 0. TEMPORARY DEBUGGING BLOCK (REMOVE AFTER SUCCESSFUL TESTING!)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// =================================================================


// 1. DATABASE CONNECTION DETAILS (CRITICAL: VERIFY THESE!)
$servername = "localhost";
$username = "root";       
$password = "";           // CHECK: Use the correct password for your XAMPP root user
$dbname = "Campus_Connect"; // CHECK: Must match the database name exactly!

// Redirect URLs
$login_page = "login.html";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $email = trim($_POST['email'] ?? '');
    $input_password = $_POST['password'] ?? ''; 

    if (empty($email) || empty($input_password)) {
        header("Location: " . $login_page . "?status=login_failed");
        exit();
    }
    
    // 3. CONNECT TO THE DATABASE
    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        error_log("DB Connection Failed: " . $conn->connect_error);
        header("Location: " . $login_page . "?status=db_error");
        exit();
    }
    
    $sql = "SELECT password, is_approved FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    
    if ($stmt === false) {
        error_log("SQL Prepare failed: " . $conn->error);
        $conn->close();
        header("Location: " . $login_page . "?status=db_error");
        exit();
    }
    
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();

    // 5. VERIFY PASSWORD AND STATUS
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        $stored_hash = $user['password'];
        $is_approved = $user['is_approved'];

        // *** SECURE PASSWORD VERIFICATION ***
        if (password_verify($input_password, $stored_hash)) {
            
            // Authentication SUCCESS (NO APPROVAL CHECK)
            $conn->close();
            header("Location: " . $login_page . "?status=login_success"); 
            exit();

        } else {
            // Password incorrect
            $conn->close();
            header("Location: " . $login_page . "?status=login_failed");
            exit();
        }
    } else {
        // User (email) not found in the database
        $conn->close();
        header("Location: " . $login_page . "?status=login_failed");
        exit();
    }

} else {
    // If accessed directly without POST data
    header("Location: " . $login_page);
    exit();
}
// Omit closing tag to prevent header output errors