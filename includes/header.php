
<?php

// Get the root directory path
$root_path = $_SERVER['DOCUMENT_ROOT'] . '/basic_crud_core_php/';
//echo $root_path;


// includes/header.php
include_once $root_path.'config/Database.php';
include_once $root_path.'includes/functions.php';

// Adjust path for files in the root directory
$path_adjust = (strpos($_SERVER['PHP_SELF'], 'index.php') !== false || 
                strpos($_SERVER['PHP_SELF'], 'dashboard.php') !== false) ? '' : '../';

// Only check login on pages other than the index
if (basename($_SERVER['PHP_SELF']) !== 'index.php') {
    requireLogin();
}

$db = new Database();
$conn = $db->getConnection();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Simple CRUD Application</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo $path_adjust; ?>css/style.css">
</head>
<body>
    <div class="container">
    <?php if (isLoggedIn() && basename($_SERVER['PHP_SELF']) !== 'index.php'): ?>
        <nav class="navbar navbar-expand-lg navbar-light bg-light mb-4">
            <div class="container-fluid">
                <a class="navbar-brand" href="<?php echo $path_adjust; ?>dashboard.php">CRUD App</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo $path_adjust; ?>dashboard.php">Dashboard</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo $path_adjust; ?>entries/list.php">Entries</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo $path_adjust; ?>users/list.php">Users</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo $path_adjust; ?>audit/list.php">Audit Logs</a>
                        </li>
                    </ul>
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo $path_adjust; ?>logout.php">Logout</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    <?php endif; ?>