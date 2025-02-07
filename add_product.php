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

                <label for="logo_size" style="font-weight: bold;">Logo Size:</label>
                <input type="range" id="logo_size" min="10" max="200" value="120"
                    style="width: 100%; margin-top: 10px;">

                <label for="logo_rotation" style="font-weight: bold; margin-top: 15px;">Logo Rotation:</label>
                <input type="range" id="logo_rotation" min="0" max="360" value="0"
                    style="width: 100%; margin-top: 10px;">

                <br><br>
                <label for="logo_color_picker" style="font-weight: bold;">Select Logo Color:</label>
                <input type="color" id="logo_color_picker" value="#ff0000"
                    style="margin-top: 10px; margin-bottom: 10px;">

                <label for="logo_hex_input" style="font-weight: bold;">Hex Color Code:</label>
                <input type="text" id="logo_hex_input" value="#ff0000" maxlength="7" pattern="#[a-fA-F0-9]{6}"
                    title="Enter a valid hex code (#RRGGBB)" style="width: 100%; padding: 10px; border-radius: 5px;">
            </div>

            <input type="hidden" name="logo_position" id="logo_position">
            <input type="hidden" name="hex_color" id="hex_color">
            <input type="hidden" name="logo_scale" id="logo_scale" value="1">
            <input type="hidden" name="logo_angle" id="logo_angle" value="0">
            <input type="hidden" name="logo_styles" id="logo_styles">
            <!-- Hidden fields to store logo dimensions -->
            <input type="hidden" name="logo_width" id="hidden_logo_width">
            <input type="hidden" name="logo_height" id="hidden_logo_height">


            <div style="text-align: center;">
                <button type="submit"
                    style="background-color: #4CAF50; color: #fff; padding: 12px 20px; border-radius: 5px; border: none; cursor: pointer; font-size: 16px; margin-top: 20px;">Save
                    Product</button>
            </div>
        </form>


    <script>
    $(document).ready(function() {
        // Initialize logo data
        const canvas = document.createElement('canvas');
        const ctx = canvas.getContext('2d');
        const logoImg = document.getElementById('logo_preview');
        let logoData = {
            position: {
                top: 0,
                left: 0
            },
            scale: 1,
            angle: 0
        };


       function applyColorToLogo(hexColor) {
            canvas.width = logoImg.naturalWidth;
            canvas.height = logoImg.naturalHeight;

            ctx.clearRect(0, 0, canvas.width, canvas.height);
            ctx.drawImage(logoImg, 0, 0);

            const imgData = ctx.getImageData(0, 0, canvas.width, canvas.height);
            const color = hexToRgb(hexColor);

            for (let i = 0; i < imgData.data.length; i += 4) {
                const alpha = imgData.data[i + 3];
                if (alpha > 0) {
                    imgData.data[i] = color.r;
                    imgData.data[i + 1] = color.g;
                    imgData.data[i + 2] = color.b;
                }
            }

            ctx.putImageData(imgData, 0, 0);
            $('#logo_preview').attr('src', canvas.toDataURL());
        }

        function hexToRgb(hex) {
            const bigint = parseInt(hex.slice(1), 16);
            return {
                r: (bigint >> 16) & 255,
                g: (bigint >> 8) & 255,
                b: bigint & 255,
            };
        }

        $('#logo_color_picker').on('input', function () {
            const color = $(this).val();
            $('#logo_hex_input').val(color);
            applyColorToLogo(color);
        });

        $('#logo_hex_input').on('input', function () {
            const hexColor = $(this).val();
            if (/^#[0-9A-Fa-f]{6}$/.test(hexColor)) {
                $('#logo_color_picker').val(hexColor);
                applyColorToLogo(hexColor);
            }
        });


        
        // Update the hidden input field when color changes
        $('#logo_color_picker, #logo_hex_input').on('input', function () {
            const hexColor = $('#logo_hex_input').val();
            $('#hex_color').val(hexColor); // Set the color to the hidden input field
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

        // Make the logo draggable within the container
        $('#logo_preview').draggable({
            containment: '#product_image',
            stop: function(event, ui) {
                // Calculate position relative to the container
                const container = $('#product_image');
                const containerOffset = container.offset();
                const logoOffset = $(this).offset();

                const containerWidth = container.width();
                const containerHeight = container.height();
                const topPercent = ((logoOffset.top - containerOffset.top) / containerHeight) * 100;
                const leftPercent = ((logoOffset.left - containerOffset.left) / containerWidth) *
                    100;

                // Update logoData with clamped values
                logoData.position.top = Math.min(Math.max(topPercent, 0), 100).toFixed(2);
                logoData.position.left = Math.min(Math.max(leftPercent, 0), 100).toFixed(2);

                logoData.position.top = $(this).css('top');
                logoData.position.left = $(this).css('left');

                // Update hidden input with logo position
                $('#logo_position').val(JSON.stringify(logoData.position));
            }
        });

        // Apply branding effect
        $('#branding_option').on('change', function() {
            const selectedEffect = $(this).val();
            $('#logo_preview').attr('class', selectedEffect);
        });

        // Update logo size using slider
        $('#logo_size').on('input', function() {
    const newSize = $(this).val();
    $('#logo_preview').css({
        width: `${newSize}px`, // Corrected with backticks for string interpolation
        height: 'auto',
    });
            // Update scale in logoData
            logoData.scale = newSize / 120; // Assuming 120 is the default size
            $('#logo_scale').val(logoData.scale);
        });

        // Update logo rotation using slider
       $('#logo_rotation').on('input', function() {
    const newAngle = $(this).val();
    $('#logo_preview').css('transform', `rotate(${newAngle}deg)`); // Corrected with backticks for string interpolation

            // Update rotation in logoData
            logoData.angle = newAngle;
            $('#logo_angle').val(logoData.angle);
        });

        // Save all logo data before form submission
        $('form').on('submit', function() {
            const logoStyles = JSON.stringify({
                position: logoData.position,
                scale: logoData.scale,
                rotation: logoData.angle,
            });

             $('#hex_color').val($('#logo_hex_input').val());
             $('#hidden_logo_width').val($('#logo_width').val());
             $('#hidden_logo_height').val($('#logo_height').val());
            $('#logo_styles').val(logoStyles); // Set the hidden input value
        });
        
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