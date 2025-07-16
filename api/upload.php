<?php

// upload.php
require_once 'utilites.php';
require_once '../config/db_connect.php';
require_once '../coding/encode.php';  // Include the encoding functions
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["file"])) {
    session_start();
    global $conn, $baseUrl;

    $authToken = getAuthorizationHeader();
    list($id, $_SESSION["plan"]) = getUserIdAndPlanFromAuthToken($authToken);
    
    $user_id = decodeString($id);


    if(!validateUserAccess($conn, $user_id, )) {
        echo json_encode(["error" => "Invalid user access"]);
        exit();
    }
    $_SESSION["user_id"] = $user_id; // Set the user_id in the session


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
    // Generate a random string to append to the filename to avoid collisions
    $filename = pathinfo($file["name"], PATHINFO_FILENAME);
    $extension = pathinfo($file["name"], PATHINFO_EXTENSION);
    $randomStr = substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 6);
    $filename = $filename . '_' . $randomStr . ($extension ? '.' . $extension : '');
    $targetFilePath = $targetFolder . $filename;
    $actual_path = $targetFolder . $filename;
    $actual_path = substr($actual_path, 3);

    // Move the uploaded file to the target directory
    if (move_uploaded_file($file["tmp_name"], $targetFilePath)) {
        // get file share id
        $shareInfo = getShareUrlForPath($conn, $_SESSION["user_id"], $actual_path, $baseUrl);
        echo json_encode(["success" => "File uploaded successfully!",  "file_id" => $shareInfo["shid"], "file_url" => $shareInfo["url"]]);
    } else {
        echo json_encode(["error" => "Failed to upload file."]);
    }
} else {
    echo json_encode(["error" => "Invalid request."]);
}
