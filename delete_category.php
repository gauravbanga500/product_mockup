<?php
include 'db_connection.php'; // Include your database connection
include 'admin_sidebar.php';

// Get category ID from URL
if (isset($_GET['id'])) {
    $category_id = $_GET['id'];

    // Delete category from the database
    $sql = "DELETE FROM categories WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $category_id);
    $stmt->execute();

    // Display success message
    $message = "Category deleted successfully.";
} else {
    $message = "No category ID provided. Unable to delete.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Category</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #f9f9f9;
        }
        .container {
            max-width: 600px;
            margin: auto;
            padding: 20px;
            background: #fff; 
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            text-align: center; 
        }
        h1 {
            color: #333;
        }
        .message {
            margin: 20px 0;
            font-size: 18px;
            color: green;
        }
        .back-button {
            display: inline-block;
            margin-top: 20px;
            background-color: #007bff;
            color: #fff;
            padding: 10px 15px;
            border-radius: 5px;
            text-decoration: none;
        }
        .back-button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        <h3><?= htmlspecialchars($message); ?></h3>
        <a href="category_list.php" class="back-button">Go Back</a>
    </div>
</body>
</html>
