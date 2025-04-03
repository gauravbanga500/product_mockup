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
// Old code ======08-02-2025
// $logo_name = $_FILES['logo']['name'];
// $upload_dir = 'uploads/';

// if (!$logo) {
//     die('Error: Logo file is required.');
// }

// // Upload logo
// $logo_ext = pathinfo($logo_name, PATHINFO_EXTENSION);
// $logo_new_name = uniqid('logo_', true) . '.' . $logo_ext;
// $logo_path = $upload_dir . $logo_new_name;

// if (!move_uploaded_file($logo, $logo_path)) {
//     die('Error: Failed to upload logo.');
// }
// =========08-02-2025=========

$upload_dir = 'uploads/';

// Check if file is uploaded
if (!isset($_FILES['logo']) || $_FILES['logo']['error'] !== UPLOAD_ERR_OK) {
    die('Error: Logo file is required.');
}

$logo_tmp = $_FILES['logo']['tmp_name'];
$logo_name = $_FILES['logo']['name'];
$logo_ext = strtolower(pathinfo($logo_name, PATHINFO_EXTENSION));
$allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];

if (!in_array($logo_ext, $allowed_extensions)) {
    die('Error: Invalid file format. Allowed formats: JPG, JPEG, PNG, GIF.');
}

// Generate a unique filename
$logo_new_name = uniqid('logo_', true) . '.' . $logo_ext;
$logo_path = $upload_dir . $logo_new_name;

// Resize settings
$target_width = 632;
$target_height = 395;

// Get original image size
list($original_width, $original_height) = getimagesize($logo_tmp);

// Calculate new width and height while maintaining aspect ratio
$ratio = min($target_width / $original_width, $target_height / $original_height);
$new_width = intval($original_width * $ratio);
$new_height = intval($original_height * $ratio);

// Calculate padding for centering
$padding_x = intval(($target_width - $new_width) / 2);
$padding_y = intval(($target_height - $new_height) / 2);

// Create a blank transparent image instead of a white background
$resized_image = imagecreatetruecolor($target_width, $target_height);

// Handle transparency for PNG & GIF
if ($logo_ext === 'png' || $logo_ext === 'gif') {
    imagesavealpha($resized_image, true);
    $transparent = imagecolorallocatealpha($resized_image, 0, 0, 0, 127); // Full transparency
    imagefill($resized_image, 0, 0, $transparent);
} else {
    // For JPG, use white background
    $white = imagecolorallocate($resized_image, 255, 255, 255);
    imagefill($resized_image, 0, 0, $white);
}

// Load original image based on type
switch ($logo_ext) {
    case 'jpg':
    case 'jpeg':
        $source_image = imagecreatefromjpeg($logo_tmp);
        break;
    case 'png':
        $source_image = imagecreatefrompng($logo_tmp);
        break;
    case 'gif':
        $source_image = imagecreatefromgif($logo_tmp);
        break;
    default:
        die('Error: Unsupported image type.');
}

// Resize the image while maintaining aspect ratio and center it
imagecopyresampled($resized_image, $source_image, $padding_x, $padding_y, 0, 0, $new_width, $new_height, $original_width, $original_height);

// Save the resized image
switch ($logo_ext) {
    case 'jpg':
    case 'jpeg':
        imagejpeg($resized_image, $logo_path, 90);
        break;
    case 'png':
        imagepng($resized_image, $logo_path, 9);
        break;
    case 'gif':
        imagegif($resized_image, $logo_path);
        break;
}

// Free memory
imagedestroy($resized_image);
imagedestroy($source_image);

function hexToRgb($hexColor)
{
    $hexColor = ltrim($hexColor, '#');
    if (strlen($hexColor) == 3) {
        $hexColor = $hexColor[0] . $hexColor[0] . $hexColor[1] . $hexColor[1] . $hexColor[2] . $hexColor[2];
    }

    return [
        hexdec(substr($hexColor, 0, 2)),
        hexdec(substr($hexColor, 2, 2)),
        hexdec(substr($hexColor, 4, 2))
    ];
}

