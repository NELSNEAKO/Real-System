<?php
function getFeaturedProperties($conn, $limit = 6) {
    $sql = "SELECT * FROM properties 
            WHERE status = 'available' 
            ORDER BY created_at DESC 
            LIMIT ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $limit);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $properties = [];
    while ($row = $result->fetch_assoc()) {
        $properties[] = $row;
    }
    
    return $properties;
}

function getPropertyById($conn, $id) {
    $sql = "SELECT p.*, 
            GROUP_CONCAT(pi.image_path) as additional_images 
            FROM properties p 
            LEFT JOIN property_images pi ON p.id = pi.property_id 
            WHERE p.id = ? 
            GROUP BY p.id";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $property = $result->fetch_assoc();
    if ($property && $property['additional_images']) {
        $property['additional_images'] = explode(',', $property['additional_images']);
    }
    
    return $property;
}

function searchProperties($conn, $location, $type, $min_price, $max_price, $page = 1, $per_page = 12) {
    $offset = ($page - 1) * $per_page;
    $sql = "SELECT SQL_CALC_FOUND_ROWS * FROM properties WHERE status = 'available'";
    $params = [];
    $types = "";
    
    if (!empty($location)) {
        $sql .= " AND location LIKE ?";
        $params[] = "%$location%";
        $types .= "s";
    }
    
    if (!empty($type)) {
        $sql .= " AND type = ?";
        $params[] = $type;
        $types .= "s";
    }
    
    if (!empty($min_price)) {
        $sql .= " AND price >= ?";
        $params[] = $min_price;
        $types .= "i";
    }
    
    if (!empty($max_price)) {
        $sql .= " AND price <= ?";
        $params[] = $max_price;
        $types .= "i";
    }
    
    $sql .= " ORDER BY created_at DESC LIMIT ? OFFSET ?";
    $types .= "ii";
    $params[] = $per_page;
    $params[] = $offset;
    
    $stmt = $conn->prepare($sql);
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    
    // Get total count for pagination
    $total_result = $conn->query("SELECT FOUND_ROWS()");
    $total = $total_result->fetch_row()[0];
    
    $properties = [];
    while ($row = $result->fetch_assoc()) {
        $properties[] = $row;
    }
    
    return [
        'properties' => $properties,
        'total' => $total,
        'total_pages' => ceil($total / $per_page),
        'current_page' => $page
    ];
}

function createProperty($conn, $data) {
    $conn->begin_transaction();
    
    try {
        $sql = "INSERT INTO properties (title, description, price, location, type, bedrooms, bathrooms, area, image, status) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'available')";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssisssiis", 
            $data['title'],
            $data['description'],
            $data['price'],
            $data['location'],
            $data['type'],
            $data['bedrooms'],
            $data['bathrooms'],
            $data['area'],
            $data['image']
        );
        
        if (!$stmt->execute()) {
            throw new Exception("Failed to create property");
        }
        
        $property_id = $conn->insert_id;
        
        // Handle additional images if any
        if (!empty($data['additional_images'])) {
            $sql = "INSERT INTO property_images (property_id, image_path) VALUES (?, ?)";
            $stmt = $conn->prepare($sql);
            
            foreach ($data['additional_images'] as $image) {
                $stmt->bind_param("is", $property_id, $image);
                if (!$stmt->execute()) {
                    throw new Exception("Failed to add property image");
                }
            }
        }
        
        $conn->commit();
        return $property_id;
    } catch (Exception $e) {
        $conn->rollback();
        return false;
    }
}

function updateProperty($conn, $id, $data) {
    $sql = "UPDATE properties SET 
            title = ?,
            description = ?,
            price = ?,
            location = ?,
            type = ?,
            bedrooms = ?,
            bathrooms = ?,
            area = ?,
            image = ?,
            featured = ?
            WHERE id = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssisssiisii", 
        $data['title'],
        $data['description'],
        $data['price'],
        $data['location'],
        $data['type'],
        $data['bedrooms'],
        $data['bathrooms'],
        $data['area'],
        $data['image'],
        $data['featured'],
        $id
    );
    
    return $stmt->execute();
}

function deleteProperty($conn, $id) {
    $sql = "DELETE FROM properties WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    
    return $stmt->execute();
}

function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

function validatePropertyData($data) {
    $errors = [];
    
    if (empty($data['title'])) {
        $errors[] = "Title is required";
    }
    
    if (empty($data['description'])) {
        $errors[] = "Description is required";
    }
    
    if (empty($data['price']) || !is_numeric($data['price'])) {
        $errors[] = "Valid price is required";
    }
    
    if (empty($data['location'])) {
        $errors[] = "Location is required";
    }
    
    if (empty($data['type'])) {
        $errors[] = "Property type is required";
    }
    
    return $errors;
}

