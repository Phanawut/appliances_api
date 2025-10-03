-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Oct 03, 2025 at 06:56 AM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `webapi_demo`
--

-- --------------------------------------------------------

--
-- Table structure for table `appliances`
--

CREATE TABLE `appliances` (
  `id` int(10) UNSIGNED NOT NULL,
  `sku` varchar(32) NOT NULL,
  `name` varchar(150) NOT NULL,
  `brand` varchar(80) NOT NULL,
  `category` varchar(80) NOT NULL,
  `price` decimal(10,2) NOT NULL CHECK (`price` >= 0),
  `stock` int(11) NOT NULL DEFAULT 0 CHECK (`stock` >= 0),
  `warranty_months` tinyint(3) UNSIGNED NOT NULL DEFAULT 12,
  `energy_rating` tinyint(3) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `appliances`
--

INSERT INTO `appliances` (`id`, `sku`, `name`, `brand`, `category`, `price`, `stock`, `warranty_months`, `energy_rating`, `created_at`, `updated_at`) VALUES
(1, 'TV-32A1', 'ทีวี 32 นิ้ว HD', 'Panaphonic', 'ทีวี', 4990.00, 12, 24, 3, '2025-10-03 03:32:01', '2025-10-03 03:32:01'),
(2, 'TV-55U2', 'ทีวี 55 นิ้ว 4K', 'Sangsung', 'ทีวี', 16990.00, 7, 24, 5, '2025-10-03 03:32:01', '2025-10-03 03:32:01'),
(3, 'FR-250S', 'ตู้เย็น 2 ประตู 250L', 'Hitano', 'ตู้เย็น', 8990.00, 10, 36, 5, '2025-10-03 03:32:01', '2025-10-03 03:32:01'),
(4, 'AC-12000', 'แอร์ 12000 BTU อินเวอร์เตอร์', 'Daika', 'แอร์', 13990.00, 6, 60, 5, '2025-10-03 03:32:01', '2025-10-03 03:32:01'),
(5, 'WM-8KG', 'เครื่องซักผ้า 8 กก.', 'Toshiha', 'เครื่องซักผ้า', 6990.00, 9, 24, 4, '2025-10-03 03:32:01', '2025-10-03 03:32:01'),
(6, 'MW-23L', 'ไมโครเวฟ 23 ลิตร', 'Panaphonic', 'ไมโครเวฟ', 2490.00, 20, 12, 3, '2025-10-03 03:32:01', '2025-10-03 03:32:01'),
(7, 'VA-1000', 'เครื่องดูดฝุ่น 1000W', 'Sangsung', 'เครื่องใช้ในบ้าน', 1590.00, 15, 12, 2, '2025-10-03 03:32:01', '2025-10-03 03:32:01'),
(8, 'IH-2000', 'เตาแม่เหล็กไฟฟ้า 2000W', 'Sharpix', 'เครื่องครัว', 1290.00, 25, 12, 3, '2025-10-03 03:32:01', '2025-10-03 03:32:01'),
(9, 'AR-5L', 'หม้อทอดไร้น้ำมัน 5 ลิตร', 'SmartCook', 'เครื่องครัว', 1790.00, 18, 12, 4, '2025-10-03 03:32:01', '2025-10-03 03:32:01'),
(10, 'FR-180S', 'ตู้เย็น 1 ประตู 180L', 'Toshiha', 'ตู้เย็น', 6490.00, 8, 24, 4, '2025-10-03 03:32:01', '2025-10-03 03:32:01');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `appliances`
--
ALTER TABLE `appliances`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `sku` (`sku`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `appliances`
--
ALTER TABLE `appliances`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
