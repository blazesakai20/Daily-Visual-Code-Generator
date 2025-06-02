<?php
include 'db.php';
session_start();

// Ensure this file is only accessible by admins (add proper authentication here if not already done)
// For demonstration, we'll assume $is_admin check or similar logic is already in admin_panel.php
// and this script is called by AJAX from an authenticated admin session.

header('Content-Type: application/json'); // Respond with JSON

$id = $_POST['quote_id'] ?? 0;

if ($id) {
    $stmt = $conn->prepare("UPDATE quotes SET approved = 1 WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            echo json_encode(['status' => 'success', 'message' => 'Quote approved successfully!', 'quote_id' => $id]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Quote not found or already approved.', 'quote_id' => $id]);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $conn->error, 'quote_id' => $id]);
    }
    $stmt->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid quote ID.', 'quote_id' => $id]);
}

$conn->close();
?>