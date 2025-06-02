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
    // Get the image path before deleting the record to delete the file too
    $stmt_select = $conn->prepare("SELECT image_path FROM quotes WHERE id = ?");
    $stmt_select->bind_param("i", $id);
    $stmt_select->execute();
    $result_select = $stmt_select->get_result();
    $row = $result_select->fetch_assoc();
    $image_path = $row['image_path'] ?? null;
    $stmt_select->close();

    // First, delete any associated favorite entries for this quote
    $stmt_favorites = $conn->prepare("DELETE FROM favorites WHERE quote_id = ?");
    $stmt_favorites->bind_param("i", $id);
    $stmt_favorites->execute();
    $stmt_favorites->close();

    // Now, delete the quote from the quotes table
    $stmt_delete = $conn->prepare("DELETE FROM quotes WHERE id = ?");
    $stmt_delete->bind_param("i", $id);

    if ($stmt_delete->execute()) {
        // If quote was deleted from DB, try to delete the image file
        if ($image_path && file_exists($image_path)) {
            unlink($image_path); // Delete the actual file
        }
        header("Location: admin_panel.php?message=Quote removed successfully!");
        exit;
    } else {
        header("Location: admin_panel.php?message=Failed to remove quote.");
        exit;
    }
    $stmt_delete->close();
} else {
    header("Location: admin_panel.php?message=Invalid quote ID.");
    exit;
}
$conn->close();
?>