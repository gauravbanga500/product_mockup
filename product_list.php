<?php
include 'db_connection.php';
include 'admin_sidebar.php';


// Fetch all products
$result = $conn->query("SELECT p.id, p.name, p.description, p.featured_image, c.name AS category_name 
                        FROM products p 
                        LEFT JOIN categories c ON p.categories = c.id");

if ($result === false) {
    die("Error: Unable to fetch products - " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product List</title>
    <link rel="stylesheet" href="style.css">
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: center;
        }
        th {
            background-color: #f4f4f4;
        }
        img {
            width: 100px;
            height: auto;
        }
        a {
            text-decoration: none;
            color: blue;
        }
        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <h1>Product List</h1>
    <a href="add_product.php" style="margin-bottom: 20px; display: inline-block;">Add New Product</a>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Product Name</th>
                <th>Description</th>
                <th>Category</th>
                <th>Featured Image</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td><?php echo htmlspecialchars($row['name']); ?></td>
                        <td><?php echo htmlspecialchars($row['description']); ?></td>
                        <td><?php echo htmlspecialchars($row['category_name'] ?: 'N/A'); ?></td>
                        <td>
                            <?php if (!empty($row['featured_image'])): ?>
                                <img src="<?php echo htmlspecialchars($row['featured_image']); ?>" alt="Featured Image">
                            <?php else: ?>
                                No Image
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="edit_product.php?id=<?php echo $row['id']; ?>">Edit</a> | 
                            <a href="delete_product.php?id=<?php echo $row['id']; ?>" onclick="return confirm('Are you sure you want to delete this product?');">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6">No products found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>
