<?php

require_once "sendmail.php";
require_once "config/env.php";
function sendWelcomeMail($welcomeEmailTemplate, $email) {
    $subject = "Welcome to A-pannel verify Your Email ";
    $body = $welcomeEmailTemplate;
    $altBody = strip_tags($body);

    $config = [
        'host'        => $_ENV['SMTP_HOST'] ?? 'localhost',
        'smtp_auth'   => true,
        'username'    => $_ENV['SMTP_USER'] ?? '',
        'password'    => $_ENV['SMTP_PASS'] ?? '',
        'port'        => $_ENV['SMTP_PORT'] ?? 25,
        'encryption'  => $_ENV['SMTP_ENCRYPTION'] ?? '',
        'debug'       => $_ENV['SMTP_DEBUG'] ?? 0,
        'from_email'  => $_ENV['NO_REPLY_EMAIL'] ?? ($_ENV['SMTP_USER'] ?? ''),
        'from_name'   => $_ENV['SMTP_FROM_NAME'] ?? '',
        'reply_to'    => $_ENV['SMTP_REPLY_TO'] ?? '',
        'reply_name'  => $_ENV['SMTP_REPLY_NAME'] ?? '',
    ];

    return sendMail($config, [$email], $subject, $body, $altBody);
}
function sendResetPasswordMail($resetPasswordTemplate,$email){
    $subject = "Reset Your Password";
    $body = $resetPasswordTemplate;
    $altBody = strip_tags($body);

    $config = [
        'host'        => $_ENV['SMTP_HOST'] ?? 'localhost',
        'smtp_auth'   => true,
        'username'    => $_ENV['SMTP_USER'] ?? '',
        'password'    => $_ENV['SMTP_PASS'] ?? '',
        'port'        => $_ENV['SMTP_PORT'] ?? 25,
        'encryption'  => $_ENV['SMTP_ENCRYPTION'] ?? '',
        'debug'       => $_ENV['SMTP_DEBUG'] ?? 0,
        'from_email'  => $_ENV['NO_REPLY_EMAIL'] ?? ($_ENV['SMTP_USER'] ?? ''),
        'from_name'   => $_ENV['SMTP_FROM_NAME'] ?? '',
        'reply_to'    => $_ENV['SMTP_REPLY_TO'] ?? '',
        'reply_name'  => $_ENV['SMTP_REPLY_NAME'] ?? '',
    ];

    return sendMail($config, [$email], $subject, $body, $altBody);

}

function sendCollegeVerificationMail($collegeVerificationTemplate, $email) {
    $subject = "Verify Your College Email and Get Student Benefits";
    $body = $collegeVerificationTemplate;
    $altBody = strip_tags($body);

    $config = [
        'host'        => $_ENV['SMTP_HOST'] ?? 'localhost',
        'smtp_auth'   => true,
        'username'    => $_ENV['SMTP_USER'] ?? '',
        'password'    => $_ENV['SMTP_PASS'] ?? '',
        'port'        => $_ENV['SMTP_PORT'] ?? 25,
        'encryption'  => $_ENV['SMTP_ENCRYPTION'] ?? '',
        'debug'       => $_ENV['SMTP_DEBUG'] ?? 0,
        'from_email'  => $_ENV['NO_REPLY_EMAIL'] ?? ($_ENV['SMTP_USER'] ?? ''),
        'from_name'   => $_ENV['SMTP_FROM_NAME'] ?? '',
        'reply_to'    => $_ENV['SMTP_REPLY_TO'] ?? '',
        'reply_name'  => $_ENV['SMTP_REPLY_NAME'] ?? '',
    ];

    return sendMail($config, [$email], $subject, $body, $altBody);
}

?>