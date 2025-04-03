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
<<<<<<< HEAD
?>
=======


?>


>>>>>>> c38e2af (updated code)
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
<<<<<<< HEAD
    <style>
        /* Overall page styling */
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

        .form-section {
            margin-bottom: 20px;
        }

        .logo-section {
            /*display: flex;*/
            justify-content: center;
            align-items: center;
            margin-top: 20px;
        }

        #product_image {
        position: relative;
        width: 500px;
        height: auto;
        margin-top: 20px;
    }
        #logo_preview {
=======
   <style>
    /* Overall page styling */
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

    .form-section {
        margin-bottom: 20px;
    }

    .logo-section {
        /*display: flex;*/
        justify-content: center;
        align-items: center;
        margin-top: 20px;
    }

    #product_image {
        position: relative;
        width: 650px;
        height: auto;
        margin-top: 20px;
    }

    #logo_preview {
>>>>>>> c38e2af (updated code)
        position: absolute;
        width: 120px;
        height: auto;
        cursor: pointer;
    }

<<<<<<< HEAD
        /* Styling for sliders */
        /*input[type="range"] {*/
        /*    width: 100%;*/
        /*    margin-top: 10px;*/
        /*}*/

       /* CSS Effects */
=======
    /* Styling for sliders */
    /*input[type="range"] {*/
    /*    width: 100%;*/
    /*    margin-top: 10px;*/
    /*}*/

    /* CSS Effects */
>>>>>>> c38e2af (updated code)
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

        <div class="form-section">
            <label for="name">Product Name:</label>
            <input type="text" name="name" id="name" value="<?php echo htmlspecialchars($product['name']); ?>" required>
        </div>

        <div class="form-section">
            <label for="description">Product Description:</label>
<<<<<<< HEAD
            <textarea name="description" id="description" required><?php echo htmlspecialchars($product['description']); ?></textarea>
=======
            <textarea name="description" id="description"
                required><?php echo htmlspecialchars($product['description']); ?></textarea>
>>>>>>> c38e2af (updated code)
        </div>

        <div class="form-section">
            <label for="featured_image">Featured Image:</label>
            <input type="file" name="featured_image" id="featured_image">
            <?php if (!empty($product['featured_image'])): ?>
<<<<<<< HEAD
            <img src="<?php echo htmlspecialchars($product['featured_image']); ?>" alt="Current Image" style="width: 150px; margin-top: 10px;">
=======
            <img src="<?php echo htmlspecialchars($product['featured_image']); ?>" alt="Current Image"
                style="width: 150px; margin-top: 10px;">
>>>>>>> c38e2af (updated code)
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

<<<<<<< HEAD
        <!-- Logo Effect -->
        <div class="form-section">
            <label for="branding_option">Logo Effect:</label>
            <select name="branding_option" id="branding_option" required>
                <option value="">Select Logo Effect</option>
                <option value="digital-uv-printing" <?php echo ($product['branding_options'] == 'digital-uv-printing') ? 'selected' : ''; ?>>Digital UV Printing</option>
                <option value="blind-embossing" <?php echo ($product['branding_options'] == 'blind-embossing') ? 'selected' : ''; ?>>Blind Embossing</option>
                <option value="golden-embossing" <?php echo ($product['branding_options'] == 'golden-embossing') ? 'selected' : ''; ?>>Golden Embossing</option>
                <option value="silver-embossing" <?php echo ($product['branding_options'] == 'silver-embossing') ? 'selected' : ''; ?>>Silver Embossing</option>
                <option value="logo-effect-digital-uv" <?php echo ($product['branding_options'] == 'logo-effect-digital-uv') ? 'selected' : ''; ?>>LOGO EFFECT - Digital UV Printing</option>
                <option value="logo-effect-laser-engraving" <?php echo ($product['branding_options'] == 'logo-effect-laser-engraving') ? 'selected' : ''; ?>>LOGO EFFECT - LASER ENGRAVING</option>
            </select>
        </div>

        <div class="logo-section">
            <div id="product_image">
                <img id="featured_image_preview" src="<?php echo htmlspecialchars($product['featured_image']); ?>" alt="Product Image" style="width: 650px; height: auto;">
                <img id="logo_preview" src="<?php echo htmlspecialchars($product['logo_path'] ?: 'uploads/sample_logo.png'); ?>" alt="Logo Preview">
            </div>
        </div>

=======
       
  <div class="logo-section">
            <div id="product_image">
                <img id="featured_image_preview" src="<?php echo htmlspecialchars($product['featured_image']); ?>"
                    alt="Product Image" style="width: 650px; height: auto;">
                <img id="logo_preview"
                    src="<?php echo htmlspecialchars($product['logo_path'] ?: 'uploads/sample_logo.png'); ?>"
                    alt="Logo Preview">
            </div>
        </div>
        
>>>>>>> c38e2af (updated code)
        <div class="form-section">
            <label for="logo_size">Logo Size:</label>
            <input type="range" id="logo_size" min="10" max="200" value="120">
        </div>

        <div class="form-section">
            <label for="logo_rotation">Logo Rotation:</label>
            <input type="range" id="logo_rotation" min="0" max="360" value="0">
        </div>
<<<<<<< HEAD
        
          <br><br>
=======

        <br><br>
