<?php
require_once 'vendor/autoload.php';

$client = new Google_Client();
$client->setClientId('YOUR_GOOGLE_CLIENT_ID');
$client->setClientSecret('YOUR_GOOGLE_CLIENT_SECRET');
$client->setRedirectUri('https://apannel.example.com/google-callback.php');
$client->addScope("email");
$client->addScope("profile");

$login_url = $client->createAuthUrl();

header("Location: " . $login_url);
exit;
