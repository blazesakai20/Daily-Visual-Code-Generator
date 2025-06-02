<?php
session_start();
require_once 'db_connection.php'; // ✅ Make sure this path is correct and connects to your DB

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["logged_in" => false, "message" => "User not logged in."]);
    exit;
}

$user_id = $_SESSION['user_id'];

// Prepare SQL query to get user details
$stmt = $conn->prepare("SELECT username, email, registered_on FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($username, $email, $registered_on);

if ($stmt->fetch()) {
    echo json_encode([
        "logged_in" => true,
        "user_id" => $user_id,
        "username" => $username,
        "email" => $email,
        "registered_on" => $registered_on
    ]);
} else {
    echo json_encode([
        "logged_in" => false,
        "message" => "User not found in database."
    ]);
}

$stmt->close();
$conn->close();
?>
