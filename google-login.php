<?php
require_once 'vendor/autoload.php';
require_once 'config/env.php';  // Load environment variables
$client_id = $_ENV['O_AUTH_CLIENT_ID'];
$client_secret = $_ENV['O_AUTH_CLIENT_SECRET'];
$redirect_uri = $_ENV['O_AUTH_REDIRECT_URI'];

$client = new Google_Client();
$client->setClientId($client_id);
$client->setClientSecret($client_secret);
$client->setRedirectUri($redirect_uri); 
$client->addScope("email");
$client->addScope("profile");

$login_url = $client->createAuthUrl();

header("Location: " . $login_url);
exit;
