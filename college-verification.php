<?php
session_start();
if (!isset($_SESSION["user_id"])) {
    header("Location: login");
    exit();
}
require_once 'config/db_connect.php';
require_once 'coding/encode.php';
require_once 'mail/mail-templates.php';
require_once 'mail/mailer.php';
global $conn;

$error_message = '';
$success_message = '';
$user_id = $_SESSION["user_id"];
// Check if the request method is POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["email"])) {
        // Get the email from the POST request
        $email = $_POST["email"];
        $stmt = $conn->prepare("SELECT id, username, college_email_status FROM users WHERE college_email=?");
        $stmt->bind_param("si", $email, $user_id);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($id, $username, $college_email_status);
        $stmt->fetch();

        if ($stmt->num_rows > 0) {
            if ($id != $user_id) {
                $error_message = "You cannot verify another user's college email.";
            } elseif (empty($email)) {
                $error_message = "Please enter a valid college email.";
            } elseif ($college_email_status == 1) {
                $success_message = "Email is already verified.";
            } else {
                $expiry = time() + 3600;

                $stmt->bind_param("is", $expiry, $email);
                $stmt->execute();

                $verification_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://{$_SERVER['HTTP_HOST']}/verify?email=" . urlencode(encodeString($email)) . "&expiry=" . urlencode($expiry);

                // Send verification email
                $mail_body = collegeVerificationEmailTemplate($username,  $verification_link);
                if (sendCollegeVerificationMail($mail_body, $email)) {
                    $success_message = "Verification email sent.";
                }

                echo "<script>
                    alert('Verification email sent to: " . htmlspecialchars($email, ENT_QUOTES) . "');
                </script>";
                sleep(1); // Sleep for 1 second to ensure the alert is shown before redirecting
                // header("Location: " . $_SERVER['HTTP_REFERER']);
                // exit();
            }
        } else {
            // If no user found with the provided college email add that 
            // email to the database or handle it as needed

            $stmt = $conn->prepare("UPDATE users SET college_email=? WHERE id=?");
            $stmt->bind_param("si", $email, $user_id);
            if ($stmt->execute()) {
                $success_message = "Email added successfully.";
                $verification_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://{$_SERVER['HTTP_HOST']}/verify?email=" . urlencode(encodeString($email)) . "&expiry=" . urlencode($expiry);

                // Send verification email
                $mail_body = collegeVerificationEmailTemplate($username,  $verification_link);
                if (sendCollegeVerificationMail($mail_body, $email)) {
                    $success_message = "Verification email sent.";
                }
                echo "<script>
                    alert('Email added successfully: " . htmlspecialchars($email, ENT_QUOTES) . "');
                
                </script>";
                sleep(1); // Sleep for 1 second to ensure the alert is shown before redirecting
                // header("Location: " . $_SERVER['HTTP_REFERER']);
            } else {
                $error_message = "Error adding email.";
            }
        }
    }
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="./assets/favicon.png" type="image/png">
    <title>College Email Verification</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>

<body>
    <div class="container">
        <h1>College Email Verification</h1>
        <?php if ($error_message): ?>
            <div class="alert alert-danger"><?php echo $error_message; ?></div>
        <?php endif; ?>
        <?php if ($success_message): ?>
            <div class="alert alert-success"><?php echo $success_message; ?></div>
        <?php endif; ?>
        <a href="<?php echo isset($_SERVER['HTTP_REFERER']) ? htmlspecialchars($_SERVER['HTTP_REFERER'], ENT_QUOTES) : '/'; ?>" class="btn btn-secondary mt-3">Go Back</a>
    </div>
</body>

</html>