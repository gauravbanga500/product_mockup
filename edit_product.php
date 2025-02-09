<?php
include 'db_connection.php';
include 'admin_sidebar.php';

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
$product['logo_styles'] = json_decode($product['logo_styles']);
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
        body {
            font-family: Arial, sans-serif;
            background-color: #f7f7f7;
            color: #333;
            margin: 0;
            padding: 20px;
        }

        h1 {
            text-align: center;
            color: #007bff;
        }

        form {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 20px;
            width: 60%;
            margin: auto;
        }

        label {
            font-weight: bold;
            display: block;
            margin-top: 10px;
            color: #555;
        }

        input[type="text"],
        input[type="file"],
        select,
        textarea {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }

        input[type="range"] {
            width: 100%;
            margin-top: 5px;
        }

        button {
            background-color: #007bff;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            display: block;
            margin-top: 20px;
            width: 100%;
        }

        button:hover {
            background-color: #0056b3;
        }

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

<body>
    <h1>Edit Product</h1>
    <form action="save_product.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">

        <div class="form-section">
            <label for="name">Product Name:</label>
            <input type="text" name="name" id="name" value="<?php echo htmlspecialchars($product['name']); ?>" required>
        </div>

        <div class="form-section">
            <label for="description">Product Description:</label>
            <textarea name="description" id="description" required><?php echo htmlspecialchars($product['description']); ?></textarea>
        </div>

        <div class="form-section">
            <label for="featured_image">Featured Image:</label>
            <input type="file" name="featured_image" id="featured_image">
            <?php if (!empty($product['featured_image'])): ?>
            <img src="<?php echo htmlspecialchars($product['featured_image']); ?>" alt="Current Image" style="width: 150px; margin-top: 10px;">
            <?php endif; ?>
        </div>

        <div class="form-section">
            <label for="category">Category:</label>
            <select name="category" id="category" required>
                <option value="">Select Category</option>
                <?php while ($row = $categories_result->fetch_assoc()): ?>
                <option value="<?php echo $row['id']; ?>"
                    <?php echo ($row['id'] == $product['categories']) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($row['name']); ?>
                </option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="logo-section">
            <div id="product_image">
                <img id="featured_image_preview" src="<?php echo htmlspecialchars($product['featured_image']); ?>" alt="Product Image" style="width: 650px; height: auto;">
                <img id="logo_preview" src="<?php echo htmlspecialchars($product['logo_path'] ?: 'uploads/sample_logo.png'); ?>" alt="Logo Preview">
            </div>
        </div>

        <div class="form-section">
            <label for="logo_size">Logo Size:</label>
            <input type="range" id="logo_size" min="10" max="200" value="120">
        </div>

        <div class="form-section">
            <label for="logo_rotation">Logo Rotation:</label>
            <input type="range" id="logo_rotation" min="0" max="360" value="0">
        </div>
        
        <label for="logo_color_picker">Select Logo Color:</label>
        <input type="color" id="logo_color_picker" value="<?php echo $product['hex_color'] ?? ''; ?>">

        <label for="logo_hex_input">Hex Color Code:</label>
        <input type="text" id="logo_hex_input" value="<?php echo $product['hex_color'] ?? ''; ?>" maxlength="7" pattern="#?[a-fA-F0-9]{6}" title="Enter a valid hex code (#RRGGBB)">
        <br><br>

        <input type="hidden" name="logo_position" id="logo_position" value='<?php echo htmlspecialchars($product['logo_positions']); ?>'>
        <input type="hidden" name="hex_color" id="hex_color">
        <input type="hidden" name="logo_scale" id="logo_scale" value="<?php echo $product['logo_styles']->scale ?>">
        <input type="hidden" name="logo_angle" id="logo_angle" value="<?php echo $product['logo_styles']->rotation ?>">
        <input type="hidden" name="logo_styles" id="logo_styles">

        <button type="submit">Save Product</button>
    </form>

    <script>
    $(document).ready(function() {
         const canvas = document.createElement('canvas');
         const ctx = canvas.getContext('2d');
         const logoImg = document.getElementById('logo_preview');
        const savedLogoPosition =
            <?php echo $product['logo_positions'] ? $product['logo_positions'] : '{ "top": 0, "left": 0 }'; ?>;
            
 // Initialize saved color from the database, empty string if not set
    const savedHexColor = "<?php echo $hex_color; ?>";

    // Set initial color picker and hex input values if a color is available
    if (savedHexColor) {
        $('#logo_color_picker').val(savedHexColor);
        $('#logo_hex_input').val(savedHexColor);
        applyColorToLogo(savedHexColor);
    }
    
    
        // Initialize logo data
        var logoData = {
            position: {
                top: savedLogoPosition.top,
                left: savedLogoPosition.left
            },
            scale: <?php echo $product['logo_styles']->scale ?>,
            angle: <?php echo $product['logo_styles']->rotation ?>
        };

       
    function applyColorToLogo(hexColor) {
        const canvas = document.createElement('canvas');
        const ctx = canvas.getContext('2d');
        const logoImg = document.getElementById('logo_preview');

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
 
              $('#logo_color_picker, #logo_hex_input').on('input', function () {
        const hexColor = $('#logo_hex_input').val();
        $('#hex_color').val(hexColor);
    });

 // Set initial color picker and hex input values
    $('#logo_color_picker').val(savedHexColor);
    $('#logo_hex_input').val(savedHexColor);

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

        const $logoPreview = $('#logo_preview');
        // Set saved logo position
        $logoPreview.css({
            top: logoData.position.top + 'px',
            left: logoData.position.left + 'px',
            width: `${logoData.scale * 120}px`,
            height: 'auto',
            'transform': `rotate(${logoData.angle}deg)`
        });

        $("#logo_size").val(logoData.scale * 120);
        // Make the logo draggable
        $logoPreview.draggable({
            containment: '#product_image',
            stop: function(event, ui) {
                // Calculate position relative to the container
                const container = $('#product_image');
                const containerOffset = container.offset();
                const logoOffset = $(this).offset();

                const containerWidth = container.width();
                const containerHeight = container.height();

                console.log($(this).css('top'))
                console.log($(this).css('left'))


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

     

        // Update logo size using slider
        $('#logo_size').on('input', function() {
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
        $('#logo_rotation').on('input', function() {
            const newAngle = $(this).val();
            $('#logo_preview').css('transform', `rotate(${newAngle}deg)`);

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
            $('#logo_styles').val(logoStyles); // Set the hidden input value
        });

    });
    </script>
</body>

</html>
