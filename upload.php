<?php
session_start();
if (!isset($_SESSION["user_id"])) {
    die("Unauthorized access!");
}

$user_folder = "uploads/user_" . $_SESSION["user_id"] . "/";
if (!is_dir($user_folder)) {
    mkdir($user_folder, 0777, true);
}

if ($_FILES["file"]["name"]) {
    $target_file = $user_folder . basename($_FILES["file"]["name"]);
    if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_file)) {
        echo "File uploaded successfully!";
    } else {
        echo "Upload failed!";
    }
}
?>
<a href="dashboard.php">Back to Dashboard</a>
