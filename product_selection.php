<?php
include 'db_connection.php'; // Include the database connection
include 'header.php'; // Include header


// Fetch categories for the category selection dropdown
$sql_categories = "SELECT * FROM categories";
$categories = $conn->query($sql_categories);

// Fetch products for the product selection dropdown
$sql_products = "SELECT * FROM products";
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
</head>
<body>
    <main class="main-content">
    <h1>Select Products for Mockup</h1>
    <form action="generate_pdf.php" method="POST" enctype="multipart/form-data" class="product-form">
        <!-- Select option to choose products -->
        <label for="product_option">Choose products:</label><br>
        <input type="radio" name="product_option" value="individual" id="individual" checked> Select products individually
        <input type="radio" name="product_option" value="category" id="category"> Select by category<br><br>

        <!-- Dropdown to select product categories -->
        <div id="category_selection" style="display: none;">
            <label for="category">Select Category:</label>
            <select name="category" id="category">
                <option value="">Choose a category</option>
                <?php while ($category = $categories->fetch_assoc()): ?>
                    <option value="<?php echo $category['id']; ?>"><?php echo $category['name']; ?></option>
                <?php endwhile; ?>
            </select><br><br>
        </div>

        <!-- Dropdown to select individual products -->
        <div id="product_selection">
            <label for="products">Select Products:</label><br>
            <select name="products[]" id="products" multiple size="5">
                <?php while ($product = $products->fetch_assoc()): ?>
                    <option value="<?php echo $product['id']; ?>"><?php echo $product['name']; ?></option>
                <?php endwhile; ?>
            </select><br><br>
        </div>

        <!-- Upload logo -->
        <label for="logo">Upload Logo:</label>
        <input type="file" name="logo" id="logo" required><br><br>

        <!-- Submit Button -->
        <button type="submit" class="submit-btn">Generate PDF</button>
    </form>

    <script>
        // Toggle the visibility of product selection and category selection
        $('input[name="product_option"]').on('change', function() {
            if ($('#individual').is(':checked')) {
                $('#product_selection').show();
                $('#category_selection').hide();
            } else {
                $('#product_selection').hide();
                $('#category_selection').show();
            }
        });
    </script>
    </main>
</body>
</html>
<?php include 'footer.php'; // Include footer ?>

