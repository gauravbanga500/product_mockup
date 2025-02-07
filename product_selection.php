<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    echo "Please login first. <a href='login.php'>Login now</a>";
    exit();
}

include 'db_connection.php'; // Include the database connection
include 'header.php'; // Include header 
include 'check_login.php';

// Fetch categories for the category selection dropdown
$sql_categories = "SELECT * FROM categories";
$categories = $conn->query($sql_categories);

// Fetch products for the product selection grid
$sql_products = "SELECT * FROM products ORDER BY id ASC";
$products = $conn->query($sql_products);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Select Product</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        .product-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
        }
        .product-item {
            width: calc(20% - 15px);
            text-align: center;
        }
        .product-item img {
            width: 150px;
            height: auto;
            display: block;
            margin: auto;
        }
        .product-item input {
            margin-top: 5px;
        }
      .product-form {
        background: #f9f9f9;
        border: 1px solid #ddd;
        border-radius: 8px;
        padding: 20px;
        max-width: 1200px;
        width: 100%;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

    </style>
</head>

<body>
    <main class="container mt-4">
        <h1 class="text-center">Select Products for Mockup</h1>
        <form action="generate_pdf.php" method="POST" enctype="multipart/form-data" class="product-form">
            <label for="product_option">Choose products:</label><br>
            <input type="radio" name="product_option" value="individual" id="individual" checked> Select products individually
            <input type="radio" name="product_option" value="category" id="category"> Select by category
            <br><br>

            <div id="category_selection" style="display: none;">
                <label for="category">Select Category:</label>
                <select name="category" id="category" class="form-select">
                    <option value="">Choose a category</option>
                    <?php while ($category = $categories->fetch_assoc()): ?>
                    <option value="<?php echo $category['id']; ?>"><?php echo $category['name']; ?></option>
                    <?php endwhile; ?>
                </select>
                <br>
            </div>

            <div id="product_selection">
                <label>Select Products:</label>
                <div class="product-grid">
                    <?php while ($product = $products->fetch_assoc()): ?>
                    <div class="product-item">
                        <img src="<?php echo $product['featured_image']; ?>" alt="<?php echo $product['name']; ?>">
                        <p><?php echo $product['name']; ?></p>
                        <input type="checkbox" name="products[]" value="<?php echo $product['id']; ?>">
                    </div>
                    <?php endwhile; ?>
                </div>
            </div>
            <br>

            <label for="logo">Upload Logo:</label>
            <input type="file" name="logo" id="logo" class="form-control" required>
            <br>
            
            <button type="submit" class="btn btn-primary">Generate PDF</button>
        </form>
    </main>

    <script>
        $(document).ready(function() {
            $('input[name="product_option"]').on('change', function() {
                if ($('#individual').is(':checked')) {
                    $('#product_selection').show();
                    $('#category_selection').hide();
                } else {
                    $('#product_selection').hide();
                    $('#category_selection').show();
                }
            });
        });
    </script>
</body>
</html>
<?php include 'footer.php'; ?>