<?php

session_start();

require_once 'config/db_connect.php';
global $conn;

if (isset($_GET['shid'])) {
    $share_id = $_GET['shid'];

    // Fetch the path and current oc value from the database
    $query = "SELECT path, oc FROM share_stats WHERE shid = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $share_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $path = $row['path'];
        $oc = $row['oc'];

        // Increment the oc value
        $new_oc = $oc + 1;

        // Update the oc value in the database
        $update_query = "UPDATE share_stats SET oc = ? WHERE shid = ?";
        $update_stmt = $conn->prepare($update_query);
        $update_stmt->bind_param("is", $new_oc, $share_id);
        $update_stmt->execute();

        // Determine the file type and handle accordingly
        $file_path =  __DIR__ . "/" . $path;

        echo $file_path;

        exit();
        if (file_exists($file_path)) {
            $mime_type = mime_content_type($file_path);

            // Check if the file is viewable in the browser
            if (in_array($mime_type, ['image/jpeg', 'image/png', 'image/gif', 'video/mp4', 'application/pdf'])) {
                header("Content-Type: $mime_type");
                readfile($file_path);
            } else {
                // Force download for other file types
                header("Content-Type: application/octet-stream");
                header("Content-Disposition: attachment; filename=\"" . basename($file_path) . "\"");
                readfile($file_path);
            }
        } else {
            echo "File not found.";
        }
    } else {
        echo "Share ID not found.";
    }

    $stmt->close();
    $conn->close();
} else {
    echo "No share ID provided.";
}

?>