CREATE DATABASE modern_estate;
USE modern_estate;

-- Table structure for table `users`

CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `role` enum('user','admin') DEFAULT 'user',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `profile_picture` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Insert default admin account
-- Password: Admin@123
INSERT INTO `users` (`username`, `email`, `password`, `full_name`, `phone`, `role`, `created_at`) VALUES
('admin', 'admin@gmail.com', '$2y$10$8K1p/a0dR1xqM8K1p/a0dR1xqM8K1p/a0dR1xqM8K1p/a0dR1xqM8K1p', 'System Administrator', '09123456789', 'admin', CURRENT_TIMESTAMP);
--Username: admin
--Email: admin@modernestate.com
--Password: Admin@123

-- Insert simple admin account
-- Username: admin123
-- Password: admin123
INSERT INTO `users` (`username`, `email`, `password`, `full_name`, `phone`, `role`, `created_at`) VALUES
('admin123', 'admin123@gmail.com', '$2y$10$YourHashedPasswordHere', 'Admin User', '09123456789', 'admin', CURRENT_TIMESTAMP);

-- Table structure for table `properties`

CREATE TABLE `properties` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `location` varchar(255) NOT NULL,
  `type` enum('house','apartment','villa','condo') NOT NULL,
  `bedrooms` int(11) NOT NULL,
  `bathrooms` int(11) NOT NULL,
  `area` decimal(10,2) NOT NULL,
  `image` varchar(255) NOT NULL,
  `status` enum('available','sold') DEFAULT 'available',
  `user_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `idx_location` (`location`),
  KEY `idx_type` (`type`),
  KEY `idx_price` (`price`),
  KEY `idx_status` (`status`),
  KEY `idx_created_at` (`created_at`),
  CONSTRAINT `properties_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table structure for table `property_images`

CREATE TABLE `property_images` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `property_id` int(11) NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `is_primary` tinyint(1) DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `property_id` (`property_id`),
  CONSTRAINT `property_images_ibfk_1` FOREIGN KEY (`property_id`) REFERENCES `properties` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table structure for table `inquiries`

CREATE TABLE `inquiries` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `property_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `message` text NOT NULL,
  `status` enum('new','read','replied') DEFAULT 'new',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `property_id` (`property_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `inquiries_ibfk_1` FOREIGN KEY (`property_id`) REFERENCES `properties` (`id`) ON DELETE CASCADE,
  CONSTRAINT `inquiries_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table structure for table `favorites`

CREATE TABLE `favorites` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `property_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_property` (`user_id`, `property_id`),
  KEY `property_id` (`property_id`),
  CONSTRAINT `favorites_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `favorites_ibfk_2` FOREIGN KEY (`property_id`) REFERENCES `properties` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `properties`
--

INSERT INTO `properties` (`id`, `title`, `description`, `price`, `location`, `type`, `bedrooms`, `bathrooms`, `area`, `image`, `status`, `user_id`, `created_at`) VALUES
(1, 'Modern Downtown Apartment', 'Beautiful modern apartment in the heart of downtown. Features high ceilings, large windows, and modern appliances.', 350000.00, 'Downtown', 'apartment', 2, 2, 1200.00, 'assets/images/properties/modern-apartment-1.jpg', 'available', NULL, '2025-05-24 10:32:09'),
(2, 'Luxury Villa with Pool', 'Stunning luxury villa with private pool, garden, and modern amenities. Perfect for family living.', 850000.00, 'Suburbs', 'villa', 4, 3, 2500.00, 'assets/images/properties/luxury-villa-1.jpg', 'available', NULL, '2025-05-24 10:38:21'),
(3, 'Family Home with Garden', 'Spacious family home with large garden and garage. Located in a family-friendly neighborhood.', 450000.00, 'Residential Area', 'house', 3, 2, 1800.00, 'assets/images/properties/family-home-1.jpg', 'available', NULL, '2025-05-24 10:38:21'),
(4, 'Luxury Apartment', 'High-end apartment with premium finishes and amenities. Located in a prestigious building.', 420000.00, 'City Center', 'apartment', 2, 2, 1300.00, 'assets/images/properties/luxury-apt-1.jpg', 'available', NULL, '2025-05-24 10:38:21'),
(5, 'Cozy Studio Apartment', 'Charming studio apartment in a quiet neighborhood. Recently renovated with new appliances.', 180000.00, 'City Center', 'apartment', 1, 1, 600.00, 'assets/images/properties/studio-1.jpg', 'available', NULL, '2025-05-27 15:56:09'),
(6, 'Modern Loft', 'Trendy loft with open floor plan and city views.', 390000.00, 'Downtown', 'apartment', 1, 1, 900.00, 'assets/images/properties/modern-apartment-3.jpg', 'available', NULL, '2025-05-27 15:56:09'),
(7, 'Suburban Family Home', 'Large home in a quiet suburb, perfect for families.', 480000.00, 'Suburbs', 'house', 4, 3, 2000.00, 'assets/images/properties/family-home-2.jpg', 'available', NULL, '2025-05-27 15:56:09'),
(8, 'Studio Retreat', 'Minimalist studio ideal for singles or students.', 160000.00, 'City Center', 'apartment', 1, 1, 500.00, 'assets/images/properties/studio-2.jpg', 'available', NULL, '2025-05-27 15:56:09'),
(9, 'Executive Suite', 'Premium suite for business professionals.', 520000.00, 'Business District', 'apartment', 2, 2, 1100.00, 'assets/images/properties/luxury-apt-2.jpg', 'available', NULL, '2025-05-27 15:56:09'),
(10, 'Urban Chic Apartment', 'Stylish apartment with modern amenities.', 370000.00, 'Downtown', 'apartment', 2, 1, 950.00, 'assets/images/properties/luxury-apt-3.jpg', 'available', NULL, '2025-05-27 15:56:09');

--
-- Dumping data for table `property_images`
--

INSERT INTO `property_images` (`id`, `property_id`, `image_path`, `is_primary`) VALUES
(1, 1, 'assets/images/properties/modern-apartment-1.jpg', 1),
(2, 1, 'assets/images/properties/modern-apartment-2.jpg', 0),
(3, 1, 'assets/images/properties/modern-apartment-3.jpg', 0),
(4, 2, 'assets/images/properties/luxury-villa-1.jpg', 1),
(5, 2, 'assets/images/properties/luxury-villa-2.jpg', 0),
(6, 3, 'assets/images/properties/family-home-1.jpg', 1),
(7, 3, 'assets/images/properties/family-home-2.jpg', 0);

COMMIT;
