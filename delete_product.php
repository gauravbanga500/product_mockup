<?php
include 'db_connection.php';

$product_id = isset($_GET['id']) ? intval($_GET['id']) : null;

if ($product_id) {
    $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
    $stmt->bind_param('i', $product_id);

    if ($stmt->execute()) {
        header("Location: product_list.php");
        exit;
    } else {
        die("Error: Failed to delete the product - " . $stmt->error);
    }
} else {
    die("Error: Product ID is missing.");
}
?>
