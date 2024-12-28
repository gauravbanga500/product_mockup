<?php
// Database connection settings
$servername = "localhost:3306"; // Replace with your server hostname
$username = "easywebindia_main"; // Replace with your database username
$password = '=}_)p5k+xM$n'; // Wrap the password in single quotes to handle special characters
$dbname = "easywebindia_product_mockup"; // Replace with your database name

// Enable error reporting for debugging
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    // Create a connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Set character encoding
    $conn->set_charset("utf8");
} catch (Exception $e) {
    die("Connection failed: " . $e->getMessage());
}
?>
