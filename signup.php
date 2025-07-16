<?php
// Start session
session_start();

// Database connection
require_once 'config/db_connect.php';

require_once 'config/env.php';  // Load environment variables

require_once 'coding/encode.php';
require_once "mail/mail-templates.php";
require_once "mail/mailer.php";

$username = $email = $password = $confirm_password = '';
$errors = [];


$is_student = isset($_GET['plan']) && $_GET['plan'] === 'student';
$domains = [];
if ($is_student) {
    $env_domains = $_ENV['STUDENT_PLAN_MAIL_DOMAINS'];
    if ($env_domains) {
        $domains = array_map('trim', explode(',', $env_domains));
    }
}
// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data and sanitize
    $username = trim(htmlspecialchars($_POST['username']));
    $email = trim(htmlspecialchars($_POST['email']));
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Validate input
    if (empty($username)) {
        $errors[] = "Username is required";
    } elseif (strlen($username) < 3) {
        $errors[] = "Username must be at least 3 characters";
    }

    if (!empty($email) && $is_student) {
        $email_local = trim(htmlspecialchars($_POST['email_local']));
        $email_domain = trim(htmlspecialchars($_POST['email_domain']));
        if (empty($email_local) || empty($email_domain)) {
            $errors[] = "Email is required";
        } else {
            $email = "{$email_local}@{$email_domain}";
        }
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format";
    }

    if (empty($password)) {
        $errors[] = "Password is required";
    } elseif (strlen($password) < 8) {
        $errors[] = "Password must be at least 8 characters";
    }

    if ($password !== $confirm_password) {
        $errors[] = "Passwords do not match";
    }

    // Check if username or email already exists
    if (empty($errors)) {
        $stmt = $conn->prepare("SELECT COUNT(*) FROM users WHERE username = ? OR email = ? OR college_email = ?");
        $stmt->bind_param("sss", $username, $email, $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $count = $result->fetch_row()[0];

        if ($count > 0) {
            $errors[] = "Username or email already exists";
        }
    }

    // If no errors, register the user
    if (empty($errors)) {
        // Hash password
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        $plan = "basic";
        // Insert user into database
        if ($is_student) {
            $email = "{$email_local}@{$email_domain}"; // Ensure email is set correctly for student plan
            $plan = "student";
            $college_email = $email;
        } else {
            $college_email = "";
        }
        $stmt = $conn->prepare("INSERT INTO users (username, email, password, college_email, plan) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $username, $email, $password_hash, $college_email, $plan);

        if ($stmt->execute()) {


            $_SESSION['message'] = "Registration successful. Please login.";

            $verification_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://{$_SERVER['HTTP_HOST']}/verify?email=" . urlencode(encodeString($email));
            $welcomeEmailTemplate = getWelcomeEmailTemplate($username, $verification_link);
            if (sendWelcomeMail($welcomeEmailTemplate, $email)) {
                echo "<script>
                    alert('Verify your email: " . htmlspecialchars($email, ENT_QUOTES) . "');
                    setTimeout(function() {
                        window.location.href = 'login';
                    }, 1000);
                </script>";
                exit();
            }
        } else {
            $errors[] = "Something went wrong. Please try again.";
        }
    }
}




?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - File Manager</title>
    <link rel="icon" href="./assets/favicon.png" type="image/png">
    <meta name="description" content="Sign up to access your files">
    <meta name="keywords" content="file manager, upload files, sign up">
    <meta name="author" content="Anil Reddy Kota">
    <meta name="robots" content="index, follow">
    <meta name="revisit-after" content="1 month">
    <meta name="language" content="EN">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #4a6fdc;
            --secondary-color: #f8f9fa;
            --accent-color: #3a56a8;
            --error-color: #dc3545;
            --success-color: #28a745;
            --text-color: #333;
            --border-radius: 8px;
            --box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .container {
            width: 100%;
            max-width: 450px;
            background-color: #fff;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            padding: 2.5rem;
            animation: fadeIn 0.5s ease-in-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        h1 {
            color: var(--primary-color);
            text-align: center;
            margin-bottom: 1.5rem;
            font-size: 2rem;
            font-weight: 600;
        }

        .logo-container {
            text-align: center;
            margin-bottom: 1rem;
        }

        .logo-container i {
            font-size: 3rem;
            color: var(--primary-color);
        }

        .error-container {
            background-color: rgba(220, 53, 69, 0.1);
            border-left: 4px solid var(--error-color);
            border-radius: 4px;
            padding: 1rem;
            margin-bottom: 1.5rem;
        }

        .error {
            color: var(--error-color);
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
        }

        .error:last-child {
            margin-bottom: 0;
        }

        .form-group {
            margin-bottom: 1.5rem;
            position: relative;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: var(--text-color);
            font-weight: 500;
            font-size: 0.9rem;
        }

        .form-group input {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: var(--border-radius);
            font-size: 1rem;
            transition: border-color 0.3s;
        }

        .form-group input:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(74, 111, 220, 0.1);
        }

        .form-group .icon {
            position: absolute;
            right: 15px;
            top: 42px;
            color: #aaa;
        }

        .btn {
            width: 100%;
            padding: 12px;
            background-color: var(--primary-color);
            color: white;
            border: none;
            border-radius: var(--border-radius);
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .btn:hover {
            background-color: var(--accent-color);
        }

        .login-link {
            text-align: center;
            margin-top: 1.5rem;
            color: var(--text-color);
            font-size: 0.9rem;
        }

        .login-link a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 600;
        }

        .login-link a:hover {
            text-decoration: underline;
        }

        .password-strength {
            height: 5px;
            margin-top: 5px;
            border-radius: 3px;
            background-color: #ddd;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="logo-container d-flex">
            <i class="fas fa-file-alt"></i>
             <h1>Create Account</h1>
         
            <?php if ($is_student): ?>
                <span style="display:inline-block; background: #e3f2fd; color: #1976d2; font-size: 0.85rem; font-weight: 600; border-radius: 12px; padding: 2px 12px; margin-left: 8px; vertical-align: middle;">
                    <i class="fas fa-graduation-cap"></i> Student Plan
                    <a href="signup" title="Remove student plan" style="margin-left:8px; color:#dc3545; text-decoration:none; font-weight:bold; font-size:3.1em;">&times;</a>
                </span>
            <?php else: ?>
                  <span style="display:inline-block; background: #e3f2fd; color: #1976d2; font-size: 0.85rem; font-weight: 600; border-radius: 12px; padding: 2px 12px; margin-left: 8px; vertical-align: middle;">
                <a href="signup?plan=student" style="color: #1976d2; text-decoration: underline;">Are you a student?</a>
            </span>
            <?php endif; ?>
        </div>


        <?php if (count($errors) > 0): ?>
            <div class="error-container">
                <?php foreach ($errors as $error): ?>
                    <p class="error"><i class="fas fa-exclamation-circle"></i> <?= $error ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form action="signup" method="POST">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" value="<?= $username ?>" placeholder="Enter your username (no spaces)" required pattern="^\S*$" title="Username cannot contain spaces">
                <span class="icon"><i class="fas fa-user"></i></span>
            </div>

            <?php if ($is_student && !empty($domains)): ?>
                <div class="form-group">
                    <label for="email">Email</label>
                    <div style="display: flex; gap: 8px;">
                        <input type="text" id="email_local" name="email_local" value="<?= htmlspecialchars(explode('@', $email)[0] ?? '') ?>" placeholder="Enter your email username" required style="flex:2;">
                        <select id="email_domain" name="email_domain" required style="flex:1;">
                            <?php foreach ($domains as $domain): ?>
                                <option value="<?= htmlspecialchars($domain) ?>" <?= (isset($email) && strpos($email, '@' . $domain) !== false) ? 'selected' : '' ?>>@<?= htmlspecialchars($domain) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                </div>
                <script>
                    document.querySelector('form').addEventListener('submit', function(e) {
                        var local = document.getElementById('email_local').value.trim();
                        var domain = document.getElementById('email_domain').value;
                        if (local && domain) {
                            let emailInput = document.getElementById('email');
                            if (!emailInput) {
                                emailInput = document.createElement('input');
                                emailInput.type = 'hidden';
                                emailInput.name = 'email';
                                emailInput.id = 'email';
                                this.appendChild(emailInput);
                            }
                            emailInput.value = local + '@' + domain;
                        }
                    });
                </script>
            <?php else: ?>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" value="<?= htmlspecialchars($email) ?>" placeholder="Enter your email" required>
                    <span class="icon"><i class="fas fa-envelope"></i></span>
                </div>
            <?php endif; ?>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Create a password" required>
                <span class="icon"><i class="fas fa-lock"></i></span>
                <div class="password-strength"></div>
            </div>

            <div class="form-group">
                <label for="confirm_password">Confirm Password</label>
                <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm your password" required>
                <span class="icon"><i class="fas fa-lock"></i></span>
            </div>

            <div class="form-group">
                <button type="submit" class="btn">Sign Up</button>
            </div>

            <p class="login-link">Already have an account? <a href="login">Log in</a></p>
        </form>
    </div>

    <script>
        // Simple password strength indicator
        document.getElementById('password').addEventListener('input', function() {
            const password = this.value;
            const strength = document.querySelector('.password-strength');

            if (password.length === 0) {
                strength.style.width = '0%';
                strength.style.backgroundColor = '#ddd';
            } else if (password.length < 6) {
                strength.style.width = '33%';
                strength.style.backgroundColor = '#dc3545';
            } else if (password.length < 10) {
                strength.style.width = '66%';
                strength.style.backgroundColor = '#ffc107';
            } else {
                strength.style.width = '100%';
                strength.style.backgroundColor = '#28a745';
            }
        });
    </script>
</body>

</html>