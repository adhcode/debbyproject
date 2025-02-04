<?php
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Connect to the database
    $conn = new mysqli('localhost', 'root', '', 'file_management');

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Fetch the file info from the database
    $result = $conn->query("SELECT * FROM files WHERE id = $id");
    $file = $result->fetch_assoc();

    if ($file) {
        // Delete the file from the server
        unlink('uploads/' . $file['file_name']);
        
        // Delete the file metadata from the database
        $conn->query("DELETE FROM files WHERE id = $id");
        echo 'File deleted successfully!';
    } else {
        echo 'File not found.';
    }

    $conn->close();
}
?>
