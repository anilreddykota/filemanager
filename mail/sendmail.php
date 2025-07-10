<?php
//Load Composer's autoloader
require 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;


function sendMail($config, $recipients, $subject, $body, $altBody = '', $attachments = []) {
    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host       = $config['host'] ?? 'localhost';
        $mail->SMTPAuth   = $config['smtp_auth'] ?? false;
        $mail->Username   = $config['username'] ?? '';
        $mail->Password   = $config['password'] ?? '';
        $mail->Port       = $config['port'] ?? 25;
        if (!empty($config['encryption'])) {
            $mail->SMTPSecure = $config['encryption'];
        }
        if (!empty($config['debug'])) {
            $mail->SMTPDebug = $config['debug'];
        }

        // From
        $fromEmail = $config['from_email'] ?? $mail->Username;
        $fromName  = $config['from_name'] ?? '';
        $mail->setFrom($fromEmail, $fromName);

        // Reply-To
        if (!empty($config['reply_to'])) {
            $replyTo = $config['reply_to'];
            $replyName = $config['reply_name'] ?? '';
            $mail->addReplyTo($replyTo, $replyName);
        }

        // Recipients
        if (is_array($recipients)) {
            foreach ($recipients as $recipient) {
                if (is_array($recipient)) {
                    $mail->addAddress($recipient['email'], $recipient['name'] ?? '');
                } else {
                    $mail->addAddress($recipient);
                }
            }
        } else {
            $mail->addAddress($recipients);
        }

        // Attachments
        foreach ($attachments as $attachment) {
            if (is_array($attachment)) {
                $mail->addAttachment($attachment['path'], $attachment['name'] ?? '');
            } else {
                $mail->addAttachment($attachment);
            }
        }

        // Content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $body;
        $mail->AltBody = $altBody ?: strip_tags($body);

        $mail->send();
        return true;
    } catch (Exception $e) {
        // Optionally log $mail->ErrorInfo
        return false;
    }
}
