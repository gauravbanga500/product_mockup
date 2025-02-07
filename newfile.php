<?php
include 'db_connection.php';
include 'admin_sidebar.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Fetch categories
$categories_result = $conn->query("SELECT * FROM categories");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product</title>
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
    </style>
</head>

<body style="font-family: Arial, sans-serif; background-color: #f4f4f9; color: #333;">
    <div style="max-width: 800px; margin: 50px auto; background-color: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);">
        <h1 style="text-align: center; color: #4CAF50;">Add New Product</h1>

        <form action="save_product.php" method="POST" enctype="multipart/form-data" style="font-size: 16px;">
            <div style="margin-bottom: 15px;">
                <label for="name" style="font-weight: bold; margin-bottom: 5px; display: block;">Product Name:</label>
                <input type="text" name="name" id="name" required
                    style="width: 100%; padding: 10px; border-radius: 5px; border: 1px solid #ccc;">
            </div>

            <div style="margin-bottom: 15px;">
                <label for="description" style="font-weight: bold; margin-bottom: 5px; display: block;">Product
                    Description:</label>
                <textarea name="description" id="description" required
                    style="width: 100%; padding: 10px; border-radius: 5px; border: 1px solid #ccc;"></textarea>
            </div>

            <div style="margin-bottom: 15px;">
                <label for="featured_image" style="font-weight: bold; margin-bottom: 5px; display: block;">Featured
                    Image:</label>
                <input type="file" name="featured_image" id="featured_image" required
                    style="padding: 10px; border-radius: 5px; border: 1px solid #ccc;">
            </div>

            <div style="margin-bottom: 15px;">
                <label for="category" style="font-weight: bold; margin-bottom: 5px; display: block;">Category:</label>
                <select name="category" id="category" required
                    style="width: 100%; padding: 10px; border-radius: 5px; border: 1px solid #ccc;">
                    <option value="">Select Category</option>
                    <?php while ($row = $categories_result->fetch_assoc()): ?>
                        <option value="<?php echo $row['id']; ?>"><?php echo $row['name']; ?></option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div style="margin-bottom: 15px; text-align: center;">
                <div id="product_image" style="position: relative; width: 500px; height: auto;">
                    <img id="featured_image_preview" src="#" alt="Product Image" style="width: 650px; height: auto;">
                    <img id="logo_preview" src="uploads/sample_logo.png" alt="Logo Preview"
                        style="position: absolute; width: 120px; height: auto; cursor: pointer;">
                </div>

                <label for="logo_width" style="font-weight: bold;">Logo Width (px):</label>
                <input type="number" id="logo_width" placeholder="e.g., 150" min="10"
                    style="width: 100%; padding: 10px; border-radius: 5px; margin-top: 10px;">

                <label for="logo_height" style="font-weight: bold; margin-top: 10px;">Logo Height (px):</label>
                <input type="number" id="logo_height" placeholder="e.g., 50" min="10"
                    style="width: 100%; padding: 10px; border-radius: 5px; margin-top: 10px;">
            </div>

            <div style="text-align: center;">
                <button type="submit"
                    style="background-color: #4CAF50; color: #fff; padding: 12px 20px; border-radius: 5px; border: none; cursor: pointer; font-size: 16px; margin-top: 20px;">Save
                    Product</button>
            </div>
        </form>

        <script>
        $(document).ready(function() {
            const logoPreview = $('#logo_preview');

            $('#logo_width').on('input', function() {
                const width = $(this).val();
                const height = $('#logo_height').val();
                logoPreview.css({ width: `${width}px`, height: height ? `${height}px` : 'auto' });
            });

            $('#logo_height').on('input', function() {
                const height = $(this).val();
                const width = $('#logo_width').val();
                logoPreview.css({ height: `${height}px`, width: width ? `${width}px` : 'auto' });
            });

            // Handle featured image preview
            $('#featured_image').on('change', function() {
                const reader = new FileReader();
                reader.onload = function(e) {
                    $('#featured_image_preview').attr('src', e.target.result).show();
                    $('#product_image').show();
                    $('#logo_preview').show();
                };
                reader.readAsDataURL(this.files[0]);
            });
        });
        </script>

</body>

</html>
