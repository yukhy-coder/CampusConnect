<?php
// 1. START SESSION
session_start();

// 2. SET TIMEZONE
date_default_timezone_set('Asia/Manila');

// 3. DATABASE CONNECTION
$servername = "localhost";
$username = "root";       
$password = "";           
$dbname = "CampusConnect"; 

// Set header to JSON
header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $email = trim($_POST['email'] ?? '');
    $input_password = $_POST['password'] ?? ''; 

    if (empty($email) || empty($input_password)) {
        echo json_encode(["success" => false, "message" => "Please fill in all fields."]);
        exit();
    }
    
    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        echo json_encode(["success" => false, "message" => "Database Connection Failed."]);
        exit();
    }
    
    // 4. SELECT QUERY
    $sql = "SELECT user_id, fullname, password, role, is_approved FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();

    // 5. VERIFY USER
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        
        if (password_verify($input_password, $user['password'])) {
            
            if ($user['is_approved'] == 0) {
                echo json_encode(["success" => false, "message" => "Account pending approval."]);
                $conn->close();
                exit();
            }
            
            // --- SUCCESS: SAVE SESSION DATA ---
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['fullname'] = $user['fullname'];
            $_SESSION['role'] = $user['role'];

            // 1. Update Last Login
            $update_sql = "UPDATE users SET last_login = NOW() WHERE user_id = ?";
            $u_stmt = $conn->prepare($update_sql);
            $u_stmt->bind_param("i", $user['user_id']);
            $u_stmt->execute();
            $u_stmt->close();

            // 2. Insert Activity Log & SAVE ID
            // Note: We rely on default timestamp for login time
            $log_sql = "INSERT INTO activity_logs (user_id, action) VALUES (?, 'Login')";
            $l_stmt = $conn->prepare($log_sql);
            $l_stmt->bind_param("i", $user['user_id']);
            
            if ($l_stmt->execute()) {
                // *** THIS IS THE MISSING PIECE ***
                // Save the ID of this log row so we can update it when they logout
                $_SESSION['current_log_id'] = $conn->insert_id; 
            }
            $l_stmt->close();

            $conn->close();
            
            echo json_encode(["success" => true, "role" => $user['role']]);
            exit();

        } else {
            echo json_encode(["success" => false, "message" => "Invalid password."]);
            exit();
        }
    } else {
        echo json_encode(["success" => false, "message" => "User not found."]);
        exit();
    }
} else {
    echo json_encode(["success" => false, "message" => "Invalid Request."]);
    exit();
}
?>