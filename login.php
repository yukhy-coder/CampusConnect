<?php
session_start();

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
    $password = $_POST['password'] ?? '';
    $remember_me = $_POST['remember_me'] ?? false;

    // Validate inputs
    if (empty($email) || empty($password)) {
        throw new Exception('Email and password are required');
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception('Invalid email format');
    }

    // Connect to database
    $conn = new mysqli($host, $user, $pass, $dbname);
    
    if ($conn->connect_error) {
        throw new Exception('Database connection failed');
    }

    // Fetch user by email
    $stmt = $conn->prepare("SELECT user_id, fullname, email, password, position, role, is_approved FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        throw new Exception('Invalid email or password');
    }

    $user = $result->fetch_assoc();
    $stmt->close();

    // Verify password
    if (!password_verify($password, $user['password'])) {
        throw new Exception('Invalid email or password');
    }

    // Check if user is approved
    if (!$user['is_approved']) {
        throw new Exception('Your account is pending approval. Please wait for admin approval.');
    }

    // Login successful - Create session
    $_SESSION['user_id'] = $user['user_id'];
    $_SESSION['fullname'] = $user['fullname'];
    $_SESSION['email'] = $user['email'];
    $_SESSION['position'] = $user['position'];
    $_SESSION['role'] = $user['role'];
    $_SESSION['logged_in'] = true;

    // Set cookie if remember me is checked
    if ($remember_me) {
        setcookie('user_email', $email, time() + (86400 * 30), "/"); // 30 days
    }

    $response['success'] = true;
    $response['message'] = 'Login successful! Welcome ' . $user['fullname'];
    $response['role'] = $user['role'];
    $response['user'] = [
        'user_id' => $user['user_id'],
        'fullname' => $user['fullname'],
        'email' => $user['email'],
        'position' => $user['position'],
        'role' => $user['role']
    ];

    $conn->close();

} catch (Exception $e) {
    $response['message'] = $e->getMessage();
    $response['error_details'] = $e->getTraceAsString();
}

echo json_encode($response);
?>