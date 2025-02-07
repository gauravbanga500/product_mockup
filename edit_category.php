<?php
include 'db_connection.php'; // Include your database connection file
include 'admin_sidebar.php';


// Get the category ID from the query string
if (!isset($_GET['id'])) {
    die('Error: No category ID provided.');
}

$category_id = $_GET['id'];

// Fetch category data from the database
$query = "SELECT * FROM categories WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('i', $category_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die('Error: Category not found.');
}

$category = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Category</title>
    <link rel="stylesheet" href="styles.css"> <!-- Link to external CSS file -->
</head>
<body>
    <div class="container">
        <h1>Edit Category</h1>
        <form action="update_category.php" method="POST">
            <input type="hidden" name="id" value="<?= $category['id'] ?>">
            <label for="category_name">Category Name</label>
            <input type="text" name="category_name" id="category_name" value="<?= htmlspecialchars($category['name']) ?>" required>
            <button type="submit">Update Category</button>
        </form>
        <a href="category_list.php">Go Back</a>
    </div>
</body>
</html>