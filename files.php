<?php
// Connect to the database
$conn = new mysqli('localhost', 'root', '', 'file_management');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch all files
$result = $conn->query("SELECT * FROM files");

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo '<div>';
        echo 'File: ' . $row['file_name'] . ' (' . $row['file_size'] . ' bytes)';
        echo ' <a href="uploads/' . $row['file_name'] . '" download>Download</a>';
        echo ' <a href="delete.php?id=' . $row['id'] . '">Delete</a>';
        echo '</div><br>';
    }
} else {
    echo 'No files uploaded.';
}

$conn->close();
?>
