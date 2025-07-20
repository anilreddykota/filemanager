<?php
session_start();
require_once 'config/db_connect.php';
require_once 'coding/encode.php';

global $conn;

// Initialize error message with error query
$error_message = $_GET['error'] ?? '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["login"])) {
        $username = $_POST["username"];
        $password = $_POST["password"];

        $stmt = $conn->prepare("SELECT id, password, plan, college_email_status, email_status, email, college_email  FROM users WHERE username=? OR email=?");
        $stmt->bind_param("ss", $username, $username);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($id, $hashed_password, $plan, $college_email_status, $email_status, $email, $college_email);
        $stmt->fetch();

        if ($stmt->num_rows > 0 && password_verify($password, $hashed_password)) {
            if (filter_var($username, FILTER_VALIDATE_EMAIL) && $username === $college_email) {
                if ($college_email_status == 1) {
                    $_SESSION["user_id"] = $id;
                    $_SESSION["username"] = $username;
                    $_SESSION["plan"] = $plan;
                    header("Location: dashboard");
                    exit();
                } else {
                    $error_message = "Please verify your college email before logging in.";
                }
            } elseif (filter_var($username, FILTER_VALIDATE_EMAIL) && $username === $email) {
                if ($email_status == 1) {
                    $_SESSION["user_id"] = $id;
                    $_SESSION["username"] = $username;
                    $_SESSION["plan"] = $plan;
                    header("Location: dashboard");
                    exit();
                } else {
                    $error_message = 'Please verify your email before logging in. <a href="send-verification?email=' . urlencode(encodeString($email)) . '" class="btn btn-sm btn-primary ms-2">Resend Verification Email</a>';
                    exit();
                }
            } else {
                $error_message = "Invalid username or password!";
            }
        } else {
            $error_message = "Invalid username or password!";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>A- Pannel - File Manager - Login</title>
    <link rel="icon" href="./assets/favicon.png" type="image/png">
    <!-- SEO meta tags for login page -->
    <meta name="description" content="Login to A-Pannel File Manager to securely access, manage, and organize your files online. Fast, secure, and easy file management for students and professionals.">
    <meta name="keywords" content="file manager, login, A-Pannel, secure file storage, online file management, student file manager, professional file manager, cloud storage, document management">
    <meta name="author" content="A-Pannel Team">
    <meta name="robots" content="noindex, nofollow">
    <meta name="revisit-after" content="7 days">
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

        .gsi-material-button {
            -moz-user-select: none;
            -webkit-user-select: none;
            -ms-user-select: none;
            -webkit-appearance: none;
            background-color: WHITE;
            background-image: none;
            border: 1px solid #747775;
            -webkit-border-radius: 4px;
            border-radius: 4px;
            -webkit-box-sizing: border-box;
            box-sizing: border-box;
            color: #1f1f1f;
            cursor: pointer;
            font-family: 'Roboto', arial, sans-serif;
            font-size: 14px;
            height: 40px;
            letter-spacing: 0.25px;
            outline: none;
            overflow: hidden;
            padding: 0 12px;
            position: relative;
            text-align: center;
            -webkit-transition: background-color .218s, border-color .218s, box-shadow .218s;
            transition: background-color .218s, border-color .218s, box-shadow .218s;
            vertical-align: middle;
            white-space: nowrap;
            width: auto;
            max-width: 400px;
            min-width: min-content;
        }

        .gsi-material-button .gsi-material-button-icon {
            height: 20px;
            margin-right: 12px;
            min-width: 20px;
            width: 20px;
        }

        .gsi-material-button .gsi-material-button-content-wrapper {
            -webkit-align-items: center;
            align-items: center;
            display: flex;
            -webkit-flex-direction: row;
            flex-direction: row;
            -webkit-flex-wrap: nowrap;
            flex-wrap: nowrap;
            height: 100%;
            justify-content: space-between;
            position: relative;
            width: 100%;
        }

        .gsi-material-button .gsi-material-button-contents {
            -webkit-flex-grow: 1;
            flex-grow: 1;
            font-family: 'Roboto', arial, sans-serif;
            font-weight: 500;
            overflow: hidden;
            text-overflow: ellipsis;
            vertical-align: top;
        }

        .gsi-material-button .gsi-material-button-state {
            -webkit-transition: opacity .218s;
            transition: opacity .218s;
            bottom: 0;
            left: 0;
            opacity: 0;
            position: absolute;
            right: 0;
            top: 0;
        }

        .gsi-material-button:disabled {
            cursor: default;
            background-color: #ffffff61;
            border-color: #1f1f1f1f;
        }

        .gsi-material-button:disabled .gsi-material-button-contents {
            opacity: 38%;
        }

        .gsi-material-button:disabled .gsi-material-button-icon {
            opacity: 38%;
        }

        .gsi-material-button:not(:disabled):active .gsi-material-button-state,
        .gsi-material-button:not(:disabled):focus .gsi-material-button-state {
            background-color: #303030;
            opacity: 12%;
        }

        .gsi-material-button:not(:disabled):hover {
            -webkit-box-shadow: 0 1px 2px 0 rgba(60, 64, 67, .30), 0 1px 3px 1px rgba(60, 64, 67, .15);
            box-shadow: 0 1px 2px 0 rgba(60, 64, 67, .30), 0 1px 3px 1px rgba(60, 64, 67, .15);
        }

        .gsi-material-button:not(:disabled):hover .gsi-material-button-state {
            background-color: #303030;
            opacity: 8%;
        }


        /* Animation keyframes */
        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translate3d(0, 20px, 0);
            }

            to {
                opacity: 1;
                transform: translate3d(0, 0, 0);
            }
        }

        @keyframes bounceIn {
            0% {
                opacity: 0;
                transform: scale3d(0.3, 0.3, 0.3);
            }

            20% {
                transform: scale3d(1.1, 1.1, 1.1);
            }

            40% {
                transform: scale3d(0.9, 0.9, 0.9);
            }

            60% {
                opacity: 1;
                transform: scale3d(1.03, 1.03, 1.03);
            }

            80% {
                transform: scale3d(0.97, 0.97, 0.97);
            }

            100% {
                opacity: 1;
                transform: scale3d(1, 1, 1);
            }
        }

        @keyframes pulse {
            0% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.1);
            }

            100% {
                transform: scale(1);
            }
        }

        @keyframes shakeX {

            0%,
            100% {
                transform: translateX(0);
            }

            10%,
            30%,
            50%,
            70%,
            90% {
                transform: translateX(-5px);
            }

            20%,
            40%,
            60%,
            80% {
                transform: translateX(5px);
            }
        }

        .input-animated {
            animation: fadeInUp 0.6s;
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
        <h4 class="form-title">Welcome Back</h4>

        <?php if (!empty($error_message)): ?>
            <div class="alert alert-danger" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i> <?php echo $error_message; ?>
            </div>
        <?php endif; ?>

        <form method="POST" class="mb-3">
            <div class="mb-4 input-animated" style="animation-delay: 0.2s">
                <label for="username" class="form-label">Username</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-user"></i></span>
                    <input type="text" class="form-control" id="username" name="username" placeholder="Enter your username" required>
                </div>
            </div>
            <div class="mb-4 input-animated" style="animation-delay: 0.4s">
                <label for="password" class="form-label">Password</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                    <input type="password" class="form-control" id="password" name="password" placeholder="Enter your password" required>
                </div>
            </div>
            <div class="d-grid input-animated" style="animation-delay: 0.6s">
                <button type="submit" class="btn btn-login py-2" name="login">
                    <i class="fas fa-sign-in-alt me-2"></i> Sign In
                </button>
            </div>
        </form>
        <div class="text-center mt-3">
            <a href="forgotpassword" class="text-decoration-none text-primary">
                Forgot Password?
            </a>
        </div>

        <div class="signup-link">
            Don't have an account? <a href="signup">Sign Up</a>
        </div>

        <!-- auth options -->
        <div class="text-center mt-4">
            <p class="text-muted">Or sign in with</p>
        </div>
        <div class="d-flex flex-column align-items-center justify-content-center">
            <a href="auth">
                <button class="gsi-material-button">
                    <div class="gsi-material-button-state"></div>
                    <div class="gsi-material-button-content-wrapper">
                        <div class="gsi-material-button-icon">
                            <svg version="1.1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48" xmlns:xlink="http://www.w3.org/1999/xlink" style="display: block;">
                                <path fill="#EA4335" d="M24 9.5c3.54 0 6.71 1.22 9.21 3.6l6.85-6.85C35.9 2.38 30.47 0 24 0 14.62 0 6.51 5.38 2.56 13.22l7.98 6.19C12.43 13.72 17.74 9.5 24 9.5z"></path>
                                <path fill="#4285F4" d="M46.98 24.55c0-1.57-.15-3.09-.38-4.55H24v9.02h12.94c-.58 2.96-2.26 5.48-4.78 7.18l7.73 6c4.51-4.18 7.09-10.36 7.09-17.65z"></path>
                                <path fill="#FBBC05" d="M10.53 28.59c-.48-1.45-.76-2.99-.76-4.59s.27-3.14.76-4.59l-7.98-6.19C.92 16.46 0 20.12 0 24c0 3.88.92 7.54 2.56 10.78l7.97-6.19z"></path>
                                <path fill="#34A853" d="M24 48c6.48 0 11.93-2.13 15.89-5.81l-7.73-6c-2.15 1.45-4.92 2.3-8.16 2.3-6.26 0-11.57-4.22-13.47-9.91l-7.98 6.19C6.51 42.62 14.62 48 24 48z"></path>
                                <path fill="none" d="M0 0h48v48H0z"></path>
                            </svg>
                        </div>
                        <span class="gsi-material-button-contents">Sign in with Google</span>
                        <span style="display: none;">Sign in with Google</span>
                    </div>
                </button>
            </a>
        </div>


    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>