function optimizeImage($source_path, $target_path, $max_width = 1200, $max_height = 1200, $quality = 80) {
    // Check if GD is available
    if (!extension_loaded('gd')) {
        // If GD is not available, just copy the file
        copy($source_path, $target_path);
        return [
            'original' => $target_path,
            'webp' => $target_path
        ];
    }

    // Get image info
    $image_info = getimagesize($source_path);
    if ($image_info === false) {
        return false;
    }

    // Create image from file
    switch ($image_info[2]) {
        case IMAGETYPE_JPEG:
            $source_image = imagecreatefromjpeg($source_path);
            break;
        case IMAGETYPE_PNG:
            $source_image = imagecreatefrompng($source_path);
            break;
        case IMAGETYPE_WEBP:
            $source_image = imagecreatefromwebp($source_path);
            break;
        default:
            return false;
    }

    if (!$source_image) {
        // If image creation fails, just copy the file
        copy($source_path, $target_path);
        return [
            'original' => $target_path,
            'webp' => $target_path
        ];
    }

    // Calculate new dimensions
    $width = $image_info[0];
    $height = $image_info[1];
    
    if ($width > $max_width || $height > $max_height) {
        $ratio = min($max_width / $width, $max_height / $height);
        $new_width = round($width * $ratio);
        $new_height = round($height * $ratio);
    } else {
        $new_width = $width;
        $new_height = $height;
    }

    // Create new image
    $new_image = imagecreatetruecolor($new_width, $new_height);

    // Preserve transparency for PNG
    if ($image_info[2] === IMAGETYPE_PNG) {
        imagealphablending($new_image, false);
        imagesavealpha($new_image, true);
    }

    // Resize image
    imagecopyresampled(
        $new_image, $source_image,
        0, 0, 0, 0,
        $new_width, $new_height,
        $width, $height
    );

    // Save as WebP if supported
    $webp_path = preg_replace('/\.(jpg|jpeg|png)$/i', '.webp', $target_path);
    if (function_exists('imagewebp')) {
        imagewebp($new_image, $webp_path, $quality);
    } else {
        $webp_path = $target_path;
    }

    // Also save original format for fallback
    switch ($image_info[2]) {
        case IMAGETYPE_JPEG:
            imagejpeg($new_image, $target_path, $quality);
            break;
        case IMAGETYPE_PNG:
            imagepng($new_image, $target_path, round(9 * $quality / 100));
            break;
    }

    // Free memory
    imagedestroy($source_image);
    imagedestroy($new_image);

    return [
        'original' => $target_path,
        'webp' => $webp_path
    ];
}

function uploadProfilePicture($file, $user_id) {
    $target_dir = "assets/images/profiles/";
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }
    
    $file_extension = strtolower(pathinfo($file["name"], PATHINFO_EXTENSION));
    $new_filename = "profile_" . $user_id . "_" . time();
    $target_file = $target_dir . $new_filename . "." . $file_extension;
    
    // Check if image file is a actual image or fake image
    $check = getimagesize($file["tmp_name"]);
    if($check === false) {
        return false;
    }
    
    // Check file size (5MB max)
    if ($file["size"] > 5000000) {
        return false;
    }
    
    // Allow certain file formats
    if($file_extension != "jpg" && $file_extension != "png" && $file_extension != "jpeg") {
        return false;
    }
    
    if (move_uploaded_file($file["tmp_name"], $target_file)) {
        // Optimize the uploaded image
        $optimized = optimizeImage($target_file, $target_file);
        if ($optimized) {
            return $optimized['webp']; // Return WebP version path
        }
        return $target_file;
    }
    
    return false;
}

function uploadPropertyImage($file, $property_id) {
    $target_dir = "assets/images/properties/";
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }
    
    $file_extension = strtolower(pathinfo($file["name"], PATHINFO_EXTENSION));
    $new_filename = "property_" . $property_id . "_" . time();
    $target_file = $target_dir . $new_filename . "." . $file_extension;
    
    // Check if image file is a actual image or fake image
    $check = getimagesize($file["tmp_name"]);
    if($check === false) {
        return false;
    }
    
    // Check file size (5MB max)
    if ($file["size"] > 5000000) {
        return false;
    }
    
    // Allow certain file formats
    if($file_extension != "jpg" && $file_extension != "png" && $file_extension != "jpeg") {
        return false;
    }
    
    if (move_uploaded_file($file["tmp_name"], $target_file)) {
        // Optimize the uploaded image
        $optimized = optimizeImage($target_file, $target_file);
        if ($optimized) {
            return $optimized['webp']; // Return WebP version path
        }
        return $target_file;
    }
    
    return false;
}

function serveImage($image_path) {
    if (!file_exists($image_path)) {
        return false;
    }

    // Check if WebP is supported
    $webp_path = preg_replace('/\.(jpg|jpeg|png)$/i', '.webp', $image_path);
    $accepts_webp = strpos($_SERVER['HTTP_ACCEPT'] ?? '', 'image/webp') !== false;

    if ($accepts_webp && file_exists($webp_path)) {
        $image_path = $webp_path;
        $mime_type = 'image/webp';
    } else {
        $mime_type = mime_content_type($image_path);
    }

    // Set cache headers
    $etag = md5_file($image_path);
    $if_none_match = $_SERVER['HTTP_IF_NONE_MATCH'] ?? '';
    
    if ($if_none_match === $etag) {
        header('HTTP/1.1 304 Not Modified');
        exit;
    }

    header('Content-Type: ' . $mime_type);
    header('Cache-Control: public, max-age=31536000');
    header('ETag: ' . $etag);
    
    readfile($image_path);
    exit;
} 