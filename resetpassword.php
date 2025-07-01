<?php
// Include database connection or necessary files
require_once 'config/db_connect.php';
require_once 'coding/encode.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $emailEncoded = $_POST['token'];
    $email = decodeString($emailEncoded);
    $expiry = decodeString($_POST['expiry']);
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'];

    if (empty($email) || empty($password) || empty($confirmPassword)) {
        $error = "All fields are required.";
    } elseif ($password !== $confirmPassword) {
        $error = "Passwords do not match.";
    } else {
        // Check if the email exists in the users table
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Update password
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
            $updateStmt = $conn->prepare("UPDATE users SET password = ? , email_status = 1 WHERE email = ?");
            $updateStmt->bind_param("ss", $hashedPassword, $email);

            if ($updateStmt->execute()) {
                $success = "Password reset successfully.";
            } else {
                $error = "Failed to reset password.";
            }
        } else {
            $error = "Email not found.";
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['email']) && isset($_GET['expiry'])) {
        $emailEncoded = $_GET['email'];
        $expiryEncoded = $_GET['expiry'];
     
        $email = decodeString($emailEncoded);
        $expiry = decodeString($expiryEncoded);
        if (empty($email) || empty($expiry)) {
            $error = "Invalid or missing parameters.";
            header("Location: forgotpassword?error=" . urlencode($error));
            exit();
            
        } elseif (strtotime($expiry) < time()) {
            $error = "The reset link has expired.";
        } else {
            $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows === 0) {
                $error = "Invalid reset link.";
            }
        }
    } else {
        $error = "Invalid reset link.";
    }
}


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
       <title>A- Pannel - File Manager - Reset Password</title>
    <link rel="icon" href="./assets/favicon.png" type="image/png">
    <!-- add seo for login  -->
    <meta name="description" content="A-Pannel - File Manager - Reset Password">
    <meta name="keywords" content="A-Pannel - File Manager - Reset Password">
    <meta name="author" content="A-Pannel - File Manager - Reset Password">
    <meta name="robots" content="index, follow">
    <meta name="revisit-after" content="1 month">
    <meta name="language" content="EN">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .container {
            background: #fff;
            padding: 20px 30px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
        }

        h2 {
            text-align: center;
            color: #333;
        }

        p {
            text-align: center;
        }

        p.error {
            color: red;
        }

        p.success {
            color: green;
        }

        form {
            display: flex;
            flex-direction: column;
        }

        label {
            margin-bottom: 5px;
            font-weight: bold;
            color: #555;
        }

        input {
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 14px;
        }

        button {
            padding: 10px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
        }

        button:hover {
            background-color: #0056b3;
        }

        .animate-fade-in {
            opacity: 0;
            animation: fadeIn 0.8s ease forwards;
        }

        @keyframes fadeIn {
            to {
                opacity: 1;
            }
        }

        #message-container p {
            transition: transform 0.4s cubic-bezier(.4, 2, .6, 1), box-shadow 0.4s;
            transform: translateY(-10px) scale(0.98);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
        }

        #message-container p.animate-fade-in {
            transform: translateY(0) scale(1);
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.08);
        }

        .success a {
            color: #28a745;
            text-decoration: underline;
            transition: color 0.3s;
        }

        .success a:hover {
            color: #155724;
        }
    </style>
</head>

<body>
    <div class="container">
        <h2>Reset Password</h2>
        <div id="message-container">
            <?php if (isset($error)): ?>
                <p class="error animate-fade-in"><?php echo $error; ?></p>
            <?php elseif (isset($success)): ?>
                <p class="success animate-fade-in"><?php echo $success; ?></p>
                <p class="animate-fade-in"><a href="login">Click here to login</a></p>
            <?php else: ?>
                <p class="animate-fade-in">Please enter your new password below.</p>
            <?php endif; ?>
        </div>


        <?php if (!isset($success) && !isset($error)): ?>
            <form method="POST" action="">
                <input type="hidden" name="token" value="<?php echo htmlspecialchars($_GET['email'] ?? ''); ?>">
                <input type="hidden" name="expiry" value="<?php echo htmlspecialchars($_GET['expiry'] ?? ''); ?>">
                <input type="hidden" name="email" id="email" value="<?php echo htmlspecialchars($email ?? ''); ?>" required>
                <label for="password">New Password:</label>
                <input type="password" name="password" id="password" required>
                <label for="confirm_password">Confirm Password:</label>
                <input type="password" name="confirm_password" id="confirm_password" required>
                <button type="submit">Reset Password</button>
            </form>
        <?php endif; ?>
    </div>
</body>

</html>