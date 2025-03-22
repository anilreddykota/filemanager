<?php

// upload.php
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["file"])) {
    session_start();

    echo "Session ID: " . session_id(); // Add this to check the session ID
echo "<br />";

if (!isset($_SESSION["user_id"]) || $_SESSION["plan"] !== "pro") {
    echo json_encode(["error" => "User not authenticated or not on developer plan"]);
    exit();
}


    // Get the folder from the POST request
    $folder = isset($_POST["folder"]) ? $_POST["folder"] : "";

    // Sanitize the folder name
    $folder = rtrim($folder, '/') . '/';

    // Create the user's upload directory if it doesn't exist
    $user_folder = "../uploads/user_" . $_SESSION["user_id"] . "/";
    if (!is_dir($user_folder)) {
        mkdir($user_folder, 0777, true);
    }

    // Ensure the subfolder exists
    $targetFolder = $user_folder . $folder;
    if (!is_dir($targetFolder)) {
        mkdir($targetFolder, 0777, true);
    }

    // Handle the uploaded file
    $file = $_FILES["file"];
    $filename = basename($file["name"]);
    $targetFilePath = $targetFolder . $filename;
    $actual_path = $folder . $filename;

    // Move the uploaded file to the target directory
    if (move_uploaded_file($file["tmp_name"], $targetFilePath)) {
        echo json_encode(["success" => "File uploaded successfully!", "file" => $actual_path]);
    } else {
        echo json_encode(["error" => "Failed to upload file."]);
    }
} else {
    echo json_encode(["error" => "Invalid request."]);
}

?>