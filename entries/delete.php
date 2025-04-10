<?php
// entries/delete.php
include_once '../config/Database.php';
include_once '../models/Entry.php';
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

// Create entry object
$entry = new Entry($conn);
$entry->id = $_GET['id'];

// Get entry details first to confirm it exists
if ($entry->readOne()) {
    // Delete the entry
    if ($entry->delete()) {
        $_SESSION['message'] = 'Entry deleted successfully';
    } else {
        $_SESSION['message'] = 'Failed to delete entry';
    }
} else {
    $_SESSION['message'] = 'Entry not found';
}

// Redirect back to list page
header('Location: list.php');
exit();
?>