<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<pre>";
echo "PHP Version: " . phpversion() . "\n";
echo "Server Software: " . $_SERVER['SERVER_SOFTWARE'] . "\n";
echo "Current User: " . get_current_user() . "\n";
echo "Script Owner: " . fileowner(__FILE__) . "\n\n";

$upload_dir = 'uploads';
$full_path = realpath($upload_dir) ?: dirname(__FILE__) . '/' . $upload_dir;

echo "Upload Directory Details:\n";
echo "Full Path: " . $full_path . "\n";

if (file_exists($upload_dir)) {
    echo "Directory exists: Yes\n";
    echo "Permissions: " . substr(sprintf('%o', fileperms($upload_dir)), -4) . "\n";
    echo "Owner: " . fileowner($upload_dir) . "\n";
    echo "Group: " . filegroup($upload_dir) . "\n";
    echo "Is writable: " . (is_writable($upload_dir) ? 'Yes' : 'No') . "\n";
    
    // Try to create a test file
    $test_file = $upload_dir . '/test.txt';
    $success = @file_put_contents($test_file, 'test');
    echo "Can create files: " . ($success !== false ? 'Yes' : 'No') . "\n";
    if ($success !== false) {
        unlink($test_file);
    }
} else {
    echo "Directory does not exist\n";
    
    // Try to create directory
    $success = @mkdir($upload_dir, 0777, true);
    echo "Can create directory: " . ($success ? 'Yes' : 'No') . "\n";
    if ($success) {
        echo "Created with permissions: " . substr(sprintf('%o', fileperms($upload_dir)), -4) . "\n";
        rmdir($upload_dir);
    }
}

echo "\nPHP Settings:\n";
echo "upload_max_filesize: " . ini_get('upload_max_filesize') . "\n";
echo "post_max_size: " . ini_get('post_max_size') . "\n";
echo "max_execution_time: " . ini_get('max_execution_time') . "\n";
echo "memory_limit: " . ini_get('memory_limit') . "\n";

echo "\nRecommended Terminal Commands:\n";
echo "cd " . dirname(__FILE__) . "\n";
echo "sudo rm -rf uploads\n";
echo "sudo mkdir uploads\n";
echo "sudo chown _www:_www uploads\n";
echo "sudo chmod 777 uploads\n";
echo "</pre>";
?> 