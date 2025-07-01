<?php
session_start();
require_once 'config/db_connect.php';
require_once 'coding/encode.php';
require_once 'mail/mail-templates.php';
require_once 'mail/mailer.php';
global $conn;

$error_message = '';
$success_message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["email"])) {
        $email = $_POST["email"];
        $email = decodeString($email);
        $stmt = $conn->prepare("SELECT id, email, username, email_status FROM users WHERE email=?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($id, $email, $username, $email_status);
        $stmt->fetch();

        if ($stmt->num_rows > 0) {
            if ($email_status == 1) {
                $success_message = "Email is already verified.";
            } else {
                $expiry = time() + 3600;

                $stmt->bind_param("is", $expiry, $email);
                $stmt->execute();

                $verification_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://{$_SERVER['HTTP_HOST']}/verify?email=" . urlencode(encodeString($email)) . "&expiry=" . urlencode($expiry);

                // Send verification email
                $mail_body = getWelcomeEmailTemplate($username,  $verification_link);
                sendWelcomeMail($mail_body, $email);

                $success_message = "Verification email sent.";
                echo "<script>
                    alert('Verification email sent to: " . htmlspecialchars($email, ENT_QUOTES) . "');
                    setTimeout(function() {
                        window.location.href = 'login';
                    }, 1000);
                </script>";
                header("Location: " . $_SERVER['HTTP_REFERER']);
                exit();
            }
        } else {
            $error_message = "Email not found.";
        }
    }
}
