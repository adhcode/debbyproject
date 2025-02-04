<?php
umask(0);
if (!ini_get('safe_mode')) {
    set_time_limit(0);
}
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['file'])) {
    $file = $_FILES['file'];
    
    // Debug information
    error_log("File upload attempt: " . print_r($file, true));
    
    // Validate the file
    if ($file['error'] == 0) {
        $upload_dir = 'uploads/';
        
        // Check upload directory with better error handling
        if (!file_exists($upload_dir)) {
            if (!@mkdir($upload_dir, 0777, true)) {
                error_log("Failed to create uploads directory. Error: " . error_get_last()['message']);
                header("Location: files.php?error=" . urlencode("Failed to create uploads directory. Please contact administrator."));
                exit();
            }
            // Ensure permissions are set even after creation
            @chmod($upload_dir, 0777);
            error_log("Created uploads directory");
        }
        
        // Double-check directory is writable
        if (!is_writable($upload_dir)) {
            $perms = substr(sprintf('%o', fileperms($upload_dir)), -4);
            error_log("Uploads directory is not writable. Current permissions: $perms");
            header("Location: files.php?error=" . urlencode("Uploads directory is not writable. Current permissions: $perms"));
            exit();
        }
        
        $file_name = basename($file['name']);
        // Make filename safe
        $file_name = preg_replace("/[^a-zA-Z0-9.-]/", "_", $file_name);
        $upload_path = $upload_dir . $file_name;
        
        error_log("Attempting to upload to: " . $upload_path);
        
        // Handle duplicate filenames
        $counter = 1;
        $name = pathinfo($file_name, PATHINFO_FILENAME);
        $ext = pathinfo($file_name, PATHINFO_EXTENSION);
        
        while (file_exists($upload_path)) {
            $file_name = $name . "($counter)." . $ext;
            $upload_path = $upload_dir . $file_name;
            $counter++;
        }
        
        if (!move_uploaded_file($file['tmp_name'], $upload_path)) {
            $error = error_get_last();
            error_log("Failed to move uploaded file. PHP Error: " . print_r($error, true));
            header("Location: files.php?error=" . urlencode("Failed to move file: " . $error['message']));
            exit();
        }
        
        // File moved successfully, now update database
        $conn = new mysqli('localhost', 'root', '', 'file_management');
        
        if ($conn->connect_error) {
            error_log("Database connection failed: " . $conn->connect_error);
            header("Location: files.php?error=database_connection");
            exit();
        }
        
        $stmt = $conn->prepare("INSERT INTO files (file_name, file_size) VALUES (?, ?)");
        $stmt->bind_param('si', $file_name, $file['size']);
        
        if ($stmt->execute()) {
            error_log("File uploaded and database updated successfully");
            header("Location: files.php?success=1");
        } else {
            error_log("Database insert failed: " . $stmt->error);
            header("Location: files.php?error=database_insert");
        }
        
        $stmt->close();
        $conn->close();
        
    } else {
        $error_messages = [
            UPLOAD_ERR_INI_SIZE => "The uploaded file exceeds the upload_max_filesize directive in php.ini",
            UPLOAD_ERR_FORM_SIZE => "The uploaded file exceeds the MAX_FILE_SIZE directive in the HTML form",
            UPLOAD_ERR_PARTIAL => "The uploaded file was only partially uploaded",
            UPLOAD_ERR_NO_FILE => "No file was uploaded",
            UPLOAD_ERR_NO_TMP_DIR => "Missing a temporary folder",
            UPLOAD_ERR_CANT_WRITE => "Failed to write file to disk",
            UPLOAD_ERR_EXTENSION => "A PHP extension stopped the file upload"
        ];
        
        $error_message = isset($error_messages[$file['error']]) 
            ? $error_messages[$file['error']] 
            : "Unknown upload error";
        
        error_log("File upload error: " . $error_message);    
        header("Location: files.php?error=" . urlencode($error_message));
    }
    exit();
} else {
    // If someone tries to access this file directly, redirect them
    header("Location: files.php");
    exit();
}
?>