function applyBrandingEffect($logo_path, $hex_color, $product_id = null)
{
    $product_id = $product_id ?? uniqid();

    if (!$hex_color) {
        // No color selected, return the original logo path without processing
        return $logo_path;
    }

    // Load the logo image
    $logo_image = imagecreatefrompng($logo_path);

    if (!$logo_image) {
        die('Error: Invalid logo image.');
    }

    $width = imagesx($logo_image);
    $height = imagesy($logo_image);

    // Create a new transparent image
    $output_image = imagecreatetruecolor($width, $height);
    imagesavealpha($output_image, true);
    $transparent = imagecolorallocatealpha($output_image, 0, 0, 0, 127); // Full transparency
    imagefill($output_image, 0, 0, $transparent);

    list($r, $g, $b) = hexToRgb($hex_color);

    // Apply branding color only where there are non-transparent pixels
    for ($x = 0; $x < $width; $x++) {
        for ($y = 0; $y < $height; $y++) {
            $alpha = (imagecolorat($logo_image, $x, $y) >> 24) & 0xFF; // Extract alpha channel

            if ($alpha < 127) { // Apply color only to non-transparent pixels
                $new_color = imagecolorallocatealpha($output_image, $r, $g, $b, $alpha);
                imagesetpixel($output_image, $x, $y, $new_color);
            }
        }
    }

    $processed_logo_path = str_replace('.png', "_processed_$product_id.png", $logo_path);
    imagepng($output_image, $processed_logo_path);
 
    imagedestroy($logo_image);
    imagedestroy($output_image);

    return $processed_logo_path;
}


// =========08-02-2025=========

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

$template_query = $conn->query("SELECT * FROM templates LIMIT 1");
$template = $template_query->fetch_assoc();

// Custom FPDF class with rotation support
class PDF extends FPDF
{
    protected $angle = 0;

    public function __construct()
    {
        parent::__construct('P', 'pt', [1000, 1000]);
    }

    public function Rotate($angle, $x = -1, $y = -1)
    {
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
            $angle *= M_PI / 180;
            $c = cos($angle);
            $s = sin($angle);
            $cx = $x * $this->k;
            $cy = ($this->h - $y) * $this->k;
            $this->_out(sprintf('q %.2F %.2F %.2F %.2F %.2F %.2F cm 1 0 0 1 %.2F %.2F cm', $c, $s, -$s, $c, $cx, $cy, -$cx, -$cy));
        }
    }

    public function RotatedImage($file, $x, $y, $w, $h, $angle)
    {
        $this->Rotate($angle, $x + $w / 2, $y + $h / 2);
        $this->Image($file, $x, $y, $w, $h);
        $this->Rotate(0);
    }

    public function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, 'Page ' . $this->PageNo(), 0, 0, 'C');
    }

}

// Create a new PDF instance
$pdf = new PDF();

$pdf->SetFont('Arial', 'B', 16);

$page_width = $pdf->GetPageWidth();
$page_height = $pdf->GetPageHeight();

$i = 0;


// Define standard DPI for conversion
define('DPI', 96); // Assuming 96 DPI as standard for image rendering
function pxToMm($px)
{
    return $px * 25.4 / DPI; // Conversion factor for pixels to millimeters
}

