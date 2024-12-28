<?php
include 'db_connection.php';

if (isset($_POST['product_id'])) {
    $product_id = intval($_POST['product_id']);
    $sql = "SELECT featured_image FROM products WHERE id = $product_id";
    $result = $conn->query($sql);

    if ($result && $row = $result->fetch_assoc()) {
        echo $row['featured_image'];
    } else {
        echo ''; // No image found
    }
}
?>
