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
function applyBrandingEffect($logo_path, $branding_option)
{
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

$template_query = $conn->query("SELECT * FROM templates LIMIT 1");
$template = $template_query->fetch_assoc();

// Custom FPDF class with rotation support
class PDF extends FPDF
{
    protected $angle = 0;

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
            $angle_rad = $angle * M_PI / 180;
            $c = cos($angle_rad);
            $s = sin($angle_rad);
            $cx = $x * $this->k;
            $cy = ($this->h - $y) * $this->k;
            $this->_out(sprintf('q %.5F %.5F %.5F %.5F %.5F %.5F cm 1 0 0 1 %.5F %.5F cm', $c, $s, -$s, $c, $cx, $cy, -$cx, -$cy));
        }
    }

    // public function _endpage()
    // {
    //     if ($this->angle != 0) {
    //         $this->angle = 0;
    //         $this->_out('Q');
    //     }
    //     parent::_endpage();
    // }


    public function Footer()
    {
        // Position at 1.5 cm from bottom
        $this->SetY(-15);
        // Set font
        $this->SetFont('Arial', 'I', 8);
        // Page number
        $this->Cell(0, 10, 'Page ' . $this->PageNo(), 0, 0, 'C');
    }

}

// Create a new PDF instance
$pdf = new PDF();

$pdf->SetFont('Arial', 'B', 16);

$page_width = $pdf->GetPageWidth(); // e.g., 210mm for A4
$page_height = $pdf->GetPageHeight();

$i = 0;

if ($template) {
    $i++;
    $pdf->AddPage();
    $pdf->Image($template['first_template'], 0, $page_height / 10, $page_width, 0);
}

while ($product = $products->fetch_assoc()) {

    $branding_option = $product['branding_options'];

    $featured_image = $product['featured_image'];
    $processed_logo_path = applyBrandingEffect($logo_path, $branding_option);

    if (file_exists($featured_image) && file_exists($processed_logo_path)) {
        $pdf->AddPage();

        $i++;


        $product_id = $product['id'];
        $product_name = $product['name'];
        $description = $product['description'];

        $logo_positions = !empty($product['logo_positions']) ? json_decode($product['logo_positions'], true) : null;
        $logo_styles = !empty($product['logo_styles']) ? json_decode($product['logo_styles'], true) : null;

        $left_percent = ($logo_positions['left'] ?? 0) / 129.8;
        $top_percent =  ($logo_positions['top'] ?? 0) / 98.6;

        $scale = $logo_styles['scale'] ?? 1.0; // Default scale to 1.0
        $rotation = 360 - $logo_styles['rotation'] ?? 0; // Default rotation to 0

        // File paths for images
        $productImage = $featured_image;
        $logoImage = $processed_logo_path;

        // Set the fixed product image width
        $productFixedWidth = 650; // Fixed width in pixels
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
        $logoScale = 0.26;
        $logoDisplayWidthMM = 120 * $scale * $logoScale;
        $logoAspectRatio = $logoWidth / $logoHeight;
        $logoDisplayHeightMM = $logoDisplayWidthMM / $logoAspectRatio;

        // $logoDisplayWidthMM = 120 * $scale * $logoScale;
        // $logoDisplayHeightMM = 0;
        // Position the logo at top-right corner of the product image
        $logoX = $productX + ($productFixedWidthMM * $left_percent); // 75% from left of product image
        $logoY = $productY + ($productFixedHeightMM * $top_percent); // 6% from top of product image

        // Add the logo to the PDF

        // $pdf->Rotate($rotation, $logoX + ($logoDisplayWidthMM / 2), $logoY + ($logoDisplayHeightMM / 2));

        $pdf->Image($logoImage, $logoX, $logoY, $logoDisplayWidthMM, $logoDisplayHeightMM);

        $pdf->SetFont('Arial', 'B', 40);

        $pdf->Cell(0, 15, $product_name, 0, 1, 'C');
    }
}

if ($template) {
    $pdf->AddPage();
    $pdf->Image($template['second_template'], 0, $page_height / 10, $page_width, 0);
}

// Output the PDF
$pdf->Output('I', 'output.pdf');
