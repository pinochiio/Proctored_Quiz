<?php
// Database connection
$conn = new mysqli('localhost', 'root', '', 'proctored_quiz'); // Replace with your credentials

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Query to fetch the image path from the database
$query = "SELECT image_path FROM image_capture WHERE candidate_id = ? AND quiz_id = ?";
$stmt = $conn->prepare($query);

if ($stmt) {
    $candidate_id = 6; // Replace with the actual candidate ID
    $quiz_id = 13;     // Replace with the actual quiz ID

    $stmt->bind_param("ii", $candidate_id, $quiz_id);
    $stmt->execute();
    $stmt->bind_result($imagePath);

    if ($stmt->fetch()) {
        // Display the image
        if (file_exists($imagePath)) {
            echo "<img src='$imagePath' alt='Captured Image' style='max-width:500px; border: 2px solid #ccc; border-radius: 10px;'>";
        } else {
            echo "Image file does not exist on the server.";
        }
    } else {
        echo "No record found for the given candidate and quiz.";
    }

    $stmt->close();
} else {
    echo "Failed to prepare the database query.";
}

$conn->close();
?>
