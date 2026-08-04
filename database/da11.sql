-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Aug 04, 2026 at 07:42 PM
-- Server version: 8.4.3
-- PHP Version: 8.3.26

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `da11`
--

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `id` int NOT NULL,
  `tour_id` int NOT NULL,
  `departure_id` int DEFAULT NULL,
  `customer_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_phone` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `num_people` int DEFAULT '1',
  `total_price` bigint DEFAULT '0',
  `booking_date` date NOT NULL,
  `status` tinyint DEFAULT '0' COMMENT '0=Chờ xác nhận,1=Đã xác nhận,2=Đã hủy',
  `check_in_status` tinyint(1) NOT NULL DEFAULT '0',
  `checked_in_at` datetime DEFAULT NULL,
  `note` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`id`, `tour_id`, `departure_id`, `customer_name`, `customer_email`, `customer_phone`, `num_people`, `total_price`, `booking_date`, `status`, `check_in_status`, `checked_in_at`, `note`, `created_at`) VALUES
(1, 1, NULL, 'Nguyen Van A', 'a@gmail.com', '0901111111', 2, 100000000, '2025-11-03', 1, 0, NULL, NULL, '2026-07-27 16:37:31'),
(2, 1, NULL, 'Tran Thi B', 'b@gmail.com', '0902222222', 1, 50000000, '2025-11-07', 1, 0, NULL, NULL, '2026-07-27 16:37:31'),
(3, 1, NULL, 'Le Van C', 'c@gmail.com', '0903333333', 3, 150000000, '2025-11-10', 1, 0, NULL, NULL, '2026-07-27 16:37:31'),
(4, 1, NULL, 'Pham Thi D', 'd@gmail.com', '0904444444', 1, 50000000, '2025-11-14', 0, 0, NULL, NULL, '2026-07-27 16:37:31'),
(5, 1, NULL, 'Hoang Van E', 'e@gmail.com', '0905555555', 2, 100000000, '2025-11-19', 1, 0, NULL, NULL, '2026-07-27 16:37:31'),
(6, 1, NULL, 'Vu Thi F', 'f@gmail.com', '0906666666', 1, 50000000, '2025-11-21', 1, 0, NULL, NULL, '2026-07-27 16:37:31'),
(7, 1, NULL, 'Dang Van G', 'g@gmail.com', '0907777777', 2, 100000000, '2025-11-24', 1, 0, NULL, NULL, '2026-07-27 16:37:31'),
(8, 1, NULL, 'Bui Thi H', 'h@gmail.com', '0908888888', 1, 50000000, '2025-11-26', 0, 0, NULL, NULL, '2026-07-27 16:37:31'),
(9, 1, 1, 'Ngo Van I', 'i@gmail.com', '0909999999', 3, 150000000, '2025-11-28', 1, 0, NULL, NULL, '2026-07-27 16:37:31'),
(10, 1, NULL, 'Do Thi K', 'k@gmail.com', '0900000000', 2, 100000000, '2025-11-30', 2, 0, NULL, NULL, '2026-07-27 16:37:31'),
(11, 2, 2, 'Chuẩn', 'zvuchuan98@gmail.com', '0349422856', 17, 42500000, '2026-08-04', 1, 0, NULL, '', '2026-08-04 15:00:20'),
(12, 2, NULL, 'Do Thi K', 'k@gmail.com', '0900000000', 3, 7500000, '2026-08-04', 0, 0, NULL, '', '2026-08-04 15:02:40'),
(13, 2, 2, 'Nguyễn Văn An', 'demo-guest-2-01@example.com', '0900020001', 1, 2500000, '2026-08-04', 1, 0, NULL, 'Dữ liệu mẫu (17 khách)', '2026-08-05 02:03:05'),
(14, 2, 2, 'Trần Thị Bích', 'demo-guest-2-02@example.com', '0900020002', 1, 2500000, '2026-08-04', 1, 0, NULL, 'Dữ liệu mẫu (17 khách)', '2026-08-05 02:03:05'),
(15, 2, 2, 'Lê Minh Châu', 'demo-guest-2-03@example.com', '0900020003', 1, 2500000, '2026-08-04', 1, 0, NULL, 'Dữ liệu mẫu (17 khách)', '2026-08-05 02:03:05'),
(16, 2, 2, 'Phạm Quốc Dũng', 'demo-guest-2-04@example.com', '0900020004', 1, 2500000, '2026-08-04', 1, 0, NULL, 'Dữ liệu mẫu (17 khách)', '2026-08-05 02:03:05'),
(17, 2, 2, 'Võ Thị Hạnh', 'demo-guest-2-05@example.com', '0900020005', 1, 2500000, '2026-08-04', 1, 0, NULL, 'Dữ liệu mẫu (17 khách)', '2026-08-05 02:03:05'),
(18, 2, 2, 'Đặng Gia Huy', 'demo-guest-2-06@example.com', '0900020006', 1, 2500000, '2026-08-04', 1, 0, NULL, 'Dữ liệu mẫu (17 khách)', '2026-08-05 02:03:05'),
(19, 2, 2, 'Bùi Thị Lan', 'demo-guest-2-07@example.com', '0900020007', 1, 2500000, '2026-08-04', 1, 0, NULL, 'Dữ liệu mẫu (17 khách)', '2026-08-05 02:03:05'),
(20, 2, 2, 'Hoàng Đức Long', 'demo-guest-2-08@example.com', '0900020008', 1, 2500000, '2026-08-04', 1, 0, NULL, 'Dữ liệu mẫu (17 khách)', '2026-08-05 02:03:05'),
(21, 2, 2, 'Phan Thị Mai', 'demo-guest-2-09@example.com', '0900020009', 1, 2500000, '2026-08-04', 1, 0, NULL, 'Dữ liệu mẫu (17 khách)', '2026-08-05 02:03:05'),
(22, 2, 2, 'Ngô Minh Nhật', 'demo-guest-2-10@example.com', '0900020010', 1, 2500000, '2026-08-04', 1, 0, NULL, 'Dữ liệu mẫu (17 khách)', '2026-08-05 02:03:05'),
(23, 2, 2, 'Đỗ Thị Oanh', 'demo-guest-2-11@example.com', '0900020011', 1, 2500000, '2026-08-04', 1, 0, NULL, 'Dữ liệu mẫu (17 khách)', '2026-08-05 02:03:05'),
(24, 2, 2, 'Dương Quốc Phong', 'demo-guest-2-12@example.com', '0900020012', 1, 2500000, '2026-08-04', 1, 0, NULL, 'Dữ liệu mẫu (17 khách)', '2026-08-05 02:03:05'),
(25, 2, 2, 'Lý Thị Quỳnh', 'demo-guest-2-13@example.com', '0900020013', 1, 2500000, '2026-08-04', 1, 0, NULL, 'Dữ liệu mẫu (17 khách)', '2026-08-05 02:03:05'),
(26, 2, 2, 'Hồ Văn Sang', 'demo-guest-2-14@example.com', '0900020014', 1, 2500000, '2026-08-04', 1, 0, NULL, 'Dữ liệu mẫu (17 khách)', '2026-08-05 02:03:05'),
(27, 2, 2, 'Tạ Minh Tâm', 'demo-guest-2-15@example.com', '0900020015', 1, 2500000, '2026-08-04', 1, 0, NULL, 'Dữ liệu mẫu (17 khách)', '2026-08-05 02:03:05'),
(28, 2, 2, 'Vũ Thị Uyên', 'demo-guest-2-16@example.com', '0900020016', 1, 2500000, '2026-08-04', 1, 0, NULL, 'Dữ liệu mẫu (17 khách)', '2026-08-05 02:03:05'),
(29, 2, 2, 'Đinh Văn Vinh', 'demo-guest-2-17@example.com', '0900020017', 1, 2500000, '2026-08-04', 1, 0, NULL, 'Dữ liệu mẫu (17 khách)', '2026-08-05 02:03:05'),
(30, 2, 2, 'Nguyễn Văn An', 'demo-guest-2-01@example.com', '0900020001', 1, 2500000, '2026-08-04', 1, 0, NULL, 'Dữ liệu mẫu (17 khách)', '2026-08-05 02:11:08'),
(31, 2, 2, 'Trần Thị Bích', 'demo-guest-2-02@example.com', '0900020002', 1, 2500000, '2026-08-04', 1, 0, NULL, 'Dữ liệu mẫu (17 khách)', '2026-08-05 02:11:08'),
(32, 2, 2, 'Lê Minh Châu', 'demo-guest-2-03@example.com', '0900020003', 1, 2500000, '2026-08-04', 1, 0, NULL, 'Dữ liệu mẫu (17 khách)', '2026-08-05 02:11:08'),
(33, 2, 2, 'Phạm Quốc Dũng', 'demo-guest-2-04@example.com', '0900020004', 1, 2500000, '2026-08-04', 1, 0, NULL, 'Dữ liệu mẫu (17 khách)', '2026-08-05 02:11:08'),
(34, 2, 2, 'Võ Thị Hạnh', 'demo-guest-2-05@example.com', '0900020005', 1, 2500000, '2026-08-04', 1, 0, NULL, 'Dữ liệu mẫu (17 khách)', '2026-08-05 02:11:08'),
(35, 2, 2, 'Đặng Gia Huy', 'demo-guest-2-06@example.com', '0900020006', 1, 2500000, '2026-08-04', 1, 0, NULL, 'Dữ liệu mẫu (17 khách)', '2026-08-05 02:11:08'),
(36, 2, 2, 'Bùi Thị Lan', 'demo-guest-2-07@example.com', '0900020007', 1, 2500000, '2026-08-04', 1, 0, NULL, 'Dữ liệu mẫu (17 khách)', '2026-08-05 02:11:08'),
(37, 2, 2, 'Hoàng Đức Long', 'demo-guest-2-08@example.com', '0900020008', 1, 2500000, '2026-08-04', 1, 0, NULL, 'Dữ liệu mẫu (17 khách)', '2026-08-05 02:11:08'),
(38, 2, 2, 'Phan Thị Mai', 'demo-guest-2-09@example.com', '0900020009', 1, 2500000, '2026-08-04', 1, 0, NULL, 'Dữ liệu mẫu (17 khách)', '2026-08-05 02:11:08'),
(39, 2, 2, 'Ngô Minh Nhật', 'demo-guest-2-10@example.com', '0900020010', 1, 2500000, '2026-08-04', 1, 0, NULL, 'Dữ liệu mẫu (17 khách)', '2026-08-05 02:11:08'),
(40, 2, 2, 'Đỗ Thị Oanh', 'demo-guest-2-11@example.com', '0900020011', 1, 2500000, '2026-08-04', 1, 0, NULL, 'Dữ liệu mẫu (17 khách)', '2026-08-05 02:11:08'),
(41, 2, 2, 'Dương Quốc Phong', 'demo-guest-2-12@example.com', '0900020012', 1, 2500000, '2026-08-04', 1, 0, NULL, 'Dữ liệu mẫu (17 khách)', '2026-08-05 02:11:08'),
(42, 2, 2, 'Lý Thị Quỳnh', 'demo-guest-2-13@example.com', '0900020013', 1, 2500000, '2026-08-04', 1, 0, NULL, 'Dữ liệu mẫu (17 khách)', '2026-08-05 02:11:08'),
(43, 2, 2, 'Hồ Văn Sang', 'demo-guest-2-14@example.com', '0900020014', 1, 2500000, '2026-08-04', 1, 0, NULL, 'Dữ liệu mẫu (17 khách)', '2026-08-05 02:11:08'),
(44, 2, 2, 'Tạ Minh Tâm', 'demo-guest-2-15@example.com', '0900020015', 1, 2500000, '2026-08-04', 1, 0, NULL, 'Dữ liệu mẫu (17 khách)', '2026-08-05 02:11:08'),
(45, 2, 2, 'Vũ Thị Uyên', 'demo-guest-2-16@example.com', '0900020016', 1, 2500000, '2026-08-04', 1, 0, NULL, 'Dữ liệu mẫu (17 khách)', '2026-08-05 02:11:08'),
(46, 2, 2, 'Đinh Văn Vinh', 'demo-guest-2-17@example.com', '0900020017', 1, 2500000, '2026-08-04', 1, 0, NULL, 'Dữ liệu mẫu (17 khách)', '2026-08-05 02:11:08'),
(47, 1, 1, 'Booking mẫu - Tài Nguyên', 'booking.sample@example.com', '0900000000', 10, 10000000, '2026-08-05', 1, 0, NULL, 'Dữ liệu mẫu', '2026-08-05 02:37:51'),
(48, 1, 1, 'Chuẩn', 'demo-booking-17@example.com', '0349422856', 17, 17000000, '2026-08-05', 1, 0, NULL, 'Dữ liệu mẫu (17 khách)', '2026-08-05 02:38:14'),
(49, 2, 2, 'Nguyễn Văn An', 'demo-guest-2-01@example.com', '0900020001', 1, 2500000, '2026-08-04', 1, 0, NULL, 'Dữ liệu mẫu (17 khách)', '2026-08-05 02:39:04'),
(50, 2, 2, 'Trần Thị Bích', 'demo-guest-2-02@example.com', '0900020002', 1, 2500000, '2026-08-04', 1, 0, NULL, 'Dữ liệu mẫu (17 khách)', '2026-08-05 02:39:04'),
(51, 2, 2, 'Lê Minh Châu', 'demo-guest-2-03@example.com', '0900020003', 1, 2500000, '2026-08-04', 1, 0, NULL, 'Dữ liệu mẫu (17 khách)', '2026-08-05 02:39:04'),
(52, 2, 2, 'Phạm Quốc Dũng', 'demo-guest-2-04@example.com', '0900020004', 1, 2500000, '2026-08-04', 1, 0, NULL, 'Dữ liệu mẫu (17 khách)', '2026-08-05 02:39:04'),
(53, 2, 2, 'Võ Thị Hạnh', 'demo-guest-2-05@example.com', '0900020005', 1, 2500000, '2026-08-04', 1, 0, NULL, 'Dữ liệu mẫu (17 khách)', '2026-08-05 02:39:04'),
(54, 2, 2, 'Đặng Gia Huy', 'demo-guest-2-06@example.com', '0900020006', 1, 2500000, '2026-08-04', 1, 0, NULL, 'Dữ liệu mẫu (17 khách)', '2026-08-05 02:39:04'),
(55, 2, 2, 'Bùi Thị Lan', 'demo-guest-2-07@example.com', '0900020007', 1, 2500000, '2026-08-04', 1, 0, NULL, 'Dữ liệu mẫu (17 khách)', '2026-08-05 02:39:04'),
(56, 2, 2, 'Hoàng Đức Long', 'demo-guest-2-08@example.com', '0900020008', 1, 2500000, '2026-08-04', 1, 0, NULL, 'Dữ liệu mẫu (17 khách)', '2026-08-05 02:39:04'),
(57, 2, 2, 'Phan Thị Mai', 'demo-guest-2-09@example.com', '0900020009', 1, 2500000, '2026-08-04', 1, 0, NULL, 'Dữ liệu mẫu (17 khách)', '2026-08-05 02:39:04'),
(58, 2, 2, 'Ngô Minh Nhật', 'demo-guest-2-10@example.com', '0900020010', 1, 2500000, '2026-08-04', 1, 0, NULL, 'Dữ liệu mẫu (17 khách)', '2026-08-05 02:39:04'),
(59, 2, 2, 'Đỗ Thị Oanh', 'demo-guest-2-11@example.com', '0900020011', 1, 2500000, '2026-08-04', 1, 0, NULL, 'Dữ liệu mẫu (17 khách)', '2026-08-05 02:39:04'),
(60, 2, 2, 'Dương Quốc Phong', 'demo-guest-2-12@example.com', '0900020012', 1, 2500000, '2026-08-04', 1, 0, NULL, 'Dữ liệu mẫu (17 khách)', '2026-08-05 02:39:04'),
(61, 2, 2, 'Lý Thị Quỳnh', 'demo-guest-2-13@example.com', '0900020013', 1, 2500000, '2026-08-04', 1, 0, NULL, 'Dữ liệu mẫu (17 khách)', '2026-08-05 02:39:04'),
(62, 2, 2, 'Hồ Văn Sang', 'demo-guest-2-14@example.com', '0900020014', 1, 2500000, '2026-08-04', 1, 0, NULL, 'Dữ liệu mẫu (17 khách)', '2026-08-05 02:39:04'),
(63, 2, 2, 'Tạ Minh Tâm', 'demo-guest-2-15@example.com', '0900020015', 1, 2500000, '2026-08-04', 1, 0, NULL, 'Dữ liệu mẫu (17 khách)', '2026-08-05 02:39:04'),
(64, 2, 2, 'Vũ Thị Uyên', 'demo-guest-2-16@example.com', '0900020016', 1, 2500000, '2026-08-04', 1, 0, NULL, 'Dữ liệu mẫu (17 khách)', '2026-08-05 02:39:04'),
(65, 2, 2, 'Đinh Văn Vinh', 'demo-guest-2-17@example.com', '0900020017', 1, 2500000, '2026-08-04', 1, 0, NULL, 'Dữ liệu mẫu (17 khách)', '2026-08-05 02:39:04'),
(66, 2, 2, 'Nguyễn Văn An', 'demo-guest-2-01@example.com', '0900020001', 1, 2500000, '2026-08-04', 1, 0, NULL, 'Dữ liệu mẫu (17 khách)', '2026-08-05 02:39:08'),
(67, 2, 2, 'Trần Thị Bích', 'demo-guest-2-02@example.com', '0900020002', 1, 2500000, '2026-08-04', 1, 0, NULL, 'Dữ liệu mẫu (17 khách)', '2026-08-05 02:39:08'),
(68, 2, 2, 'Lê Minh Châu', 'demo-guest-2-03@example.com', '0900020003', 1, 2500000, '2026-08-04', 1, 0, NULL, 'Dữ liệu mẫu (17 khách)', '2026-08-05 02:39:08'),
(69, 2, 2, 'Phạm Quốc Dũng', 'demo-guest-2-04@example.com', '0900020004', 1, 2500000, '2026-08-04', 1, 0, NULL, 'Dữ liệu mẫu (17 khách)', '2026-08-05 02:39:08'),
(70, 2, 2, 'Võ Thị Hạnh', 'demo-guest-2-05@example.com', '0900020005', 1, 2500000, '2026-08-04', 1, 0, NULL, 'Dữ liệu mẫu (17 khách)', '2026-08-05 02:39:08'),
(71, 2, 2, 'Đặng Gia Huy', 'demo-guest-2-06@example.com', '0900020006', 1, 2500000, '2026-08-04', 1, 0, NULL, 'Dữ liệu mẫu (17 khách)', '2026-08-05 02:39:08'),
(72, 2, 2, 'Bùi Thị Lan', 'demo-guest-2-07@example.com', '0900020007', 1, 2500000, '2026-08-04', 1, 0, NULL, 'Dữ liệu mẫu (17 khách)', '2026-08-05 02:39:08'),
(73, 2, 2, 'Hoàng Đức Long', 'demo-guest-2-08@example.com', '0900020008', 1, 2500000, '2026-08-04', 1, 0, NULL, 'Dữ liệu mẫu (17 khách)', '2026-08-05 02:39:08'),
(74, 2, 2, 'Phan Thị Mai', 'demo-guest-2-09@example.com', '0900020009', 1, 2500000, '2026-08-04', 1, 0, NULL, 'Dữ liệu mẫu (17 khách)', '2026-08-05 02:39:08'),
(75, 2, 2, 'Ngô Minh Nhật', 'demo-guest-2-10@example.com', '0900020010', 1, 2500000, '2026-08-04', 1, 0, NULL, 'Dữ liệu mẫu (17 khách)', '2026-08-05 02:39:08'),
(76, 2, 2, 'Đỗ Thị Oanh', 'demo-guest-2-11@example.com', '0900020011', 1, 2500000, '2026-08-04', 1, 0, NULL, 'Dữ liệu mẫu (17 khách)', '2026-08-05 02:39:08'),
(77, 2, 2, 'Dương Quốc Phong', 'demo-guest-2-12@example.com', '0900020012', 1, 2500000, '2026-08-04', 1, 0, NULL, 'Dữ liệu mẫu (17 khách)', '2026-08-05 02:39:08'),
(78, 2, 2, 'Lý Thị Quỳnh', 'demo-guest-2-13@example.com', '0900020013', 1, 2500000, '2026-08-04', 1, 0, NULL, 'Dữ liệu mẫu (17 khách)', '2026-08-05 02:39:08'),
(79, 2, 2, 'Hồ Văn Sang', 'demo-guest-2-14@example.com', '0900020014', 1, 2500000, '2026-08-04', 1, 0, NULL, 'Dữ liệu mẫu (17 khách)', '2026-08-05 02:39:08'),
(80, 2, 2, 'Tạ Minh Tâm', 'demo-guest-2-15@example.com', '0900020015', 1, 2500000, '2026-08-04', 1, 0, NULL, 'Dữ liệu mẫu (17 khách)', '2026-08-05 02:39:08'),
(81, 2, 2, 'Vũ Thị Uyên', 'demo-guest-2-16@example.com', '0900020016', 1, 2500000, '2026-08-04', 1, 0, NULL, 'Dữ liệu mẫu (17 khách)', '2026-08-05 02:39:08'),
(82, 2, 2, 'Đinh Văn Vinh', 'demo-guest-2-17@example.com', '0900020017', 1, 2500000, '2026-08-04', 1, 0, NULL, 'Dữ liệu mẫu (17 khách)', '2026-08-05 02:39:08');

