<?php
require_once "config/db_connect.php";
require_once "coding/encode.php";

$message = "";
$messageType = "info";

// Check if 'email' parameter exists
if (!isset($_GET['email'])) {
    http_response_code(400);
    $message = "Missing email parameter.";
    $messageType = "danger";
} else {
    $encodedEmail = $_GET['email'];
    $decodedEmail = decodeString($encodedEmail);

    if ($decodedEmail === false || !filter_var($decodedEmail, FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);
        $message = "Invalid email parameter.";
        $messageType = "danger";
    } else {
        // Fetch user by email or college_email
        $stmt = $conn->prepare("SELECT id, email, college_email, email_status, college_email_status FROM users WHERE email = ? OR college_email = ?");
        $stmt->bind_param("ss", $decodedEmail, $decodedEmail);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        if ($user) {
            if ($user['email_status'] && $user['college_email_status']) {
                $message = "Link expired: both emails are already verified.";
                $messageType = "warning";
            } elseif ($user['email'] === $user['college_email']) {
                $update = $conn->prepare("UPDATE users SET college_email_status = 1, email_status = 1, plan = 'student' WHERE id = ?");
                $update->bind_param("i", $user['id']);
                $update->execute();
                $message = "Email and college email match. Status updated and plan upgraded to student.";
                $messageType = "success";
            } elseif ($user['email'] === $decodedEmail) {
                $update = $conn->prepare("UPDATE users SET email_status = 1 WHERE id = ?");
                $update->bind_param("i", $user['id']);
                $update->execute();
                $message = "Normal email verified: " . htmlspecialchars($decodedEmail);
                $messageType = "success";
            } elseif ($user['college_email'] === $decodedEmail) {
                if ($user['email_status'] == 1) {
                    $update = $conn->prepare("UPDATE users SET college_email_status = 1, plan = 'student' WHERE id = ?");
                    $update->bind_param("i", $user['id']);
                    $update->execute();
                    $message = "College email verified: " . htmlspecialchars($decodedEmail);
                    $messageType = "success";
                } else {
                    $message = "College email verified, but basic email is not verified.";
                    $messageType = "warning";
                }
            } else {
                $message = "Email verified: " . htmlspecialchars($decodedEmail);
                $messageType = "success";
            }
        } else {
            $message = "User not found.";
            $messageType = "danger";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
       <title>A- Pannel - File Manager - Verify Email </title>
    <link rel="icon" href="./assets/favicon.png" type="image/png">
    <!-- add seo for login  -->
    <meta name="description" content="A-Pannel - File Manager - Verify Email">
    <meta name="keywords" content="A-Pannel - File Manager - Verify Email">
    <meta name="author" content="A-Pannel - File Manager - Verify Email">
    <meta name="robots" content="index, follow">
    <meta name="revisit-after" content="1 month">
    <meta name="language" content="EN">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap 5 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #f8fafc;
            min-height: 100vh;
        }
        .verify-container {
            max-width: 420px;
            margin: 60px auto;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 24px rgba(0,0,0,0.08);
            padding: 2.5rem 2rem;
        }
        .verify-title {
            font-weight: 700;
            margin-bottom: 1.5rem;
            color: #2d3748;
        }
    </style>
</head>
<body>
    <div class="verify-container">
        <h2 class="verify-title text-center">Email Verification</h2>
        <div class="alert alert-<?php echo $messageType; ?> text-center" role="alert">
            <?php echo $message; ?>
        </div>
        <div class="text-center mt-4">
            <a href="/" class="btn btn-outline-primary">Go to Home</a>
        </div>
    </div>
</body>
</html>
