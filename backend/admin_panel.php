<?php
include 'db.php';
session_start();

// Basic admin check (you should enhance this for a real application)
$is_admin = true; // For demonstration, assume true
if (!$is_admin) {
    die("Access denied");
}

// Display messages from redirects
$message = $_GET['message'] ?? '';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - InspireFrame</title>
    <link rel="stylesheet" href="../frontend/style.css">
    <style>
        /* Admin-specific styles or overrides */
        body {
            /* Inherits most styles from style.css, but ensure no conflicting margins */
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 900px; /* Adjust as needed for content width */
            margin: 20px auto;
            background-color: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        h1, h2 {
            color: #555;
            text-align: center;
            margin-bottom: 25px;
        }
        .message {
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 5px;
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
            text-align: center;
        }
        .quote-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); /* Responsive grid */
            gap: 20px;
        }
        .quote-item {
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            padding: 15px;
            background-color: #fcfcfc;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            display: flex;
            flex-direction: column;
            justify-content: space-between; /* Pushes buttons to bottom */
        }
        .quote-item img {
            max-width: 100%;
            height: 180px; /* Fixed height for consistent image display */
            object-fit: cover; /* Crops image to fit the container */
            border-radius: 5px;
            margin-bottom: 10px;
            display: block; /* Removes extra space below image */
            margin-left: auto;
            margin-right: auto;
        }
        .quote-item strong {
            display: block;
            margin-bottom: 5px;
            font-size: 1.1em;
            color: #444;
        }
        .quote-item em {
            display: block;
            margin-bottom: 10px;
            color: #777;
        }
        .quote-item p {
            margin: 5px 0;
            font-size: 0.9em;
            color: #666;
        }
        .button-group {
            margin-top: 15px;
            display: flex;
            flex-wrap: wrap; /* Allows buttons to wrap on smaller screens */
            gap: 10px;
            justify-content: center; /* Center buttons horizontally */
        }
        .button-group form {
            margin: 0; /* Remove default form margin */
        }
        /* Ensure these match/override styles in style.css for buttons */
        .button {
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            color: white;
            cursor: pointer;
            font-size: 0.9em;
            transition: background-color 0.2s ease;
            flex-grow: 1; /* Allow buttons to expand */
        }
        .button-approve {
            background-color: #28a745; /* Green */
        }
        .button-approve:hover {
            background-color: #218838;
        }
        .button-reject {
            background-color: #ffc107; /* Yellow/Orange */
            color: #333; /* Ensure text is readable on yellow */
        }
        .button-reject:hover {
            background-color: #e0a800;
        }
        .button-remove {
            background-color: #dc3545; /* Red */
        }
        .button-remove:hover {
            background-color: #c82333;
        }
        hr {
            border: 0;
            height: 1px;
            background: #e0e0e0;
            margin: 40px 0;
        }
    </style>
</head>
<body>
    <nav>
        <a href="../frontend/index.html">Home</a>
        <a href="../frontend/upload.html">Upload Quote</a>
        <a href="../frontend/login.html">Login</a>
        <a href="../frontend/register.html">Register</a>
        <a href="../frontend/profile.html">Profile</a>
        <a href="admin_panel.php" class="active">Admin Panel</a> <a href="logout.php">Logout</a>
    </nav>

    <div class="container">
        <h1>Admin Panel</h1>

        <?php if ($message): ?>
            <p class="message"><?php echo htmlspecialchars($message); ?></p>
        <?php endif; ?>

        <h2>Pending Quotes</h2>
        <div class="quote-grid">
            <?php
            // Fetch pending quotes
            $pending_result = $conn->query("SELECT * FROM quotes WHERE approved = 0 ORDER BY created_at DESC");

            if ($pending_result->num_rows > 0) {
                while ($row = $pending_result->fetch_assoc()) {
                    echo "<div class='quote-item'>";
                    // Fixed image path: image_path already contains the relative path like ../uploads/image.jpg
                    echo "<img src='" . htmlspecialchars($row['image_path']) . "' alt='Quote Image'>";
                    echo "<strong>Quote: </strong>" . htmlspecialchars($row['quote_text']) . "<br>";
                    echo "<strong>Author: </strong><em>" . htmlspecialchars($row['author']) . "</em><br>";
                    echo "<p>Status: Pending</p>";
                    echo "<div class='button-group'>";
                    // Approve button
                    echo "<form method='POST' action='approve_quote.php'>";
                    echo "<input type='hidden' name='quote_id' value='" . $row['id'] . "'>";
                    echo "<button type='submit' class='button button-approve'>Approve</button>";
                    echo "</form>";
                    // Reject button (ensure reject_quote.php exists)
                    echo "<form method='POST' action='reject_quote.php'>";
                    echo "<input type='hidden' name='quote_id' value='" . $row['id'] . "'>";
                    echo "<button type='submit' class='button button-reject' onclick='return confirm(\"Are you sure you want to reject this quote?\");'>Reject</button>";
                    echo "</form>";
                    // Remove (Delete) button (ensure delete_quote.php exists)
                    echo "<form method='POST' action='delete_quote.php'>";
                    echo "<input type='hidden' name='quote_id' value='" . $row['id'] . "'>";
                    echo "<button type='submit' class='button button-remove' onclick='return confirm(\"Are you sure you want to PERMANENTLY remove this quote and its image?\");'>Remove</button>";
                    echo "</form>";
                    echo "</div>"; // .button-group
                    echo "</div>"; // .quote-item
                }
            } else {
                echo "<p style='text-align: center; color: #666;'>No pending quotes.</p>";
            }
            ?>
        </div><hr>

        <h2>Approved Quotes</h2>
        <div class="quote-grid">
            <?php
            // Fetch approved quotes
            $approved_result = $conn->query("SELECT * FROM quotes WHERE approved = 1 ORDER BY created_at DESC");

            if ($approved_result->num_rows > 0) {
                while ($row = $approved_result->fetch_assoc()) {
                    echo "<div class='quote-item'>";
                    // Fixed image path: image_path already contains the relative path like ../uploads/image.jpg
                    echo "<img src='" . htmlspecialchars($row['image_path']) . "' alt='Quote Image'>";
                    echo "<strong>Quote: </strong>" . htmlspecialchars($row['quote_text']) . "<br>";
                    echo "<strong>Author: </strong><em>" . htmlspecialchars($row['author']) . "</em><br>";
                    echo "<p>Status: Approved</p>";
                    echo "<div class='button-group'>";
                    // Remove (Delete) button for approved quotes (ensure delete_quote.php exists)
                    echo "<form method='POST' action='delete_quote.php'>";
                    echo "<input type='hidden' name='quote_id' value='" . $row['id'] . "'>";
                    echo "<button type='submit' class='button button-remove' onclick='return confirm(\"Are you sure you want to PERMANENTLY remove this quote and its image?\");'>Remove</button>";
                    echo "</form>";
                    echo "</div>"; // .button-group
                    echo "</div>"; // .quote-item
                }
            } else {
                echo "<p style='text-align: center; color: #666;'>No approved quotes.</p>";
            }
            $conn->close();
            ?>
        </div></div></body>
</html>