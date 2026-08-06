-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Aug 06, 2026 at 08:34 PM
-- Server version: 8.0.30
-- PHP Version: 8.3.29

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `da10`
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
  `pickup_address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
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

INSERT INTO `bookings` (`id`, `tour_id`, `departure_id`, `customer_name`, `customer_email`, `customer_phone`, `pickup_address`, `num_people`, `total_price`, `booking_date`, `status`, `check_in_status`, `checked_in_at`, `note`, `created_at`) VALUES
(103, 5, 9, 'Thanh Huệ', 'thanhhue001@gmail.com', '0865144307', '8B nghách 46 ngõ 1 Bùi Xương Trạch', 5, 10000000, '2026-08-08', 1, 0, NULL, '', '2026-08-07 02:16:38'),
(104, 6, 8, 'Chuẩn', 'zvuchuan98@gmail.com', '0349422856', '8B nghách 46 ngõ 1 Bùi Xương Trạch', 5, 12500000, '2026-08-07', 1, 0, NULL, '', '2026-08-07 02:54:59');

-- --------------------------------------------------------

--
-- Table structure for table `booking_guests`
--

CREATE TABLE `booking_guests` (
  `id` int NOT NULL,
  `booking_id` int NOT NULL,
  `full_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `gender` enum('male','female','other') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `phone` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `identity_no` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payment_status` enum('unpaid','deposit','paid') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'unpaid',
  `check_in_status` tinyint(1) NOT NULL DEFAULT '0',
  `checked_in_at` datetime DEFAULT NULL,
  `note` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `booking_guests`
--

INSERT INTO `booking_guests` (`id`, `booking_id`, `full_name`, `gender`, `dob`, `phone`, `email`, `identity_no`, `address`, `payment_status`, `check_in_status`, `checked_in_at`, `note`, `created_at`, `updated_at`) VALUES
(34, 104, 'Chuẩn', NULL, NULL, '0349422856', 'zvuchuan98@gmail.com', NULL, NULL, 'paid', 1, '2026-08-06 20:13:13', NULL, '2026-08-07 02:59:21', '2026-08-07 03:13:13'),
(35, 104, 'Chuẩn 2', NULL, NULL, '0349422856', 'zvuchuan98@gmail.com', NULL, NULL, 'paid', 1, '2026-08-06 20:13:17', NULL, '2026-08-07 02:59:21', '2026-08-07 03:13:17'),
(36, 104, 'Chuẩn 3', NULL, NULL, '0349422856', 'zvuchuan98@gmail.com', NULL, NULL, 'paid', 1, '2026-08-06 20:13:19', NULL, '2026-08-07 02:59:21', '2026-08-07 03:13:19'),
(37, 104, 'Chuẩn 4', NULL, NULL, '0349422856', 'zvuchuan98@gmail.com', NULL, NULL, 'paid', 1, '2026-08-06 20:13:41', NULL, '2026-08-07 02:59:21', '2026-08-07 03:13:41'),
(38, 104, 'Chuẩn 5', NULL, NULL, '0349422856', 'zvuchuan98@gmail.com', NULL, NULL, 'paid', 1, '2026-08-06 20:13:44', NULL, '2026-08-07 02:59:21', '2026-08-07 03:13:44'),
(39, 103, 'Thanh Huệ', NULL, NULL, '0865144307', 'thanhhue001@gmail.com', NULL, NULL, 'paid', 0, NULL, NULL, '2026-08-07 03:20:04', '2026-08-07 03:20:10'),
(40, 103, 'Thanh Huệ 2', NULL, NULL, '0865144307', 'thanhhue001@gmail.com', NULL, NULL, 'paid', 0, NULL, NULL, '2026-08-07 03:20:04', '2026-08-07 03:20:10'),
(41, 103, 'Thanh Huệ 3', NULL, NULL, '0865144307', 'thanhhue001@gmail.com', NULL, NULL, 'paid', 0, NULL, NULL, '2026-08-07 03:20:04', '2026-08-07 03:20:10'),
(42, 103, 'Thanh Huệ 4', NULL, NULL, '0865144307', 'thanhhue001@gmail.com', NULL, NULL, 'paid', 0, NULL, NULL, '2026-08-07 03:20:04', '2026-08-07 03:20:10'),
(43, 103, 'Thanh Huệ 5', NULL, NULL, '0865144307', 'thanhhue001@gmail.com', NULL, NULL, 'paid', 0, NULL, NULL, '2026-08-07 03:20:04', '2026-08-07 03:20:10');

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
(7, 5, 'Chuẩn  (0349422856) - Quảng Ninh (2026-08-07)', '2026-08-08', '2026-08-10', 5, '8B nghách 46 ngõ 1 Bùi Xương Trạch', '06:00:00', '', '', 'scheduled', '2026-08-06 19:24:31', '2026-08-06 19:57:11'),
(8, 6, 'Chuẩn  (0349422856) - Cà mau (2026-08-07)', '2026-08-08', '2026-08-10', 5, '8B nghách 46 ngõ 1 Bùi Xương Trạch', '06:00:00', 'Xe khách', '', 'scheduled', '2026-08-06 19:55:42', '2026-08-06 20:10:49'),
(9, 5, 'Thanh Huệ  (0865144307) - Quảng Ninh (2026-08-08)', '2026-08-13', '2026-08-15', 5, '8B nghách 46 ngõ 1 Bùi Xương Trạch', '06:00:00', 'Xe khách', '', 'scheduled', '2026-08-06 20:17:41', '2026-08-06 20:17:58');

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
(1, 'Nguyễn Văn An', '1990-05-15', 'Nam', '0901234567 - an.nguyen@email.com', 'Tiếng Anh, Tiếng Việt', '123 Nguyễn Huệ, Q.1, TP.HCM', 'HDV Quốc tế #9921', 5, '2020-01-15', 'Tốt, thể lực đảm bảo leo núi', 'Nhiệt tình, dẫn tour nội địa & quốc tế tốt', '4.8', 'Khách hàng phản hồi rất tích cực về thái độ phục vụ.', 1, 'active', '2026-07-28 20:50:04', NULL),
(2, 'Trần Thị Bích', '1995-08-20', 'Nữ', '0902345678 - bich.tran@email.com', 'Tiếng Trung, Tiếng Việt', '456 Lê Lợi, Q.3, TP.HCM', 'HDV Nội địa #4412', 3, '2022-03-01', 'Bình thường', 'Phù hợp các tour nghỉ dưỡng văn hóa', '4.5', 'Nhiệt tình chăm sóc đoàn khách gia đình.', 2, 'active', '2026-07-28 20:50:04', NULL),
(3, 'Lê Hoàng Cường', '1988-12-10', 'Nam', '0903456789 - cuong.le@email.com', 'Tiếng Nhật, Tiếng Anh', '789 Trần Hưng Đạo, Q.5, TP.HCM', 'HDV Quốc tế #8820', 8, '2019-06-20', 'Tốt', 'Chuyên tuyến tour Nhật Bản và Đông Nam Á', '4.9', 'Rất chuyên nghiệp, nhiều kinh nghiệm xử lý sự cố.', 1, 'active', '2026-07-28 20:50:04', NULL),
(4, 'Phạm Minh Đức', '1993-03-25', 'Nam', '0904567890 - duc.pham@email.com', 'Tiếng Hàn', '321 Hai Bà Trưng, Q.1, TP.HCM', 'HDV Nội địa #5123', 2, '2023-01-10', 'Tốt', 'Đang học thêm tiếng Anh nâng cao', '4.2', 'Năng nổ, hòa đồng.', 2, 'on_leave', '2026-07-28 20:50:04', NULL),
(5, 'Hoàng Thị Em', '1997-07-30', 'Nữ', '0905678901 - em.hoang@email.com', 'Tiếng Pháp', '654 Võ Văn Tần, Q.3, TP.HCM', 'HDV Quốc tế #3301', 4, '2021-09-05', 'Bình thường', 'Đã hết hạn hợp đồng', '4.0', 'Hoàn thành tốt các công việc được giao.', 3, 'inactive', '2026-07-28 20:50:04', NULL),
(6, 'Nguyễn Minh Tâm', '1992-09-18', 'Nam', '0906789012 - tam.nguyen@email.com', 'Tiếng Anh', '789 Nguyễn Thị Minh Khai, Q.3, TP.HCM', 'HDV Nội địa #5567', 4, '2021-04-15', 'Tốt', 'Nhiệt tình, chủ yếu dẫn tour Miền Nam', '4.6', 'Dẫn tour nhẹ nhàng, chăm sóc khách tốt.', 3, 'active', '2026-08-05 09:10:00', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `services`
--

CREATE TABLE `services` (
  `id` int NOT NULL,
  `tour_id` int NOT NULL,
  `departure_id` int DEFAULT NULL,
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

INSERT INTO `services` (`id`, `tour_id`, `departure_id`, `service_types`, `supplier`, `quantity`, `status`, `start_time`, `end_time`, `note`, `created_at`, `updated_at`) VALUES
(14, 5, 5, 'Tham quan, Khách sạn', 'Công ty Xe Anh Tài', 5, 1, '2026-08-08 06:00:00', '2026-08-10 20:00:00', '', '2026-08-06 19:18:43', '2026-08-06 19:18:43'),
(15, 6, 8, 'Tham quan, Nhà hàng, Khách sạn, Xe', 'Công ty Xe Anh Tài', 5, 1, '2026-08-08 06:00:00', '2026-08-10 20:00:00', '', '2026-08-06 19:58:07', '2026-08-06 19:58:07'),
(16, 5, 9, 'Tham quan, Nhà hàng, Khách sạn, Xe', 'Công ty Xe Anh Tài', 5, 1, '2026-08-13 06:00:00', '2026-08-15 20:00:00', '', '2026-08-06 20:18:44', '2026-08-06 20:18:44');

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
(10, 7, 2, 'lead_guide', '', '', 'confirmed', '2026-08-06 19:56:49', NULL),
(11, 8, 1, 'lead_guide', '', '', 'confirmed', '2026-08-06 20:10:22', NULL),
(12, 9, 6, 'assistant_guide', '', '', 'assigned', '2026-08-06 20:18:19', '2026-08-06 20:19:27'),
(13, 9, 1, 'lead_guide', '', '', 'assigned', '2026-08-06 20:19:11', NULL);

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
  `location` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `image` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tours`
