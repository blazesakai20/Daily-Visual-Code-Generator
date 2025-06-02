<?php
include 'db.php'; // Include your database connection

header('Content-Type: application/json'); // Set header to JSON

$offset = isset($_GET['offset']) ? intval($_GET['offset']) : 0;

// Get total number of approved quotes
$totalQuotesResult = $conn->query("SELECT COUNT(*) as total FROM quotes WHERE approved = 1");
$totalQuotesRow = $totalQuotesResult->fetch_assoc();
$totalQuotes = $totalQuotesRow['total'];

$quote = null; // Initialize quote as null

if ($totalQuotes > 0) {
    // Ensure offset wraps around if it exceeds total quotes
    $offset = $offset % $totalQuotes;

    $stmt = $conn->prepare("SELECT id, quote_text, author, image_path FROM quotes WHERE approved = 1 LIMIT 1 OFFSET ?");
    $stmt->bind_param("i", $offset);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $quote = $result->fetch_assoc();
    }
    $stmt->close();
}

// Return the quote data and total quotes count
echo json_encode(['quote' => $quote, 'total_quotes' => $totalQuotes]);

$conn->close();
?>