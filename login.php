<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include 'db_connection.php';

// Redirect logged-in users based on their user_role
if (isset($_SESSION['user_id']) && isset($_SESSION['user_role'])) {
    if ($_SESSION['user_role'] === 'employee') {
        header('Location: https://easywebindia.com/website/product-mockup/product_selection.php');
        exit();
    } elseif ($_SESSION['user_role'] === 'admin') {
        header('Location: https://easywebindia.com/website/product-mockup');
        exit();
    }
    // If user_role is invalid, destroy the session and force re-login
    session_destroy();
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (empty($email) || empty($password)) {
        $error = "Email and password are required.";
    } else {
        $sql = "SELECT * FROM users WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            if (!empty($user['user_role'])) {
                $_SESSION['user_role'] = $user['user_role'];

                // Redirect based on user role
                if ($user['user_role'] === 'employee') {
                    header('Location: https://easywebindia.com/website/product-mockup/product_selection.php');
                } elseif ($user['user_role'] === 'admin') {
                    header('Location: https://easywebindia.com/website/product-mockup');
                } else {
                    $error = "Unknown user role assigned. Please contact admin.";
                }
            } else {
                $error = "User role is missing. Please contact admin.";
            }
            exit();
        } else {
            $error = "Invalid email or password.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-4">
                <div class="card shadow">
                    <div class="card-header text-center bg-primary text-white">
                        <h4>User Login</h4>
                    </div>
                    <div class="card-body">
                        <!-- Display error message -->
                        <?php if (isset($error)): ?>
                            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                        <?php endif; ?>
                        <form action="" method="POST">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" name="email" id="email" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" name="password" id="password" class="form-control" required>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Login</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
