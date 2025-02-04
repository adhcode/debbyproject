<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['file'])) {
    $file = $_FILES['file'];
    
    // Validate the file (you can customize the conditions)
    if ($file['error'] == 0) {
        $upload_dir = 'uploads/';
        $file_name = basename($file['name']);
        $upload_path = $upload_dir . $file_name;
        
        if (move_uploaded_file($file['tmp_name'], $upload_path)) {
            // Connect to the database
            $conn = new mysqli('localhost', 'root', '', 'file_management');
            
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }
            
            // Insert metadata into the database
            $stmt = $conn->prepare("INSERT INTO files (file_name, file_size) VALUES (?, ?)");
            $stmt->bind_param('si', $file_name, $file['size']);
            $stmt->execute();
            
            echo 'File uploaded successfully!';
        } else {
            echo 'File upload failed!';
        }
    } else {
        echo 'There was an error with the file upload.';
    }
}
?>

<form action="index.php" method="POST" enctype="multipart/form-data">
    <label for="file">Upload File:</label>
    <input type="file" name="file" id="file" required>
    <button type="submit">Upload</button>
</form>
