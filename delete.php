<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (isset($_GET['id'])) {
    $conn = new mysqli('localhost', 'root', '', 'file_management');
    
    if ($conn->connect_error) {
        header("Location: files.php?error=connection_failed");
        exit();
    }
    
    // First get the filename
    $stmt = $conn->prepare("SELECT file_name FROM files WHERE id = ?");
    $stmt->bind_param('i', $_GET['id']);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        $file_path = 'uploads/' . $row['file_name'];
        
        // Delete the file from uploads directory
        if (file_exists($file_path)) {
            unlink($file_path);
        }
        
        // Delete the database record
        $stmt = $conn->prepare("DELETE FROM files WHERE id = ?");
        $stmt->bind_param('i', $_GET['id']);
        $stmt->execute();
        
        header("Location: files.php?success=deleted");
    } else {
        header("Location: files.php?error=file_not_found");
    }
    
    $stmt->close();
    $conn->close();
} else {
    header("Location: files.php");
}
exit();
?>
