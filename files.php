<?php

session_start();

require_once 'config/db_connect.php';
global $conn;

function get_mime_type($file_path) {
    $mime = mime_content_type($file_path);
    $ext = strtolower(pathinfo($file_path, PATHINFO_EXTENSION));

    // Fallbacks
    $fallback_mimes = [
        'css'  => 'text/css',
        'js'   => 'application/javascript',
        'json' => 'application/json',
        'svg'  => 'image/svg+xml',
        'woff' => 'font/woff',
        'woff2'=> 'font/woff2',
    ];

    if (isset($fallback_mimes[$ext]) && strpos($mime, 'text/plain') === 0) {
        return $fallback_mimes[$ext];
    }

    return $mime;
}

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


        // if file_path is folder download as zip

        if (is_dir($file_path)) {
            $zip_file = tempnam(sys_get_temp_dir(), 'zip');
            $zip = new ZipArchive();

            if ($zip->open($zip_file, ZipArchive::CREATE) === TRUE) {
                $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($file_path), RecursiveIteratorIterator::LEAVES_ONLY);

                foreach ($files as $name => $file) {
                    if (!$file->isDir()) {
                        $file_path_relative = substr($file->getRealPath(), strlen($file_path) + 1);
                        $zip->addFile($file->getRealPath(), $file_path_relative);
                    }
                }

                $zip->close();

                header("Content-Type: application/zip");
                header("Content-Disposition: attachment; filename=\"" . basename($file_path) . ".zip\"");
                header("Content-Length: " . filesize($zip_file));
                readfile($zip_file);

                unlink($zip_file);
                exit;
            } else {
                echo "Failed to create zip file.";
                exit;
            }
        }

     
        if (file_exists($file_path)) {
            $mime_type = get_mime_type($file_path);
            $mime_list = ['image/jpeg', 'image/png', 'image/gif', 'video/mp4', 'application/pdf', 'text/css', 'application/javascript', 'text/plain', 'text/html',  'image/svg+xml', 'application/json', 'font/woff', 'font/woff2', 'application/xml'];
         
            if (in_array($mime_type, $mime_list)) {
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