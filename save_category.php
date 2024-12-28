<?php
include 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $category_name = $_POST['category_name'];

    // Insert category into the database
    $stmt = $conn->prepare("INSERT INTO categories (name) VALUES (?)");
    $stmt->bind_param('s', $category_name);
    if ($stmt->execute()) {
        echo "Category added successfully! <a href='add_category.php'>Add Another</a> | <a href='index.php'>Go Back</a>";
    } else {
        echo "Error: " . $stmt->error;
    }
}
?>