--

INSERT INTO `tours` (`id`, `category_id`, `name`, `price`, `duration`, `location`, `description`, `image`, `status`) VALUES
(5, 2, 'Hạ Long - Quảng Ninh', 2000000, '3 ngày', 'Quảng Ninh', 'Vịnh Hạ Long là một trong những điểm du lịch nổi tiếng nhất của Việt Nam, nằm ở tỉnh Quảng Ninh, cách Hà Nội khoảng 160 km về phía Đông. Với diện tích hơn 1.500 km² và gần 2.000 hòn đảo lớn nhỏ được hình thành từ đá vôi qua hàng triệu năm, Vịnh Hạ Long sở hữu khung cảnh thiên nhiên hùng vĩ và độc đáo, thu hút hàng triệu du khách trong và ngoài nước mỗi năm.\r\n\r\nNăm 1994 và năm 2000, Vịnh Hạ Long được UNESCO công nhận là Di sản Thiên nhiên Thế giới nhờ giá trị nổi bật về cảnh quan và địa chất. Đến năm 2011, nơi đây tiếp tục được bình chọn là một trong Bảy kỳ quan thiên nhiên mới của thế giới.\r\n\r\nMô tả cảnh quan\r\n4\r\n\r\nVịnh Hạ Long nổi bật với hàng nghìn đảo đá vôi có hình dáng kỳ lạ, tạo nên khung cảnh vừa thơ mộng vừa hùng vĩ. Nước biển trong xanh quanh năm kết hợp với các hang động kỳ ảo như Hang Sửng Sốt, Hang Thiên Cung và Hang Đầu Gỗ tạo nên sức hấp dẫn đặc biệt. Du khách còn có thể tham quan Đảo Ti Tốp, chèo kayak tại Hang Luồn hoặc khám phá cuộc sống của người dân tại Làng chài Cửa Vạn.\r\n\r\nGiá trị du lịch\r\n\r\nHạ Long không chỉ nổi tiếng bởi vẻ đẹp thiên nhiên mà còn mang giá trị lớn về địa chất, sinh thái và văn hóa. Đây là điểm đến lý tưởng cho các hoạt động như:\r\n\r\nTham quan vịnh bằng du thuyền.\r\nChèo thuyền kayak và tắm biển.\r\nKhám phá hang động.\r\nNgắm bình minh và hoàng hôn trên biển.\r\nThưởng thức hải sản tươi sống đặc trưng của Quảng Ninh.\r\nTrải nghiệm nghỉ dưỡng tại các khu nghỉ dưỡng cao cấp.\r\nKết luận\r\n\r\nVịnh Hạ Long là biểu tượng du lịch của Việt Nam với vẻ đẹp thiên nhiên hiếm có và giá trị văn hóa, địa chất đặc sắc. Sự kết hợp giữa những dãy núi đá vôi kỳ vĩ, làn nước xanh ngọc và hệ thống hang động độc đáo đã tạo nên một điểm đến hấp dẫn đối với du khách trong nước và quốc tế. Đây là nơi lý tưởng để khám phá thiên nhiên, nghỉ dưỡng và trải nghiệm những nét đẹp đặc trưng của vùng biển Đông Bắc Việt Nam.', 'storage/uploads/tours/1786035445-tải xuống.jpg', 1),
(6, 2, 'Du lịch cà mau', 2500000, '2 ngày 1 đêm', 'Cà mau', 'Cà Mau là tỉnh nằm ở cực Nam của Việt Nam, được biết đến là vùng đất cuối cùng của Tổ quốc với ba mặt giáp biển. Đây là nơi có hệ sinh thái rừng ngập mặn lớn nhất cả nước, cảnh quan thiên nhiên phong phú cùng nền văn hóa đặc sắc của người dân vùng sông nước.\r\n\r\nCà Mau có diện tích khoảng 5.300 km² và là trung tâm kinh tế, văn hóa của khu vực bán đảo Cà Mau. Với vị trí địa lý đặc biệt, tỉnh đóng vai trò quan trọng trong phát triển kinh tế biển, nuôi trồng thủy sản và du lịch sinh thái.\r\n\r\nĐiểm nổi bật của Cà Mau\r\nĐất Mũi Cà Mau: Điểm cực Nam của Việt Nam, nơi du khách có thể ngắm bình minh trên biển Đông và hoàng hôn trên biển Tây trong cùng một ngày.\r\nRừng ngập mặn: Một trong những khu rừng ngập mặn lớn và đa dạng sinh học nhất Việt Nam, là nơi sinh sống của nhiều loài động, thực vật quý hiếm.\r\nHệ sinh thái đa dạng: Bao gồm rừng ngập mặn, rừng tràm, sông ngòi và vùng ven biển, rất thích hợp cho du lịch khám phá thiên nhiên.\r\nẨm thực phong phú: Nổi tiếng với cua Cà Mau, tôm sú, cá thòi lòi, ba khía, mật ong rừng U Minh và nhiều món ăn dân dã đặc trưng.\r\nCác địa điểm du lịch nổi tiếng\r\nMũi Cà Mau\r\nVườn Quốc gia Mũi Cà Mau\r\nVườn Quốc gia U Minh Hạ\r\nHòn Đá Bạc\r\nĐầm Thị Tường\r\nVăn hóa và con người\r\n\r\nNgười dân Cà Mau nổi tiếng với sự hiền hòa, chân chất và hiếu khách. Cuộc sống gắn liền với sông nước, nghề đánh bắt hải sản và nuôi trồng thủy sản đã tạo nên bản sắc văn hóa đặc trưng của vùng đất cực Nam. Nơi đây còn lưu giữ nhiều lễ hội truyền thống và nét sinh hoạt văn hóa đặc sắc của cộng đồng người Kinh, Khmer và Hoa.\r\n\r\nTiềm năng du lịch\r\n\r\nCà Mau có nhiều lợi thế để phát triển du lịch sinh thái, du lịch cộng đồng và du lịch trải nghiệm. Du khách đến đây không chỉ được khám phá thiên nhiên hoang sơ, tìm hiểu hệ sinh thái rừng ngập mặn mà còn được thưởng thức các món hải sản tươi ngon và trải nghiệm cuộc sống bình dị của người dân miền Tây Nam Bộ.\r\n\r\nTóm lại, Cà Mau là điểm đến hấp dẫn dành cho những ai yêu thiên nhiên, muốn khám phá vùng đất cực Nam của Việt Nam và trải nghiệm vẻ đẹp độc đáo của hệ sinh thái rừng ngập mặn cùng nền văn hóa đặc sắc của miền sông nước.', 'storage/uploads/tours/1786045184-images (4).jpg', 1);

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
  `tour_log_id` int DEFAULT NULL,
  `created_by_hdv_id` int DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `diary_date` date NOT NULL,
  `weather` varchar(100) DEFAULT NULL,
  `mood` varchar(100) DEFAULT NULL,
  `photos` text,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

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
-- Dữ liệu timeline mẫu cho các chuyến đã được phân công.
-- Các hoạt động này được hiển thị tại màn hình HDV > Chi tiết tour
-- và có thể được cập nhật trực tiếp bởi hướng dẫn viên.
--
INSERT INTO `tour_logs` (`id`, `departure_id`, `title`, `content`, `log_date`, `location`, `weather`, `mood`, `images`, `author_id`, `status`, `created_at`, `updated_at`) VALUES
(1, 8, 'Tập trung và đón đoàn', 'Hướng dẫn viên điểm danh, kiểm tra hành lý và phổ biến quy định an toàn trước khi khởi hành.', '2026-08-08 06:00:00', '8B ngách 46 ngõ 1 Bùi Xương Trạch', 'Nắng nhẹ', 'Sẵn sàng khởi hành', NULL, 1, 'published', '2026-08-07 04:00:00', '2026-08-07 04:00:00'),
(2, 8, 'Khởi hành đến Cà Mau', 'Đoàn bắt đầu di chuyển theo lịch trình. Hướng dẫn viên giới thiệu chương trình tour và các điểm dừng.', '2026-08-08 06:30:00', 'Điểm tập trung', 'Nắng nhẹ', 'Đúng kế hoạch', NULL, 1, 'published', '2026-08-07 04:00:00', '2026-08-07 04:00:00'),
(3, 8, 'Tham quan Đất Mũi Cà Mau', 'Đoàn tham quan cột mốc tọa độ quốc gia, chụp ảnh lưu niệm và nghe thuyết minh về hệ sinh thái rừng ngập mặn.', '2026-08-09 09:00:00', 'Khu du lịch Đất Mũi, Cà Mau', 'Có mây', 'Hào hứng', NULL, 1, 'published', '2026-08-07 04:00:00', '2026-08-07 04:00:00'),
(4, 8, 'Kết thúc tour và tiễn đoàn', 'Tổng kết hành trình, kiểm tra đầy đủ hành lý và cảm ơn đoàn khách trước khi chia tay.', '2026-08-10 20:00:00', 'Điểm trả khách', 'Mát mẻ', 'Hoàn thành', NULL, 1, 'published', '2026-08-07 04:00:00', '2026-08-07 04:00:00'),
(5, 7, 'Tập trung và đón đoàn', 'Hướng dẫn viên điểm danh, kiểm tra hành lý và phổ biến quy định an toàn trước khi khởi hành.', '2026-08-08 06:00:00', '8B ngách 46 ngõ 1 Bùi Xương Trạch', 'Nắng nhẹ', 'Sẵn sàng khởi hành', NULL, 2, 'published', '2026-08-07 04:00:00', '2026-08-07 04:00:00'),
(6, 7, 'Khởi hành đi Hạ Long', 'Đoàn lên xe, bắt đầu hành trình tới Quảng Ninh. Hướng dẫn viên giới thiệu chương trình và các điểm dừng.', '2026-08-08 06:30:00', 'Điểm tập trung', 'Nắng nhẹ', 'Đúng kế hoạch', NULL, 2, 'published', '2026-08-07 04:00:00', '2026-08-07 04:00:00'),
(7, 7, 'Tham quan Vịnh Hạ Long', 'Đoàn tham quan vịnh bằng du thuyền, ngắm cảnh các đảo đá vôi và trải nghiệm hoạt động theo chương trình.', '2026-08-09 09:00:00', 'Vịnh Hạ Long, Quảng Ninh', 'Có mây', 'Hào hứng', NULL, 2, 'published', '2026-08-07 04:00:00', '2026-08-07 04:00:00'),
(8, 7, 'Kết thúc tour và tiễn đoàn', 'Tổng kết hành trình, kiểm tra đầy đủ hành lý và cảm ơn đoàn khách trước khi chia tay.', '2026-08-10 20:00:00', 'Điểm trả khách', 'Mát mẻ', 'Hoàn thành', NULL, 2, 'published', '2026-08-07 04:00:00', '2026-08-07 04:00:00'),
(9, 9, 'Tập trung và đón đoàn', 'Hướng dẫn viên điểm danh, kiểm tra hành lý và phổ biến quy định an toàn trước khi khởi hành.', '2026-08-13 06:00:00', '8B ngách 46 ngõ 1 Bùi Xương Trạch', 'Nắng nhẹ', 'Sẵn sàng khởi hành', NULL, 1, 'published', '2026-08-07 04:00:00', '2026-08-07 04:00:00'),
(10, 9, 'Khởi hành đi Hạ Long', 'Đoàn lên xe, bắt đầu hành trình tới Quảng Ninh. Hướng dẫn viên giới thiệu chương trình và các điểm dừng.', '2026-08-13 06:30:00', 'Điểm tập trung', 'Nắng nhẹ', 'Đúng kế hoạch', NULL, 1, 'published', '2026-08-07 04:00:00', '2026-08-07 04:00:00'),
(11, 9, 'Tham quan Vịnh Hạ Long', 'Đoàn tham quan vịnh bằng du thuyền, ngắm cảnh các đảo đá vôi và trải nghiệm hoạt động theo chương trình.', '2026-08-14 09:00:00', 'Vịnh Hạ Long, Quảng Ninh', 'Có mây', 'Hào hứng', NULL, 1, 'published', '2026-08-07 04:00:00', '2026-08-07 04:00:00'),
(12, 9, 'Kết thúc tour và tiễn đoàn', 'Tổng kết hành trình, kiểm tra đầy đủ hành lý và cảm ơn đoàn khách trước khi chia tay.', '2026-08-15 20:00:00', 'Điểm trả khách', 'Mát mẻ', 'Hoàn thành', NULL, 1, 'published', '2026-08-07 04:00:00', '2026-08-07 04:00:00');

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
  `role` enum('admin','user','hdv') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'user',
  `hdv_id` int DEFAULT NULL,
  `avatar` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint DEFAULT '1',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `phone`, `role`, `hdv_id`, `avatar`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Administrator', 'admin@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '0901234567', 'admin', NULL, NULL, 1, '2026-07-27 16:37:31', '2026-07-27 16:37:31'),
