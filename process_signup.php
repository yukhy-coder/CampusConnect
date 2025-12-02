<?php
// Omit closing tag to prevent header output errors

// =================================================================
// 0. TEMPORARY DEBUGGING BLOCK (KEEP THIS WHILE TESTING!)
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
$signup_page = "signup.html";


// 2. CHECK IF THE REQUEST METHOD IS POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // --- Data Capture and Hashing ---
    $fullname = trim($_POST['fullname'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $position = trim($_POST['position'] ?? ''); // <-- CAPTURING POSITION
    $raw_password = $_POST['password'] ?? ''; 
    $hashed_password = password_hash($raw_password, PASSWORD_DEFAULT);
    
    // Determine Role (for the ENUM field)
    $db_role = 'officer'; 
    if (strpos(strtolower($position), 'admin') !== false) {
        $db_role = 'admin';
    } 
    
    // 6. CONNECT TO THE DATABASE
    $conn = new mysqli($servername, $username, $password, $dbname);

    // CRITICAL CHECK 1: Database Connection Failure
    if ($conn->connect_error) {
        die("❌ DATABASE ERROR: Connection failed. Check credentials/XAMPP. Error: " . $conn->connect_error);
    }
    
    // 7. PREPARE SQL INSERT STATEMENT - TARGETING 'UsersInfo' TABLE
    // FIXED: Now includes the 'position' column.
    $sql = "INSERT INTO users (fullname, email, password, position, role, is_approved) 
            VALUES (?, ?, ?, ?, ?, 0)"; 
            
    $stmt = $conn->prepare($sql);
    
    // CRITICAL CHECK 2: Prepared Statement Failure
    if ($stmt === false) {
        $conn->close();
        die("❌ SQL PREPARE FAILED. Check 'UsersInfo' table structure and column names. Error: " . $conn->error);
    }
    
    // 8. BIND PARAMETERS: "sssss" for 5 string variables 
    // Order: fullname, email, HASHED password, POSITION, DB ENUM role
    $stmt->bind_param("sssss", $fullname, $email, $hashed_password, $position, $db_role);
    
    // 9. EXECUTE AND CHECK FOR ERRORS
    if ($stmt->execute()) {
        // SUCCESS: Data stored. Redirect to the signup page with success status.
        $stmt->close();
        $conn->close();
        header("Location: " . $signup_page . "?status=success"); 
        exit();
    } else {
        // CRITICAL CHECK 3: Execution Failure
        if ($conn->errno == 1062) {
            $conn->close();
            // Redirect with error status for duplicate email
            header("Location: " . $signup_page . "?status=duplicate");
            exit();
        } else {
            $conn->close();
            // Display general insertion failure
            die("❌ INSERT FAILED. MySQL Error: " . $stmt->error); 
        }
    }

} else {
    // If accessed directly without POST data
    header("Location: " . $signup_page);
    exit();
}
// Omit closing tag to prevent header output errors