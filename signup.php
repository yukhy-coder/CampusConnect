<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json');

// Database configuration
$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'ctuconnect';

$response = ['success' => false, 'message' => ''];

try {
    // Get POST data
    $email = $_POST['email'] ?? '';
    $fullname = $_POST['fullname'] ?? '';
    $position = $_POST['position'] ?? '';
    $password = $_POST['password'] ?? '';

    // Validate inputs
    if (empty($email) || empty($fullname) || empty($position) || empty($password)) {
        throw new Exception('All fields are required');
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception('Invalid email format');
    }

    // Connect to database
    $conn = new mysqli($host, $user, $pass, $dbname);
    
    if ($conn->connect_error) {
        throw new Exception('Database connection failed');
    }

    // Check if email exists
    $stmt = $conn->prepare("SELECT user_id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        throw new Exception('Email already exists');
    }
    $stmt->close();

    // Generate unique user_id
    $user_id = 'USR_' . uniqid() . '_' . time();

    // Hash password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Insert user
    $stmt = $conn->prepare("INSERT INTO users (user_id, fullname, email, password, position, role, is_approved) VALUES (?, ?, ?, ?, ?, 'officer', FALSE)");
    $stmt->bind_param("sssss", $user_id, $fullname, $email, $hashed_password, $position);

    if ($stmt->execute()) {
        $response['success'] = true;
        $response['message'] = 'Account created successfully! Your account is pending approval.';
    } else {
        throw new Exception('Failed to create account');
    }

    $stmt->close();
    $conn->close();

} catch (Exception $e) {
    $response['message'] = $e->getMessage();
    $response['error_details'] = $e->getTraceAsString();
}

echo json_encode($response);
?>