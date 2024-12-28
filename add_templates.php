<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require('db_connection.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $upload_dir = 'uploads/';

    // Process first template
    if (!empty($_FILES['first_template']['tmp_name'])) {
        $first_template_name = $_FILES['first_template']['name'];
        $first_template_ext = pathinfo($first_template_name, PATHINFO_EXTENSION);
        $first_template_new_name = uniqid('template1_', true) . '.' . $first_template_ext;
        $first_template_path = $upload_dir . $first_template_new_name;

        if (move_uploaded_file($_FILES['first_template']['tmp_name'], $first_template_path)) {
            $first_template_uploaded = true;
        } else {
            die('Error: Failed to upload the first template.');
        }
    }

    // Process second template
    if (!empty($_FILES['second_template']['tmp_name'])) {
        $second_template_name = $_FILES['second_template']['name'];
        $second_template_ext = pathinfo($second_template_name, PATHINFO_EXTENSION);
        $second_template_new_name = uniqid('template2_', true) . '.' . $second_template_ext;
        $second_template_path = $upload_dir . $second_template_new_name;

        if (move_uploaded_file($_FILES['second_template']['tmp_name'], $second_template_path)) {
            $second_template_uploaded = true;
        } else {
            die('Error: Failed to upload the second template.');
        }
    }

    // Save paths to database
    $stmt = $conn->prepare("INSERT INTO templates (first_template, second_template) VALUES (?, ?)");
    $stmt->bind_param('ss', $first_template_path, $second_template_path);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        echo "Templates uploaded successfully.";
    } else {
        echo "Error: Failed to save templates.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Templates</title>
</head>
<body>
    <form method="POST" enctype="multipart/form-data">
        <label>First Template (First Page):</label>
        <input type="file" name="first_template" required><br>
        <label>Second Template (Last Page):</label>
        <input type="file" name="second_template" required><br>
        <button type="submit">Add Templates</button>
    </form>
</body>
</html>
