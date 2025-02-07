<?php
session_start();
include 'db_connection.php';
include 'admin_sidebar.php';

// Start output buffering to prevent accidental output before headers
ob_start();

if (isset($_GET['id'])) {
    $id = intval($_GET['id']); // Sanitize the ID parameter

    // SQL query to delete the user
    $sql = "DELETE FROM users WHERE id = $id";

    if (mysqli_query($conn, $sql)) {
        // Success message and redirection
        if (!headers_sent()) {
            header('Location: user.php?message=User deleted successfully');
            exit();
        } else {
            echo "<script>window.location.href='user.php?message=User deleted successfully';</script>";
            exit();
        }
    } else {
        // If there's an error with the database query
        echo "Error: " . mysqli_error($conn);
    }
} else {
    // If no ID is provided
    echo "Invalid request.";
}

// End output buffering
ob_end_flush();
?>