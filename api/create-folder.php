<?php


if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["folder_name"])) {
    session_start();
    // Check if the user is authenticated
    if (!isset($_SESSION["user_id"]) || $_SESSION["plan"] !== "pro") {
        echo json_encode(["error" => "User not authenticated or not on developer plan"]);
        exit();
    }


    $user_folder = "../uploads/user_" . $_SESSION["user_id"] . "/";
    $folder_name = $_POST["folder_name"];
    $new_folder_path = $user_folder . $folder_name;

    if (!is_dir($new_folder_path)) {
        if (mkdir($new_folder_path, 0777, true)) {
            echo json_encode(["success" => "Folder created successfully!"]);
        } else {
            echo json_encode(["error" => "Failed to create folder."]);
        }
    } else {
        echo json_encode(["error" => "Folder already exists."]);
    }
} else {
    echo json_encode(["error" => "Invalid request."]);
}


?>