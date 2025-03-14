<?php
session_start();
require_once 'config/db_connect.php';
global $conn;

$error_message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["login"])) {
        $username = $_POST["username"];
        $password = $_POST["password"];

        $stmt = $conn->prepare("SELECT id, password,plan FROM users WHERE username=?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($id, $hashed_password, $plan);
        $stmt->fetch();

        if ($stmt->num_rows > 0 && password_verify($password, $hashed_password)) {
            $_SESSION["user_id"] = $id;
            $_SESSION["username"] = $username;
            $_SESSION["plan"] = $plan;
            header("Location: dashboard.php");
            exit();
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
    <!-- add seo for login  -->
    <meta name="description" content="A - Pannel - File Manager - Login">
    <meta name="keywords" content="A - Pannel - File Manager - Login">
    <meta name="author" content="A - Pannel - File Manager - Login">
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
        
        /* Animation keyframes */
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
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
            0% { transform: scale(1); }
            50% { transform: scale(1.1); }
            100% { transform: scale(1); }
        }
        
        @keyframes shakeX {
            0%, 100% { transform: translateX(0); }
            10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
            20%, 40%, 60%, 80% { transform: translateX(5px); }
        }
        
        .input-animated {
            animation: fadeInUp 0.6s;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="logo">
            <a href="index.php" class="text-decoration-none text-dark">
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
        
        <div class="signup-link">
            Don't have an account? <a href="signup.php">Sign Up</a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
