<?php
// save_product.php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include 'db_connection.php';
include 'admin_sidebar.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = isset($_POST['name']) ? trim($_POST['name']) : null;
    $description = isset($_POST['description']) ? trim($_POST['description']) : null;
    $category_id = isset($_POST['category']) ? intval($_POST['category']) : null;
    // $branding_option = isset($_POST['branding_option']) ? trim($_POST['branding_option']) : null;
    $logo_hex_color = isset($_POST['hex_color']) ? trim($_POST['hex_color']) : '#FFFFFF';
    
    // $logo_width = isset($_POST['logo_width']) ? intval($_POST['logo_width']) : null;
    // $logo_height = isset($_POST['logo_height']) ? intval($_POST['logo_height']) : null;

    $imagePath = null;
    $logoPath = null;
    $logo_positions = null;
    $logo_styles = null;

    // Handle featured image upload
    if (!empty($_FILES['featured_image']['name'])) {
        $image = $_FILES['featured_image']['name'];
        $imagePath = 'uploads/' . uniqid('image_', true) . '.' . pathinfo($image, PATHINFO_EXTENSION);

        if (!move_uploaded_file($_FILES['featured_image']['tmp_name'], $imagePath)) {
            die("Error: Failed to upload the featured image.");
        }
    }

    // Handle logo upload
    if (!empty($_FILES['logo']['name'])) {
        $logo = $_FILES['logo']['name'];
        $logoPath = 'uploads/' . uniqid('logo_', true) . '.' . pathinfo($logo, PATHINFO_EXTENSION);

        if (!move_uploaded_file($_FILES['logo']['tmp_name'], $logoPath)) {
            $logoWidth = $dimensions['width'];
            $logoHeight = $dimensions['height'];
            
            
            die("Error: Failed to upload the logo.");
        }
    }

    // Fetch and validate logo positions
    if (isset($_POST['logo_position']) && !empty($_POST['logo_position'])) {
        $logo_positions = $_POST['logo_position'];
        $decoded_positions = json_decode($logo_positions, true);
        if (json_last_error() !== JSON_ERROR_NONE || !is_array($decoded_positions)) {
            die("Error: Invalid logo position data.");
        }

        $formatted_positions = [
            'top' => floatval($decoded_positions['top']),
            'left' => floatval($decoded_positions['left'])
        ];
        $logo_positions = json_encode($formatted_positions);
    }

    // Fetch and validate logo styles
    if (isset($_POST['logo_styles']) && !empty($_POST['logo_styles'])) {
        $logo_styles = $_POST['logo_styles'];
        $decoded_styles = json_decode($logo_styles, true);
        if (json_last_error() !== JSON_ERROR_NONE || !is_array($decoded_styles)) {
            die("Error: Invalid logo styles data.");
        }
        $logo_styles = json_encode($decoded_styles);
    }

    if (isset($_POST['product_id']) && !empty($_POST['product_id'])) {
        // Update existing product
        $product_id = intval($_POST['product_id']);
        $query = "UPDATE products SET name = ?, description = ?, categories = ?, logo_hex_color = ?";

        $params = [$name, $description, $category_id, $logo_hex_color];
        $types = "sssss";

        if ($imagePath !== null) {
            $query .= ", featured_image = ?";
            $params[] = $imagePath;
            $types .= "s";
        }
        if ($logo_positions !== null) {
            $query .= ", logo_positions = ?";
            $params[] = $logo_positions;
            $types .= "s";
        }
        if ($logo_styles !== null) {
            $query .= ", logo_styles = ?";
            $params[] = $logo_styles;
            $types .= "s";
        }
        if ($logoPath !== null) {
            $query .= ", logo_path = ?";
            $params[] = $logoPath;
            $types .= "s";
        }
        $query .= " WHERE id = ?";
        $params[] = $product_id;
        $types .= "i";

        $stmt = $conn->prepare($query);
        if ($stmt === false) {
            die("Error: Failed to prepare the query - " . $conn->error);
        }
        $stmt->bind_param($types, ...$params); 
    } else { 
        // Insert new product
        $stmt = $conn->prepare("INSERT INTO products (name, description, featured_image, categories, logo_positions, logo_styles, logo_path, logo_hex_color) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        if ($stmt === false) {
            die("Error: Failed to prepare the query - " . $conn->error);
        }

        $stmt->bind_param('sssssssss', $name, $description, $imagePath, $category_id, $logo_positions, $logo_styles, $logoPath, $logo_hex_color);
    }

    if ($stmt->execute()) { 
        echo "Product saved successfully! <a href='product_list.php'>Go Back</a>";
    } else {
        echo "Error: " . $stmt->error;
    }
}
?>