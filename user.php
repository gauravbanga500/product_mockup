<?php
session_start();
include 'check_login.php';
include 'db_connection.php';
include 'admin_sidebar.php';


// Check for success message in query parameters
$message = isset($_GET['message']) ? $_GET['message'] : '';


// Display success message from session, if available
if (!empty($_SESSION['message'])) {
    echo '<div class="alert alert-success" role="alert">' . htmlspecialchars($_SESSION['message']) . '</div>';
    unset($_SESSION['message']); // Clear the message after displaying it
}
?> 


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> 
    <title>User Management</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <h1 class="text-center">User Management</h1>
        <?php if (!empty($message)): ?>
    <div class="alert alert-success" role="alert">
        <?= htmlspecialchars($message); ?>
    </div>
        <?php endif; ?>

        <div class="text-end mb-3">
            <a href="create_user.php" class="btn btn-success">Add New User</a>
        </div>
        <table class="table table-bordered table-striped">
            <thead class="bg-primary text-white">
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Actions</th>
                </tr>
            </thead> 
            <tbody>
                <?php
                $sql = "SELECT * FROM users ORDER BY id ASC";
                $result = $conn->query($sql);

                if ($result->num_rows > 0):
                    while ($row = $result->fetch_assoc()):
                ?>
                <tr>
                    <td><?php echo $serial_number++; ?></td> <!-- Displaying serial number -->
                    <td><?php echo htmlspecialchars($row['name']); ?></td>
                    <td><?php echo htmlspecialchars($row['email']); ?></td>
                    <td><?php echo htmlspecialchars($row['user_role']); ?></td>
                    <td>
                        <a href="edit_user.php?id=<?php echo $row['id']; ?>" class="btn btn-warning btn-sm">Edit</a>
                        <a href="delete_user.php?id=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">Delete</a>
                    </td>
                </tr>
                <?php
                    endwhile;
                else:
                ?>
                <tr>
                    <td colspan="5" class="text-center">No users found</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
