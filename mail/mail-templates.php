<?php



function getWelcomeEmailTemplate($username, $verificationLink) {
    $welcomeEmailTemplate = <<<EOT
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to Apannel FileManager</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f4; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; background-color: #fff; border-radius: 5px; }
        h1 { color: #4CAF50; }
        p { line-height: 1.6; }
        a { color: #4CAF50; text-decoration: none; }
    </style>

</head>
<body>
    <div class="container">
        <h1>Welcome to Apannel FileManager</h1>
        <p>Dear {$username},</p>
        <p>Thank you for signing up for Apannel FileManager! We're excited to have you on board.</p>
        <p>To get started, please verify your email address by clicking the link below:</p>
        <p><a href="{$verificationLink}">Verify Email Address</a></p>
        <p>If you have any questions, feel free to reply to this email.</p>
        <p>Best regards,<br> Team A-pannel</p>
    </div>
</body>
</html>
EOT;


    return $welcomeEmailTemplate;
}


function forgotPasswordEmailTemplate($username, $resetLink) {
    $forgotPasswordTemplate = <<<EOT
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Your Password</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f4; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; background-color: #fff; border-radius: 5px; }
        h1 { color: #f44336; }
        p { line-height: 1.6; }
        a { color: #f44336; text-decoration: none; }
    </style>
</head>
<body>

    <div class="container">
        <h1>Reset Your Password</h1>
        <p>Dear {$username},</p>
        <p>We received a request to reset your password for your Apannel FileManager account.</p>
        <p>If you did not request this, please ignore this email. Otherwise, you can reset your password by clicking the link below:</p>
        <p><a href="{$resetLink}">Reset Password</a></p>
        
        <p>If you have any questions, feel free to reply to this email.</p>
        <p>Best regards,<br> Team A-pannel</p>
    </div>

</body>
</html>
EOT;

    return $forgotPasswordTemplate;
}

function collegeVerificationEmailTemplate($username, $verificationLink) {
    $collegeVerificationTemplate = <<<EOT
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>College Email Verification</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f4; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; background-color: #fff; border-radius: 5px; }
        h1 { color: #2196F3; }
        p { line-height: 1.6; }
        a { color: #2196F3; text-decoration: none; }
    </style>
</head>
<body>
    <div class="container">
        <h1>College Email Verification</h1>
        <p>Dear {$username},</p>
        <p>Thank you for providing your college email address. To complete the verification process, please click the link below:</p>
        <p><a href="{$verificationLink}">Verify College Email Address</a></p>
        
        <p>If you have any questions, feel free to reply to this email.</p>
        <p>Best regards,<br> Team A-pannel</p>
    </div>
</body>
</html>
EOT;

    return $collegeVerificationTemplate;
}





