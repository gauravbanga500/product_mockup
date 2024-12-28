<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include 'db_connection.php';
?>



<?php
include 'db_connection.php'; // Include your database connection file
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Category</title>
</head>
<body>
    <h1>Add New Category</h1>
    <form action="save_category.php" method="POST">
        <label for="category_name">Category Name:</label>
        <input type="text" name="category_name" id="category_name" required><br><br>
        <button type="submit">Save Category</button>
    </form>
    <a href="index.php">Go Back</a>
</body>
</html>
