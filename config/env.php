<?php
require __DIR__ . '/../vendor/autoload.php';  // Load Composer dependencies

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();
?>