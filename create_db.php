<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$host = 'localhost';
$username = 'root';
$password = '';

try {
    // First, create a connection without selecting a database
    $conn = new mysqli($host, $username, $password);
    
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }

    // Drop the database if it exists and create it fresh
    $conn->query("DROP DATABASE IF EXISTS file_management");
    
    if (!$conn->query("CREATE DATABASE file_management")) {
        throw new Exception("Error creating database: " . $conn->error);
    }
    
    // Select the database
    if (!$conn->select_db("file_management")) {
        throw new Exception("Error selecting database: " . $conn->error);
    }
    
    // Create the files table
    $sql = "CREATE TABLE files (
        id INT(11) NOT NULL AUTO_INCREMENT,
        file_name VARCHAR(255) NOT NULL,
        file_size INT(11) NOT NULL,
        upload_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
    
    if (!$conn->query($sql)) {
        throw new Exception("Error creating table: " . $conn->error);
    }
    
    echo "<div style='color: green; padding: 20px; background: #e8f5e9; border: 1px solid #c8e6c9; margin: 20px; border-radius: 5px;'>";
    echo "✅ Database and table created successfully!<br>";
    echo "You can now go to <a href='files.php'>files.php</a>";
    echo "</div>";
    
} catch (Exception $e) {
    echo "<div style='color: #721c24; padding: 20px; background: #f8d7da; border: 1px solid #f5c6cb; margin: 20px; border-radius: 5px;'>";
    echo "❌ Error: " . $e->getMessage();
    echo "</div>";
} finally {
    if (isset($conn)) {
        $conn->close();
    }
}
?> 