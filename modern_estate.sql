-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 28, 2025 at 08:19 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `modern_estate`
--

-- --------------------------------------------------------

--
-- Table structure for table `favorites`
--

CREATE TABLE `favorites` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `property_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `inquiries`
--

CREATE TABLE `inquiries` (
  `id` int(11) NOT NULL,
  `property_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `message` text NOT NULL,
  `status` enum('new','read','replied') DEFAULT 'new',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `properties`
--

CREATE TABLE `properties` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `price` decimal(12,2) NOT NULL,
  `location` varchar(255) NOT NULL,
  `type` enum('house','apartment','villa','condo') NOT NULL,
  `bedrooms` int(11) NOT NULL,
  `bathrooms` int(11) NOT NULL,
  `area` decimal(10,2) NOT NULL,
  `image` varchar(255) NOT NULL,
  `featured` tinyint(1) DEFAULT 0,
  `status` enum('available','sold','pending') DEFAULT 'available',
  `user_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `properties`
--

INSERT INTO `properties` (`id`, `title`, `description`, `price`, `location`, `type`, `bedrooms`, `bathrooms`, `area`, `image`, `featured`, `status`, `user_id`, `created_at`, `updated_at`) VALUES
(1, 'Modern Downtown Apartment', 'Beautiful modern apartment in the heart of downtown. Features high ceilings, large windows, and modern appliances.', 350000.00, 'Downtown', 'apartment', 2, 2, 1200.00, '', 0, '', NULL, '2025-05-24 10:32:09', '2025-05-24 10:32:09'),
(9, 'Modern Downtown Apartment', 'Beautiful modern apartment in the heart of downtown. Features high ceilings, large windows, and modern appliances.', 350000.00, 'Downtown', 'apartment', 2, 2, 1200.00, 'assets/images/properties/modern-apartment-1.jpg', 0, 'available', NULL, '2025-05-24 10:38:21', '2025-05-24 10:38:21'),
(10, 'Luxury Villa with Pool', 'Stunning luxury villa with private pool, garden, and modern amenities. Perfect for family living.', 850000.00, 'Suburbs', 'villa', 4, 3, 2500.00, 'assets/images/properties/luxury-villa-1.jpg', 0, 'available', NULL, '2025-05-24 10:38:21', '2025-05-24 10:38:21'),
(12, 'Family Home with Garden', 'Spacious family home with large garden and garage. Located in a family-friendly neighborhood.', 450000.00, 'Residential Area', 'house', 3, 2, 1800.00, 'assets/images/properties/family-home-1.jpg', 0, 'available', NULL, '2025-05-24 10:38:21', '2025-05-24 10:38:21'),
(16, 'Luxury Apartment', 'High-end apartment with premium finishes and amenities. Located in a prestigious building.', 420000.00, 'City Center', 'apartment', 2, 2, 1300.00, 'assets/images/properties/luxury-apt-1.jpg', 0, 'available', NULL, '2025-05-24 10:38:21', '2025-05-24 10:38:21'),
(17, 'Modern Downtown Apartment', 'Beautiful modern apartment in the heart of downtown. Features high ceilings, large windows, and modern appliances.', 350000.00, 'Downtown', 'apartment', 2, 2, 1200.00, 'assets/images/properties/modern-apartment-1.jpg', 0, 'available', NULL, '2025-05-24 10:49:13', '2025-05-24 10:49:13'),
(18, 'Luxury Villa with Pool', 'Stunning luxury villa with private pool, garden, and modern amenities. Perfect for family living.', 850000.00, 'Suburbs', 'villa', 4, 3, 2500.00, 'assets/images/properties/luxury-villa-1.jpg', 0, 'available', NULL, '2025-05-24 10:49:13', '2025-05-24 10:49:13'),
(25, 'Modern Downtown Apartment', 'Beautiful modern apartment in the heart of downtown. Features high ceilings, large windows, and modern appliances.', 350000.00, 'Downtown', 'apartment', 2, 2, 1200.00, 'assets/images/properties/modern-apartment-1.jpg', 0, 'available', NULL, '2025-05-27 15:56:09', '2025-05-27 15:56:09'),
(26, 'Luxury Villa with Pool', 'Stunning luxury villa with private pool, garden, and modern amenities. Perfect for family living.', 850000.00, 'Suburbs', 'villa', 4, 3, 2500.00, 'assets/images/properties/luxury-villa-1.jpg', 0, 'available', NULL, '2025-05-27 15:56:09', '2025-05-27 15:56:09'),
(27, 'Cozy Studio Apartment', 'Charming studio apartment in a quiet neighborhood. Recently renovated with new appliances.', 180000.00, 'City Center', 'apartment', 1, 1, 600.00, 'assets/images/properties/studio-1.jpg', 0, 'available', NULL, '2025-05-27 15:56:09', '2025-05-27 15:56:09'),
(28, 'Family Home with Garden', 'Spacious family home with large garden and garage. Located in a family-friendly neighborhood.', 450000.00, 'Residential Area', 'house', 3, 2, 1800.00, 'assets/images/properties/family-home-1.jpg', 0, 'available', NULL, '2025-05-27 15:56:09', '2025-05-27 15:56:09'),
(29, 'Luxury Apartment', 'High-end apartment with premium finishes and amenities. Located in a prestigious building.', 420000.00, 'City Center', 'apartment', 2, 2, 1300.00, 'assets/images/properties/luxury-apt-1.jpg', 0, 'available', NULL, '2025-05-27 15:56:09', '2025-05-27 15:56:09'),
(30, 'Modern Loft', 'Trendy loft with open floor plan and city views.', 390000.00, 'Downtown', 'apartment', 1, 1, 900.00, 'assets/images/properties/modern-apartment-3.jpg', 0, 'available', NULL, '2025-05-27 15:56:09', '2025-05-27 15:56:09'),
(31, 'Suburban Family Home', 'Large home in a quiet suburb, perfect for families.', 480000.00, 'Suburbs', 'house', 4, 3, 2000.00, 'assets/images/properties/family-home-2.jpg', 0, 'available', NULL, '2025-05-27 15:56:09', '2025-05-27 15:56:09'),
(32, 'Studio Retreat', 'Minimalist studio ideal for singles or students.', 160000.00, 'City Center', 'apartment', 1, 1, 500.00, 'assets/images/properties/studio-2.jpg', 0, 'available', NULL, '2025-05-27 15:56:09', '2025-05-27 15:56:09'),
(33, 'Executive Suite', 'Premium suite for business professionals.', 520000.00, 'Business District', 'apartment', 2, 2, 1100.00, 'assets/images/properties/luxury-apt-2.jpg', 0, 'available', NULL, '2025-05-27 15:56:09', '2025-05-27 15:56:09'),
(34, 'Urban Chic Apartment', 'Stylish apartment with modern amenities.', 370000.00, 'Downtown', 'apartment', 2, 1, 950.00, 'assets/images/properties/luxury-apt-3.jpg', 0, 'available', NULL, '2025-05-27 15:56:09', '2025-05-27 15:56:09');

-- --------------------------------------------------------

--
-- Table structure for table `property_images`
--

CREATE TABLE `property_images` (
  `id` int(11) NOT NULL,
  `property_id` int(11) NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `is_primary` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `property_images`
--

INSERT INTO `property_images` (`id`, `property_id`, `image_path`, `is_primary`, `created_at`) VALUES
(1, 1, 'assets/images/properties/modern-apartment-1.jpg', 0, '2025-05-24 10:32:09'),
(2, 1, 'assets/images/properties/modern-apartment-2.jpg', 0, '2025-05-24 10:32:09'),
(3, 1, 'assets/images/properties/modern-apartment-3.jpg', 0, '2025-05-24 10:32:09'),
(25, 1, 'assets/images/properties/modern-apartment-1.jpg', 0, '2025-05-24 10:38:21'),
(26, 1, 'assets/images/properties/modern-apartment-2.jpg', 0, '2025-05-24 10:38:21'),
(27, 1, 'assets/images/properties/modern-apartment-3.jpg', 0, '2025-05-24 10:38:21'),
(49, 1, 'assets/images/properties/modern-apartment-1.jpg', 0, '2025-05-24 10:49:13'),
(50, 1, 'assets/images/properties/modern-apartment-2.jpg', 0, '2025-05-24 10:49:13'),
(51, 1, 'assets/images/properties/modern-apartment-3.jpg', 0, '2025-05-24 10:49:13');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `role` enum('user','admin') DEFAULT 'user',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `profile_picture` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `full_name`, `phone`, `role`, `created_at`, `updated_at`, `profile_picture`) VALUES
(1, 'jdskie', 'jd@gmail.com', '$2y$10$qEV2e46CvVHpU7fbzjg/hOqldZebDufreEYEQeGXc7UXbNaqpweBW', 'jd lapzz', '09229802919', 'user', '2025-05-24 10:26:47', '2025-05-24 10:26:47', NULL),
(2, 'Jdadmin', 'jdadmin@gmail.com', '$2y$10$wH8QwQwQwQwQwQwQwQwQwOQwQwQwQwQwQwQwQwQwQwQwQwQW', 'JD Admin', '09171234567', 'admin', '2025-05-24 11:17:36', '2025-05-24 11:17:36', NULL),
(3, 'Chanadmin', 'chanadmin@gmail.com', '$2y$10$wH8QwQwQwQwQwQwQwQwQwOQwQwQwQwQwQwQwQwQwQwQwQwQW', 'Chan Admin', '09179876543', 'admin', '2025-05-24 11:17:36', '2025-05-24 11:17:36', NULL),
(4, 'gensonadmin', 'gensonadmin@gmail.com', '$2y$10$Kf3n/dlpJeXPvamUbfd54.TcHSqSpgz0TqLa.C.cwXqnDB.EXdahi', 'Allen Genson Admin', '0912345678', 'admin', '2025-05-24 11:49:32', '2025-05-24 11:49:32', NULL),
(6, 'aw', 'awaw@gmail.com', '$2y$10$ibWrONo4VDSU4ZS3eiSQx.wfT73L.iVSW9sYuOz1bLkVFzbNuQHdO', 'awawaw', '123', 'user', '2025-05-27 15:59:20', '2025-05-27 16:02:39', 'assets/images/profiles/profile_6_1748361759.png');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `favorites`
--
ALTER TABLE `favorites`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_favorite` (`user_id`,`property_id`),
  ADD KEY `property_id` (`property_id`);

--
-- Indexes for table `inquiries`
--
ALTER TABLE `inquiries`
  ADD PRIMARY KEY (`id`),
  ADD KEY `property_id` (`property_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `properties`
--
ALTER TABLE `properties`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `property_images`
--
ALTER TABLE `property_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `property_id` (`property_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `favorites`
--
ALTER TABLE `favorites`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `inquiries`
--
ALTER TABLE `inquiries`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `properties`
--
ALTER TABLE `properties`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT for table `property_images`
--
ALTER TABLE `property_images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=75;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `favorites`
--
ALTER TABLE `favorites`
  ADD CONSTRAINT `favorites_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `favorites_ibfk_2` FOREIGN KEY (`property_id`) REFERENCES `properties` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `inquiries`
--
ALTER TABLE `inquiries`
  ADD CONSTRAINT `inquiries_ibfk_1` FOREIGN KEY (`property_id`) REFERENCES `properties` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `inquiries_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `properties`
--
ALTER TABLE `properties`
  ADD CONSTRAINT `properties_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `property_images`
--
ALTER TABLE `property_images`
  ADD CONSTRAINT `property_images_ibfk_1` FOREIGN KEY (`property_id`) REFERENCES `properties` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
