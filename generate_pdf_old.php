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

function hexToRgb($hexColor) {
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

function applyBrandingEffect($logo_path, $hex_color, $product_id = null) {
    $product_id = $product_id ?? uniqid();
    $logo_image = imagecreatefrompng($logo_path);

    if (!$logo_image) {
        die('Error: Invalid logo image.');
    }

    $width = imagesx($logo_image);
    $height = imagesy($logo_image);

    $output_image = imagecreatetruecolor($width, $height);
    imagesavealpha($output_image, true);
    $transparent = imagecolorallocatealpha($output_image, 255, 255, 255, 127);
    imagefill($output_image, 0, 0, $transparent);

    if (!$hex_color) {
        $hex_color = '#ffffff';
    }

    list($r, $g, $b) = hexToRgb($hex_color);
    $color = imagecolorallocate($output_image, $r, $g, $b);

    for ($x = 0; $x < $width; $x++) {
        for ($y = 0; $y < $height; $y++) {
            $pixel_color = imagecolorsforindex($logo_image, imagecolorat($logo_image, $x, $y));
            if ($pixel_color['alpha'] < 127) {
                imagesetpixel($output_image, $x, $y, $color);
            }
        }
    }

    $processed_logo_path = str_replace('.png', "_processed_$product_id.png", $logo_path);
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
function pxToMm($px) {
    return $px * 25.4 / DPI; // Conversion factor for pixels to millimeters
}

if ($template) {
    $i++;
    $pdf->AddPage();
    $pdf->Image($template['first_template'], 0, 5, $page_width, 0);

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
    }
}


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

        $left_percent = ($logo_positions['left'] ?? 0) * 729.8;
        $top_percent =  ($logo_positions['top'] ?? 0) * 721.6;

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

// // Add product description
// $pdf->MultiCell(0, 20, $description, 0, 'C');

        // Add the logo to the PDF
        $pdf->RotatedImage($logoImage, $logoX, $logoY, $logoDisplayWidthMM, $logoDisplayHeightMM, $rotation);
        

        $pdf->SetFont('Arial', 'B', 40);
       // $pdf->Cell(0, 20, "Branding Hex Color: $logo_hex_color", 0, 1, 'C');
        $pdf->Cell(0, 50, $product_name, 0, 1, 'C');
        
        
    }
}

if ($template) {
    $pdf->AddPage();
    $pdf->Image($template['second_template'], 0, 5, $page_width, 0);
}

// Output the PDF
$pdf->Output('I', 'output.pdf');
