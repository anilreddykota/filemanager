<?php
// list-files.php

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    session_start();
    $authToken = isset($_SERVER["HTTP_AUTHORIZATION"]) ? str_replace("Bearer ", "", $_SERVER["HTTP_AUTHORIZATION"]) : "";
list($_SESSION["user_id"], $_SESSION["plan"]) = explode(":", $authToken);   

    // Check if the user is authenticated
    if (!isset($_SESSION["user_id"]) || $_SESSION["plan"] !== "pro") {
        echo json_encode(["error" => "User not authenticated or not on developer plan"]);
        exit();
    }

    $user_folder = "../uploads/user_" . $_SESSION["user_id"] . "/";

    function listFilesRecursively($directory) {
        $result = [];
        $items = scandir($directory);

        foreach ($items as $item) {
            if ($item === "." || $item === "..") {
                continue;
            }

            $path = $directory . $item;

            if (is_dir($path)) {
                $result[$item] = listFilesRecursively($path . "/");
            } else {
                $result[] = $item;
            }
        }

        return $result;
    }

    if (is_dir($user_folder)) {
        $files = listFilesRecursively($user_folder);
        echo json_encode(["files" => $files]);
    } else {
        echo json_encode(["error" => "User folder not found"]);
    }
}
?>