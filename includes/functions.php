<?php
function getFeaturedProperties($conn, $limit = 6) {
    $sql = "SELECT * FROM properties WHERE featured = 1 ORDER BY created_at DESC LIMIT ?";
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
    $sql = "SELECT * FROM properties WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    return $result->fetch_assoc();
}

function searchProperties($conn, $location, $type, $min_price, $max_price) {
    $sql = "SELECT * FROM properties WHERE 1=1";
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
    
    $sql .= " ORDER BY created_at DESC";
    
    $stmt = $conn->prepare($sql);
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    
    $properties = [];
    while ($row = $result->fetch_assoc()) {
        $properties[] = $row;
    }
    
    return $properties;
}

function createProperty($conn, $data) {
    $sql = "INSERT INTO properties (title, description, price, location, type, bedrooms, bathrooms, area, image, featured) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssisssiisi", 
        $data['title'],
        $data['description'],
        $data['price'],
        $data['location'],
        $data['type'],
        $data['bedrooms'],
        $data['bathrooms'],
        $data['area'],
        $data['image'],
        $data['featured']
    );
    
    return $stmt->execute();
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

function uploadProfilePicture($file, $user_id) {
    $target_dir = "assets/images/profiles/";
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }
    
    $file_extension = strtolower(pathinfo($file["name"], PATHINFO_EXTENSION));
    $new_filename = "profile_" . $user_id . "_" . time() . "." . $file_extension;
    $target_file = $target_dir . $new_filename;
    
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
        return $target_file;
    }
    
    return false;
} 