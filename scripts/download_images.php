<?php
// Get the absolute path to the project root
$project_root = dirname(__DIR__);

// Create directories if they don't exist
$directories = [
    $project_root . '/assets/images/properties',
    $project_root . '/assets/images/profiles'
];

foreach ($directories as $dir) {
    if (!file_exists($dir)) {
        mkdir($dir, 0777, true);
    }
}

// Sample image URLs from Unsplash (free to use)
$images = [
    // Modern Apartment
    'https://images.unsplash.com/photo-1522708323590-d24dbb6b0267' => 'modern-apartment-1.jpg',
    'https://images.unsplash.com/photo-1502672260266-1c1ef2d93688' => 'modern-apartment-2.jpg',
    'https://images.unsplash.com/photo-1536376072261-38c75010e6c9' => 'modern-apartment-3.jpg',
    
    // Luxury Villa
    'https://images.unsplash.com/photo-1613490493576-7fde63acd811' => 'luxury-villa-1.jpg',
    'https://images.unsplash.com/photo-1600585154340-be6161a56a0c' => 'luxury-villa-2.jpg',
    'https://images.unsplash.com/photo-1600607687939-ce8a6c25118c' => 'luxury-villa-3.jpg',
    
    // Studio
    'https://images.unsplash.com/photo-1502005229762-cf1b2da7c5d6' => 'studio-1.jpg',
    'https://images.unsplash.com/photo-1522771739844-6a9f6d5f14af' => 'studio-2.jpg',
    'https://images.unsplash.com/photo-1502672260266-1c1ef2d93688' => 'studio-3.jpg',
    
    // Family Home
    'https://images.unsplash.com/photo-1564013799919-ab600027ffc6' => 'family-home-1.jpg',
    'https://images.unsplash.com/photo-1583608205776-bfd35f0d9f83' => 'family-home-2.jpg',
    'https://images.unsplash.com/photo-1600585154340-be6161a56a0c' => 'family-home-3.jpg',
    
    // Penthouse
    'https://images.unsplash.com/photo-1600607687939-ce8a6c25118c' => 'penthouse-1.jpg',
    'https://images.unsplash.com/photo-1600566753086-00f18fb6b3ea' => 'penthouse-2.jpg',
    'https://images.unsplash.com/photo-1600585154340-be6161a56a0c' => 'penthouse-3.jpg',
    
    // Beachfront
    'https://images.unsplash.com/photo-1600585154340-be6161a56a0c' => 'beachfront-1.jpg',
    'https://images.unsplash.com/photo-1600607687939-ce8a6c25118c' => 'beachfront-2.jpg',
    'https://images.unsplash.com/photo-1600566753086-00f18fb6b3ea' => 'beachfront-3.jpg',
    
    // Townhouse
    'https://images.unsplash.com/photo-1600585154340-be6161a56a0c' => 'townhouse-1.jpg',
    'https://images.unsplash.com/photo-1600607687939-ce8a6c25118c' => 'townhouse-2.jpg',
    'https://images.unsplash.com/photo-1600566753086-00f18fb6b3ea' => 'townhouse-3.jpg',
    
    // Luxury Apartment
    'https://images.unsplash.com/photo-1600585154340-be6161a56a0c' => 'luxury-apt-1.jpg',
    'https://images.unsplash.com/photo-1600607687939-ce8a6c25118c' => 'luxury-apt-2.jpg',
    'https://images.unsplash.com/photo-1600566753086-00f18fb6b3ea' => 'luxury-apt-3.jpg'
];

// Download images
foreach ($images as $url => $filename) {
    $target_path = $project_root . '/assets/images/properties/' . $filename;
    if (!file_exists($target_path)) {
        $image_data = file_get_contents($url);
        if ($image_data !== false) {
            file_put_contents($target_path, $image_data);
            echo "Downloaded: $filename\n";
        } else {
            echo "Failed to download: $filename\n";
        }
    } else {
        echo "File already exists: $filename\n";
    }
}

echo "Image download complete!\n"; 