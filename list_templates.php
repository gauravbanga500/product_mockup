<?php
require('db_connection.php');
include 'admin_sidebar.php';


if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM templates WHERE id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        echo "Template deleted successfully.";
    } else {
        echo "Error: Failed to delete template.";
    }
}

$templates = $conn->query("SELECT * FROM templates");
?>

<!DOCTYPE html>
<html>
<head>
    <title>List Templates</title>
        <a class="add-btn" href="add_templates.php">Add New Template</a>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        h1 { text-align: center; color: #333; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        table th, table td { border: 1px solid #ddd; padding: 10px; text-align: center; }
        table th { background-color: #f4f4f4; }
        a { color: #007bff; text-decoration: none; }
        a:hover { text-decoration: underline; }
        .success-message, .error-message { text-align: center; margin: 10px 0; padding: 10px; border-radius: 5px; }
        .success-message { background-color: #d4edda; color: #155724; }
        .error-message { background-color: #f8d7da; color: #721c24; }
    </style>
</head>
<body>
    <h1>Templates</h1>
    <table>
        <tr>
            <th>ID</th>
            <th>First Template</th>
            <th>Second Template</th>
            <th>Actions</th>
        </tr>
        <?php while ($template = $templates->fetch_assoc()): ?>
            <tr>
                <td><?= $template['id'] ?></td>
                <td><img src="<?= $template['first_template'] ?>" width="100"></td>
                <td><img src="<?= $template['second_template'] ?>" width="100"></td>
                <td>
                    <a href="edit_template.php?id=<?= $template['id'] ?>">Edit</a> | 
                    <a href="?delete=<?= $template['id'] ?>" onclick="return confirm('Are you sure?')">Delete</a>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>
