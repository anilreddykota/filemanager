<?php
session_start();
if (!isset($_SESSION["user_id"])) {
    echo json_encode(["error" => "User not logged in"]);
    exit();
}

require_once 'config/db_connect.php';
global $conn;

$user_id = $_SESSION["user_id"];

if (isset($_GET['path'])) {
    $path = $_GET['path'];
    $path = urldecode($path);
    $path = str_replace("//", "/", $path);

    // Check if the path with the user_id already exists in the share_stats table
    $stmt = $conn->prepare("SELECT shid FROM share_stats WHERE user_id=? AND path=?");
    $stmt->bind_param("is", $user_id, $path);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($shid);

    if ($stmt->fetch()) {
        // If it exists, return the existing shid as a JSON response
        $stmt->close();
        echo json_encode(["shid" => $shid]);
        exit();
    }
    $stmt->close();

    // If it doesn't exist, generate a unique 12-digit shid
    $shid = str_pad(mt_rand(1, 999999999999), 12, '0', STR_PAD_LEFT);

    // Insert the new record into the share_stats table
    $stmt = $conn->prepare("INSERT INTO share_stats (user_id, path, shid) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $user_id, $path, $shid);
    $stmt->execute();
    $stmt->close();

    // Return the new shid as a JSON response
    echo json_encode(["shid" => $shid]);
    exit();
} else {
    echo json_encode(["error" => "Path not provided"]);
    exit();
}
?>
