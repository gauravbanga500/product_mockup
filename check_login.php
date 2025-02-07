<?php
ob_start(); // Start output buffering
session_start();
if (!isset($_SESSION['user_id'])) {
    // Redirect if no user ID is set
    header('Location: login.php?error=Please login first');
    exit();
}

// Ensure the user role is valid
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    // Redirect if the user is not an admin
    header('Location: dashboard.php?error=AccessDenied');
    exit();
}

// Debugging: Session is valid
?>
