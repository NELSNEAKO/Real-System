<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

// Get image path from URL parameter
$image_path = $_GET['path'] ?? '';

// Validate path to prevent directory traversal
$image_path = str_replace(['..', '//'], '', $image_path);
$full_path = __DIR__ . '/' . $image_path;

// Serve the image
serveImage($full_path); 