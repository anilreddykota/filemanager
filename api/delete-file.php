<?php
// delete-file.php
echo $_SERVER["REQUEST_METHOD"] . "<br>";
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["file"])) {
    session_start();
    $authToken = isset($_SERVER["HTTP_AUTHORIZATION"]) ? str_replace("Bearer ", "", $_SERVER["HTTP_AUTHORIZATION"]) : "";
    list($_SESSION["user_id"], $_SESSION["plan"]) = explode(":", $authToken);

    // Check if the user is authenticated
    if (!isset($_SESSION["user_id"]) || $_SESSION["plan"] !== "pro") {
        echo json_encode(["error" => "User not authenticated or not on developer plan"]);
        exit();
    }


    $user_folder = "../uploads/user_" . $_SESSION["user_id"] . "/";
    $item_to_delete = $_POST["file"];
    echo $item_to_delete;

    // Sanitize the file path to prevent directory traversal attacks
    $item_to_delete = str_replace(['..', './', '\\'], '', $item_to_delete);
    $item_path = realpath($user_folder . $item_to_delete);
    // Ensure the item is within the user's folder
    if (strpos($item_path, realpath($user_folder)) !== 0) {
        echo json_encode(["error" => "Invalid file or folder path."]);
        exit();
    }

    if (file_exists($item_path)) {
        if (is_dir($item_path)) {
            // Delete folder and its contents
            if (deleteFolder($item_path)) {
                echo json_encode(["success" => "Folder deleted successfully!"]);
            } else {
                echo json_encode(["error" => "Failed to delete folder."]);
            }
        } else {
            // Delete file
            if (unlink($item_path)) {
                echo json_encode(["success" => "File deleted successfully!"]);
            } else {
                echo json_encode(["error" => "Failed to delete file."]);
            }
        }
    } else {
        echo json_encode(["error" => "File or folder not found."]);
    }
} else {
    echo json_encode(["error" => "Invalid request method."]);
}

// Recursive function to delete a folder and its contents
function deleteFolder($folder)
{
    $files = array_diff(scandir($folder), ['.', '..']);
    foreach ($files as $file) {
        $file_path = $folder . DIRECTORY_SEPARATOR . $file;
        if (is_dir($file_path)) {
            deleteFolder($file_path);
        } else {
            unlink($file_path);
        }
    }
    return rmdir($folder);
}
