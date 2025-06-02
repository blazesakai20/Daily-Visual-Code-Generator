<?php
include 'db.php'; // Include your database connection file

header('Content-Type: application/json'); // Set header to JSON

try {
    // Select a random quote (no approval status filter)
    $stmt = $conn->prepare("SELECT id, quote_text, author, image_path FROM quotes ORDER BY RAND() LIMIT 1");

    if ($stmt === false) {
        throw new Exception("Database prepare failed: " . $conn->error);
    }

    $stmt->execute();
    $result = $stmt->get_result();

    $quote = null;
    if ($result->num_rows > 0) {
        $quote = $result->fetch_assoc();
    } else {
        // Fallback if no quotes are found
        $quote = [
            'id' => null,
            'quote_text' => 'No quotes available yet. Upload one!',
            'author' => 'InspireFrame',
            'image_path' => '' // No image path
        ];
    }

    echo json_encode($quote);

    $stmt->close();
} catch (Exception $e) {
    error_log("Error in get_random_quote.php: " . $e->getMessage());
    echo json_encode(['error' => 'Failed to load random quote: ' . $e->getMessage()]);
}

$conn->close();
?>