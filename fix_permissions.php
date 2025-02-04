<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<pre>";

// Directory to check/fix
$upload_dir = 'uploads';

// Create directory if it doesn't exist
if (!file_exists($upload_dir)) {
    if (mkdir($upload_dir, 0777, true)) {
        echo "✅ Created directory: $upload_dir\n";
    } else {
        echo "❌ Failed to create directory: $upload_dir\n";
    }
}

// Get current permissions
$perms = substr(sprintf('%o', fileperms($upload_dir)), -4);
echo "Current permissions for $upload_dir: $perms\n";

// Try to make directory writable for everyone
if (@chmod($upload_dir, 0777)) {
    echo "✅ Successfully set permissions to 777\n";
} else {
    echo "❌ Failed to set permissions. Try running these commands in terminal:\n\n";
    $project_path = dirname(__FILE__);
    echo "cd " . $project_path . "\n";
    echo "sudo chmod -R 777 uploads\n";
    echo "sudo chown daemon:daemon uploads\n\n";
}

// Check if directory is writable
if (is_writable($upload_dir)) {
    echo "✅ Directory is writable\n";
} else {
    echo "❌ Directory is not writable\n";
}

// Get directory owner/group
echo "\nDirectory details:";
echo "\nFull path: " . realpath($upload_dir);
echo "\nOwner: " . fileowner($upload_dir);
echo "\nGroup: " . filegroup($upload_dir);

// Show suggested terminal commands
echo "\n\nIf you're still having issues, open Terminal and run these commands:\n\n";
echo "cd " . dirname(__FILE__) . "\n";
echo "sudo rm -rf uploads\n";
echo "sudo mkdir uploads\n";
echo "sudo chmod 777 uploads\n";
echo "sudo chown daemon:daemon uploads\n";

echo "</pre>";
?> 