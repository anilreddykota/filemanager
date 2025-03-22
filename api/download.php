<?php
// download.php
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET["file"])) {
    session_start();
    // Check if the user is authenticated
    if (!isset($_SESSION["user_id"]) || $_SESSION["plan"] !== "pro") {
        echo json_encode(["error" => "User not authenticated or not on developer plan"]);
        exit();
    }


    $user_folder = "../uploads/user_" . $_SESSION["user_id"] . "/";
    $file_to_download = $_GET["file"];
    $file_path = $user_folder . $file_to_download;

    if (file_exists($file_path)) {
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($file_path) . '"');
        header('Content-Length: ' . filesize($file_path));
        readfile($file_path);
        exit();
    } else {
        echo json_encode(["error" => "File not found."]);
    }
}




?>