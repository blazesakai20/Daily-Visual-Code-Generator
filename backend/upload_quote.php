<?php
include 'db.php'; // Include your database connection file
session_start(); // Start the session to access user_id

header('Content-Type: text/plain'); // Set header to plain text for debugging ease

// 1. Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "Error: You must be logged in to upload quotes.";
    exit;
}

$user_id = $_SESSION['user_id']; // Get the logged-in user's ID

// 2. Get form data
$quote_text = $_POST['quote_text'] ?? '';
$author = $_POST['author'] ?? '';
$image_file = $_FILES['image'] ?? null;

// Validate basic input
if (empty($quote_text) || empty($author)) {
    echo "Error: Quote text and author are required.";
    exit;
}

// 3. Handle image upload
$uploadDir = '../uploads/'; // Directory where images will be stored (relative to backend/ directory)

// Create uploads directory if it doesn't exist
if (!is_dir($uploadDir)) {
    // Attempt to create the directory with full permissions for testing (adjust for production)
    if (!mkdir($uploadDir, 0777, true)) {
        echo "Error: Failed to create uploads directory. Check permissions.";
        error_log("Failed to create directory: " . $uploadDir);
        exit;
    }
}

$imagePath = ''; // Initialize image path

if ($image_file && $image_file['error'] === UPLOAD_ERR_OK) {
    $fileName = basename($image_file['name']);
    $targetFilePath = $uploadDir . $fileName;
    $fileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));

    // --- REMOVED FILE TYPE VALIDATION ---
    // The previous code block for checking allowedTypes (jpg, png, jpeg, gif) has been removed.
    // This allows any file type to be uploaded. Be aware of potential security implications
    // of allowing arbitrary file uploads without strict validation in a production environment.

    // Check file size (e.g., max 5MB)
    if ($image_file['size'] > 5 * 1024 * 1024) {
        echo "Error: File size exceeds the limit (5MB).";
        exit;
    }

    // Move the uploaded file to the target directory
    if (move_uploaded_file($image_file['tmp_name'], $targetFilePath)) {
        $imagePath = $targetFilePath; // This now holds '../uploads/filename.jpg' or other extension
    } else {
        echo "Error: Failed to move uploaded file. Check directory permissions.";
        error_log("Failed to move uploaded file from {$image_file['tmp_name']} to {$targetFilePath}. Error: " . error_get_last()['message']);
        exit;
    }
} else if ($image_file && $image_file['error'] !== UPLOAD_ERR_NO_FILE) {
    // Handle other upload errors
    switch ($image_file['error']) {
        case UPLOAD_ERR_INI_SIZE:
        case UPLOAD_ERR_FORM_SIZE:
            echo "Error: Uploaded file exceeds maximum allowed size.";
            break;
        case UPLOAD_ERR_PARTIAL:
            echo "Error: File was only partially uploaded.";
            break;
        case UPLOAD_ERR_CANT_WRITE:
            echo "Error: Failed to write file to disk.";
            break;
        case UPLOAD_ERR_NO_TMP_DIR:
            echo "Error: Missing a temporary folder for uploads.";
            break;
        case UPLOAD_ERR_EXTENSION:
            echo "Error: A PHP extension stopped the file upload.";
            break;
        default:
            echo "Error: Unknown upload error.";
            break;
    }
    exit;
} else {
    // If no file was uploaded, or file is empty, handle accordingly
    echo "Error: No image file uploaded or file is empty. An image is required for quotes.";
    exit;
}

// 4. Insert data into the database
// Set initial approval status to 'pending' (you can adjust this as needed)
$approval_status = 'pending'; // All new uploads require admin approval

$stmt = $conn->prepare("INSERT INTO quotes (quote_text, author, image_path, user_id, approval_status, created_at) VALUES (?, ?, ?, ?, ?, NOW())");

if ($stmt === false) {
    echo "Error: Database prepare failed: " . $conn->error;
    error_log("Database prepare failed: " . $conn->error);
    // If database insertion fails, attempt to delete the uploaded image file
    if (file_exists($imagePath)) {
        unlink($imagePath);
    }
    exit;
}

// Bind the correct imagePath (which is already ../uploads/filename.ext)
$stmt->bind_param("sisss", $quote_text, $author, $imagePath, $user_id, $approval_status);

if ($stmt->execute()) {
    echo "Quote uploaded successfully! It is awaiting admin approval.";
} else {
    echo "Error: Failed to upload quote to database: " . $stmt->error;
    error_log("Failed to insert quote: " . $stmt->error);
    // If database insertion fails, attempt to delete the uploaded image file
    if (file_exists($imagePath)) {
        unlink($imagePath);
    }
}

$stmt->close();
$conn->close();
?>