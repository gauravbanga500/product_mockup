<?php
session_start();
include 'db_connection.php'; // Replace with your database connection file

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Check if fields are empty
    if (empty($email) || empty($password)) {
        header("Location: login.php?error=Please fill in all fields");
        exit();
    }

    // Validate email and password 
    $sql = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user && password_verify($password, $user['password'])) {
        // Login successful, set session
        $_SESSION['logged_in'] = true;
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];

        // Redirect to admin dashboard or desired page
        header("Location: user.php");
        exit();
    } else {
        // Login failed
        header("Location: login.php?error=Invalid email or password");
        exit();
    }
} else {
    // Redirect to login page if accessed directly
    header("Location: login.php");
    exit();
}
?>
