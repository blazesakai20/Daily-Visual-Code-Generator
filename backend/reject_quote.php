<?php
include 'db.php';
session_start();

// Basic admin check (you should enhance this for a real application)
$is_admin = true; // For demonstration, assume true
if (!$is_admin) {
    die("Access denied");
}

$id = $_POST['quote_id'] ?? 0;

if ($id) {
    // Set approved status to 0 (rejected/unapproved)
    $stmt = $conn->prepare("UPDATE quotes SET approved = 0 WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        header("Location: admin_panel.php?message=Quote rejected!");
        exit;
    } else {
        header("Location: admin_panel.php?message=Failed to reject quote.");
        exit;
    }
    $stmt->close();
} else {
    header("Location: admin_panel.php?message=Invalid quote ID.");
    exit;
}
$conn->close();
?>