<?php

require_once 'config/env.php';  // Load environment variables

$key = $_ENV['ENCODER_KEY'];
function encodeString($string) {
    global $key;
    $ivlen = openssl_cipher_iv_length('AES-256-CBC');
    $iv = openssl_random_pseudo_bytes($ivlen);
    $ciphertext = openssl_encrypt($string, 'AES-256-CBC', hash('sha256', $key, true), 0, $iv);
    // Encode IV + ciphertext together
    return base64_encode($iv . $ciphertext);
}

function decodeString($encoded) {
    global $key;
    $data = base64_decode($encoded);
    $ivlen = openssl_cipher_iv_length('AES-256-CBC');
    $iv = substr($data, 0, $ivlen);
    $ciphertext = substr($data, $ivlen);
    return openssl_decrypt($ciphertext, 'AES-256-CBC', hash('sha256', $key, true), 0, $iv);
}


?>