<?php
include 'db.php';
session_start();

header('Content-Type: application/json');

$response = array();

if (isset($_SESSION['user_id'])) {
    // User is logged in, fetch user data
    $user_id = $_SESSION['user_id'];
    $stmt = $conn->prepare("SELECT id, username, email FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user_data = $result->fetch_assoc();
        $response['logged_in'] = true;
        $response['user_id'] = $user_data['id'];
        $response['username'] = $user_data['username'];
        $response['email'] = $user_data['email']; // Include email if needed
    } else {
        $response['logged_in'] = false;
        $response['message'] = 'No user found with this ID.';
    }
    $stmt->close();
} else {
    // No user is logged in
    $response['logged_in'] = false;
    $response['message'] = 'No user is logged in.';
}

echo json_encode($response);

$conn->close();
?>