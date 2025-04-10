<?php
// logout.php
include_once 'includes/functions.php';

// Unset all session variables
$_SESSION = array();

// Delete the session cookie
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Destroy the session
session_destroy();

// Delete user_login cookie
setcookie('user_login', '', time() - 3600, '/');

// Redirect to login page
header("Location: index.php");
exit();
?>