if ($template) {
    $i++;
    $pdf->AddPage();
    $pdf->Image($template['first_template'], 0, 5, $page_width, 0);

<<<<<<< HEAD
    // Fetch logo dimensions from database
    $sql_logo_dimensions = "SELECT logo_width, logo_height FROM products WHERE id IN ($product_ids) LIMIT 1";
    $logo_dimensions_result = $conn->query($sql_logo_dimensions);
    $logo_dimensions = $logo_dimensions_result->fetch_assoc();

    if ($logo_dimensions) {
        // Convert dimensions from pixels to mm
        $logoWidthMm = pxToMm($logo_dimensions['logo_width']);
        $logoHeightMm = pxToMm($logo_dimensions['logo_height']);

        // Set dynamic logo position (keeping margins)
        $logoX = ($page_width - $logoWidthMm) - 10; // Margin of 10mm from the right
        $logoY = 20; // Fixed vertical position

        $pdf->RotatedImage($logo_path, $logoX, $logoY, $logoWidthMm, $logoHeightMm, 0);
=======
    // Ensure the uploaded logo exists before adding
    if (file_exists($logo_path)) {
        // **Set width to 120px and auto height while keeping aspect ratio**
        $logoWidthPx = 500; // Fixed width in pixels
        list($originalLogoWidth, $originalLogoHeight) = getimagesize($logo_path);
        $logoAspectRatio = $originalLogoWidth / $originalLogoHeight;
        $logoHeightPx = $logoWidthPx / $logoAspectRatio; // Auto height to maintain aspect ratio

        // Convert pixels to mm
        $logoWidthMm = pxToMm($logoWidthPx);
        $logoHeightMm = pxToMm($logoHeightPx);

        // **Set Position (25px margin from top & right)**
        $marginPx = 350; // Margin in pixels
        $marginMm = pxToMm($marginPx); // Convert margin from px to mm

        $logoX_top_right = $page_width - $logoWidthMm - $marginMm; // Right margin
        $logoY_top_right = $marginMm; // Top margin

        // **Add the same logo to the top-right**
        $pdf->Image($logo_path, $logoX_top_right, $logoY_top_right, $logoWidthMm, $logoHeightMm);

        // Debugging: Log the exact position
        error_log("Logo placed at Top-Right: X = $logoX_top_right, Y = $logoY_top_right, Width = $logoWidthMm, Height = $logoHeightMm");
    } else {
        error_log("Error: Logo file not found at $logo_path");
>>>>>>> c38e2af (updated code)
    }
}


<<<<<<< HEAD
=======

>>>>>>> c38e2af (updated code)
while ($product = $products->fetch_assoc()) {

    $branding_option = $product['branding_options'];
    $logo_hex_color = $product['logo_hex_color']; // Use directly from the database
    $featured_image = $product['featured_image'];
    $processed_logo_path = applyBrandingEffect($logo_path, $logo_hex_color);

    if (file_exists($featured_image) && file_exists($processed_logo_path)) {
        $pdf->AddPage();

        $i++;
        $product_id = $product['id'];
        $product_name = $product['name'];
        $description = $product['description'];

        $logo_positions = !empty($product['logo_positions']) ? json_decode($product['logo_positions'], true) : null;
        $logo_styles = !empty($product['logo_styles']) ? json_decode($product['logo_styles'], true) : null;

<<<<<<< HEAD
        $left_percent = ($logo_positions['left'] ?? 0) * 729.8;
        $top_percent =  ($logo_positions['top'] ?? 0) * 721.6;
=======
        $left_percent = ($logo_positions['left'] ?? 0) * 725.8;
        $top_percent =  ($logo_positions['top'] ?? 0) * 740.6;
>>>>>>> c38e2af (updated code)

        $scale = $logo_styles['scale'] ?? 1.0; // Default scale to 1.0
        $rotation = 360 - $logo_styles['rotation'] ?? 0; // Default rotation to 0

        // File paths for images
        $productImage = $featured_image;
        $logoImage = $processed_logo_path;

        // Set the fixed product image width
        $productFixedWidth = 2600; // Fixed width in pixels
        $pixelToMM = 0.264583; // Conversion factor from pixels to mm (1 px = 0.264583 mm)
        $productFixedWidthMM = $productFixedWidth * $pixelToMM;

        // Get original dimensions of the product image
        list($productWidth, $productHeight) = getimagesize($productImage);

        // Calculate product image height dynamically based on aspect ratio
        $productAspectRatio = $productWidth / $productHeight;
        $productFixedHeightMM = $productFixedWidthMM / $productAspectRatio;

        // Center the product image on the PDF
        $pageWidth = $pdf->GetPageWidth();
        $pageHeight = $pdf->GetPageHeight();

        $productX = ($pageWidth - $productFixedWidthMM) / 2;
        $productY = ($pageHeight - $productFixedHeightMM) / 2;

        // Add the product image to the PDF
        $pdf->Image($productImage, $productX, $productY, $productFixedWidthMM, $productFixedHeightMM);

        // Get original dimensions of the logo
        list($logoWidth, $logoHeight) = getimagesize($logoImage);


        // Calculate logo size as 15% of product image width
        $logoScale = 1.01;
        $logoDisplayWidthMM = 120 * $scale * $logoScale;
        $logoAspectRatio = $logoWidth / $logoHeight;
        $logoDisplayHeightMM = $logoDisplayWidthMM / $logoAspectRatio;

        // Position the logo at top-right corner of the product image
        $logoX = $productX + ($left_percent / $productFixedWidthMM); // 75% from left of product image
        $logoY = $productY + ($top_percent / $productFixedHeightMM); // 6% from top of product image

        // Set font for the description
        $pdf->SetFont('Arial', '', 12);
        
//                 // Check if description is not empty
// if (!empty($product['description'])) {
//     $pdf->MultiCell(0, 10, "Description: " . $product['description']);
// } else {
//     $pdf->MultiCell(0, 10, "No description available.");
// }
     

        // // Add product description
        // $pdf->MultiCell(0, 20, $description, 0, 'C');

        // Add the logo to the PDF
        $pdf->RotatedImage($logoImage, $logoX, $logoY, $logoDisplayWidthMM, $logoDisplayHeightMM, $rotation);


<<<<<<< HEAD
        $pdf->SetFont('Arial', 'B', 40);
        // $pdf->Cell(0, 20, "Branding Hex Color: $logo_hex_color", 0, 1, 'C');
        $pdf->Cell(0, 50, $product_name, 0, 1, 'C');
=======
      $pdf->SetFont('Arial', 'B', 25);
    // **Add 25px margin before displaying the product name**
    $marginTopPx = 135;
    $marginTopMm = pxToMm($marginTopPx); // Convert pixels to mm
    $pdf->Ln($marginTopMm); // Add vertical space
    
    // Display product name
    $pdf->Cell(0, 25, $product_name, 0, 1, 'C');

        
        
        
>>>>>>> c38e2af (updated code)
        
   
        


    }
}

if ($template) {
    $pdf->AddPage();
    $pdf->Image($template['second_template'], 0, 5, $page_width, 0);
<<<<<<< HEAD
    
}

=======

    // Ensure the uploaded logo exists before adding
    if (file_exists($logo_path)) {
        // **Set width to 120px and auto height while keeping aspect ratio**
        $logoWidthPx = 500; // Fixed width in pixels
        list($originalLogoWidth, $originalLogoHeight) = getimagesize($logo_path);
        $logoAspectRatio = $originalLogoWidth / $originalLogoHeight;
        $logoHeightPx = $logoWidthPx / $logoAspectRatio; // Auto height to maintain aspect ratio

        // Convert pixels to mm
        $logoWidthMm = pxToMm($logoWidthPx);
        $logoHeightMm = pxToMm($logoHeightPx);

        // **Set Position (25px margin from top & right)**
        $marginPx = 350; // Margin in pixels
        $marginMm = pxToMm($marginPx); // Convert margin from px to mm

        $logoX_top_right = $page_width - $logoWidthMm - $marginMm; // Right margin
        $logoY_top_right = $marginMm; // Top margin

        // **Add the same logo to the top-right of final template**
        $pdf->Image($logo_path, $logoX_top_right, $logoY_top_right, $logoWidthMm, $logoHeightMm);

        // Debugging: Log the exact position
        error_log("Logo placed at Top-Right of Final Template: X = $logoX_top_right, Y = $logoY_top_right, Width = $logoWidthMm, Height = $logoHeightMm");
    } else {
        error_log("Error: Logo file not found at $logo_path");
    }
}


>>>>>>> c38e2af (updated code)
// Output the PDF
$pdf->Output('I', 'output.pdf');
