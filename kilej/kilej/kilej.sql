-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 25, 2025 at 10:10 PM
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
-- Database: `kilej`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `admin_id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`admin_id`, `username`, `email`, `password`) VALUES
(1, 'admin', 'admin@gmail.com', '$2y$10$3gaqlNpNg0WdDrKg3Bfm1eFjega/JdGAowhUyI3gUMheygdDY72Di');

-- --------------------------------------------------------

--
-- Table structure for table `clients`
--

CREATE TABLE `clients` (
  `client_id` int(11) NOT NULL,
  `client_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `clients`
--

INSERT INTO `clients` (`client_id`, `client_name`, `email`, `phone`, `password`) VALUES
(1, 'client', 'client@gmail.com', '0700000000', '$2y$10$G/40OS.EjLM0B4RkCE2Tmupi8PGUMCeSgD8hjU5u22HVMNJGWQm2y');

-- --------------------------------------------------------

--
-- Table structure for table `contractors`
--

CREATE TABLE `contractors` (
  `contractor_id` int(11) NOT NULL,
  `contractor_name` varchar(255) DEFAULT NULL,
  `phone_number` varchar(15) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `specialization` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `employees`
--

CREATE TABLE `employees` (
  `employee_id` int(11) NOT NULL,
  `employee_name` varchar(255) DEFAULT NULL,
  `phone_number` varchar(15) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `roles` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `feedback`
--

CREATE TABLE `feedback` (
  `feedback_id` int(11) NOT NULL,
  `client_id` int(11) DEFAULT NULL,
  `message` varchar(255) DEFAULT NULL,
  `response` varchar(255) DEFAULT NULL,
  `date_submitted` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `house_construction`
--

CREATE TABLE `house_construction` (
  `project_id` int(11) NOT NULL,
  `client_id` int(11) DEFAULT NULL,
  `id` int(11) DEFAULT NULL,
  `house_type` varchar(255) DEFAULT NULL,
  `house_size` decimal(10,2) DEFAULT NULL,
  `total_cost` decimal(10,2) DEFAULT NULL,
  `house_status` varchar(255) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `completion_date` date DEFAULT NULL,
  `contractor_id` int(11) DEFAULT NULL
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `legal_documents`
--

CREATE TABLE`legal_documents` (
  `document_id` int(11) NOT NULL,
  `transaction_id` int(11) DEFAULT NULL,
  `document_type` varchar(255) DEFAULT NULL,
  `document_url` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `property_image`
--

CREATE TABLE `property_image` (
  `image_id` int(11) NOT NULL,
  `id` int(11) DEFAULT NULL,
  `project_id` int(11) DEFAULT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `image_type` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `property_listings`
--

CREATE TABLE `property_listings` (
  `id` int(11) NOT NULL,
  `property_title` varchar(255) NOT NULL,
  `location` varchar(255) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `status` enum('Available','Sold','Pending') NOT NULL DEFAULT 'Available',
  `image_path` varchar(255) DEFAULT NULL,
  `date_added` timestamp NOT NULL DEFAULT current_timestamp(),
  `seller_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `property_listings`
--

INSERT INTO `property_listings` (`id`, `property_title`, `location`, `price`, `status`, `image_path`, `date_added`, `seller_id`) VALUES
(1, 'Land', 'MUT', 1000999.00, 'Available', 'logo.jpg', '2025-03-25 20:46:45', 1);

-- --------------------------------------------------------

--
-- Table structure for table `sellers`
--

CREATE TABLE `sellers` (
  `seller_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `address` text DEFAULT NULL,
  `date_registered` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sellers`
--

INSERT INTO `sellers` (`seller_id`, `name`, `email`, `phone`, `address`, `date_registered`) VALUES
(1, 'qwerty', 'seller@gmail.com', '0722000000', NULL, '2025-03-25 20:43:04');

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `transaction_id` int(11) NOT NULL,
  `client_id` int(11) NOT NULL,
  `id` int(11) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `payment_status` enum('Pending','Completed') NOT NULL,
  `transaction_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `virtual_tour`
--

CREATE TABLE `virtual_tour` (
  `tour_id` int(11) NOT NULL,
  `project_id` int(11) DEFAULT NULL,
  `media_type` enum('image','video') DEFAULT NULL,
  `media_url` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `upload_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`admin_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `clients`
--
ALTER TABLE `clients`
  ADD PRIMARY KEY (`client_id`);

--
-- Indexes for table `contractors`
--
ALTER TABLE `contractors`
  ADD PRIMARY KEY (`contractor_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `employees`
--
ALTER TABLE `employees`
  ADD PRIMARY KEY (`employee_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `feedback`
--
ALTER TABLE `feedback`
  ADD PRIMARY KEY (`feedback_id`),
  ADD KEY `client_id` (`client_id`);

--
-- Indexes for table `house_construction`
--
ALTER TABLE `house_construction`
  ADD PRIMARY KEY (`project_id`),
  ADD KEY `client_id` (`client_id`),
  ADD KEY `id` (`id`),
  ADD KEY `contractor_id` (`contractor_id`);

--
-- Indexes for table `legal_documents`
--
ALTER TABLE `legal_documents`
  ADD PRIMARY KEY (`document_id`),
  ADD KEY `transaction_id` (`transaction_id`);

--
-- Indexes for table `property_image`
--
ALTER TABLE `property_image`
  ADD PRIMARY KEY (`image_id`),
  ADD KEY `id` (`id`),
  ADD KEY `project_id` (`project_id`);

--
-- Indexes for table `property_listings`
--
ALTER TABLE `property_listings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_seller` (`seller_id`);

--
-- Indexes for table `sellers`
--
ALTER TABLE `sellers`
  ADD PRIMARY KEY (`seller_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`transaction_id`),
  ADD KEY `client_id` (`client_id`),
  ADD KEY `id` (`id`);

--
-- Indexes for table `virtual_tour`
--
ALTER TABLE `virtual_tour`
  ADD PRIMARY KEY (`tour_id`),
  ADD KEY `project_id` (`project_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `clients`
--
ALTER TABLE `clients`
  MODIFY `client_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `contractors`
--
ALTER TABLE `contractors`
  MODIFY `contractor_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `employees`
--
ALTER TABLE `employees`
  MODIFY `employee_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `feedback`
--
ALTER TABLE `feedback`
  MODIFY `feedback_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `house_construction`
--
ALTER TABLE `house_construction`
  MODIFY `project_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `legal_documents`
--
ALTER TABLE `legal_documents`
  MODIFY `document_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `property_image`
--
ALTER TABLE `property_image`
  MODIFY `image_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `property_listings`
--
ALTER TABLE `property_listings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `sellers`
--
ALTER TABLE `sellers`
  MODIFY `seller_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `transaction_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `virtual_tour`
--
ALTER TABLE `virtual_tour`
  MODIFY `tour_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `feedback`
--
ALTER TABLE `feedback`
  ADD CONSTRAINT `feedback_ibfk_1` FOREIGN KEY (`client_id`) REFERENCES `clients` (`client_id`);

--
-- Constraints for table `house_construction`
--
ALTER TABLE `house_construction`
  ADD CONSTRAINT `house_construction_ibfk_1` FOREIGN KEY (`client_id`) REFERENCES `clients` (`client_id`),
  ADD CONSTRAINT `house_construction_ibfk_2` FOREIGN KEY (`id`) REFERENCES `property_listings` (`id`),
  ADD CONSTRAINT `house_construction_ibfk_3` FOREIGN KEY (`contractor_id`) REFERENCES `contractors` (`contractor_id`);

--
-- Constraints for table `legal_documents`
--
ALTER TABLE `legal_documents`
  ADD CONSTRAINT `legal_documents_ibfk_1` FOREIGN KEY (`transaction_id`) REFERENCES `transactions` (`transaction_id`);

--
-- Constraints for table `property_image`
--
ALTER TABLE `property_image`
  ADD CONSTRAINT `property_image_ibfk_1` FOREIGN KEY (`id`) REFERENCES `property_listings` (`id`),
  ADD CONSTRAINT `property_image_ibfk_2` FOREIGN KEY (`project_id`) REFERENCES `house_construction` (`project_id`);

--
-- Constraints for table `property_listings`
--
ALTER TABLE `property_listings`
  ADD CONSTRAINT `fk_seller` FOREIGN KEY (`seller_id`) REFERENCES `sellers` (`seller_id`) ON DELETE SET NULL;

--
-- Constraints for table `transactions`
--
ALTER TABLE `transactions`
  ADD CONSTRAINT `transactions_ibfk_1` FOREIGN KEY (`client_id`) REFERENCES `clients` (`client_id`),
  ADD CONSTRAINT `transactions_ibfk_2` FOREIGN KEY (`id`) REFERENCES `property_listings` (`id`);

--
-- Constraints for table `virtual_tour`
--
ALTER TABLE `virtual_tour`
  ADD CONSTRAINT `virtual_tour_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `house_construction` (`project_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
