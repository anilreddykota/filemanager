<?php

// rename.php
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["old_name"]) && isset($_POST["new_name"])) {
    session_start();
    $authToken = isset($_SERVER["HTTP_AUTHORIZATION"]) ? str_replace("Bearer ", "", $_SERVER["HTTP_AUTHORIZATION"]) : "";
list($_SESSION["user_id"], $_SESSION["plan"]) = explode(":", $authToken);   

    // Check if the user is authenticated
    if (!isset($_SESSION["user_id"]) || $_SESSION["plan"] !== "pro") {
        echo json_encode(["error" => "User not authenticated or not on developer plan"]);
        exit();
    }


    $user_folder = "../uploads/user_" . $_SESSION["user_id"] . "/";
    $old_name = $_POST["old_name"];
    $new_name = $_POST["new_name"];
    $old_path = $user_folder . $old_name;
    $new_path = $user_folder . $new_name;

    if (file_exists($old_path)) {
        if (rename($old_path, $new_path)) {
            echo json_encode(["success" => "Renamed successfully!"]);
        } else {
            echo json_encode(["error" => "Failed to rename."]);
        }
    } else {
        echo json_encode(["error" => "File or folder not found."]);
    }
}


?>