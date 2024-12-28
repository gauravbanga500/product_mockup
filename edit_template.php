<?php
require('db_connection.php');

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
