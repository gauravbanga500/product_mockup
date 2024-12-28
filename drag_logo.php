<?php
include 'db_connection.php'; // Include your database connection

// Fetch product details
$sql = "SELECT * FROM products";
$result = $conn->query($sql);

// Fetch branding options
$branding_options = [
    'Digital UV Printing',
    'Blind Embossing',
    'Golden Embossing',
    'Silver Embossing',
    'LOGO EFFECT - Digital UV Printing',
    'LOGO EFFECT - LASER ENGRAVING'
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Drag Logo</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
</head>
<body>
    <h1>Drag and Drop Logo onto Product</h1>

    <form action="save_product_with_logo.php" method="POST" enctype="multipart/form-data">
        <!-- Select Product -->
        <label for="product_id">Select Product:</label>
        <select name="product_id" id="product_id" onchange="loadProductImage(this.value)">
            <option value="">Select Product</option>
            <?php while ($row = $result->fetch_assoc()): ?>
                <option value="<?php echo $row['id']; ?>"><?php echo $row['name']; ?></option>
            <?php endwhile; ?>
        </select><br><br>

        <!-- Branding Options -->
        <label for="branding_option">Branding Option:</label>
        <select name="branding_option" id="branding_option" required>
            <?php foreach ($branding_options as $option): ?>
                <option value="<?php echo $option; ?>"><?php echo $option; ?></option>
            <?php endforeach; ?>
        </select><br><br>

        <!-- Logo Upload -->
        <label for="logo">Upload Logo:</label>
        <input type="file" name="logo" id="logo" required><br><br>

        <!-- Product Image and Logo Preview -->
        <div id="product_image" style="position: relative; width: 500px; height: 500px; border: 1px solid #ccc;">
            <img id="product_image_preview" src="" alt="Product Image" style="width: 100%; height: 100%; display: none;">
            <img id="logo_preview" src="#" alt="Logo" style="position: absolute; top: 0; left: 0; width: 50px; height: 50px; cursor: move; display: none;">
        </div><br><br>

        <!-- Hidden Fields to Save Logo Position -->
        <input type="hidden" name="logo_position_top" id="logo_position_top">
        <input type="hidden" name="logo_position_left" id="logo_position_left">

        <button type="submit">Save Product with Logo</button>
    </form>

    <script>
        // Handle logo upload
        document.getElementById('logo').onchange = function(e) {
            var reader = new FileReader();
            reader.onload = function(event) {
                $('#logo_preview').attr('src', event.target.result).show();
            };
            reader.readAsDataURL(this.files[0]);
        };

        // Load product image dynamically
        function loadProductImage(productId) {
            if (!productId) {
                $('#product_image_preview').hide();
                return;
            }
            $.ajax({
                url: 'get_product_image.php',
                type: 'POST',
                data: { product_id: productId },
                success: function(response) {
                    $('#product_image_preview').attr('src', response).show();
                }
            });
        }

        // Make logo draggable
        $('#logo_preview').draggable({
            containment: '#product_image',
            scroll: false,
            stop: function(event, ui) {
                $('#logo_position_top').val(ui.position.top);
                $('#logo_position_left').val(ui.position.left);
            }
        });
    </script>
</body>
</html>