>>>>>>> c38e2af (updated code)
        <label for="logo_color_picker">Select Logo Color:</label>
        <input type="color" id="logo_color_picker" value="<?php echo $product['hex_color'] ?? '#ff0000'; ?>">

        <label for="logo_hex_input">Hex Color Code:</label>
<<<<<<< HEAD
        <input type="text" id="logo_hex_input" value="<?php echo $product['hex_color'] ?? '#ff0000'; ?>" maxlength="7" pattern="#?[a-fA-F0-9]{6}" title="Enter a valid hex code (#RRGGBB)">
        <br><br>

        <input type="hidden" name="logo_position" id="logo_position" value= '<?php echo htmlspecialchars($product['logo_positions']); ?>'>
=======
        <input type="text" id="logo_hex_input" value="<?php echo $product['hex_color'] ?? '#ff0000'; ?>" maxlength="7"
            pattern="#?[a-fA-F0-9]{6}" title="Enter a valid hex code (#RRGGBB)">
        <br><br>

        <input type="hidden" name="logo_position" id="logo_position"
            value='<?php echo htmlspecialchars($product['logo_positions']); ?>'>
>>>>>>> c38e2af (updated code)
        <input type="hidden" name="hex_color" id="hex_color">
        <input type="hidden" name="logo_scale" id="logo_scale" value="<?php echo $product['logo_styles']->scale ?>">
        <input type="hidden" name="logo_angle" id="logo_angle" value="<?php echo $product['logo_styles']->rotation ?>">
        <input type="hidden" name="logo_styles" id="logo_styles">

        <button type="submit">Save Product</button>
    </form>

    <script>
    $(document).ready(function() {
<<<<<<< HEAD
         const canvas = document.createElement('canvas');
         const ctx = canvas.getContext('2d');
         const logoImg = document.getElementById('logo_preview');
        const savedLogoPosition =
            <?php echo $product['logo_positions'] ? $product['logo_positions'] : '{ "top": 0, "left": 0 }'; ?>;
            
               // Initialize saved color from database 
    const savedHexColor = "<?php echo $product['hex_color'] ?? ''; ?>";
    
    
=======
        const canvas = document.createElement('canvas');
        const ctx = canvas.getContext('2d');
        const logoImg = document.getElementById('logo_preview');
        const savedLogoPosition =
            <?php echo $product['logo_positions'] ? $product['logo_positions'] : '{ "top": 0, "left": 0 }'; ?>;

        // Initialize saved color from database 
        const savedHexColor = "<?php echo $product['logo_hex_color'] ?? ''; ?>";


>>>>>>> c38e2af (updated code)
        // Initialize logo data
        var logoData = {
            position: {
                top: savedLogoPosition.top,
                left: savedLogoPosition.left
            },
            scale: <?php echo $product['logo_styles']->scale ?>,
            angle: <?php echo $product['logo_styles']->rotation ?>
        };

<<<<<<< HEAD
       
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
=======

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

        $('#logo_color_picker').on('input', function() {
            const color = $(this).val();
            $('#logo_hex_input').val(color);
            applyColorToLogo(color);
        });

        $('#logo_hex_input').on('input', function() {
            const hexColor = $(this).val();
            if (/^#[0-9A-Fa-f]{6}$/.test(hexColor)) {
                $('#logo_color_picker').val(hexColor);
                applyColorToLogo(hexColor);
            }
        });

        $('#logo_color_picker, #logo_hex_input').on('input', function() {
            const hexColor = $('#logo_hex_input').val();
            $('#hex_color').val(hexColor);
        });

        // Set initial color picker and hex input values
        $('#logo_color_picker').val(savedHexColor);
        $('#logo_hex_input').val(savedHexColor);

       $('#featured_image').on('change', function() {
            const reader = new FileReader();
            reader.onload = function(e) {
                $('#featured_image_preview').attr('src', e.target.result).show();
                $('#product_image').show();
                $('#logo_preview').show();
            };
            reader.readAsDataURL(this.files[0]);
        });
>>>>>>> c38e2af (updated code)

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

<<<<<<< HEAD
=======
        <?php if($product['branding_options'] != "") { ?>
        $('#logo_preview').attr('class', '<?php echo $product['branding_options'];?>');
        <?php } ?>

        <?php if ($product['logo_hex_color'] != "") { ?>
        applyColorToLogo('<?php echo $product['logo_hex_color'] ?? '#ff0000';?>');
        <?php } ?>
>>>>>>> c38e2af (updated code)
        // Apply branding effect
        $('#branding_option').on('change', function() {
            const selectedEffect = $(this).val();
            $('#logo_preview').attr('class', selectedEffect);
        });

<<<<<<< HEAD
        // Update logo size using slider
=======
         // Update logo size using slider
>>>>>>> c38e2af (updated code)
        $('#logo_size').on('input', function() {
            const newSize = $(this).val();
            $('#logo_preview').css({
                width: `${newSize}px`,
                height: 'auto',
            });

            // Update scale in logoData
            logoData.scale = newSize / 120; // Assuming 120 is the default size
<<<<<<< HEAD
            $('#logo_scale').val(logoData.scale); 
=======
            $('#logo_scale').val(logoData.scale);
>>>>>>> c38e2af (updated code)
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

<<<<<<< HEAD
</html>
=======
</html>
>>>>>>> c38e2af (updated code)
