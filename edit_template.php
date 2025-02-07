<?php
require('db_connection.php');
include 'admin_sidebar.php';


$id = $_GET['id'] ?? null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $upload_dir = 'uploads/';
    $first_template_path = $_POST['existing_first_template'];
    $second_template_path = $_POST['existing_second_template'];

    if (!empty($_FILES['first_template']['tmp_name'])) {
        $first_template_name = $_FILES['first_template']['name'];
        $first_template_ext = pathinfo($first_template_name, PATHINFO_EXTENSION);
        $first_template_new_name = uniqid('template1_', true) . '.' . $first_template_ext;
        $first_template_path = $upload_dir . $first_template_new_name;
        move_uploaded_file($_FILES['first_template']['tmp_name'], $first_template_path);
    }

    if (!empty($_FILES['second_template']['tmp_name'])) {
        $second_template_name = $_FILES['second_template']['name'];
        $second_template_ext = pathinfo($second_template_name, PATHINFO_EXTENSION);
        $second_template_new_name = uniqid('template2_', true) . '.' . $second_template_ext;
        $second_template_path = $upload_dir . $second_template_new_name;
        move_uploaded_file($_FILES['second_template']['tmp_name'], $second_template_path);
    }

    $stmt = $conn->prepare("UPDATE templates SET first_template = ?, second_template = ? WHERE id = ?");
    $stmt->bind_param('ssi', $first_template_path, $second_template_path, $id);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        echo "Templates updated successfully.";
    } else {
        echo "Error: Failed to update templates.";
    }
}

$template = $conn->query("SELECT * FROM templates WHERE id = $id")->fetch_assoc();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Template</title> 
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        h1 { text-align: center; color: #333; }
        form { width: 50%; margin: 0 auto; background: #f9f9f9; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        label { font-weight: bold; display: block; margin-bottom: 8px; }
        input[type="file"] { margin-bottom: 15px; }
        button { background-color: #007bff; color: white; padding: 10px 15px; border: none; border-radius: 5px; cursor: pointer; }
        button:hover { background-color: #0056b3; }
        .success-message, .error-message { text-align: center; margin: 10px 0; padding: 10px; border-radius: 5px; }
        .success-message { background-color: #d4edda; color: #155724; }
        .error-message { background-color: #f8d7da; color: #721c24; }
    </style>
</head>
<body>
    <form method="POST" enctype="multipart/form-data">
        <label>First Template (First Page):</label>
        <input type="file" name="first_template"><br>
        <label>Second Template (Last Page):</label>
        <input type="file" name="second_template"><br>
        <input type="hidden" name="existing_first_template" value="<?= $template['first_template'] ?>">
        <input type="hidden" name="existing_second_template" value="<?= $template['second_template'] ?>">
        <button type="submit">Update Templates</button>
    </form>
</body>
</html>
