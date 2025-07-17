<?php
$baseUrl = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https://{$_SERVER['HTTP_HOST']}" : "http://{$_SERVER['HTTP_HOST']}/filemanager";
function getAuthorizationHeader() {
    if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
        return trim($_SERVER["HTTP_AUTHORIZATION"]);
    } elseif (isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])) {
        return trim($_SERVER["REDIRECT_HTTP_AUTHORIZATION"]);
    } elseif (function_exists('apache_request_headers')) {
        $requestHeaders = apache_request_headers();
        if (isset($requestHeaders['Authorization'])) {
            return trim($requestHeaders['Authorization']);
        }
    }
    return null;
}
function getUserIdAndPlanFromAuthToken($authToken) {
    if (empty($authToken)) {
        return [null, null];
    }
    $parts = explode(":", $authToken);
    if (count($parts) !== 2) {
        return [null, null];
    }
    // Assuming the auth token is in the format "Bearer user_id:plan"
    if (strpos($parts[0], 'Bearer') === 0) {
        $parts[0] = substr($parts[0], 6); // Remove 'Bearer ' prefix
    }
    return [$parts[0], $parts[1]];
}
function isUserAuthenticated($userId, $plan) {
    return isset($userId) && $plan === "pro";
}
function getUserFolder($userId) {
    return "../uploads/user_" . $userId . "/";
}
function isValidFileType($mimeType) {
    $validMimeTypes = ['image/jpeg', 'image/png', 'image/gif', 'video/mp4', 'application/pdf'];
    return in_array($mimeType, $validMimeTypes);
}
function sendFile($filePath) {
    if (file_exists($filePath) && is_file($filePath)) {
        $mimeType = mime_content_type($filePath);
        if (isValidFileType($mimeType)) {
            header("Content-Type: $mimeType");
            readfile($filePath);
            exit();
        } else {
            echo json_encode(["error" => "Invalid file type."]);
        }
    } else {
        echo json_encode(["error" => "File not found."]);
    }
}
function deleteFileOrFolder($itemPath) {
    if (file_exists($itemPath)) {
        if (is_dir($itemPath)) {
            return deleteFolder($itemPath);
        } else {
            return unlink($itemPath);
        }
    }
    return false;
}
function deleteFolder($folder) {
    $files = array_diff(scandir($folder), ['.', '..']);
    foreach ($files as $file) {
        $filePath = $folder . DIRECTORY_SEPARATOR . $file;
        if (is_dir($filePath)) {
            deleteFolder($filePath);
        } else {
            unlink($filePath);
        }
    }
    return rmdir($folder);
}
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
function sanitizeFilePath($filePath) {
    return str_replace(['..', './', '\\'], '', $filePath);
}
function isValidFilePath($itemPath, $userFolder) {
    $realItemPath = realpath($itemPath);
    return strpos($realItemPath, realpath($userFolder)) === 0;
}
function respondWithJson($data) {
    header('Content-Type: application/json');
    echo json_encode($data);
    exit();
}
function handleFileDownload($fileToDownload, $userFolder) {
    $filePath = $userFolder . $fileToDownload;
    if (file_exists($filePath)) {
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($filePath) . '"');
        header('Content-Length: ' . filesize($filePath));
        readfile($filePath);
        exit();
    } else {
        respondWithJson(["error" => "File not found."]);
    }
}
function handleFileUpload($userFolder, $file) {
    if (isset($file) && $file['error'] === UPLOAD_ERR_OK) {
        $targetFilePath = $userFolder . basename($file['name']);
        if (move_uploaded_file($file['tmp_name'], $targetFilePath)) {
            respondWithJson(["success" => "File uploaded successfully!"]);
        } else {
            respondWithJson(["error" => "Failed to upload file."]);
        }
    } else {
        respondWithJson(["error" => "No file uploaded or upload error."]);
    }
}
function handleFileRename($oldName, $newName, $userFolder) {
    $oldPath = $userFolder . sanitizeFilePath($oldName);
    $newPath = $userFolder . sanitizeFilePath($newName);
    
    if (file_exists($oldPath)) {
        if (rename($oldPath, $newPath)) {
            respondWithJson(["success" => "File renamed successfully!"]);
        } else {
            respondWithJson(["error" => "Failed to rename file."]);
        }
    } else {
        respondWithJson(["error" => "File not found."]);
    }
}
function handleFileDeletion($itemToDelete, $userFolder) {
    $itemPath = $userFolder . sanitizeFilePath($itemToDelete);
    
    if (isValidFilePath($itemPath, $userFolder)) {
        if (deleteFileOrFolder($itemPath)) {
            respondWithJson(["success" => "Item deleted successfully!"]);
        } else {
            respondWithJson(["error" => "Failed to delete item."]);
        }
    } else {
        respondWithJson(["error" => "Invalid file or folder path."]);
    }
}
function handleFileListing($userFolder) {
    if (is_dir($userFolder)) {
        $files = listFilesRecursively($userFolder);
        respondWithJson(["files" => $files]);
    } else {
        respondWithJson(["error" => "User folder does not exist."]);
    }
}

function getShareUrlForPath($conn, $user_id, $path, $baseUrl) {
    $path = urldecode($path);
    $path = str_replace("//", "/", $path);

    // Check if the path with the user_id already exists in the share_stats table
    $stmt = $conn->prepare("SELECT shid FROM share_stats WHERE user_id=? AND path=?");
    $stmt->bind_param("is", $user_id, $path);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($shid);

    if ($stmt->fetch()) {
        $stmt->close();
    } else {
        $stmt->close();
        // Generate a unique 12-digit shid
        $shid = str_pad(mt_rand(1, 999999999999), 12, '0', STR_PAD_LEFT);
        $stmt = $conn->prepare("INSERT INTO share_stats (user_id, path, shid) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $user_id, $path, $shid);
        $stmt->execute();
        $stmt->close();
    }

    // Build the share URL
    $shareUrl = rtrim($baseUrl, "/") . "/files/" . $shid;
    return [
        "shid" => $shid,
        "url" => $shareUrl
    ];
}

function validateUserAccess($conn, $user_id) {
    $stmt = $conn->prepare("SELECT plan FROM users WHERE id=?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->store_result();
    
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($plan);
        $stmt->fetch();
        $stmt->close();
        return isset($plan) && $plan === "pro";
    } else {
        $stmt->close();
        return false;
    }
}

?>