<?php
include 'db_connection.php'; // Include your database connection

// Fetch existing categories
$sql = "SELECT * FROM categories";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Categories</title>
</head>
<body>
    <h1>Manage Categories</h1>
    
    <!-- Add New Category Form -->
    <h2>Add New Category</h2>
    <form action="save_category.php" method="POST">
        <label for="category_name">Category Name:</label>
        <input type="text" name="category_name" id="category_name" required><br><br>
        <button type="submit">Save Category</button>
    </form>

    <!-- Display Existing Categories -->
    <h2>Existing Categories</h2>
    <table border="1">
        <thead>
            <tr>
                <th>ID</th>
                <th>Category Name</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo $row['name']; ?></td>
                    <td><a href="delete_category.php?id=<?php echo $row['id']; ?>">Delete</a></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <a href="index.php">Go Back</a>
</body>
</html>
