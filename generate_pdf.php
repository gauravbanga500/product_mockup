<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

ob_start();

require('fpdf/fpdf.php');
require('db_connection.php'); // Replace with your actual database connection file

if (!isset($_POST['product_option'])) {
    die('Error: Product option not selected.');
}

$product_option = $_POST['product_option'];
$logo = $_FILES['logo']['tmp_name'];
$logo_name = $_FILES['logo']['name'];
$upload_dir = 'uploads/';

if (!$logo) {
    die('Error: Logo file is required.');
}

// Upload logo
$logo_ext = pathinfo($logo_name, PATHINFO_EXTENSION);
$logo_new_name = uniqid('logo_', true) . '.' . $logo_ext;
$logo_path = $upload_dir . $logo_new_name;

if (!move_uploaded_file($logo, $logo_path)) {
    die('Error: Failed to upload logo.');
}

// Function to apply branding effect to the logo
function applyBrandingEffect($logo_path, $branding_option) {
    $logo_image = imagecreatefrompng($logo_path);
    if (!$logo_image) {
        die('Error: Invalid logo image.');
    }

    $width = imagesx($logo_image);
    $height = imagesy($logo_image);

    // Create a blank image to apply effects
    $output_image = imagecreatetruecolor($width, $height);
    imagesavealpha($output_image, true);
    $transparent = imagecolorallocatealpha($output_image, 0, 0, 0, 127);
    imagefill($output_image, 0, 0, $transparent);

    // Apply color effect
    switch ($branding_option) {
        case 'digital-uv-printing':
            $color = imagecolorallocate($output_image, 255, 0, 0); // Red
            break;
        case 'golden-embossing':
            $color = imagecolorallocate($output_image, 255, 215, 0); // Gold
            break;
        case 'silver-embossing':
            $color = imagecolorallocate($output_image, 192, 192, 192); // Silver
            break;
        case 'logo-effect-laser-engraving':
            $color = imagecolorallocate($output_image, 128, 128, 128); // Gray
            break;
        case 'blind-embossing':
            $color = imagecolorallocate($output_image, 211, 211, 211); // Light Gray
            break;
        default:
            $color = null; // No color change
    }

    if ($color) {
        for ($x = 0; $x < $width; $x++) {
            for ($y = 0; $y < $height; $y++) {
                $pixel_color = imagecolorsforindex($logo_image, imagecolorat($logo_image, $x, $y));
                if ($pixel_color['alpha'] < 127) {
                    imagesetpixel($output_image, $x, $y, $color);
                }
            }
        }
    } else {
        imagecopy($output_image, $logo_image, 0, 0, 0, 0, $width, $height);
    }

    $processed_logo_path = str_replace('.png', '_processed_' . $branding_option . '.png', $logo_path);
    imagepng($output_image, $processed_logo_path);
    imagedestroy($logo_image);
    imagedestroy($output_image);

    return $processed_logo_path;
}

// Prepare SQL query based on selection
if ($product_option === 'individual' && isset($_POST['products'])) {
    $selected_products = $_POST['products'];
    $product_ids = implode(',', $selected_products);
    $sql_products = "SELECT * FROM products WHERE id IN ($product_ids)";
} elseif ($product_option === 'category' && isset($_POST['category'])) {
    $category_id = $_POST['category'];
    $sql_products = "SELECT * FROM products WHERE categories = $category_id";
} else {
    die('Error: Invalid product or category selection.');
}

$products = $conn->query($sql_products);

if ($conn->error) {
    die('SQL Error: ' . $conn->error);
}
if ($products->num_rows === 0) {
    die('Error: No products found.');
}

// Custom FPDF class with rotation support
class PDF extends FPDF {
    protected $angle = 0;

    function Rotate($angle, $x = -1, $y = -1) {
        if ($x == -1) {
            $x = $this->x;
        }
        if ($y == -1) {
            $y = $this->y;
        }
        if ($this->angle != 0) {
            $this->_out('Q');
        }
        $this->angle = $angle;
        if ($angle != 0) {
            $angle_rad = $angle * M_PI / 180;
            $c = cos($angle_rad);
            $s = sin($angle_rad);
            $cx = $x * $this->k;
            $cy = ($this->h - $y) * $this->k;
            $this->_out(sprintf('q %.5F %.5F %.5F %.5F %.5F %.5F cm 1 0 0 1 %.5F %.5F cm', $c, $s, -$s, $c, $cx, $cy, -$cx, -$cy));
        }
    }

