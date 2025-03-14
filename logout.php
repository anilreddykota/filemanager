<?php
// Start the session
session_start();

// Unset all session variables
$_SESSION = array();

// If session cookie is used, destroy it
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time()-42000, '/');
}

// Finally, destroy the session
session_destroy();

// Redirect to login page
header('Location: index.php');
exit;
?>