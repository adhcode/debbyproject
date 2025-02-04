<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
// Connect to the database
$conn = new mysqli('localhost', 'root', '', 'file_management');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>File Management System</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #4f46e5;
            --primary-hover: #4338ca;
            --success-color: #059669;
            --danger-color: #dc2626;
            --background-color: #f3f4f6;
            --card-background: #ffffff;
            --text-primary: #111827;
            --text-secondary: #6b7280;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', sans-serif;
        }

        body {
            background-color: var(--background-color);
            color: var(--text-primary);
            line-height: 1.5;
        }

        .container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 1rem;
        }

        .header {
            background: var(--card-background);
            padding: 1.5rem 2rem;
            border-radius: 1rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            margin-bottom: 2rem;
        }

        .header h1 {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--text-primary);
        }

        .alert {
            padding: 1rem;
            border-radius: 0.5rem;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .alert i {
            font-size: 1.25rem;
        }

        .alert-success {
            background-color: #ecfdf5;
            color: var(--success-color);
            border: 1px solid #a7f3d0;
        }

        .alert-error {
            background-color: #fef2f2;
            color: var(--danger-color);
            border: 1px solid #fecaca;
        }

        .upload-section {
            background: var(--card-background);
            padding: 2rem;
            border-radius: 1rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            margin-bottom: 2rem;
        }

        .upload-section h2 {
            font-size: 1.25rem;
            font-weight: 600;
            margin-bottom: 1.5rem;
            color: var(--text-primary);
        }

        .upload-form {
            display: flex;
            gap: 1rem;
            align-items: center;
        }

        .file-input-wrapper {
            flex: 1;
            position: relative;
        }

        .file-input {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 2px dashed #e5e7eb;
            border-radius: 0.5rem;
            cursor: pointer;
            transition: all 0.2s;
        }

        .file-input:hover {
            border-color: var(--primary-color);
        }

        .upload-btn {
            background: var(--primary-color);
            color: white;
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 0.5rem;
            font-weight: 500;
            cursor: pointer;
            transition: background 0.2s;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .upload-btn:hover {
            background: var(--primary-hover);
        }

        .files-grid {
            display: grid;
            gap: 1rem;
        }

        .file-card {
            background: var(--card-background);
            padding: 1.5rem;
            border-radius: 1rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .file-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .file-info {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .file-icon {
            width: 2.5rem;
            height: 2.5rem;
            background: #f3f4f6;
            border-radius: 0.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text-secondary);
        }

        .file-details h3 {
            font-size: 1rem;
            font-weight: 500;
            margin-bottom: 0.25rem;
        }

        .file-size {
            color: var(--text-secondary);
            font-size: 0.875rem;
        }

        .file-actions {
            display: flex;
            gap: 0.75rem;
        }

        .btn {
            padding: 0.5rem 1rem;
            border-radius: 0.5rem;
            font-weight: 500;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            transition: opacity 0.2s;
        }

        .btn:hover {
            opacity: 0.9;
        }

        .btn-download {
            background: var(--success-color);
            color: white;
        }

        .btn-delete {
            background: var(--danger-color);
            color: white;
        }

        .no-files {
            text-align: center;
            padding: 3rem;
            color: var(--text-secondary);
            background: var(--card-background);
            border-radius: 1rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .no-files i {
            font-size: 3rem;
            margin-bottom: 1rem;
            color: #d1d5db;
        }

        @media (max-width: 768px) {
            .upload-form {
                flex-direction: column;
            }
            
            .upload-btn {
                width: 100%;
                justify-content: center;
            }

            .file-card {
                flex-direction: column;
                gap: 1rem;
                text-align: center;
            }

            .file-info {
                flex-direction: column;
            }

            .file-actions {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>File Management System</h1>
        </div>

        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                <?php 
                    if ($_GET['success'] == 'deleted') {
                        echo 'File deleted successfully!';
                    } else {
                        echo 'File uploaded successfully!';
                    }
                ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i>
                <?php echo htmlspecialchars($_GET['error']); ?>
            </div>
        <?php endif; ?>

        <div class="upload-section">
            <h2>Upload New File</h2>
            <form class="upload-form" action="file_upload_system.php" method="post" enctype="multipart/form-data">
                <div class="file-input-wrapper">
                    <input type="file" name="file" class="file-input" required>
                </div>
                <button type="submit" class="upload-btn">
                    <i class="fas fa-cloud-upload-alt"></i>
                    Upload File
                </button>
            </form>
        </div>

        <div class="files-grid">
            <?php
            $result = $conn->query("SELECT * FROM files ORDER BY upload_date DESC");

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $file_ext = strtolower(pathinfo($row['file_name'], PATHINFO_EXTENSION));
                    $icon_class = 'fa-file';
                    
                    // Set icon based on file type
                    if (in_array($file_ext, ['jpg', 'jpeg', 'png', 'gif'])) {
                        $icon_class = 'fa-file-image';
                    } elseif (in_array($file_ext, ['pdf'])) {
                        $icon_class = 'fa-file-pdf';
                    } elseif (in_array($file_ext, ['doc', 'docx'])) {
                        $icon_class = 'fa-file-word';
                    } elseif (in_array($file_ext, ['xls', 'xlsx'])) {
                        $icon_class = 'fa-file-excel';
                    }

                    echo '<div class="file-card">';
                    echo '<div class="file-info">';
                    echo '<div class="file-icon">';
                    echo '<i class="fas ' . $icon_class . '"></i>';
                    echo '</div>';
                    echo '<div class="file-details">';
                    echo '<h3>' . htmlspecialchars($row['file_name']) . '</h3>';
                    echo '<span class="file-size">' . number_format($row['file_size'] / 1024, 2) . ' KB</span>';
                    echo '</div>';
                    echo '</div>';
                    echo '<div class="file-actions">';
                    echo '<a href="uploads/' . htmlspecialchars($row['file_name']) . '" class="btn btn-download" download>';
                    echo '<i class="fas fa-download"></i> Download';
                    echo '</a>';
                    echo '<a href="delete.php?id=' . $row['id'] . '" class="btn btn-delete" onclick="return confirm(\'Are you sure you want to delete this file?\')">';
                    echo '<i class="fas fa-trash-alt"></i> Delete';
                    echo '</a>';
                    echo '</div>';
                    echo '</div>';
                }
            } else {
                echo '<div class="no-files">';
                echo '<i class="fas fa-file-upload"></i>';
                echo '<p>No files uploaded yet</p>';
                echo '</div>';
            }

            $conn->close();
            ?>
        </div>
    </div>
</body>
</html>
