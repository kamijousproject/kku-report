<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Define the upload directory
$uploadDir = 'C:/inetpub/wwwroot/EPM/kku-report/server/uploads/';

// Ensure the upload directory exists
if (!is_dir($uploadDir)) {
    if (!mkdir($uploadDir, 0777, true)) {
        die("Failed to create upload directory. Check permissions.");
    }
}

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if a file was uploaded
    if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
        // Get the temporary file path
        $tmpFilePath = $_FILES['file']['tmp_name'];

        // Get the original file name
        $fileName = basename($_FILES['file']['name']);

        // Define the destination path
        $destinationPath = $uploadDir . $fileName;

        // Attempt to move the uploaded file
        if (move_uploaded_file($tmpFilePath, $destinationPath)) {
            echo "File uploaded successfully to: " . $destinationPath;
        } else {
            echo "Failed to move uploaded file. Check permissions for the destination directory.";
        }
    } else {
        echo "No file uploaded or an error occurred during upload.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>File Upload Test</title>
</head>
<body>
    <h1>Upload a File</h1>
    <form method="post" enctype="multipart/form-data">
        <label for="file">Choose a file:</label>
        <input type="file" name="file" id="file" required>
        <br><br>
        <input type="submit" value="Upload">
    </form>
</body>
</html>