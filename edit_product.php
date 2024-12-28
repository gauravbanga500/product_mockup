<?php
include 'db_connection.php';

// Fetch product details using ID
$product_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($product_id <= 0) {
    die("Invalid Product ID.");
}

$stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
$stmt->bind_param('i', $product_id);
$stmt->execute();
$result = $stmt->get_result();
$product = $result->fetch_assoc();

if (!$product) {
    die("Product not found.");
}

// Fetch categories
$categories_result = $conn->query("SELECT * FROM categories");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.13.0/jquery-ui.min.js"></script>
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.0/themes/base/jquery-ui.css">
    <style>
        #product_image {
            position: relative;
            width: 500px;
            height: auto;
            margin-top: 20px;
        }
        #logo_preview {
            position: absolute;
            width: 120px;
            height: auto;
            cursor: pointer;
        }
        /* CSS Effects */
        .digital-uv-printing {
            color: #e60012;
            text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.3); 
            filter: brightness(1.2) contrast(1.1);
        }
        .blind-embossing {
            color: #7d7d7d;
            text-shadow: 0px 0px 3px rgba(0, 0, 0, 0.4); 
            filter: grayscale(1) brightness(1.1);
        }
        .golden-embossing {
            filter: sepia(1) saturate(10) hue-rotate(15deg) brightness(1.1);
        }
        .silver-embossing {
            filter: grayscale(1) brightness(1.5) contrast(1.2);
        }
        .logo-effect-digital-uv {
            color: #e60012;
            text-shadow: 0px 0px 5px rgba(255, 0, 0, 0.6);
            filter: contrast(1.4) brightness(1.3);
        }
        .logo-effect-laser-engraving {
            color: #ffffff;
            text-shadow: 0px 0px 3px rgba(0, 0, 0, 0.8), 1px 1px 4px rgba(0, 0, 0, 0.5);
            filter: grayscale(1) brightness(0.9);
        }
    </style>
</head>
<body>
    <h1>Edit Product</h1>
    <form action="save_product.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">

        <label for="name">Product Name:</label>
        <input type="text" name="name" id="name" value="<?php echo htmlspecialchars($product['name']); ?>" required><br><br>

        <label for="description">Product Description:</label>
        <textarea name="description" id="description" required><?php echo htmlspecialchars($product['description']); ?></textarea><br><br>

        <label for="featured_image">Featured Image:</label>
        <input type="file" name="featured_image" id="featured_image"><br><br>
        <?php if (!empty($product['featured_image'])): ?>
            <img src="<?php echo htmlspecialchars($product['featured_image']); ?>" alt="Current Image" style="width: 150px;">
        <?php endif; ?><br><br>

        <label for="category">Category:</label>
        <select name="category" id="category" required>
            <option value="">Select Category</option>
            <?php while ($row = $categories_result->fetch_assoc()): ?>
                <option value="<?php echo $row['id']; ?>" <?php echo ($row['id'] == $product['categories']) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($row['name']); ?>
                </option>
            <?php endwhile; ?>
        </select><br><br>

        <!-- Logo Effect -->
        <label for="branding_option">Logo Effect:</label>
        <select name="branding_option" id="branding_option" required>
            <option value="">Select Logo Effect</option>
            <option value="digital-uv-printing" <?php echo ($product['branding_options'] == 'digital-uv-printing') ? 'selected' : ''; ?>>Digital UV Printing</option>
            <option value="blind-embossing" <?php echo ($product['branding_options'] == 'blind-embossing') ? 'selected' : ''; ?>>Blind Embossing</option>
            <option value="golden-embossing" <?php echo ($product['branding_options'] == 'golden-embossing') ? 'selected' : ''; ?>>Golden Embossing</option>
            <option value="silver-embossing" <?php echo ($product['branding_options'] == 'silver-embossing') ? 'selected' : ''; ?>>Silver Embossing</option>
            <option value="logo-effect-digital-uv" <?php echo ($product['branding_options'] == 'logo-effect-digital-uv') ? 'selected' : ''; ?>>LOGO EFFECT - Digital UV Printing</option>
            <option value="logo-effect-laser-engraving" <?php echo ($product['branding_options'] == 'logo-effect-laser-engraving') ? 'selected' : ''; ?>>LOGO EFFECT - LASER ENGRAVING</option>
        </select><br><br>

        <!-- Product Image for Drag-and-Drop -->
        <div id="product_image">
            <img id="featured_image_preview" src="<?php echo htmlspecialchars($product['featured_image']); ?>" alt="Product Image" style="width: 650px; height: auto;">
            <img id="logo_preview" src="<?php echo htmlspecialchars($product['logo_path'] ?: 'uploads/sample_logo.png'); ?>" alt="Logo Preview"style="width: 120px; height: auto;">
        </div>

        <input type="hidden" name="logo_position" id="logo_position" value='<?php echo htmlspecialchars($product['logo_positions']); ?>'>

        <button type="submit">Update Product</button>
    </form>

    <script>
        $(document).ready(function () {
            const savedLogoPosition = <?php echo $product['logo_positions'] ? $product['logo_positions'] : '{ "top": 0, "left": 0 }'; ?>;
            const $logoPreview = $('#logo_preview');

            // Set saved logo position
            $logoPreview.css({
                top: savedLogoPosition.top + '%',
                left: savedLogoPosition.left + '%',
            });

            // Make the logo draggable
            $logoPreview.draggable({
                containment: '#product_image',
                stop: function (event, ui) {
                    const containerWidth = $('#product_image').width();
                    const containerHeight = $('#product_image').height(); 

                    const topPercent = (ui.position.top / containerHeight) * 100;
                    const leftPercent = (ui.position.left / containerWidth) * 100;

                    $('#logo_position').val(JSON.stringify({ top: topPercent, left: leftPercent }));
                }
            });
        });
    </script>
    
      <script>
        $('#branding_option').on('change', function() {
            const selectedEffect = $(this).val();
            $('#logo_preview').attr('class', selectedEffect);
        });
    </script>
</body>
</html>
