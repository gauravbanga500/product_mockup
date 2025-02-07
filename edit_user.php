<?php
ob_start(); // Start output buffering
session_start();
include 'check_login.php';
include 'db_connection.php';
include 'admin_sidebar.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($_GET['id'])) {
    header('Location: user.php'); // Redirect to user management page if no user ID is provided
    exit();
}

$user_id = $_GET['id'];
$error = "";

// Fetch user details
$sql = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    header('Location: user.php?message=User not found');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $role = $_POST['user_role'];

    if (!empty($password)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $update_sql = "UPDATE users SET name = ?, email = ?, password = ?, user_role = ? WHERE id = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("ssssi", $name, $email, $hashed_password, $role, $user_id);
    } else {
        $update_sql = "UPDATE users SET name = ?, email = ?, user_role = ? WHERE id = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("sssi", $name, $email, $role, $user_id);
    }

    if ($update_stmt->execute()) {
        header('Location: user.php?message=User updated successfully');
        exit();
    } else {
        $error = "Failed to update user. " . $update_stmt->error;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit User</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <h1>Edit User</h1>
        <?php if ($error): ?>
            <p style="color: red;"><?= htmlspecialchars($error); ?></p>
        <?php endif; ?>
        <form action="" method="POST">
            <div class="mb-3">
                <label for="name" class="form-label">Name:</label> 
                <input type="text" id="name" name="name" class="form-control" value="<?= htmlspecialchars($user['name']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email:</label>
                <input type="email" id="email" name="email" class="form-control" value="<?= htmlspecialchars($user['email']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password (leave blank to keep current password):</label>
                <input type="password" id="password" name="password" class="form-control">
            </div>
            <div class="mb-3">
                <label for="user_role" class="form-label">Role:</label>
                <select id="user_role" name="user_role" class="form-control" required>
                    <option value="sub_admin" <?= $user['user_role'] === 'sub_admin' ? 'selected' : ''; ?>>Sub Admin</option>
                    <option value="employee" <?= $user['user_role'] === 'employee' ? 'selected' : ''; ?>>Employee</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Update</button>
            <a href="user.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</body>
</html>
