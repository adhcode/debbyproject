# debbyproject

# File Management System

A secure and user-friendly file management system built with PHP and MySQL. This system allows users to upload, download, and manage files with a modern, responsive interface.

## Features

- 📤 File Upload with progress feedback
- 📥 Secure File Download
- 🗑️ File Deletion
- 📊 File Size Display
- 🔒 Security Measures
- 📱 Responsive Design
- 🎨 Modern UI with Icons
- ⚡ Real-time Feedback

## Installation

1. **Prerequisites**
   - PHP 7.4 or higher
   - MySQL 5.7 or higher
   - Apache web server
   - XAMPP/MAMP/WAMP installed

2. **Setup**
   ```bash
   # Clone or download the project to your web server directory
   cd /path/to/xampp/htdocs
   git clone [your-repo-url]
   # or copy files manually
   ```

3. **Database Setup**
   - Create a new database named `file_management`
   - Import the `file_management.sql` file
   - Or visit `http://localhost/project/setup.php`

4. **Directory Permissions**
   ```bash
   # Create uploads directory if it doesn't exist
   mkdir uploads
   
   # Set permissions (Linux/Mac)
   chmod 755 uploads
   
   # For XAMPP on Mac
   sudo chown daemon:daemon uploads
   
   # For MAMP on Mac
   sudo chown _www:_www uploads
   ```

5. **Configuration**
   - Ensure `.htaccess` files are in place
   - Verify MySQL connection details in PHP files

## Project Structure


├── files.php # Main interface
├── file_upload_system.php # Upload handler
├── delete.php # Deletion handler
├── setup.php # Database setup
├── index.php # Entry point
├── .htaccess # Apache config
├── README.md # Documentation
└── uploads/ # Upload directory
└── .htaccess # Upload security


## Security Features

- Directory listing prevention
- File type restrictions
- SQL injection prevention
- XSS protection
- Secure file deletion
- Upload directory protection

## File Types Supported

- Images (jpg, jpeg, png, gif)
- Documents (pdf, doc, docx)
- Spreadsheets (xls, xlsx)
- Other file types can be added in `.htaccess`

## Usage

1. **Uploading Files**
   - Click "Choose File" or drag & drop
   - Select file and click "Upload"
   - Wait for success message

2. **Downloading Files**
   - Click the "Download" button next to file
   - File will download automatically

3. **Deleting Files**
   - Click "Delete" button
   - Confirm deletion in popup
   - File and record will be removed





files.php - Main Interface: 

// Database connection
$conn = new mysqli('localhost', 'root', '', 'file_management');

// File display logic
$result = $conn->query("SELECT * FROM files ORDER BY upload_date DESC");
while ($row = $result->fetch_assoc()) {
    // Get file extension for icon
    $file_ext = strtolower(pathinfo($row['file_name'], PATHINFO_EXTENSION));
    
    // Choose appropriate icon
    $icon_class = 'fa-file';
    if (in_array($file_ext, ['jpg', 'jpeg', 'png', 'gif'])) {
        $icon_class = 'fa-file-image';
    }
    
    // Display file card with:
    // - File name
    // - File size (converted to KB)
    // - Download link
    // - Delete button
}

file_upload_system.php - Upload Handler:
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['file'])) {
    $file = $_FILES['file'];
    
    // Check for upload errors
    if ($file['error'] == 0) {
        // Create uploads directory if needed
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        // Sanitize filename
        $file_name = preg_replace("/[^a-zA-Z0-9.-]/", "_", basename($file['name']));
        
        // Handle duplicate filenames
        while (file_exists($upload_path)) {
            $name = pathinfo($file_name, PATHINFO_FILENAME);
            $ext = pathinfo($file_name, PATHINFO_EXTENSION);
            $counter++;
            $file_name = $name . "($counter)." . $ext;
            $upload_path = $upload_dir . $file_name;
        }
        
        // Move file and update database
        if (move_uploaded_file($file['tmp_name'], $upload_path)) {
            $stmt = $conn->prepare("INSERT INTO files (file_name, file_size) VALUES (?, ?)");
            $stmt->bind_param('si', $file_name, $file['size']);
            $stmt->execute();
        }
    }
}

delete.php - File Deletion:

if (isset($_GET['id'])) {
    // Get file info from database
    $stmt = $conn->prepare("SELECT file_name FROM files WHERE id = ?");
    $stmt->bind_param('i', $_GET['id']);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        $file_path = 'uploads/' . $row['file_name'];
        
        // Delete physical file
        if (file_exists($file_path)) {
            unlink($file_path);
        }
        
        // Delete database record
        $stmt = $conn->prepare("DELETE FROM files WHERE id = ?");
        $stmt->bind_param('i', $_GET['id']);
        $stmt->execute();
    }
}

setup.php - Database Setup:
// Create database
$sql = "CREATE DATABASE IF NOT EXISTS file_management";
$conn->query($sql);

// Create table
$sql = "CREATE TABLE IF NOT EXISTS files (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    file_name VARCHAR(255) NOT NULL,
    file_size INT(11) NOT NULL,
    upload_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
$conn->query($sql);
.htaccess - Security Configuration:

# Prevent directory listing
Options -Indexes

# Protect sensitive files
<FilesMatch "^\.htaccess|\.sql$">
    Order allow,deny
    Deny from all
</FilesMatch>

# Allow specific file types
<FilesMatch "\.(jpg|jpeg|png|gif|pdf|doc|docx)$">
    Order allow,deny
    Allow from all
</FilesMatch>

# Prevent PHP execution in uploads
<FilesMatch "\.ph(p[3-5]?|tml)$">
    Order deny,allow
    Deny from all
</FilesMatch>

Data Flow:

Upload Process:
1. User selects file → files.php (form)
2. Form submits to → file_upload_system.php
3. File saved to → uploads/ directory
4. File info saved to → database
5. Redirect back to → files.php

Download Process:
1. User clicks download → files.php
2. Browser downloads from → uploads/ directory

Delete Process:
1. User clicks delete → delete.php
2. File removed from → uploads/ directory
3. Record deleted from → database
4. Redirect back to → files.php