(4, 'HDV Nguyễn Văn An', 'hdv12@example.com', '$2y$10$gd4kLyNpw/v270i9GeAJ/uX6z7dJ30nWrwuDyvfN4BmobcLwbUA7u', '0901234567', 'hdv', 1, NULL, 1, '2026-08-05 10:00:00', '2026-08-06 15:44:52');

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
  ADD KEY `idx_tour_log_id` (`tour_log_id`),
  ADD KEY `idx_diary_date` (`diary_date`),
  ADD KEY `idx_created_by_hdv_id` (`created_by_hdv_id`);

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
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_users_hdv_id` (`hdv_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=105;

--
-- AUTO_INCREMENT for table `booking_guests`
--
ALTER TABLE `booking_guests`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT for table `departures`
--
ALTER TABLE `departures`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `hdv`
--
ALTER TABLE `hdv`
  MODIFY `HDV_id` int NOT NULL AUTO_INCREMENT COMMENT 'Mã định danh duy nhất cho Hướng dẫn viên', AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `services`
--
ALTER TABLE `services`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `staff_assignments`
--
ALTER TABLE `staff_assignments`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `tours`
--
ALTER TABLE `tours`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `tour_categories`
--
ALTER TABLE `tour_categories`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `tour_diaries`
--
ALTER TABLE `tour_diaries`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `tour_logs`
--
ALTER TABLE `tour_logs`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

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
  ADD CONSTRAINT `tour_diaries_ibfk_1` FOREIGN KEY (`departure_id`) REFERENCES `departures` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_tour_diaries_tour_log` FOREIGN KEY (`tour_log_id`) REFERENCES `tour_logs` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `tour_logs`
--
ALTER TABLE `tour_logs`
  ADD CONSTRAINT `fk_tour_logs_departure` FOREIGN KEY (`departure_id`) REFERENCES `departures` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `fk_users_hdv` FOREIGN KEY (`hdv_id`) REFERENCES `hdv` (`HDV_id`) ON DELETE SET NULL ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
