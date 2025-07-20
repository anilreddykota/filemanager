<?php
require_once 'vendor/autoload.php';
require_once 'config/env.php';  // Load environment variables
require_once __DIR__ . '/config/db_connect.php';
require_once 'coding/encode.php';  // Load encoding functions

session_start();

$client = new Google_Client();
$client->setClientId($_ENV['O_AUTH_CLIENT_ID']);
$client->setClientSecret($_ENV['O_AUTH_CLIENT_SECRET']);
$client->setRedirectUri($_ENV['O_AUTH_REDIRECT_URI'] ?? 'https://apannel.srkramc.in/redirect');
$client->addScope("email");
$client->addScope("profile");

if (isset($_GET['code'])) {
    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);

    if (isset($token['error'])) {
        echo "<h3>Authentication failed</h3>";
        echo "<p>Error: " . htmlspecialchars($token['error']) . "</p>";
        echo "<p>Description: " . htmlspecialchars($token['error_description'] ?? 'No description') . "</p>";
        exit;
    }

    $client->setAccessToken($token);

    $oauth = new Google_Service_Oauth2($client);
    $userInfo = $oauth->userinfo->get();



    // Check if user exists by Google email
    $stmt = $conn->prepare("SELECT id, plan, college_email_status, email_status, email, college_email FROM users WHERE email=? OR college_email=?");
    $stmt->bind_param("ss", $userInfo->email, $userInfo->email);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($id, $plan, $college_email_status, $email_status, $email, $college_email);
    $stmt->fetch();

    if ($stmt->num_rows > 0) {
        // Check verification status
        if ($userInfo->email === $college_email) {
            if ($college_email_status == 1) {
                $_SESSION["user_id"] = $id;
                $_SESSION["username"] = strstr($userInfo->email, '@', true);
                $_SESSION["plan"] = $plan;

                header("Location: dashboard.php");
                exit();
            } else {
                $error_message = "Please verify your college email before logging in.";
                header("Location: login.php?error=" . urlencode($error_message));
                exit();
            }
        } elseif ($userInfo->email === $email) {
            if ($email_status == 1) {
                $_SESSION["user_id"] = $id;
                $_SESSION["username"] = strstr($userInfo->email, '@', true);
                $_SESSION["plan"] = $plan;

                header("Location: dashboard.php");
                exit();
            } else {
                //    set email status as this is authorized login
                $stmt = $conn->prepare("UPDATE users SET email_status = 1 WHERE id = ?");
                $stmt->bind_param("i", $id);
                $stmt->execute();
                $stmt->close();
                $_SESSION["user_id"] = $id;
                $_SESSION["username"] = strstr($userInfo->email, '@', true);
                $_SESSION["plan"] = $plan;

                header("Location: dashboard.php");
                exit();
            }
        } else {
            $error_message = "Invalid Google account for login!";
            header("Location: login.php?error=" . urlencode($error_message));
            exit();
        }
    } else {
        $error_message = "No account found for this Google email!";
        header("Location: login.php?error=" . urlencode($error_message));
        exit();
    }
} else {
    // No code parameter in URL
    header('Location: login.php?error=oauth');
    exit;
}