    function _endpage() {
        if ($this->angle != 0) {
            $this->angle = 0;
            $this->_out('Q');
        }
        parent::_endpage();
    }
}

// Generate PDF
$pdf = new PDF('P', 'mm', [1000, 1000]);
$pdf->SetFont('Arial', 'B', 16);

// Retrieve templates
$template_query = $conn->query("SELECT * FROM templates LIMIT 1");
$template = $template_query->fetch_assoc();

if ($template) {
    $pdf->AddPage();
    $pdf->Image($template['first_template'], 0, 0, 1000, 1000);
}

// Process each product
while ($product = $products->fetch_assoc()) {
    $product_id = $product['id'];
    $product_name = $product['name'];
    $description = $product['description'];
    $featured_image = $product['featured_image'];
    $branding_option = $product['branding_options'];
    $logo_positions = !empty($product['logo_positions']) ? json_decode($product['logo_positions'], true) : null;
    $logo_styles = !empty($product['logo_styles']) ? json_decode($product['logo_styles'], true) : null;
    
    $processed_logo_path = applyBrandingEffect($logo_path, $branding_option);

    $scale = $logo_styles['scale'] ?? 1.0; // Default scale to 1.0
    $rotation = $logo_styles['rotation'] ?? 0; // Default rotation to 0

    $pdf->AddPage();

    if (file_exists($featured_image)) {
        list($image_width_original, $image_height_original) = getimagesize($featured_image);

        $image_width = 600;
        $image_height = ($image_height_original / $image_width_original) * $image_width;

        $image_x = 150;
        $image_y = 180;

        $pdf->Image($featured_image, $image_x, $image_y, $image_width, $image_height);
    } else {
        $pdf->Cell(0, 10, 'Featured image not found.', 0, 1);
    }

    if (file_exists($processed_logo_path)) {
        list($logo_width_original, $logo_height_original) = getimagesize($processed_logo_path);

        $logo_width = 120 * $scale; // Adjust width based on scale
        $logo_height = ($logo_height_original / $logo_width_original) * $logo_width;
       if (is_array($logo_positions)) {
            $left_percent = $logo_positions['left'] ?? 0;
            $top_percent = $logo_positions['top'] ?? 0;

          // Adjust for scaling inconsistencies
$scaling_adjustment_x = $image_width * 0.02; // 2% adjustment for X-axis 
$scaling_adjustment_y = $image_height * 0.06; // 1% adjustment for Y-axis 


// Calculate aspect ratio of the image 
$aspect_ratio = $image_width / $image_height;

// Adjust factors dynamically for different product types
$dynamic_left_factor = 101 + ($aspect_ratio * 3); // Fine-tune this value for left adjustment
$dynamic_top_factor = 90 + (($image_height / $image_width) * 3); // Fine-tune this value for top adjustment

// Dynamic Logo Placement Formula
$logo_x = $image_x + ($image_width * ($left_percent / $dynamic_left_factor)) - ($logo_width / 2);
$logo_y = $image_y + ($image_height * ($top_percent / $dynamic_top_factor)) - ($logo_height / 2);
 
 

        } else {
            $logo_x = $image_x + ($image_width / 2) - ($logo_width / 2);
            $logo_y = $image_y + ($image_height / 2) - ($logo_height / 2); 
        }

        $pdf->Rotate($rotation, $logo_x + ($logo_width / 2), $logo_y + ($logo_height / 2)); // Rotate logo around its center
        $pdf->Image($processed_logo_path, $logo_x, $logo_y, $logo_width, $logo_height);
        $pdf->Rotate(0); // Reset rotation
    } else {
        $pdf->Cell(0, 10, 'Logo not uploaded or invalid.', 0, 1);
    }

    $pdf->SetFont('Arial', 'B', 50);
    $pdf->SetY($image_y + $image_height + 20);
    $pdf->Cell(0, 15, $product_name, 0, 1, 'C');
}

if ($template) {
    $pdf->AddPage();
    $pdf->Image($template['second_template'], 0, 0, 1000, 1000);
}

ob_end_clean();
$pdf->Output('D', 'product_with_logo_and_effects.pdf');
?>
