<?php
include 'db.php'; // Include your database connection file

header('Content-Type: application/json'); // Set header to JSON

try {
    // Select a random approved quote that has an image path
    $stmt = $conn->prepare("SELECT id, quote_text, author, image_path FROM quotes WHERE approved = 1 AND image_path IS NOT NULL AND image_path != '' ORDER BY RAND() LIMIT 1");

    if ($stmt === false) {
        throw new Exception("Database prepare failed: " . $conn->error);
    }

    $stmt->execute();
    $result = $stmt->get_result();

    $quote = null;
    if ($result->num_rows > 0) {
        $quote = $result->fetch_assoc();
    } else {
        // Fallback if no approved quotes with images are found
        $quote = [
            'id' => null,
            'quote_text' => 'No approved quotes with images available yet. Please upload one via the "Upload Quote" page and ensure an admin approves it!',
            'author' => 'InspireFrame',
            'image_path' => '' // No image path
        ];
    }

    echo json_encode($quote);

    $stmt->close();
} catch (Exception $e) {
    error_log("Error in get_daily_quote.php: " . $e->getMessage());
    echo json_encode(['error' => 'Failed to load quote: ' . $e->getMessage()]);
}

$conn->close();
?>