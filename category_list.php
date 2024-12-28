<?php
include 'db_connection.php'; // Include your database connection file

$query = "SELECT * FROM categories";
$result = $conn->query($query);

if (!$result) {
    die('Error fetching categories: ' . $conn->error);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Category List</title>  
</head>
<body>
    <h1>Category List</h1>
    <a href="add_category.php">Add New Category</a><br><br>
    <table border="1">
        <thead>
            <tr>
                <th>ID</th>
                <th>Category Name</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['id'] ?></td>
                    <td><?= $row['name'] ?></td> <!-- Corrected column name -->
                    <td>
                        <a href="edit_category.php?id=<?= $row['id'] ?>">Edit</a>
                        <a href="delete_category.php?id=<?= $row['id'] ?>" onclick="return confirm('Are you sure you want to delete this category?');">Delete</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
    <a href="index.php">Go Back</a>
</body>
</html>
