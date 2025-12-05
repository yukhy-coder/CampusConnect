<?php
// Set this to your local timezone
date_default_timezone_set('Asia/Manila'); 

$host = "localhost";
$user = "root";
$pass = "";
$db   = "CampusConnect";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>