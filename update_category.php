<?php
include 'db_connection.php'; // Include your database connection file

// Check if form data is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $category_id = $_POST['id'];
    $category_name = $_POST['category_name'];

    // Update category in the database
    $query = "UPDATE categories SET name = ? WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('si', $category_name, $category_id);

    if ($stmt->execute()) {
        // Redirect back to the category list after successful update
        header('Location: category_list.php');
        exit;
    } else {
        die('Error updating category: ' . $conn->error);
    }
} else {
    die('Invalid request method.');
}
