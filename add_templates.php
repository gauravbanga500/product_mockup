<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require('db_connection.php');
include 'admin_sidebar.php';

$message = '';
$message_class = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $upload_dir = 'uploads/';

    // Initialize upload status
    $first_template_uploaded = false;
    $second_template_uploaded = false;

    // Process first template
    if (!empty($_FILES['first_template']['tmp_name'])) {
        $first_template_name = $_FILES['first_template']['name'];
        $first_template_ext = pathinfo($first_template_name, PATHINFO_EXTENSION);
        $first_template_new_name = uniqid('template1_', true) . '.' . $first_template_ext;
        $first_template_path = $upload_dir . $first_template_new_name;

        if (move_uploaded_file($_FILES['first_template']['tmp_name'], $first_template_path)) {
            $first_template_uploaded = true;
        } else {
            $message = 'Error: Failed to upload the first template.';
            $message_class = 'error';
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
            $message = 'Error: Failed to upload the second template.';
            $message_class = 'error';
        }
    }

    // Save paths to the database if both templates uploaded successfully
    if ($first_template_uploaded && $second_template_uploaded) {
        $stmt = $conn->prepare("INSERT INTO templates (first_template, second_template) VALUES (?, ?)");
        $stmt->bind_param('ss', $first_template_path, $second_template_path);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            $message = 'Templates uploaded successfully.';
            $message_class = 'success';
        } else {
            $message = 'Error: Failed to save templates.';
            $message_class = 'error';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Templates</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #f9f9f9;
        }
        h1 {
            color: #333;
        }
        .form-container {
            max-width: 600px;
            margin: auto;
            padding: 20px;
            background: #fff;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #555;
        }
        input[type="file"] {
            display: block;
            margin-bottom: 15px;
        }
        button {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 4px;
            cursor: pointer;
        }
        button:hover {
            background-color: #0056b3;
        }
        .message {
            margin-bottom: 20px;
            padding: 10px;
            border-radius: 4px;
            font-size: 14px;
        }
        .message.success {
            background-color: #d4edda;
            color: #155724;
        }
        .message.error {
            background-color: #f8d7da;
            color: #721c24;
        }
        a {
            color: #007bff;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h1>Add Templates</h1>
        <?php if ($message): ?>
            <div class="message <?= $message_class; ?>">
                <?= $message; ?>
            </div>
        <?php endif; ?>
        <form method="POST" enctype="multipart/form-data">
            <label for="first_template">First Template (First Page):</label>
            <input type="file" name="first_template" id="first_template" required>

            <label for="second_template">Second Template (Last Page):</label>
            <input type="file" name="second_template" id="second_template" required>

            <button type="submit">Add Templates</button>
        </form>
        <br>
        <a href="list_templates.php">Back to Template List</a>
    </div>
</body>
</html>