-- --------------------------------------------------------

--
-- Table structure for table `booking_guests`
--

CREATE TABLE `booking_guests` (
  `id` int NOT NULL,
  `booking_id` int NOT NULL,
  `full_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `gender` enum('male','female','other') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `phone` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `identity_no` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payment_status` enum('unpaid','deposit','paid') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'unpaid',
  `check_in_status` tinyint(1) NOT NULL DEFAULT '0',
  `checked_in_at` datetime DEFAULT NULL,
  `note` text COLLATE utf8mb4_unicode_ci,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `booking_guests`
--

INSERT INTO `booking_guests` (`id`, `booking_id`, `full_name`, `gender`, `dob`, `phone`, `email`, `identity_no`, `address`, `payment_status`, `check_in_status`, `checked_in_at`, `note`, `created_at`, `updated_at`) VALUES
(1, 47, 'Huỳnh Bảo Ny', 'female', '2010-08-05', '0986714037', 'nguyenanhtai24082006@gmail.com', '12345', 'Huế', 'unpaid', 0, NULL, NULL, '2026-08-05 02:37:51', NULL),
(2, 47, 'Nguyễn Anh Tài', 'male', '2025-11-30', '0986714036', 'nguyenanhtai24082006@gmail.com', '12345', 'Hà Nội', 'deposit', 1, '2026-08-05 02:37:51', 'Yêu cầu: có cỏ', '2026-08-05 02:37:51', NULL),
(3, 46, 'Đinh Văn Vinh', NULL, NULL, '0900020017', 'demo-guest-2-17@example.com', NULL, NULL, 'unpaid', 0, NULL, NULL, '2026-08-05 02:38:40', NULL),
(4, 65, 'Đinh Văn Vinh', NULL, NULL, '0900020017', 'demo-guest-2-17@example.com', NULL, NULL, 'unpaid', 0, NULL, NULL, '2026-08-05 02:39:04', NULL),
(5, 82, 'Đinh Văn Vinh', NULL, NULL, '0900020017', 'demo-guest-2-17@example.com', NULL, NULL, 'unpaid', 0, NULL, NULL, '2026-08-05 02:39:08', NULL),
(6, 48, 'Chuẩn', NULL, NULL, '0349422856', 'demo-booking-17@example.com', NULL, NULL, 'unpaid', 1, '2026-08-04 19:39:45', NULL, '2026-08-05 02:39:28', '2026-08-05 02:39:45'),
(7, 48, 'Chuẩn 2', NULL, NULL, '0349422856', 'demo-booking-17@example.com', NULL, NULL, 'unpaid', 0, NULL, NULL, '2026-08-05 02:39:28', NULL),
(8, 48, 'Chuẩn 3', NULL, NULL, '0349422856', 'demo-booking-17@example.com', NULL, NULL, 'unpaid', 0, NULL, NULL, '2026-08-05 02:39:28', NULL),
(9, 48, 'Chuẩn 4', NULL, NULL, '0349422856', 'demo-booking-17@example.com', NULL, NULL, 'unpaid', 0, NULL, NULL, '2026-08-05 02:39:28', NULL),
(10, 48, 'Chuẩn 5', NULL, NULL, '0349422856', 'demo-booking-17@example.com', NULL, NULL, 'unpaid', 0, NULL, NULL, '2026-08-05 02:39:28', NULL),
(11, 48, 'Chuẩn 6', NULL, NULL, '0349422856', 'demo-booking-17@example.com', NULL, NULL, 'unpaid', 0, NULL, NULL, '2026-08-05 02:39:28', NULL),
(12, 48, 'Chuẩn 7', NULL, NULL, '0349422856', 'demo-booking-17@example.com', NULL, NULL, 'unpaid', 0, NULL, NULL, '2026-08-05 02:39:28', NULL),
(13, 48, 'Chuẩn 8', NULL, NULL, '0349422856', 'demo-booking-17@example.com', NULL, NULL, 'unpaid', 0, NULL, NULL, '2026-08-05 02:39:28', NULL),
(14, 48, 'Chuẩn 9', NULL, NULL, '0349422856', 'demo-booking-17@example.com', NULL, NULL, 'unpaid', 0, NULL, NULL, '2026-08-05 02:39:28', NULL),
(15, 48, 'Chuẩn 10', NULL, NULL, '0349422856', 'demo-booking-17@example.com', NULL, NULL, 'unpaid', 0, NULL, NULL, '2026-08-05 02:39:28', NULL),
(16, 48, 'Chuẩn 11', NULL, NULL, '0349422856', 'demo-booking-17@example.com', NULL, NULL, 'unpaid', 0, NULL, NULL, '2026-08-05 02:39:28', NULL),
(17, 48, 'Chuẩn 12', NULL, NULL, '0349422856', 'demo-booking-17@example.com', NULL, NULL, 'unpaid', 0, NULL, NULL, '2026-08-05 02:39:28', NULL),
(18, 48, 'Chuẩn 13', NULL, NULL, '0349422856', 'demo-booking-17@example.com', NULL, NULL, 'unpaid', 0, NULL, NULL, '2026-08-05 02:39:28', NULL),
(19, 48, 'Chuẩn 14', NULL, NULL, '0349422856', 'demo-booking-17@example.com', NULL, NULL, 'unpaid', 0, NULL, NULL, '2026-08-05 02:39:28', NULL),
(20, 48, 'Chuẩn 15', NULL, NULL, '0349422856', 'demo-booking-17@example.com', NULL, NULL, 'unpaid', 0, NULL, NULL, '2026-08-05 02:39:28', NULL),
(21, 48, 'Chuẩn 16', NULL, NULL, '0349422856', 'demo-booking-17@example.com', NULL, NULL, 'unpaid', 0, NULL, NULL, '2026-08-05 02:39:28', NULL),
(22, 48, 'Chuẩn 17', NULL, NULL, '0349422856', 'demo-booking-17@example.com', NULL, NULL, 'unpaid', 0, NULL, NULL, '2026-08-05 02:39:28', NULL),
(24, 9, 'Ngo Van I', NULL, NULL, '0909999999', 'i@gmail.com', NULL, NULL, 'unpaid', 0, NULL, NULL, '2026-08-05 02:41:35', NULL),
(25, 9, 'Ngo Van I 2', NULL, NULL, '0909999999', 'i@gmail.com', NULL, NULL, 'unpaid', 0, NULL, NULL, '2026-08-05 02:41:35', NULL),
(26, 9, 'Ngo Van I 3', NULL, NULL, '0909999999', 'i@gmail.com', NULL, NULL, 'unpaid', 0, NULL, NULL, '2026-08-05 02:41:35', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `departures`
--

CREATE TABLE `departures` (
  `id` int NOT NULL,
  `tour_id` int NOT NULL,
  `group_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `departure_date` date NOT NULL,
  `return_date` date DEFAULT NULL,
  `max_participants` int NOT NULL DEFAULT '0',
  `meeting_point` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `meeting_time` time DEFAULT NULL,
  `vehicle` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `status` enum('scheduled','in_progress','completed','cancelled') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'scheduled',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `departures`
--

INSERT INTO `departures` (`id`, `tour_id`, `group_name`, `departure_date`, `return_date`, `max_participants`, `meeting_point`, `meeting_time`, `vehicle`, `notes`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, NULL, '2026-08-10', '2026-08-12', 30, 'Văn phòng công ty', '08:00:00', 'Xe du lịch', 'Chuyến mẫu', 'scheduled', '2026-08-03 13:04:26', NULL),
(2, 2, NULL, '2026-08-11', '2026-08-13', 20, 'Hà nội', '06:00:00', 'Xe khách', '', 'completed', '2026-08-04 08:16:48', '2026-08-04 08:25:54'),
(3, 1, 'Đoàn Mùa Hè', '2026-08-18', '2026-08-20', 25, 'Bến xe Miền Đông', '07:30:00', 'Xe ghế ngồi', 'Tour hè miền Nam', 'scheduled', '2026-08-05 10:00:00', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `hdv`
--

CREATE TABLE `hdv` (
  `HDV_id` int NOT NULL COMMENT 'Mã định danh duy nhất cho Hướng dẫn viên',
  `Hoten` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Họ và tên đầy đủ',
  `Ngaysinh` date DEFAULT NULL COMMENT 'Ngày sinh',
  `Gioitinh` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Giới tính',
  `Lienhe` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Thông tin liên hệ (SĐT hoặc email)',
  `Ngonngu` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Ngôn ngữ hướng dẫn',
  `Diachi` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Địa chỉ cư trú',
  `chungchiHDV` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Số hoặc loại chứng chỉ HDV',
  `Kinhnghiem` int DEFAULT NULL COMMENT 'Số năm kinh nghiệm',
  `Ngaybatdaulam` date DEFAULT NULL COMMENT 'Ngày bắt đầu làm việc',
  `Trangthaisuckhoe` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT 'Tình trạng sức khỏe',
  `Ghichunoibo` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT 'Ghi chú nội bộ',
  `Diemdanhgia` decimal(3,1) DEFAULT '0.0' COMMENT 'Điểm đánh giá trung bình',
  `Nhanxetdanhgia` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT 'Nhận xét chi tiết',
  `HDV_group_id` int DEFAULT NULL COMMENT 'ID nhóm HDV',
  `Status` enum('active','inactive','on_leave') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active' COMMENT 'Trạng thái làm việc',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `hdv`
--

INSERT INTO `hdv` (`HDV_id`, `Hoten`, `Ngaysinh`, `Gioitinh`, `Lienhe`, `Ngonngu`, `Diachi`, `chungchiHDV`, `Kinhnghiem`, `Ngaybatdaulam`, `Trangthaisuckhoe`, `Ghichunoibo`, `Diemdanhgia`, `Nhanxetdanhgia`, `HDV_group_id`, `Status`, `created_at`, `updated_at`) VALUES
(1, 'Nguyễn Văn An', '1990-05-15', 'Nam', '0901234567 - an.nguyen@email.com', 'Tiếng Anh, Tiếng Việt', '123 Nguyễn Huệ, Q.1, TP.HCM', 'HDV Quốc tế #9921', 5, '2020-01-15', 'Tốt, thể lực đảm bảo leo núi', 'Nhiệt tình, dẫn tour nội địa & quốc tế tốt', 4.8, 'Khách hàng phản hồi rất tích cực về thái độ phục vụ.', 1, 'active', '2026-07-28 20:50:04', NULL),
(2, 'Trần Thị Bích', '1995-08-20', 'Nữ', '0902345678 - bich.tran@email.com', 'Tiếng Trung, Tiếng Việt', '456 Lê Lợi, Q.3, TP.HCM', 'HDV Nội địa #4412', 3, '2022-03-01', 'Bình thường', 'Phù hợp các tour nghỉ dưỡng văn hóa', 4.5, 'Nhiệt tình chăm sóc đoàn khách gia đình.', 2, 'active', '2026-07-28 20:50:04', NULL),
(3, 'Lê Hoàng Cường', '1988-12-10', 'Nam', '0903456789 - cuong.le@email.com', 'Tiếng Nhật, Tiếng Anh', '789 Trần Hưng Đạo, Q.5, TP.HCM', 'HDV Quốc tế #8820', 8, '2019-06-20', 'Tốt', 'Chuyên tuyến tour Nhật Bản và Đông Nam Á', 4.9, 'Rất chuyên nghiệp, nhiều kinh nghiệm xử lý sự cố.', 1, 'active', '2026-07-28 20:50:04', NULL),
(4, 'Phạm Minh Đức', '1993-03-25', 'Nam', '0904567890 - duc.pham@email.com', 'Tiếng Hàn', '321 Hai Bà Trưng, Q.1, TP.HCM', 'HDV Nội địa #5123', 2, '2023-01-10', 'Tốt', 'Đang học thêm tiếng Anh nâng cao', 4.2, 'Năng nổ, hòa đồng.', 2, 'on_leave', '2026-07-28 20:50:04', NULL),
(5, 'Hoàng Thị Em', '1997-07-30', 'Nữ', '0905678901 - em.hoang@email.com', 'Tiếng Pháp', '654 Võ Văn Tần, Q.3, TP.HCM', 'HDV Quốc tế #3301', 4, '2021-09-05', 'Bình thường', 'Đã hết hạn hợp đồng', 4.0, 'Hoàn thành tốt các công việc được giao.', 3, 'inactive', '2026-07-28 20:50:04', NULL),
(6, 'Nguyễn Minh Tâm', '1992-09-18', 'Nam', '0906789012 - tam.nguyen@email.com', 'Tiếng Anh', '789 Nguyễn Thị Minh Khai, Q.3, TP.HCM', 'HDV Nội địa #5567', 4, '2021-04-15', 'Tốt', 'Nhiệt tình, chủ yếu dẫn tour Miền Nam', 4.6, 'Dẫn tour nhẹ nhàng, chăm sóc khách tốt.', 3, 'active', '2026-08-05 09:10:00', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `services`
--

CREATE TABLE `services` (
  `id` int NOT NULL,
  `tour_id` int NOT NULL,
  `service_types` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `supplier` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `quantity` int NOT NULL DEFAULT '1',
  `status` tinyint NOT NULL DEFAULT '0' COMMENT '0: Cho, 1: Xac nhan, 2: Hoan tat',
  `start_time` datetime DEFAULT NULL,
  `end_time` datetime DEFAULT NULL,
  `note` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `services`
--

INSERT INTO `services` (`id`, `tour_id`, `service_types`, `supplier`, `quantity`, `status`, `start_time`, `end_time`, `note`, `created_at`, `updated_at`) VALUES
(4, 1, 'Tham quan, Nhà hàng, Vé máy bay, Khách sạn, Xe', 'Công ty Xe Anh Tài', 4, 1, '2025-12-01 12:22:00', '2025-12-03 12:22:00', '', '2026-08-04 09:48:58', '2026-08-04 09:48:58'),
(5, 2, 'Nhà hàng, Khách sạn, Xe', 'Công ty Xe Anh Tài', 10, 0, NULL, NULL, '', '2026-08-04 09:48:58', '2026-08-04 09:48:58'),
(6, 1, 'Xe', 'Công ty Xe Anh Tài', 10, 1, NULL, NULL, '', '2026-08-04 09:48:58', '2026-08-04 09:50:23');

-- --------------------------------------------------------

--
-- Table structure for table `staff_assignments`
--

CREATE TABLE `staff_assignments` (
  `id` int NOT NULL,
  `departure_id` int NOT NULL,
  `staff_id` int NOT NULL,
  `role` enum('lead_guide','assistant_guide','driver','photographer','other') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'other',
  `responsibilities` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `status` enum('assigned','confirmed','completed','rejected') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'assigned',
  `assigned_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `staff_assignments`
--

INSERT INTO `staff_assignments` (`id`, `departure_id`, `staff_id`, `role`, `responsibilities`, `notes`, `status`, `assigned_at`, `updated_at`) VALUES
(1, 1, 3, 'lead_guide', '', '', 'assigned', '2026-08-03 15:47:04', NULL),
(2, 2, 1, 'lead_guide', '', '', 'assigned', '2026-08-04 08:17:03', NULL),
(3, 1, 6, 'driver', 'Chịu trách nhiệm lái xe theo hành trình', 'Xe khách VIP', 'confirmed', '2026-08-05 09:00:00', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `tours`
--

CREATE TABLE `tours` (
  `id` int NOT NULL,
  `category_id` int NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `price` int DEFAULT '0',
  `duration` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `image` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tours`
--

INSERT INTO `tours` (`id`, `category_id`, `name`, `price`, `duration`, `description`, `image`, `status`) VALUES
(1, 1, 'Du lịch Cáp', 50000000, '3 ngày', 'Tour Du lịch Cáp mang đến trải nghiệm khám phá thiên nhiên núi rừng tuyệt đẹp.', 'storage/uploads/tours/1785829660-Dangnhap1.jpg', 1),
(2, 2, 'Vũ Đình Chuẩn', 2500000, '3 ngày', 'rdzjeryk', 'storage/uploads/tours/1785829956-OIP (3).jpg', 1),
(3, 1, 'Tour Biển Miền Nam', 3200000, '4 ngày', 'Khám phá bờ biển miền Nam với trải nghiệm nghỉ dưỡng trọn gói.', 'storage/uploads/tours/beach-tour.jpg', 1);

-- --------------------------------------------------------

--
-- Table structure for table `tour_categories`
--

CREATE TABLE `tour_categories` (
  `id` int NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tour_categories`
--

INSERT INTO `tour_categories` (`id`, `name`, `description`) VALUES
(1, 'Du lịch núi', 'Các tour khám phá núi rừng, trekking'),
(2, 'Du lịch thành phố', 'Các tour khám phá thành thị, văn hóa');

-- --------------------------------------------------------

--
-- Table structure for table `tour_diaries`
--

CREATE TABLE `tour_diaries` (
  `id` int NOT NULL,
  `departure_id` int NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `diary_date` date NOT NULL,
  `weather` varchar(100) DEFAULT NULL,
  `mood` varchar(100) DEFAULT NULL,
  `photos` text,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tour_diaries`
--

INSERT INTO `tour_diaries` (`id`, `departure_id`, `title`, `content`, `diary_date`, `weather`, `mood`, `photos`, `created_at`, `updated_at`) VALUES
(3, 1, 'ưefa', 'àvSFV', '2026-08-11', 'Mưa phùn', 'Kỳ diệu', 'Array', '2026-08-04 17:33:56', '2026-08-05 00:33:56');

-- --------------------------------------------------------

--
-- Table structure for table `tour_logs`
--

CREATE TABLE `tour_logs` (
  `id` int NOT NULL,
  `departure_id` int NOT NULL,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `log_date` datetime NOT NULL,
  `location` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `weather` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mood` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `images` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `author_id` int DEFAULT NULL,
  `status` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'published',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tour_logs`
--

INSERT INTO `tour_logs` (`id`, `departure_id`, `title`, `content`, `log_date`, `location`, `weather`, `mood`, `images`, `author_id`, `status`, `created_at`, `updated_at`) VALUES
(1, 2, 'ninh bình', 'ok', '2026-08-04 12:38:00', 'Ninh Bình', 'Nắng', 'happy', NULL, NULL, 'published', '2026-08-04 05:38:43', '2026-08-04 12:38:43');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `role` enum('admin','user') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'user',
  `avatar` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint DEFAULT '1',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `phone`, `role`, `avatar`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Administrator', 'admin@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '0901234567', 'admin', NULL, 1, '2026-07-27 16:37:31', '2026-07-27 16:37:31'),
(2, 'Người dùng 1', 'user1@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '0902345678', 'user', NULL, 1, '2026-07-27 16:37:31', '2026-07-27 16:37:31'),
(3, 'Người dùng 2', 'user2@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '0903456789', 'user', NULL, 1, '2026-07-27 16:37:31', '2026-07-27 16:37:31');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_bookings_tour` (`tour_id`),
  ADD KEY `idx_bookings_departure_id` (`departure_id`);

--
-- Indexes for table `booking_guests`
--
ALTER TABLE `booking_guests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_booking_guests_booking_id` (`booking_id`),
  ADD KEY `idx_booking_guests_check_in_status` (`check_in_status`);

--
-- Indexes for table `departures`
--
ALTER TABLE `departures`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_departures_tour_id` (`tour_id`);

--
-- Indexes for table `hdv`
--
ALTER TABLE `hdv`
  ADD PRIMARY KEY (`HDV_id`);

--
-- Indexes for table `services`
--
ALTER TABLE `services`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tour_id` (`tour_id`);

--
-- Indexes for table `staff_assignments`
--
ALTER TABLE `staff_assignments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_departure_staff` (`departure_id`,`staff_id`),
  ADD KEY `idx_staff_assignments_departure_id` (`departure_id`),
  ADD KEY `idx_staff_assignments_staff_id` (`staff_id`);

--
-- Indexes for table `tours`
--
ALTER TABLE `tours`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_tours_category` (`category_id`);

--
-- Indexes for table `tour_categories`
--
ALTER TABLE `tour_categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tour_diaries`
--
ALTER TABLE `tour_diaries`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_departure_id` (`departure_id`),
  ADD KEY `idx_diary_date` (`diary_date`);

--
-- Indexes for table `tour_logs`
--
ALTER TABLE `tour_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_departure_id` (`departure_id`),
  ADD KEY `idx_log_date` (`log_date`),
  ADD KEY `idx_status` (`status`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=83;

--
-- AUTO_INCREMENT for table `booking_guests`
--
ALTER TABLE `booking_guests`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `departures`
--
ALTER TABLE `departures`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `hdv`
--
ALTER TABLE `hdv`
  MODIFY `HDV_id` int NOT NULL AUTO_INCREMENT COMMENT 'Mã định danh duy nhất cho Hướng dẫn viên', AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `services`
--
ALTER TABLE `services`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `staff_assignments`
--
ALTER TABLE `staff_assignments`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `tours`
--
ALTER TABLE `tours`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `tour_categories`
--
ALTER TABLE `tour_categories`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `tour_diaries`
--
ALTER TABLE `tour_diaries`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `tour_logs`
--
ALTER TABLE `tour_logs`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `fk_bookings_departure` FOREIGN KEY (`departure_id`) REFERENCES `departures` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_bookings_tour` FOREIGN KEY (`tour_id`) REFERENCES `tours` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `booking_guests`
--
ALTER TABLE `booking_guests`
  ADD CONSTRAINT `fk_booking_guests_booking` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `departures`
--
ALTER TABLE `departures`
  ADD CONSTRAINT `fk_departures_tour` FOREIGN KEY (`tour_id`) REFERENCES `tours` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `services`
--
ALTER TABLE `services`
  ADD CONSTRAINT `services_ibfk_1` FOREIGN KEY (`tour_id`) REFERENCES `tours` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `staff_assignments`
--
ALTER TABLE `staff_assignments`
  ADD CONSTRAINT `fk_staff_departure` FOREIGN KEY (`departure_id`) REFERENCES `departures` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_staff_hdv` FOREIGN KEY (`staff_id`) REFERENCES `hdv` (`HDV_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tours`
--
ALTER TABLE `tours`
  ADD CONSTRAINT `fk_tours_category` FOREIGN KEY (`category_id`) REFERENCES `tour_categories` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `tour_diaries`
--
ALTER TABLE `tour_diaries`
  ADD CONSTRAINT `tour_diaries_ibfk_1` FOREIGN KEY (`departure_id`) REFERENCES `departures` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `tour_logs`
--
ALTER TABLE `tour_logs`
  ADD CONSTRAINT `fk_tour_logs_departure` FOREIGN KEY (`departure_id`) REFERENCES `departures` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
