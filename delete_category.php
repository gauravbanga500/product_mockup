<?php
include 'db_connection.php'; // Include your database connection

// Get category ID from URL
if (isset($_GET['id'])) {
    $category_id = $_GET['id'];

    // Delete category from the database
    $sql = "DELETE FROM categories WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $category_id);
    $stmt->execute();

    // Redirect back to manage categories page
    header('Location: manage_categories.php');
    exit();
}
?>
