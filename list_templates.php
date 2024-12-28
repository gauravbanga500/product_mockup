<?php
require('db_connection.php');

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
</head>
<body>
    <h1>Templates</h1>
    <table border="1">
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
