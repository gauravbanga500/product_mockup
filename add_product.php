<?php
include 'db_connection.php';
include 'admin_sidebar.php';

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
            width: 140px;
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
    <h1>Add New Product</h1>
    <form action="save_product.php" method="POST" enctype="multipart/form-data">
        <label for="name">Product Name:</label>
        <input type="text" name="name" id="name" required><br><br>

        <label for="description">Product Description:</label>
        <textarea name="description" id="description" required></textarea><br><br>

        <label for="featured_image">Featured Image:</label>
        <input type="file" name="featured_image" id="featured_image" required><br><br>

        <label for="category">Category:</label>
        <select name="category" id="category" required>
            <option value="">Select Category</option>
            <?php while ($row = $categories_result->fetch_assoc()): ?>
                <option value="<?php echo $row['id']; ?>"><?php echo $row['name']; ?></option>
            <?php endwhile; ?>
        </select><br><br>

       <label for="branding_option">Branding Option:</label>
        <select name="branding_option" id="branding_option" required>
            <option value="">Select Branding Option</option>
            <option value="digital-uv-printing">Digital UV Printing</option>
            <option value="blind-embossing">Blind Embossing</option>
            <option value="golden-embossing">Golden Embossing</option>
            <option value="silver-embossing">Silver Embossing</option>
            <option value="logo-effect-digital-uv">LOGO EFFECT - Digital UV Printing</option>
            <option value="logo-effect-laser-engraving">LOGO EFFECT - LASER ENGRAVING</option>
        </select><br><br>

        <div id="product_image">
            <img id="featured_image_preview" src="#" alt="Product Image" style="width: 650px; height: auto;">
            <img id="logo_preview" src="uploads/sample_logo.png" alt="Logo Preview">
        </div>

        <label for="logo_size">Logo Size:</label>
        <input type="range" id="logo_size" min="10" max="200" value="140">

        <label for="logo_rotation">Logo Rotation:</label>
        <input type="range" id="logo_rotation" min="0" max="360" value="0">
        
        <input type="hidden" name="logo_position" id="logo_position">
        <input type="hidden" name="logo_scale" id="logo_scale" value="1">
        <input type="hidden" name="logo_angle" id="logo_angle" value="0">
        <input type="hidden" name="logo_styles" id="logo_styles">


        <button type="submit">Save Product</button>
    </form>
    
    
<script>
  $(document).ready(function () {
    // Initialize logo data
    let logoData = {
        position: { top: 0, left: 0 },
        scale: 1,
        angle: 0
    };

    // Handle featured image preview
    $('#featured_image').on('change', function () {
        const reader = new FileReader();
        reader.onload = function (e) {
            $('#featured_image_preview').attr('src', e.target.result).show();
            $('#product_image').show();
            $('#logo_preview').show();
        };
        reader.readAsDataURL(this.files[0]);
    });

    // Make the logo draggable within the container
    $('#logo_preview').draggable({
        containment: '#product_image',
        stop: function (event, ui) {
            // Calculate position relative to the container
            const container = $('#product_image');
            const containerOffset = container.offset();
            const logoOffset = $(this).offset();

            const containerWidth = container.width();
            const containerHeight = container.height();

            const topPercent = ((logoOffset.top - containerOffset.top) / containerHeight) * 100;
            const leftPercent = ((logoOffset.left - containerOffset.left) / containerWidth) * 100;

            // Update logoData with clamped values
            logoData.position.top = Math.min(Math.max(topPercent, 0), 100).toFixed(2);
            logoData.position.left = Math.min(Math.max(leftPercent, 0), 100).toFixed(2);

            // Update hidden input with logo position
            $('#logo_position').val(JSON.stringify(logoData.position));
        }
    });
    
    // Apply branding effect
        $('#branding_option').on('change', function () {
            const selectedEffect = $(this).val();
            $('#logo_preview').attr('class', selectedEffect);
        });

    // Update logo size using slider
    $('#logo_size').on('input', function () {
        const newSize = $(this).val();
        $('#logo_preview').css({
            width: `${newSize}px`,
            height: 'auto',
        });

        // Update scale in logoData
        logoData.scale = newSize / 120; // Assuming 120 is the default size
        $('#logo_scale').val(logoData.scale);
    });

    // Update logo rotation using slider
    $('#logo_rotation').on('input', function () {
        const newAngle = $(this).val();
        $('#logo_preview').css('transform', `rotate(${newAngle}deg)`);

        // Update rotation in logoData
        logoData.angle = newAngle;
        $('#logo_angle').val(logoData.angle);
    });

    // Save all logo data before form submission
    $('form').on('submit', function () {
    const logoStyles = JSON.stringify({
        position: logoData.position,
        scale: logoData.scale,
        rotation: logoData.angle,
    });

    $('#logo_styles').val(logoStyles); // Set the hidden input value
});

});



</script>

</body>
</html>