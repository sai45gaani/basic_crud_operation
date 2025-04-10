<?php
// includes/functions.php
session_start();

// Function to check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Function to redirect if not logged in
function requireLogin() {
    if (!isLoggedIn()) {
        header("Location: ../index.php");
        exit();
    }
}

// Function for simple error handling
function showError($message) {
    return "<div class='alert alert-danger'>{$message}</div>";
}

// Function for success messages
function showSuccess($message) {
    return "<div class='alert alert-success'>{$message}</div>";
}

// Get base URL
function getBaseUrl() {
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    $script = dirname($_SERVER['SCRIPT_NAME']);
    
    if ($script == '/' || $script == '\\') {
        $script = '';
    }
    
    return $protocol . '://' . $host . $script;
}
?>