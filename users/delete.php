<?php
// users/delete.php
include_once '../config/Database.php';
include_once '../models/User.php';
include_once '../includes/functions.php';

// Check if user is logged in
requireLogin();

$db = new Database();
$conn = $db->getConnection();

// Check if id parameter exists
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: list.php');
    exit();
}

// Create user object
$user = new User($conn);
$user->id = $_GET['id'];

// Prevent deleting yourself
if ($user->id == $_SESSION['user_id']) {
    $_SESSION['message'] = 'Cannot delete your own account';
    header('Location: list.php');
    exit();
}

// Get user details first to confirm it exists
if ($user->readOne()) {
    // Delete the user
    if ($user->delete()) {
        $_SESSION['message'] = 'User deleted successfully';
    } else {
        $_SESSION['message'] = 'Failed to delete user';
    }
} else {
    $_SESSION['message'] = 'User not found';
}

// Redirect back to list page
header('Location: list.php');
exit();
?>