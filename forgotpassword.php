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
    if (isset($_POST["forgot_password"])) {
        $username = $_POST["username"];

        $stmt = $conn->prepare("SELECT email, username FROM users WHERE username=? OR email=? OR college_email = ?");
        $stmt->bind_param("sss", $username, $username, $username);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($email, $username);
        $stmt->fetch();

        if ($stmt->num_rows > 0) {
            
            $expiry = time() + (30 * 60); // 30 minutes from now
            $expiryString = date("Y-m-d H:i:s", $expiry);

            $encodeExpiry = encodeString($expiryString);
            $verification_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://{$_SERVER['HTTP_HOST']}/resetpassword?email=" . urlencode(encodeString($email)) . "&expiry=". urlencode($encodeExpiry);

            $emailTemplate = forgotPasswordEmailTemplate($username, $verification_link);

            if (sendResetPasswordMail($emailTemplate, $email)) {
                echo "<script>
                    alert('Check email for password reset link: " . htmlspecialchars($email, ENT_QUOTES) . "');
                    setTimeout(function() {
                        window.location.href = 'login';
                    }, 1000);
                </script>";
                exit();
            }
            $success_message = "A password reset link has been sent to your email: $email.";
        } else {
            $error_message = "No account found with the provided username or email.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>A- Pannel - File Manager - Forgot Password</title>
    <link rel="icon" href="./assets/favicon.png" type="image/png">
    <meta name="description" content="A-Pannel - File Manager - Forgot Password">
    <meta name="keywords" content="A-Pannel - File Manager - Forgot Password">
    <meta name="author" content="A-Pannel - File Manager - Forgot Password">
    <meta name="robots" content="index, follow">
    <meta name="revisit-after" content="1 month">
    <meta name="language" content="EN">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <style>
        body {
            background-color: #f0f2f5;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .login-container {
            max-width: 400px;
            width: 100%;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            background-color: #fff;
            animation: fadeIn 0.8s ease-in-out;
        }

        .logo {
            text-align: center;
            margin-bottom: 25px;
            color: #4285F4;
            font-size: 28px;
            font-weight: bold;
            animation: bounceIn 1s;
        }

        .logo i {
            animation: pulse 2s infinite;
        }

        .form-title {
            text-align: center;
            margin-bottom: 25px;
            color: #333;
            animation: fadeIn 1s ease-in-out;
        }

        .btn-login {
            background-color: #4285F4;
            color: white;
            font-weight: 500;
            padding: 10px;
            transition: all 0.3s;
            border: none;
        }

        .btn-login:hover {
            background-color: #3367d6;
            transform: translateY(-2px);
        }

        .signup-link {
            text-align: center;
            margin-top: 20px;
            animation: fadeInUp 1.2s;
        }

        .signup-link a {
            color: #4285F4;
            text-decoration: none;
            font-weight: 500;
        }

        .signup-link a:hover {
            text-decoration: underline;
        }

        .input-group-text {
            background-color: transparent;
        }

        .form-control:focus {
            box-shadow: 0 0 0 0.25rem rgba(66, 133, 244, 0.25);
            border-color: #4285F4;
            animation: pulse 0.5s;
        }

        .alert {
            animation: shakeX 0.8s;
        }
    </style>
</head>

<body>
    <div class="login-container">
        <div class="logo">
            <a href="index" class="text-decoration-none text-dark">
                <i class="fas fa-folder-open me-2"></i>
                A - Pannel
            </a>
        </div>
        <h4 class="form-title">Forgot Password</h4>

        <?php if (!empty($error_message)): ?>
            <div class="alert alert-danger" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i> <?php echo $error_message; ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($success_message)): ?>
            <div class="alert alert-success" role="alert">
                <i class="fas fa-check-circle me-2"></i> <?php echo $success_message; ?>
            </div>
        <?php endif; ?>

        <form method="POST" class="mb-3">
            <div class="mb-4 input-animated" style="animation-delay: 0.2s">
                <label for="username" class="form-label">Username or Email</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-user"></i></span>
                    <input type="text" class="form-control" id="username" name="username" placeholder="Enter your username or email" required>
                </div>
            </div>
            <div class="d-grid input-animated" style="animation-delay: 0.4s">
                <button type="submit" class="btn btn-login py-2" name="forgot_password">
                    <i class="fas fa-envelope me-2"></i> Send Reset Link
                </button>
            </div>
        </form>
        <div class="signup-link">
            Remembered your password? <a href="login">Login</a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
