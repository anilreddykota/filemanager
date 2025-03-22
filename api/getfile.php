<?php
// getfile.php
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET["file"])) {
    session_start();
    // Check if the user is authenticated
    if (!isset($_SESSION["user_id"]) || $_SESSION["plan"] !== "pro") {
        echo json_encode(["error" => "User not authenticated or not on developer plan"]);
        exit();
    }

    $user_folder = "../uploads/user_" . $_SESSION["user_id"] . "/";
    $file_to_download =$_GET["file"]; 
    $file_path = $user_folder . $file_to_download;
    if (file_exists($file_path) && is_file($file_path)) {
        $mime_type = mime_content_type($file_path);
        if (in_array($mime_type, ['image/jpeg', 'image/png', 'image/gif', 'video/mp4', 'application/pdf'])) {
            header("Content-Type: $mime_type");
            readfile($file_path);
        } else {
            echo json_encode(["error" => "Invalid file type."]);
        }
        exit();
    } else {
        echo json_encode(["error" => "File not found."]);
    }
      
  
} else {
    echo json_encode(["error" => "Invalid request."]);
}
?>
