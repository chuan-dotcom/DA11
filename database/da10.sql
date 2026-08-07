-- Database Dump for `da10`
-- Generated on 2026-08-07 03:50:18

SET FOREIGN_KEY_CHECKS=0;
SET UNIQUE_CHECKS=0;
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";
/*!40101 SET NAMES utf8mb4 */;

-- --------------------------------------------------------
-- Table structure for table `tour_categories`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `tour_categories`;
CREATE TABLE `tour_categories` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table `tour_categories`
INSERT INTO `tour_categories` (`id`, `name`, `description`) VALUES
('1', 'Du lịch núi', 'Các tour khám phá núi rừng, trekking'),
('2', 'Du lịch thành phố', 'Các tour khám phá thành thị, văn hóa');

-- --------------------------------------------------------
-- Table structure for table `tours`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `tours`;
CREATE TABLE `tours` (
  `id` int NOT NULL AUTO_INCREMENT,
  `category_id` int NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `price` int DEFAULT '0',
  `duration` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `location` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `image` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `fk_tours_category` (`category_id`),
  CONSTRAINT `fk_tours_category` FOREIGN KEY (`category_id`) REFERENCES `tour_categories` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table `tours`
INSERT INTO `tours` (`id`, `category_id`, `name`, `price`, `duration`, `location`, `description`, `image`, `status`) VALUES
('5', '2', 'Hạ Long - Quảng Ninh', '2000000', '3 ngày', 'Quảng Ninh', 'Vịnh Hạ Long là một trong những điểm du lịch nổi tiếng nhất của Việt Nam, nằm ở tỉnh Quảng Ninh, cách Hà Nội khoảng 160 km về phía Đông. Với diện tích hơn 1.500 km² và gần 2.000 hòn đảo lớn nhỏ được hình thành từ đá vôi qua hàng triệu năm, Vịnh Hạ Long sở hữu khung cảnh thiên nhiên hùng vĩ và độc đáo, thu hút hàng triệu du khách trong và ngoài nước mỗi năm.

Năm 1994 và năm 2000, Vịnh Hạ Long được UNESCO công nhận là Di sản Thiên nhiên Thế giới nhờ giá trị nổi bật về cảnh quan và địa chất. Đến năm 2011, nơi đây tiếp tục được bình chọn là một trong Bảy kỳ quan thiên nhiên mới của thế giới.

Mô tả cảnh quan
4

Vịnh Hạ Long nổi bật với hàng nghìn đảo đá vôi có hình dáng kỳ lạ, tạo nên khung cảnh vừa thơ mộng vừa hùng vĩ. Nước biển trong xanh quanh năm kết hợp với các hang động kỳ ảo như Hang Sửng Sốt, Hang Thiên Cung và Hang Đầu Gỗ tạo nên sức hấp dẫn đặc biệt. Du khách còn có thể tham quan Đảo Ti Tốp, chèo kayak tại Hang Luồn hoặc khám phá cuộc sống của người dân tại Làng chài Cửa Vạn.

Giá trị du lịch

Hạ Long không chỉ nổi tiếng bởi vẻ đẹp thiên nhiên mà còn mang giá trị lớn về địa chất, sinh thái và văn hóa. Đây là điểm đến lý tưởng cho các hoạt động như:

Tham quan vịnh bằng du thuyền.
Chèo thuyền kayak và tắm biển.
Khám phá hang động.
Ngắm bình minh và hoàng hôn trên biển.
Thưởng thức hải sản tươi sống đặc trưng của Quảng Ninh.
Trải nghiệm nghỉ dưỡng tại các khu nghỉ dưỡng cao cấp.
Kết luận

Vịnh Hạ Long là biểu tượng du lịch của Việt Nam với vẻ đẹp thiên nhiên hiếm có và giá trị văn hóa, địa chất đặc sắc. Sự kết hợp giữa những dãy núi đá vôi kỳ vĩ, làn nước xanh ngọc và hệ thống hang động độc đáo đã tạo nên một điểm đến hấp dẫn đối với du khách trong nước và quốc tế. Đây là nơi lý tưởng để khám phá thiên nhiên, nghỉ dưỡng và trải nghiệm những nét đẹp đặc trưng của vùng biển Đông Bắc Việt Nam.', 'storage/uploads/tours/halong.jpg', '1'),
('6', '2', 'Du lịch cà mau', '2500000', '2 ngày 1 đêm', 'Cà mau', 'Cà Mau là tỉnh nằm ở cực Nam của Việt Nam, được biết đến là vùng đất cuối cùng của Tổ quốc với ba mặt giáp biển. Đây là nơi có hệ sinh thái rừng ngập mặn lớn nhất cả nước, cảnh quan thiên nhiên phong phú cùng nền văn hóa đặc sắc của người dân vùng sông nước.

Cà Mau có diện tích khoảng 5.300 km² và là trung tâm kinh tế, văn hóa của khu vực bán đảo Cà Mau. Với vị trí địa lý đặc biệt, tỉnh đóng vai trò quan trọng trong phát triển kinh tế biển, nuôi trồng thủy sản và du lịch sinh thái.

Điểm nổi bật của Cà Mau
Đất Mũi Cà Mau: Điểm cực Nam của Việt Nam, nơi du khách có thể ngắm bình minh trên biển Đông và hoàng hôn trên biển Tây trong cùng một ngày.
Rừng ngập mặn: Một trong những khu rừng ngập mặn lớn và đa dạng sinh học nhất Việt Nam, là nơi sinh sống của nhiều loài động, thực vật quý hiếm.
Hệ sinh thái đa dạng: Bao gồm rừng ngập mặn, rừng tràm, sông ngòi và vùng ven biển, rất thích hợp cho du lịch khám phá thiên nhiên.
Ẩm thực phong phú: Nổi tiếng với cua Cà Mau, tôm sú, cá thòi lòi, ba khía, mật ong rừng U Minh và nhiều món ăn dân dã đặc trưng.
Các địa điểm du lịch nổi tiếng
Mũi Cà Mau
Vườn Quốc gia Mũi Cà Mau
Vườn Quốc gia U Minh Hạ
Hòn Đá Bạc
Đầm Thị Tường
Văn hóa và con người

Người dân Cà Mau nổi tiếng với sự hiền hòa, chân chất và hiếu khách. Cuộc sống gắn liền với sông nước, nghề đánh bắt hải sản và nuôi trồng thủy sản đã tạo nên bản sắc văn hóa đặc trưng của vùng đất cực Nam. Nơi đây còn lưu giữ nhiều lễ hội truyền thống và nét sinh hoạt văn hóa đặc sắc của cộng đồng người Kinh, Khmer và Hoa.

Tiềm năng du lịch

Cà Mau có nhiều lợi thế để phát triển du lịch sinh thái, du lịch cộng đồng và du lịch trải nghiệm. Du khách đến đây không chỉ được khám phá thiên nhiên hoang sơ, tìm hiểu hệ sinh thái rừng ngập mặn mà còn được thưởng thức các món hải sản tươi ngon và trải nghiệm cuộc sống bình dị của người dân miền Tây Nam Bộ.

Tóm lại, Cà Mau là điểm đến hấp dẫn dành cho những ai yêu thiên nhiên, muốn khám phá vùng đất cực Nam của Việt Nam và trải nghiệm vẻ đẹp độc đáo của hệ sinh thái rừng ngập mặn cùng nền văn hóa đặc sắc của miền sông nước.', 'storage/uploads/tours/camau.jpg', '1'),
('7', '1', 'Sapa - Chinh Phục Đỉnh Fansipan & Bản Cát Cát', '3500000', '3 ngày 2 đêm', 'Sapa, Lào Cai', 'Sapa là thị trấn sương mù nổi tiếng thuộc tỉnh Lào Cai với cảnh quan núi rừng Tây Bắc hùng vĩ, đỉnh Fansipan - Nóc nhà Đông Dương, bản làng H\'Mông cổ kính Cát Cát và những thửa ruộng bậc thang tuyệt đẹp ngút ngàn.

Du khách tham gia tour sẽ được trải nghiệm tuyến cáp treo Fansipan hiện đại, thưởng thức ẩm thực Tây Bắc đậm đà như lẩu cá hồi, thắng cố, thịt trâu gác bếp và hòa mình vào không gian văn hóa sương mờ mộng mơ.', 'storage/uploads/tours/sapa.jpg', '1'),
('8', '2', 'Đà Nẵng - Hội An - Bà Nà Hills - Cầu Vàng', '3800000', '3 ngày 2 đêm', 'Đà Nẵng & Quảng Nam', 'Hành trình khám phá Đà Nẵng - thành phố đáng sống nhất Việt Nam. Check-in siêu phẩm Cầu Vàng Bàn Tay khổng lồ trên đỉnh Bà Nà Hills, dạo Phố cổ Hội An lung linh sắc đèn lồng bên dòng sông Hoài thơ mộng và đắm mình trong làn nước xanh mát của biển Mỹ Khê.

Tour trọn gói bao gồm vé cáp treo Bà Nà, du thuyền sông Hoài, buffet Châu Âu và các bữa ăn đặc sản Mì Quảng, Bánh tráng thịt heo 2 đầu da.', 'storage/uploads/tours/danang.jpg', '1'),
('9', '1', 'Đà Lạt Mộng Mơ - Thành Phố Ngàn Hoa & Langbiang', '2900000', '3 ngày 2 đêm', 'Đà Lạt, Lâm Đồng', 'Trải nghiệm không khí se lạnh mộng mơ của Đà Lạt. Chinh phục đỉnh núi Langbiang huyền thoại bằng xe Jeep, check-in Đồi Chè Cầu Đất tháp quạt gió, Quảng trường Lâm Viên, Thung lũng Tình Yêu và thưởng thức lẩu gà lá é, lẩu bò Ba Toa thơm nức lòng.

Đặc biệt du khách còn được tham gia đêm giao lưu Cồng Chiêng Tây Nguyên cùng người bản địa dưới chân núi Langbiang.', 'storage/uploads/tours/dalat.jpg', '1'),
('10', '1', 'Ninh Bình - Danh Thắng Tràng An - Bái Đính - Hang Múa', '1850000', '2 ngày 1 đêm', 'Ninh Bình', 'Khám phá Quần thể di sản thiên nhiên & văn hóa thế giới Tràng An bằng thuyền nan uốn lượn qua các hang động kỳ vĩ. Viếng Chùa Bái Đính ngôi chùa lớn nhất Việt Nam, chinh phục 500 bậc đá Hang Múa ngắm toàn cảnh Tam Cốc tuyệt đẹp.

Thưởng thức đặc sản Cơm cháy Ninh Bình, Dê núi tái chanh và nghỉ dưỡng tại Emeralda Resort Ninh Bình 5 sao thanh bình.', 'storage/uploads/tours/ninhbinh.jpg', '1'),
('11', '2', 'Huế - Cố Đô Di Sản & Đêm Sông Hương', '4290000', '3 ngày 2 đêm', 'Thừa Thiên Huế', 'Hành trình khám phá di sản văn hóa thế giới Cố đô Huế, Đại Nội, Chùa Thiên Mụ, Lăng Khải Định, thưởng thức Nhã nhạc cung đình Huế trên thuyền Sông Hương và ẩm thực Cung đình Huế truyền thống.', 'storage/uploads/tours/hue.jpg', '1');

-- --------------------------------------------------------
-- Table structure for table `hdv`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `hdv`;
CREATE TABLE `hdv` (
  `HDV_id` int NOT NULL AUTO_INCREMENT COMMENT 'Mã định danh duy nhất cho Hướng dẫn viên',
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
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`HDV_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table `hdv`
INSERT INTO `hdv` (`HDV_id`, `Hoten`, `Ngaysinh`, `Gioitinh`, `Lienhe`, `Ngonngu`, `Diachi`, `chungchiHDV`, `Kinhnghiem`, `Ngaybatdaulam`, `Trangthaisuckhoe`, `Ghichunoibo`, `Diemdanhgia`, `Nhanxetdanhgia`, `HDV_group_id`, `Status`, `created_at`, `updated_at`) VALUES
('1', 'Nguyễn Văn An', '1990-05-15', 'Nam', '0901234567 - an.nguyen@email.com', 'Tiếng Anh, Tiếng Việt', '123 Nguyễn Huệ, Q.1, TP.HCM', 'HDV Quốc tế #9921', '5', '2020-01-15', 'Tốt, thể lực đảm bảo leo núi', 'Nhiệt tình, dẫn tour nội địa & quốc tế tốt', '4.8', 'Khách hàng phản hồi rất tích cực về thái độ phục vụ.', '1', 'active', '2026-07-28 20:50:04', NULL),
('2', 'Trần Thị Bích', '1995-08-20', 'Nữ', '0902345678 - bich.tran@email.com', 'Tiếng Trung, Tiếng Việt', '456 Lê Lợi, Q.3, TP.HCM', 'HDV Nội địa #4412', '3', '2022-03-01', 'Bình thường', 'Phù hợp các tour nghỉ dưỡng văn hóa', '4.5', 'Nhiệt tình chăm sóc đoàn khách gia đình.', '2', 'active', '2026-07-28 20:50:04', NULL),
('3', 'Lê Hoàng Cường', '1988-12-10', 'Nam', '0903456789 - cuong.le@email.com', 'Tiếng Nhật, Tiếng Anh', '789 Trần Hưng Đạo, Q.5, TP.HCM', 'HDV Quốc tế #8820', '8', '2019-06-20', 'Tốt', 'Chuyên tuyến tour Nhật Bản và Đông Nam Á', '4.9', 'Rất chuyên nghiệp, nhiều kinh nghiệm xử lý sự cố.', '1', 'active', '2026-07-28 20:50:04', NULL),
('4', 'Phạm Minh Đức', '1993-03-25', 'Nam', '0904567890 - duc.pham@email.com', 'Tiếng Hàn', '321 Hai Bà Trưng, Q.1, TP.HCM', 'HDV Nội địa #5123', '2', '2023-01-10', 'Tốt', 'Đang học thêm tiếng Anh nâng cao', '4.2', 'Năng nổ, hòa đồng.', '2', 'on_leave', '2026-07-28 20:50:04', NULL),
('5', 'Hoàng Thị Em', '1997-07-30', 'Nữ', '0905678901 - em.hoang@email.com', 'Tiếng Pháp', '654 Võ Văn Tần, Q.3, TP.HCM', 'HDV Quốc tế #3301', '4', '2021-09-05', 'Bình thường', 'Đã hết hạn hợp đồng', '4.0', 'Hoàn thành tốt các công việc được giao.', '3', 'inactive', '2026-07-28 20:50:04', NULL),
('6', 'Nguyễn Minh Tâm', '1992-09-18', 'Nam', '0906789012 - tam.nguyen@email.com', 'Tiếng Anh', '789 Nguyễn Thị Minh Khai, Q.3, TP.HCM', 'HDV Nội địa #5567', '4', '2021-04-15', 'Tốt', 'Nhiệt tình, chủ yếu dẫn tour Miền Nam', '4.6', 'Dẫn tour nhẹ nhàng, chăm sóc khách tốt.', '3', 'active', '2026-08-05 09:10:00', NULL);

-- --------------------------------------------------------
-- Table structure for table `users`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `role` enum('admin','user','hdv') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'user',
  `hdv_id` int DEFAULT NULL,
  `avatar` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint DEFAULT '1',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  KEY `idx_users_hdv_id` (`hdv_id`),
  CONSTRAINT `fk_users_hdv` FOREIGN KEY (`hdv_id`) REFERENCES `hdv` (`HDV_id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table `users`
INSERT INTO `users` (`id`, `name`, `email`, `password`, `phone`, `role`, `hdv_id`, `avatar`, `status`, `created_at`, `updated_at`) VALUES
('1', 'Administrator', 'admin@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '0901234567', 'admin', NULL, NULL, '1', '2026-07-27 16:37:31', '2026-07-27 16:37:31'),
('4', 'HDV Nguyễn Văn An', 'hdv12@example.com', '$2y$10$gd4kLyNpw/v270i9GeAJ/uX6z7dJ30nWrwuDyvfN4BmobcLwbUA7u', '0901234567', 'hdv', '1', NULL, '1', '2026-08-05 10:00:00', '2026-08-06 15:44:52'),
('5', 'Nguyễn Đạt Trọng', 'hdv@example.com', '$2y$10$kSrwSGSxKUBEsdctSxiZwOqF4IJx/NIWLOnpCeciQ5RS0BKEGlC0q', '0856321288', 'hdv', NULL, NULL, '1', '2026-08-07 03:12:35', '2026-08-07 10:12:35');

-- --------------------------------------------------------
-- Table structure for table `departures`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `departures`;
CREATE TABLE `departures` (
  `id` int NOT NULL AUTO_INCREMENT,
  `tour_id` int NOT NULL,
  `group_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `departure_date` date NOT NULL,
  `return_date` date DEFAULT NULL,
  `max_participants` int NOT NULL DEFAULT '0',
  `meeting_point` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `meeting_time` time DEFAULT NULL,
  `vehicle` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `incurred_cost` bigint NOT NULL DEFAULT '0',
  `incurred_cost_note` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `status` enum('scheduled','in_progress','completed','cancelled') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'scheduled',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_departures_tour_id` (`tour_id`),
  CONSTRAINT `fk_departures_tour` FOREIGN KEY (`tour_id`) REFERENCES `tours` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table `departures`
INSERT INTO `departures` (`id`, `tour_id`, `group_name`, `departure_date`, `return_date`, `max_participants`, `meeting_point`, `meeting_time`, `vehicle`, `notes`, `incurred_cost`, `incurred_cost_note`, `status`, `created_at`, `updated_at`) VALUES
('8', '6', 'Chuẩn  (0349422856) - Cà mau (2026-08-07)', '2026-08-08', '2026-08-10', '5', '8B nghách 46 ngõ 1 Bùi Xương Trạch', '06:00:00', 'Xe khách', '', 0, NULL, 'scheduled', '2026-08-06 19:55:42', '2026-08-06 20:10:49'),
('9', '5', 'Thanh Huệ  (0865144307) - Quảng Ninh (2026-08-08)', '2026-08-13', '2026-08-15', '5', '8B nghách 46 ngõ 1 Bùi Xương Trạch', '06:00:00', 'Xe khách', '', 0, NULL, 'scheduled', '2026-08-06 20:17:41', '2026-08-06 20:17:58'),
('10', '7', 'Đoàn Sapa Fansipan (20/08 - 22/08/2026)', '2026-08-20', '2026-08-22', '15', 'Công viên Thống Nhất, Hà Nội', '06:00:00', 'Xe giường nằm cao cấp', 'Mang theo áo ấm, giày thể thao và giấy tờ tùy thân', 0, NULL, 'scheduled', '2026-08-07 09:48:12', NULL),
('11', '8', 'Đoàn Đà Nẵng - Hội An (25/08 - 27/08/2026)', '2026-08-25', '2026-08-27', '20', 'Sân bay Đà Nẵng / Ga Đà Nẵng', '07:30:00', 'Xe du lịch 29 chỗ đời mới', 'Nên mang theo kem chống nắng, đồ bơi', 0, NULL, 'scheduled', '2026-08-07 09:48:12', NULL),
('12', '9', 'Đoàn Đà Lạt Ngàn Hoa (28/08 - 30/08/2026)', '2026-08-28', '2026-08-30', '18', 'Sân bay Liên Khương / Bến xe Đà Lạt', '07:00:00', 'Xe du lịch 16 chỗ Hyundai Solati', 'Chuẩn bị trang phục chụp ảnh đẹp, áo khoác nhẹ', 0, NULL, 'scheduled', '2026-08-07 09:48:12', NULL),
('13', '10', 'Đoàn Ninh Bình Tràng An (01/09 - 02/09/2026)', '2026-09-01', '2026-09-02', '12', 'Nhà hát Lớn Hà Nội', '07:00:00', 'Xe Limousine 19 chỗ', 'Trang phục lịch sự khi đi chùa Bái Đính', 0, NULL, 'scheduled', '2026-08-07 09:48:12', NULL),
('14', '11', 'Đoàn Huế Cố Đô Di Sản (01/08 - 03/08/2026)', '2026-08-01', '2026-08-03', '20', 'Ga Huế / Sân bay Phú Bài, Thừa Thiên Huế', '07:00:00', 'Xe du lịch 29 chỗ Universe', 'Chuyến đi đã hoàn thành xuất sắc, 100% khách hài lòng.', 87500000, 'Ví dụ: Mua thêm nước uống cho đoàn', 'completed', '2026-08-07 10:05:53', '2026-08-07 10:05:53');

-- --------------------------------------------------------
-- Table structure for table `bookings`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `bookings`;
CREATE TABLE `bookings` (
  `id` int NOT NULL AUTO_INCREMENT,
  `tour_id` int NOT NULL,
  `departure_id` int DEFAULT NULL,
  `customer_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_phone` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pickup_address` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `num_people` int DEFAULT '1',
  `total_price` bigint DEFAULT '0',
  `booking_date` date NOT NULL,
  `status` tinyint DEFAULT '0' COMMENT '0=Chờ xác nhận,1=Đã xác nhận,2=Đã hủy',
  `check_in_status` tinyint(1) NOT NULL DEFAULT '0',
  `checked_in_at` datetime DEFAULT NULL,
  `note` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_bookings_tour` (`tour_id`),
  KEY `idx_bookings_departure_id` (`departure_id`),
  CONSTRAINT `fk_bookings_departure` FOREIGN KEY (`departure_id`) REFERENCES `departures` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_bookings_tour` FOREIGN KEY (`tour_id`) REFERENCES `tours` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=105 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table `bookings`
INSERT INTO `bookings` (`id`, `tour_id`, `departure_id`, `customer_name`, `customer_email`, `customer_phone`, `pickup_address`, `num_people`, `total_price`, `booking_date`, `status`, `check_in_status`, `checked_in_at`, `note`, `created_at`) VALUES
('10', '11', '14', 'Lê Hoàng Nam', 'nam.lehoang@gmail.com', '0912345678', 'Hà Nội', '2', '7980000', '2026-07-25', '1', '1', '2026-08-01 07:15:00', 'Ăn chay 1 người', '2026-08-07 10:05:53'),
('11', '11', '14', 'Trần Thu Hà', 'thuha.tran@yahoo.com', '0987654321', 'TP.HCM', '4', '15960000', '2026-07-26', '1', '1', '2026-08-01 07:20:00', 'Dị ứng hải sản vỏ cứng', '2026-08-07 10:05:53'),
('12', '11', '14', 'Nguyễn Minh Tuấn', 'minhtuan.nguyen@gmail.com', '0901234567', 'Số 15 Nguyễn Huệ, Q.1, TP.HCM', '2', '8580000', '2026-07-20', '1', '1', '2026-08-01 06:55:00', 'Dị ứng hải sản', '2026-08-07 10:24:17'),
('13', '11', '14', 'Phạm Thị Lan', 'lan.pham92@gmail.com', '0918765432', 'Số 42 Hai Bà Trưng, Hà Nội', '1', '4290000', '2026-07-22', '1', '1', '2026-08-01 07:00:00', 'Suất ăn chay', '2026-08-07 10:24:17'),
('14', '11', '14', 'Vũ Đức Anh', 'ducanh.vu@hotmail.com', '0935678901', 'Số 8 Trần Phú, Đà Nẵng', '3', '12870000', '2026-07-22', '1', '1', '2026-08-01 07:05:00', 'Dị ứng đậu phụng', '2026-08-07 10:24:17'),
('15', '11', '14', 'Hoàng Thị Mai', 'mai.hoang@outlook.com', '0976543210', '120 Lê Lợi, Q.3, TP.HCM', '2', '8580000', '2026-07-23', '1', '1', '2026-08-01 07:10:00', 'Ăn kiêng đường', '2026-08-07 10:24:17'),
('16', '11', '14', 'Đặng Quốc Bảo', 'quocbao.dang@gmail.com', '0862345678', 'Số 55 Nguyễn Trãi, Thanh Xuân, Hà Nội', '1', '4290000', '2026-07-24', '1', '1', '2026-08-01 07:12:00', 'Ăn chay trường', '2026-08-07 10:24:17'),
('17', '11', '14', 'Trịnh Thùy Linh', 'thuylinh.trinh@yahoo.com', '0945678123', '22 Pasteur, Q.1, TP.HCM', '2', '8580000', '2026-07-25', '1', '1', '2026-08-01 07:18:00', '', '2026-08-07 10:24:17'),
('18', '11', '14', 'Bùi Văn Hùng', 'vanhung.bui@gmail.com', '0923456789', '78 Bạch Đằng, Hải Phòng', '1', '4290000', '2026-07-27', '1', '0', NULL, 'Hay bị say xe, ngồi đầu xe', '2026-08-07 10:24:17'),
('19', '11', '14', 'Lý Thanh Hương', 'thanhhuong.ly@gmail.com', '0956789012', '34 Ngô Quyền, Huế', '2', '8580000', '2026-07-28', '1', '1', '2026-08-01 07:25:00', '', '2026-08-07 10:24:17'),
('103', '5', '9', 'Thanh Huệ', 'thanhhue001@gmail.com', '0865144307', '8B nghách 46 ngõ 1 Bùi Xương Trạch', '5', '10000000', '2026-08-08', '1', '0', NULL, '', '2026-08-07 02:16:38'),
('104', '6', '8', 'Chuẩn', 'zvuchuan98@gmail.com', '0349422856', '8B nghách 46 ngõ 1 Bùi Xương Trạch', '5', '12500000', '2026-08-07', '1', '0', NULL, '', '2026-08-07 02:54:59');

-- --------------------------------------------------------
-- Table structure for table `services`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `services`;
CREATE TABLE `services` (
  `id` int NOT NULL AUTO_INCREMENT,
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
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `tour_id` (`tour_id`),
  CONSTRAINT `services_ibfk_1` FOREIGN KEY (`tour_id`) REFERENCES `tours` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table `services`
INSERT INTO `services` (`id`, `tour_id`, `departure_id`, `service_types`, `supplier`, `quantity`, `status`, `start_time`, `end_time`, `note`, `created_at`, `updated_at`) VALUES
('14', '5', '5', 'Tham quan, Khách sạn', 'Công ty Xe Anh Tài', '5', '1', '2026-08-08 06:00:00', '2026-08-10 20:00:00', '', '2026-08-08 13:18:43', '2026-08-08 13:18:43'),
('15', '6', '8', 'Tham quan, Nhà hàng, Khách sạn, Xe', 'Công ty Xe Anh Tài', '5', '1', '2026-08-08 06:00:00', '2026-08-10 20:00:00', '', '2026-08-08 13:58:07', '2026-08-08 13:58:07'),
('16', '5', '9', 'Tham quan, Nhà hàng, Khách sạn, Xe', 'Công ty Xe Anh Tài', '5', '1', '2026-08-13 06:00:00', '2026-08-15 20:00:00', '', '2026-08-08 14:18:44', '2026-08-08 14:18:44');

-- --------------------------------------------------------
-- Table structure for table `staff_assignments`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `staff_assignments`;
CREATE TABLE `staff_assignments` (
  `id` int NOT NULL AUTO_INCREMENT,
  `departure_id` int NOT NULL,
  `staff_id` int NOT NULL,
  `role` enum('lead_guide','assistant_guide','driver','photographer','other') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'other',
  `responsibilities` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `status` enum('assigned','confirmed','completed','rejected') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'assigned',
  `assigned_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_departure_staff` (`departure_id`,`staff_id`),
  KEY `idx_staff_assignments_departure_id` (`departure_id`),
  KEY `idx_staff_assignments_staff_id` (`staff_id`),
  CONSTRAINT `fk_staff_departure` FOREIGN KEY (`departure_id`) REFERENCES `departures` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_staff_hdv` FOREIGN KEY (`staff_id`) REFERENCES `hdv` (`HDV_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table `staff_assignments`
INSERT INTO `staff_assignments` (`id`, `departure_id`, `staff_id`, `role`, `responsibilities`, `notes`, `status`, `assigned_at`, `updated_at`) VALUES
('11', '8', '1', 'lead_guide', '', '', 'confirmed', '2026-08-06 20:10:22', NULL),
('12', '9', '6', 'assistant_guide', '', '', 'assigned', '2026-08-06 20:18:19', '2026-08-06 20:19:27'),
('13', '9', '1', 'lead_guide', '', '', 'assigned', '2026-08-06 20:19:11', NULL),
('14', '14', '2', 'lead_guide', 'Chịu trách nhiệm chính điều hành chuyến đi', 'Hoàn thành tốt nhiệm vụ', 'completed', '2026-08-07 10:05:53', '2026-08-07 10:05:53');

-- --------------------------------------------------------
-- Table structure for table `tour_logs`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `tour_logs`;
CREATE TABLE `tour_logs` (
  `id` int NOT NULL AUTO_INCREMENT,
  `departure_id` int NOT NULL,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `log_date` datetime NOT NULL,
  `location` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `weather` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mood` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `activity_type` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `meal_info` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `accommodation` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `images` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `author_id` int DEFAULT NULL,
  `status` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'published',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_departure_id` (`departure_id`),
  CONSTRAINT `fk_tour_logs_departure` FOREIGN KEY (`departure_id`) REFERENCES `departures` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=163 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table `tour_logs`
INSERT INTO `tour_logs` (`id`, `departure_id`, `title`, `content`, `log_date`, `location`, `weather`, `mood`, `activity_type`, `meal_info`, `accommodation`, `images`, `author_id`, `status`, `created_at`, `updated_at`) VALUES
('23', '8', 'Tập trung và đón đoàn đi Cà Mau', 'Hướng dẫn viên điểm danh đoàn khách, kiểm tra danh sách hành lý và phổ biến quy định an toàn hành trình.', '2026-08-08 06:00:00', '8B ngách 46 ngõ 1 Bùi Xương Trạch', 'Nắng nhẹ', 'Sẵn sàng khởi hành', 'transfer', NULL, NULL, NULL, '1', 'published', '2026-08-08 13:51:26', '2026-08-08 13:51:26'),
('24', '8', 'Khởi hành đi vĩnh Nam Cà Mau', 'Xe bắt đầu lăn bánh qua các tỉnh Miền Tây Nam Bộ. HDV giới thiệu nét văn hóa đặc sắc của miền sông nước.', '2026-08-08 06:30:00', 'Điểm tập trung', 'Nắng ấm', 'Đúng kế hoạch', 'transfer', NULL, NULL, NULL, '1', 'published', '2026-08-08 13:51:26', '2026-08-08 13:51:26'),
('25', '8', 'Ăn sáng hủ tiếu Mỹ Tho Tiền Giang', 'Đoàn dừng chân dùng bữa sáng tô hủ tiếu Mỹ Tho thơm nức tiếng vùng Nam Bộ.', '2026-08-08 07:30:00', 'Nhà hàng Hai Bà, Mỹ Tho, Tiền Giang', 'Nắng nhẹ', 'Vui vẻ', 'breakfast', 'Hủ tiếu Mỹ Tho đặc biệt, cà phê sữa đá', 'Nhà hàng Hai Bà', NULL, '1', 'published', '2026-08-08 13:51:26', '2026-08-08 13:51:26'),
('26', '8', 'Ăn trưa hải sản Cà Mau', 'Thưởng thức bữa trưa đặc sản cua Cà Mau chắc thịt, tôm sú nướng muối ớt đậm đà.', '2026-08-08 11:30:00', 'Nhà hàng Phố Biển, TP. Cà Mau', 'Có mây mát', 'Hài lòng', 'lunch', 'Cua Cà Mau rang me, tôm sú nướng, lẩu cá kèo rau đắng', 'Nhà hàng Phố Biển', NULL, '1', 'published', '2026-08-08 13:51:26', '2026-08-08 13:51:26'),
('27', '8', 'Check-in Khách sạn Mường Thanh Cà Mau', 'Đoàn làm thủ tục nhận phòng khách sạn 4 sao Mường Thanh Luxe Cà Mau, nghỉ ngơi thư giãn.', '2026-08-08 13:30:00', 'Khách sạn Mường Thanh Cà Mau', 'Nắng dịu', 'Thoải mái', 'accommodation', NULL, 'Khách sạn Mường Thanh Cà Mau 4★', NULL, '1', 'published', '2026-08-08 13:51:26', '2026-08-08 13:51:26'),
('28', '8', 'Tham quan Khu du lịch Đất Mũi Cà Mau', 'Đoàn chụp ảnh lưu niệm tại Cột mốc tọa độ quốc gia GPS 0001, Biểu tượng con tàu Mũi Cà Mau hướng ra biển lớn.', '2026-08-08 15:00:00', 'Khu du lịch Đất Mũi, Cà Mau', 'Gió biển lồng lộng', 'Hào hứng', 'sightseeing', NULL, NULL, NULL, '1', 'published', '2026-08-08 13:51:26', '2026-08-08 13:51:26'),
('29', '8', 'Ăn tối lẩu mắm sông nước U Minh', 'Thưởng thức bữa tối chuẩn vị miền Tây với món Lẩu mắm U Minh cùng 20 loại rau đồng quê độc đáo.', '2026-08-08 18:30:00', 'Nhà hàng Hương Rừng, TP. Cà Mau', 'Chiều mát', 'Ấm cúng', 'dinner', 'Lẩu mắm U Minh, ba khía muối chấm chanh, cá lóc nướng trui lá chuối', 'Nhà hàng Hương Rừng', NULL, '1', 'published', '2026-08-08 13:51:26', '2026-08-08 13:51:26'),
('30', '8', 'Dạo Chợ đêm TP. Cà Mau', 'Đoàn tự do dạo phố đêm Cà Mau, thưởng thức chè mâm, bánh tằm gỏi gà và mua sản vật địa phương.', '2026-08-08 20:00:00', 'Chợ đêm TP. Cà Mau', 'Gió mát', 'Sôi nổi', 'shopping', NULL, 'Khu Chợ đêm Cà Mau', NULL, '1', 'published', '2026-08-08 13:51:26', '2026-08-08 13:51:26'),
('31', '8', 'Ăn sáng buffet tại khách sạn Mường Thanh', 'Dùng bữa sáng buffet nạp năng lượng chuẩn bị cho chuyến khám phá rừng ngập nước U Minh.', '2026-08-09 07:00:00', 'Khách sạn Mường Thanh Cà Mau', 'Nắng sớm tươi mát', 'Sảng khoái', 'breakfast', 'Buffet sáng: phở, bún nước lèo, bánh mì pate, trái cây', 'Khách sạn Mường Thanh Cà Mau', NULL, '1', 'published', '2026-08-08 13:51:26', '2026-08-08 13:51:26'),
('32', '8', 'Tham quan Vườn Quốc gia U Minh Hạ', 'Đoàn ngồi vỏ lãi chạy xuyên rừng tràm U Minh Hạ, ngắm vọng lâm đài quan sát toàn cảnh rừng nguyên sinh.', '2026-08-09 08:30:00', 'Vườn Quốc gia U Minh Hạ, Cà Mau', 'Nắng dịu dưới vòm cây', 'Thích thú', 'sightseeing', NULL, NULL, NULL, '1', 'published', '2026-08-08 13:51:26', '2026-08-08 13:51:26'),
('33', '8', 'Ăn trưa dân dã rừng U Minh', 'Thưởng thức bữa trưa cơm quê rừng tràm với cá lóc nướng trui chấm muối ớt hột và mật ong rừng.', '2026-08-09 11:30:00', 'Khu du lịch sinh thái U Minh Hạ', 'Trời mát', 'Ngon miệng', 'lunch', 'Cá lóc nướng trui, lươn um lá nhàu, canh chua cá rô đồng, rau rừng chấm kho quẹt', 'Nhà hàng sinh thái U Minh', NULL, '1', 'published', '2026-08-08 13:51:26', '2026-08-08 13:51:26'),
('34', '8', 'Tham quan Danh thắng Hòn Đá Bạc', 'Đoàn di chuyển tham quan Quần thể Hòn Đá Bạc, viếng Đền thờ Bác Hồ và Chùa Hang linh thiêng.', '2026-08-09 14:00:00', 'Hòn Đá Bạc, Trần Văn Thời, Cà Mau', 'Nắng trong', 'Ấn tượng', 'sightseeing', NULL, NULL, NULL, '1', 'published', '2026-08-08 13:51:26', '2026-08-08 13:51:26'),
('35', '8', 'Trở về khách sạn nghỉ ngơi', 'Đoàn về lại khách sạn nghỉ ngơi, tắm hồ bơi thư giãn chuẩn bị cho tiệc Gala tối.', '2026-08-09 17:00:00', 'Khách sạn Mường Thanh Cà Mau', 'Chiều mát', 'Thư thái', 'rest', NULL, 'Khách sạn Mường Thanh Cà Mau', NULL, '1', 'published', '2026-08-08 13:51:26', '2026-08-08 13:51:26'),
('36', '8', 'Ăn tối Tiệc Hải Sản Gala', 'Bữa tối tiệc gala sang trọng với các món hải sản Cà Mau hảo hạng tươi ngon.', '2026-08-09 18:30:00', 'Nhà hàng Ánh Mây, TP. Cà Mau', 'Mát mẻ', 'Hài lòng', 'dinner', 'Hải sản nướng BBQ, lẩu cua đồng đậm đà, mực hấp gừng', 'Nhà hàng Ánh Mây', NULL, '1', 'published', '2026-08-08 13:51:26', '2026-08-08 13:51:26'),
('37', '8', 'Giao lưu Đờn ca tài tử Nam Bộ', 'Thưởng thức chương trình biểu diễn Đờn ca tài tử Nam Bộ di sản phi vật thể đại diện nhân loại.', '2026-08-09 20:30:00', 'Trung tâm Văn hóa TP. Cà Mau', 'Mát mẻ', 'Say mê', 'activity', NULL, NULL, NULL, '1', 'published', '2026-08-08 13:51:26', '2026-08-08 13:51:26'),
('38', '8', 'Ăn sáng & Trả phòng khách sạn', 'Đoàn ăn sáng buffet và hoàn tất thủ tục trả phòng khách sạn Mường Thanh Cà Mau.', '2026-08-10 07:00:00', 'Khách sạn Mường Thanh Cà Mau', 'Nắng đẹp', 'Thoải mái', 'breakfast', 'Buffet sáng khách sạn', 'Khách sạn Mường Thanh Cà Mau', NULL, '1', 'published', '2026-08-08 13:51:26', '2026-08-08 13:51:26'),
('39', '8', 'Mua sắm đặc sản Cà Mau làm quà', 'Đoàn ghé cơ sở đặc sản mua Tôm khô Năm Căn, Mật ong rừng U Minh nguyên chất, Mắm cá linh.', '2026-08-10 08:30:00', 'Cửa hàng Đặc sản Năm Căn, TP. Cà Mau', 'Nắng dịu', 'Vui vẻ', 'shopping', NULL, 'Cửa hàng Đặc sản Năm Căn', NULL, '1', 'published', '2026-08-08 13:51:26', '2026-08-08 13:51:26'),
('40', '8', 'Ăn trưa tại Cần Thơ trên đường về', 'Đoàn dừng chân tại TP. Cần Thơ thưởng thức bữa trưa đậm đà chất Nam Bộ.', '2026-08-10 11:30:00', 'Nhà hàng Hoa Súng, Cần Thơ', 'Có mây mát', 'Ngon miệng', 'lunch', 'Lẩu vịt nấu chao, bánh xèo miền Tây giòn rụm, cá lóc chiên xù', 'Nhà hàng Hoa Súng', NULL, '1', 'published', '2026-08-08 13:51:26', '2026-08-08 13:51:26'),
('41', '8', 'Khởi hành trở về điểm đón ban đầu', 'Đoàn lên xe bắt đầu chuyến trở về. HDV tổng kết chuyến đi và thu thập ý kiến khách hàng.', '2026-08-10 14:00:00', 'Xe du lịch', 'Nắng nhẹ', 'Nghỉ ngơi', 'transfer', NULL, NULL, NULL, '1', 'published', '2026-08-08 13:51:26', '2026-08-08 13:51:26'),
('42', '8', 'Kết thúc chuyến đi & Tiễn đoàn', 'Xe về tới điểm hẹn ban đầu, HDV hỗ trợ lấy hành lý và chia tay quý khách thân yêu.', '2026-08-10 19:30:00', '8B ngách 46 ngõ 1 Bùi Xương Trạch', 'Chiều mát', 'Hoàn thành tuyệt vời', 'return', NULL, NULL, NULL, '1', 'published', '2026-08-08 13:51:26', '2026-08-08 13:51:26'),
('43', '9', 'Tập trung và đón đoàn đi Hạ Long đợt 2', 'HDV đón khách, làm thủ tục kiểm đếm hành lý và chuẩn bị khởi hành chuyến du lịch biển Hạ Long.', '2026-08-13 06:00:00', '8B ngách 46 ngõ 1 Bùi Xương Trạch', 'Nắng trong', 'Sẵn sàng khởi hành', 'transfer', NULL, NULL, NULL, '1', 'published', '2026-08-08 13:51:26', '2026-08-08 13:51:26'),
('44', '9', 'Khởi hành đi Hạ Long', 'Xe lăn bánh trên cao tốc Hà Nội - Hải Phòng - Hạ Long. HDV tổ chức các trò chơi vui nhộn trên xe.', '2026-08-13 06:30:00', 'Điểm tập trung', 'Nắng nhẹ', 'Sôi nổi', 'transfer', NULL, NULL, NULL, '1', 'published', '2026-08-08 13:51:26', '2026-08-08 13:51:26'),
('45', '9', 'Ăn sáng tại trạm nghỉ Hải Dương', 'Đoàn dừng ăn sáng phở gà thơm nức hoặc bánh mì pate tại trạm dừng chân hiện đại.', '2026-08-13 08:00:00', 'Trạm nghỉ Hải Dương', 'Nắng đẹp', 'Vui vẻ', 'breakfast', 'Phở gà ta, xôi ruốc lá cẩm, bánh mì pate', 'Trạm nghỉ Hải Dương', NULL, '1', 'published', '2026-08-08 13:51:26', '2026-08-08 13:51:26'),
('46', '9', 'Đến Cảng Quốc tế Tuần Châu', 'Đoàn tập trung tại bến Tuần Châu làm thủ tục xuống du thuyền Hạ Long Deluxe 5 sao.', '2026-08-13 10:30:00', 'Cảng tàu Quốc tế Tuần Châu, Hạ Long', 'Trời trong xanh', 'Hào hứng', 'transfer', NULL, NULL, NULL, '1', 'published', '2026-08-08 13:51:26', '2026-08-08 13:51:26'),
('47', '9', 'Ăn trưa Buffet hải sản du thuyền 5★', 'Thưởng thức đại tiệc buffet hải sản cao cấp hàu sống, tôm hùm, cua hấp giữa lòng Vịnh Hạ Long.', '2026-08-13 12:00:00', 'Du thuyền Hạ Long Deluxe 5★', 'Gió biển nhẹ', 'Tuyệt vời', 'lunch', 'Buffet hải sản Quốc tế: hàu sống, tôm nướng, cua hấp, súp hải sản', 'Du thuyền Hạ Long Deluxe', NULL, '1', 'published', '2026-08-08 13:51:26', '2026-08-08 13:51:26'),
('48', '9', 'Tham quan Động Thiên Cung & Hang Trinh Nữ', 'Đoàn khám phá Động Thiên Cung nguy nga như lăng điện với dải nhũ đá lung linh rực rỡ.', '2026-08-13 14:30:00', 'Động Thiên Cung, Vịnh Hạ Long', 'Trong động mát lạnh', 'Ấn tượng', 'sightseeing', NULL, NULL, NULL, '1', 'published', '2026-08-08 13:51:26', '2026-08-08 13:51:26'),
('49', '9', 'Tắm biển bãi bồi Đảo Tuần Châu', 'Đoàn tự do tắm biển và tham gia các trò chơi vận động bãi biển Tuần Châu.', '2026-08-13 16:30:00', 'Bãi tắm Tuần Châu, Hạ Long', 'Nắng dịu chiều', 'Sảng khoái', 'activity', NULL, NULL, NULL, '1', 'published', '2026-08-08 13:51:26', '2026-08-08 13:51:26'),
('50', '9', 'Check-in FLC Halong Bay Resort 5★', 'Xe đưa đoàn về Resort FLC trên đỉnh đồi view trực diện toàn cảnh Vịnh Hạ Long.', '2026-08-13 18:00:00', 'FLC Halong Bay Golf & Luxury Resort', 'Chiều mát', 'Thoải mái', 'accommodation', NULL, 'FLC Halong Bay Resort 5★', NULL, '1', 'published', '2026-08-08 13:51:26', '2026-08-08 13:51:26'),
('51', '9', 'Ăn tối hải sản phong cách 5 sao', 'Thưởng thức bữa tối hải sản phong cách hiện đại tại nhà hàng Grandeur thuộc FLC Resort.', '2026-08-13 19:30:00', 'Nhà hàng Grandeur FLC Resort', 'Đêm mát', 'Sang trọng', 'dinner', 'Hải sản nướng phô mai, cá song hấp xì dầu, canh hải sản, tráng miệng hoa quả', 'Nhà hàng FLC Grandeur', NULL, '1', 'published', '2026-08-08 13:51:26', '2026-08-08 13:51:26'),
('52', '9', 'Thư giãn tại Rooftop Lounge ngắm Vịnh đêm', 'Đoàn tự do thưởng thức ly cocktail ngắm toàn cảnh thành phố Hạ Long rực rỡ sắc màu về đêm.', '2026-08-13 21:00:00', 'Rooftop Lounge FLC Resort', 'Đêm mát lồng lộng', 'Thư giãn tuyệt vời', 'rest', NULL, 'Rooftop Lounge FLC', NULL, '1', 'published', '2026-08-08 13:51:26', '2026-08-08 13:51:26'),
('53', '9', 'Ăn sáng buffet tại FLC Resort', 'Thưởng thức bữa sáng buffet quốc tế đẳng cấp 5 sao tại nhà hàng FLC Resort.', '2026-08-14 07:00:00', 'FLC Halong Bay Resort 5★', 'Nắng đẹp', 'Sảng khoái', 'breakfast', 'Buffet 5 sao: phở, bún, bánh mì Pháp, trái cây nhiệt đới', 'FLC Halong Resort', NULL, '1', 'published', '2026-08-08 13:51:26', '2026-08-08 13:51:26'),
('54', '9', 'Khám phá Làng chài Ba Hang & Chèo thuyền thúng', 'Đoàn trải nghiệm chèo thuyền thúng do ngư dân địa phương chèo qua các hòn đảo Ba Hang.', '2026-08-14 08:30:00', 'Làng chài Ba Hang, Vịnh Hạ Long', 'Nắng mát', 'Hào hứng', 'activity', NULL, NULL, NULL, '1', 'published', '2026-08-08 13:51:26', '2026-08-08 13:51:26'),
('55', '9', 'Ăn trưa tại Nhà hàng Cua Vàng', 'Thưởng thức món Lẩu Cua Vàng trứ danh và các loại hải sản quý nướng mỡ hành.', '2026-08-14 11:30:00', 'Nhà hàng Cua Vàng, Hạ Long', 'Trời xanh', 'Ngon miệng', 'lunch', 'Lẩu cua vàng, tôm mũ ni hấp, tu hài nướng mỡ hành', 'Nhà hàng Cua Vàng', NULL, '1', 'published', '2026-08-08 13:51:26', '2026-08-08 13:51:26'),
('56', '9', 'Tham quan Chùa Long Tiên & Núi Bài Thơ', 'Đoàn viếng Chùa Long Tiên ngôi chùa cổ linh thiêng nhất dưới chân núi Bài Thơ.', '2026-08-14 14:00:00', 'Chùa Long Tiên, Chân núi Bài Thơ', 'Nắng dịu', 'Thanh tịnh', 'sightseeing', NULL, NULL, NULL, '1', 'published', '2026-08-08 13:51:26', '2026-08-08 13:51:26'),
('57', '9', 'Tắm hồ bơi vô cực khách sạn FLC', 'Đoàn đắm mình trong hồ bơi vô cực tràn bờ ngắm khung cảnh vịnh biển tuyệt đẹp.', '2026-08-14 16:30:00', 'Infinity Pool FLC Resort', 'Chiều mát', 'Thư giãn', 'rest', NULL, 'FLC Resort Pool', NULL, '1', 'published', '2026-08-08 13:51:26', '2026-08-08 13:51:26'),
('58', '9', 'Ăn tối tiệc Lẩu Nướng Hải Sản', 'Bữa tối ấm cúng với tiệc lẩu nướng hải sản tươi ngon đậm đà phong vị Quảng Ninh.', '2026-08-14 19:00:00', 'Nhà hàng Biển Sáng, Bãi Cháy', 'Đêm mát', 'Sôi nổi', 'dinner', 'Lẩu hải sản thập cẩm, hàu nướng phô mai, mực trứng nướng sa tế', 'Nhà hàng Biển Sáng', NULL, '1', 'published', '2026-08-08 13:51:26', '2026-08-08 13:51:26'),
('59', '9', 'Trải nghiệm Xe buýt 2 tầng ngắm phố biển', 'Đoàn trải nghiệm ngắm thành phố Hạ Long ban đêm trên tuyến xe buýt 2 tầng mui trần.', '2026-08-14 20:30:00', 'Đường bờ biển Trần Quốc Nghiễn, Hạ Long', 'Gió biển mát rượi', 'Hào hứng', 'sightseeing', NULL, NULL, NULL, '1', 'published', '2026-08-08 13:51:26', '2026-08-08 13:51:26'),
('60', '9', 'Ăn sáng & Checkout FLC Resort', 'Đoàn dùng điểm tâm sáng buffet và làm thủ tục checkout FLC Resort.', '2026-08-15 07:00:00', 'FLC Halong Bay Resort 5★', 'Nắng đẹp', 'Thoải mái', 'breakfast', 'Buffet sáng resort', 'FLC Halong Resort', NULL, '1', 'published', '2026-08-08 13:51:26', '2026-08-08 13:51:26'),
('61', '9', 'Tham quan Chợ hải sản Bãi Cháy', 'Đoàn ghé thăm khu chợ hải sản nhộn nhịp, xem các ghe tàu đánh bắt hải sản tươi cập bến.', '2026-08-15 08:30:00', 'Chợ hải sản Bãi Cháy, Hạ Long', 'Nắng nhẹ', 'Hào hứng', 'sightseeing', NULL, 'Chợ hải sản Bãi Cháy', NULL, '1', 'published', '2026-08-08 13:51:26', '2026-08-08 13:51:26'),
('62', '9', 'Mua sắm đặc sản Chả mực Thoan', 'Đoàn ghé thương hiệu Chả mực giã tay Thoan nổi tiếng nhất Hạ Long chọn mua đặc sản cao cấp.', '2026-08-15 10:30:00', 'Cơ sở Chả mực Thoan, Hạ Long', 'Nắng dịu', 'Vui vẻ', 'shopping', NULL, 'Cơ sở Chả mực Thoan', NULL, '1', 'published', '2026-08-08 13:51:26', '2026-08-08 13:51:26'),
('63', '9', 'Ăn trưa tiệc chia tay Hạ Long', 'Thưởng thức bữa trưa ấm cúng chia tay Hạ Long trước khi khởi hành trở về.', '2026-08-15 12:00:00', 'Nhà hàng Phương Nam Hạ Long', 'Nắng mát', 'Ngon miệng', 'lunch', 'Chả mực rán nóng, cơm rang hải sản, canh cá bớp', 'Nhà hàng Phương Nam', NULL, '1', 'published', '2026-08-08 13:51:26', '2026-08-08 13:51:26'),
('64', '9', 'Khởi hành trở về Hà Nội', 'Đoàn lên xe trở về. HDV gửi tặng nón kỷ niệm và phát phiếu đánh giá chất lượng tour.', '2026-08-15 14:00:00', 'Xe du lịch', 'Nắng nhẹ', 'Thoải mái', 'transfer', NULL, NULL, NULL, '1', 'published', '2026-08-08 13:51:26', '2026-08-08 13:51:26'),
('65', '9', 'Kết thúc chuyến đi & Tiễn đoàn', 'Xe về tới điểm trả khách ban đầu, HDV hỗ trợ hành lý và chia tay đoàn.', '2026-08-15 17:30:00', '8B ngách 46 ngõ 1 Bùi Xương Trạch', 'Mát mẻ', 'Hoàn thành tốt đẹp', 'return', NULL, NULL, NULL, '1', 'published', '2026-08-08 13:51:26', '2026-08-08 13:51:26'),
('66', '10', 'Tập trung đón đoàn đi Sapa Fansipan', 'HDV tập trung du khách, kiểm tra vé xe giường nằm, phổ biến lưu ý khi du lịch vùng cao.', '2026-08-20 06:00:00', 'Công viên Thống Nhất, Hà Nội', 'Nắng dịu', 'Sẵn sàng khởi hành', 'transfer', NULL, NULL, NULL, '1', 'published', '2026-08-08 13:51:26', '2026-08-08 13:51:26'),
('67', '10', 'Khởi hành đi Sapa qua cao tốc Nội Bài - Lào Cai', 'Xe xuất phát đi Lào Cai. HDV giới thiệu thiên nhiên Tây Bắc và lịch trình chi tiết 3 ngày 2 đêm.', '2026-08-20 06:30:00', 'Cao tốc Nội Bài - Lào Cai', 'Nắng mát', 'Hào hứng', 'transfer', NULL, NULL, NULL, '1', 'published', '2026-08-08 13:51:26', '2026-08-08 13:51:26'),
('68', '10', 'Ăn sáng tại trạm dừng Phú Thọ', 'Đoàn dừng chân ăn sáng tô bún chả thơm lừng hoặc bánh mì kẹp thịt tại trạm nghỉ.', '2026-08-20 08:00:00', 'Trạm nghỉ Phú Thọ', 'Mát mẻ', 'Vui vẻ', 'breakfast', 'Bún chả Phú Thọ, bánh mì pate, cà phê sữa nóng', 'Trạm nghỉ Phú Thọ', NULL, '1', 'published', '2026-08-08 13:51:26', '2026-08-08 13:51:26'),
('69', '10', 'Đến Sapa & Ăn trưa đặc sản Tây Bắc', 'Xe đến thị trấn sương mờ Sapa. Đoàn thưởng thức bữa trưa thắng cố, lợn mán quay nóng hổi.', '2026-08-20 12:00:00', 'Nhà hàng Cầu Mây, Thị trấn Sapa', 'Se lạnh dịu mát', 'Ngon miệng', 'lunch', 'Lợn mán quay giòn da, thắng cố ngựa, gà bản nướng mật ong, xôi ngũ sắc', 'Nhà hàng Cầu Mây', NULL, '1', 'published', '2026-08-08 13:51:26', '2026-08-08 13:51:26'),
('70', '10', 'Check-in Khách sạn Sapa Charm 4★', 'Đoàn về khách sạn Sapa Charm nhận phòng nghỉ ngơi ngắm thung lũng Mường Hoa.', '2026-08-20 13:30:00', 'Khách sạn Sapa Charm, Sapa', 'Se lạnh', 'Thoải mái', 'accommodation', NULL, 'Khách sạn Sapa Charm 4★', NULL, '1', 'published', '2026-08-08 13:51:26', '2026-08-08 13:51:26'),
('71', '10', 'Tham quan Bản Cát Cát của người H\'Mông', 'Đoàn đi bộ tham quan bản làng cổ Cát Cát, check-in guồng nước gỗ, suối Hoa và khoác trang phục dân tộc.', '2026-08-20 15:00:00', 'Bản Cát Cát, Sapa', 'Trời trong mây nhẹ', 'Ấn tượng', 'sightseeing', NULL, NULL, NULL, '1', 'published', '2026-08-08 13:51:26', '2026-08-08 13:51:26'),
('72', '10', 'Ăn tối tiệc Lẩu Cá Hồi Cá Tầm Sapa', 'Thưởng thức bữa tối Lẩu cá hồi cá tầm tươi sống đánh bắt tại suối Sapa đậm đà thơm ngậy.', '2026-08-20 18:30:00', 'Nhà hàng Ô Quý Hồ, Sapa', 'Sương mờ se lạnh', 'Ấm cúng', 'dinner', 'Lẩu cá hồi Sapa, cá tầm nướng riềng mẻ, rau mầm đá chấm kho quẹt', 'Nhà hàng Ô Quý Hồ', NULL, '1', 'published', '2026-08-08 13:51:26', '2026-08-08 13:51:26'),
('73', '10', 'Dạo Quảng trường & Chợ tình Sapa', 'Đoàn tự do dạo Nhà thờ Đá cổ, thưởng thức ngô nướng trứng nướng và hòa vào không gian Chợ tình Sapa.', '2026-08-20 20:30:00', 'Quảng trường Sapa & Nhà thờ Đá', 'Lạnh sương mù', 'Mộng mơ', 'shopping', NULL, 'Khu Quảng trường Sapa', NULL, '1', 'published', '2026-08-08 13:51:26', '2026-08-08 13:51:26'),
('74', '10', 'Ăn sáng buffet tại khách sạn Sapa Charm', 'Dùng điểm tâm sáng buffet ấm áp tại khách sạn trước hành trình chinh phục đỉnh Fansipan.', '2026-08-21 07:00:00', 'Khách sạn Sapa Charm', 'Nắng sớm se lạnh', 'Sảng khoái', 'breakfast', 'Buffet sáng: phở gà bản, bánh mì, trứng ốp la, cà phê nóng', 'Khách sạn Sapa Charm', NULL, '1', 'published', '2026-08-08 13:51:26', '2026-08-08 13:51:26'),
('75', '10', 'Chinh phục Đỉnh Fansipan 3.143m', 'Đoàn trải nghiệm cáp treo 3 dây đỉnh cao thế giới vượt biển mây, chiêm bái Đại tượng Phật và chạm tay cột mốc Nóc nhà Đông Dương.', '2026-08-21 08:30:00', 'Đỉnh Fansipan, Sun World Fansipan Legend', 'Biển mây vần vũ rực rỡ', 'Tự hào & Phấn khích', 'sightseeing', NULL, NULL, NULL, '1', 'published', '2026-08-08 13:51:26', '2026-08-08 13:51:26'),
('76', '10', 'Ăn trưa buffet đại ngàn Fansipan', 'Thưởng thức bữa trưa buffet phong phú hơn 50 món ẩm thực mộc mạc vùng cao ngay trên nhà hàng nhà ga Fansipan.', '2026-08-21 11:30:00', 'Nhà hàng Vân Sam, Đỉnh Fansipan', 'Trời mây mát', 'Ngon miệng', 'lunch', 'Buffet đại ngàn Fansipan: thịt trâu gác bếp, lợn quay, cơm lam, rau rừng', 'Nhà hàng Vân Sam', NULL, '1', 'published', '2026-08-08 13:51:26', '2026-08-08 13:51:26'),
('77', '10', 'Tham quan Cổng trời Đèo Ô Quý Hồ', 'Đoàn xuống núi ghé đèo Ô Quý Hồ - một trong Tứ đại đỉnh đèo Việt Nam, săn mây và ngắm hoàng hôn rực rỡ.', '2026-08-21 14:00:00', 'Đèo Ô Quý Hồ, Sapa', 'Hoàng hôn rực rỡ', 'Mê đắm', 'sightseeing', NULL, NULL, NULL, '1', 'published', '2026-08-08 13:51:26', '2026-08-08 13:51:26'),
('78', '10', 'Ngâm chân lá thuốc người Dao Đỏ', 'Trải nghiệm ngâm chân khoáng lá thuốc dân tộc Dao Đỏ giúp xua tan mệt mỏi sau ngày leo núi.', '2026-08-21 17:00:00', 'Cơ sở Tắm lá thuốc Dao Đỏ Sapa', 'Chiều se lạnh', 'Thư giãn hoàn toàn', 'rest', NULL, 'Cơ sở Dao Đỏ Spa', NULL, '1', 'published', '2026-08-08 13:51:26', '2026-08-08 13:51:26'),
('79', '10', 'Ăn tối tiệc Đồ Nướng Sapa', 'Thưởng thức tiệc đồ nướng mộc mạc bên bếp than hồng: xiên nướng thịt lợn bản, trứng nướng, cơm lam.', '2026-08-21 19:00:00', 'Nhà hàng Phố Nướng Sapa', 'Đêm rét nhẹ', 'Vui vẻ', 'dinner', 'Thịt dẻ sườn nướng, dạ tày nướng, trứng gà nướng, cơm lam nướng than', 'Nhà hàng Phố Nướng', NULL, '1', 'published', '2026-08-08 13:51:26', '2026-08-08 13:51:26'),
('80', '10', 'Cà phê VIEW mây tại Viettrekking', 'Thưởng thức ly cacao nóng ngắm thung lũng sương mù đêm rực rỡ đèn từ quán cafe đẹp nhất Sapa.', '2026-08-21 20:30:00', 'Viettrekking Coffee, Sapa', 'Sương mù lãng mạn', 'Yên bình', 'rest', NULL, 'Viettrekking Coffee', NULL, '1', 'published', '2026-08-08 13:51:26', '2026-08-08 13:51:26'),
('81', '10', 'Ăn sáng & Trả phòng khách sạn', 'Ăn sáng buffet và làm thủ tục checkout khách sạn Sapa Charm.', '2026-08-22 07:00:00', 'Khách sạn Sapa Charm', 'Nắng sớm dịu mát', 'Thoải mái', 'breakfast', 'Buffet sáng khách sạn', 'Khách sạn Sapa Charm', NULL, '1', 'published', '2026-08-08 13:51:26', '2026-08-08 13:51:26'),
('82', '10', 'Tham quan Núi Hàm Rồng', 'Đoàn leo núi Hàm Rồng qua Vườn Lan, Vườn Đào, Cổng Trời và ngắm toàn cảnh thị trấn Sapa từ trên cao.', '2026-08-22 08:30:00', 'Khu du lịch Núi Hàm Rồng, Sapa', 'Trời xanh mây trắng', 'Hào hứng', 'sightseeing', NULL, NULL, NULL, '1', 'published', '2026-08-08 13:51:26', '2026-08-08 13:51:26'),
('83', '10', 'Mua sắm đặc sản Tây Bắc', 'Đoàn ghé Chợ Sapa mua đặc sản thịt trâu gác bếp, măng nứa, nấm hương rừng và mận tả van.', '2026-08-22 10:30:00', 'Chợ Sapa, Lào Cai', 'Nắng mát', 'Vui vẻ', 'shopping', NULL, 'Chợ Sapa', NULL, '1', 'published', '2026-08-08 13:51:26', '2026-08-08 13:51:26'),
('84', '10', 'Ăn trưa mâm cơm Tây Bắc', 'Dùng bữa trưa chia tay Sapa với các món ẩm thực mộc mạc vùng cao.', '2026-08-22 12:00:00', 'Nhà hàng Mộc Sapa', 'Trời dịu', 'Ngon miệng', 'lunch', 'Thịt lợn cắp nách xào sả ớt, cá suối chiên giòn, măng rừng luộc, xôi cẩm', 'Nhà hàng Mộc Sapa', NULL, '1', 'published', '2026-08-08 13:51:26', '2026-08-08 13:51:26'),
('85', '10', 'Khởi hành trở về Hà Nội', 'Đoàn lên xe chất lượng cao khởi hành về lại Hà Nội qua đường cao tốc Nội Bài - Lào Cai.', '2026-08-22 14:00:00', 'Xe du lịch', 'Nắng dịu', 'Thoải mái', 'transfer', NULL, NULL, NULL, '1', 'published', '2026-08-08 13:51:26', '2026-08-08 13:51:26'),
('86', '10', 'Trở về Hà Nội & Kết thúc tour', 'Xe về tới Công viên Thống Nhất Hà Nội, HDV hỗ trợ lấy hành lý và chia tay quý khách.', '2026-08-22 19:00:00', 'Công viên Thống Nhất, Hà Nội', 'Chiều mát', 'Hoàn thành tốt đẹp', 'return', NULL, NULL, NULL, '1', 'published', '2026-08-08 13:51:26', '2026-08-08 13:51:26'),
('87', '11', 'Đón đoàn tại Sân bay Đà Nẵng', 'HDV đón quý khách tại ga đến Sân bay Đà Nẵng, tặng nón du lịch và chào mừng đoàn tới miền Trung.', '2026-08-25 07:30:00', 'Sân bay Quốc tế Đà Nẵng', 'Nắng trong lành', 'Hào hứng', 'transfer', NULL, NULL, NULL, '1', 'published', '2026-08-08 13:51:26', '2026-08-08 13:51:26'),
('88', '11', 'Ăn sáng Mì Quảng Bà Mua', 'Thưởng thức bữa sáng thương hiệu Mì Quảng Bà Mua nổi tiếng Đà Nẵng với ếch, tôm, thịt ếch giòn thơm.', '2026-08-25 08:30:00', 'Nhà hàng Mì Quảng Bà Mua, Đà Nẵng', 'Nắng nhẹ', 'Ngon miệng', 'breakfast', 'Mì Quảng tôm thịt ếch, bánh tráng nướng, sữa đậu nành', 'Mì Quảng Bà Mua', NULL, '1', 'published', '2026-08-08 13:51:26', '2026-08-08 13:51:26'),
('89', '11', 'Tham quan Bán đảo Sơn Trà & Chùa Linh Ứng', 'Đoàn tham quan Bán đảo Sơn Trà, chiêm bái Tượng Phật Bà Quan Thế Âm cao 67m ngắm toàn cảnh thành phố biển.', '2026-08-25 09:30:00', 'Chùa Linh Ứng Bán đảo Sơn Trà', 'Nắng đẹp gió biển', 'Ấn tượng', 'sightseeing', NULL, NULL, NULL, '1', 'published', '2026-08-08 13:51:26', '2026-08-08 13:51:26'),
('90', '11', 'Ăn trưa đặc sản Bánh tráng thịt heo', 'Dùng bữa trưa đặc sản Bánh tráng cuốn thịt heo hai đầu da chấm mắm nêm đậm vị miền Trung.', '2026-08-25 11:30:00', 'Nhà hàng Trần, Đà Nẵng', 'Trời trong mát', 'Ngon miệng', 'lunch', 'Bánh tráng thịt heo hai đầu da, bánh bèo, mắm nêm thơm nức', 'Nhà hàng Trần', NULL, '1', 'published', '2026-08-08 13:51:26', '2026-08-08 13:51:26'),
('91', '11', 'Check-in Mường Thanh Luxury Đà Nẵng', 'Đoàn về nhận phòng Khách sạn 5 sao Mường Thanh Luxury view biển Mỹ Khê nghỉ ngơi.', '2026-08-25 13:30:00', 'Khách sạn Mường Thanh Luxury Đà Nẵng', 'Nắng ấm', 'Thoải mái', 'accommodation', NULL, 'Khách sạn Mường Thanh Luxury 5★', NULL, '1', 'published', '2026-08-08 13:51:26', '2026-08-08 13:51:26'),
('92', '11', 'Khám phá Phố cổ Hội An', 'Đoàn di chuyển tới Hội An, tản bộ qua Chùa Cầu Nhật Bản, Nhà cổ Phùng Hưng, Hội quan Quảng Đông.', '2026-08-25 15:30:00', 'Phố cổ Hội An, Quảng Nam', 'Chiều mát lãng mạn', 'Mê đắm', 'sightseeing', NULL, NULL, NULL, '1', 'published', '2026-08-08 13:51:26', '2026-08-08 13:51:26'),
('93', '11', 'Ăn tối đặc sản Phố cổ Hội An', 'Thưởng thức bữa tối đặc sản Cao lầu Hội An, cơm gà Bà Buổi, hoành thánh chiên.', '2026-08-25 18:30:00', 'Nhà hàng Cơm gà Bà Buổi, Hội An', 'Đêm đèn lồng lung linh', 'Ấm cúng', 'dinner', 'Cao lầu Hội An, cơm gà Bà Buổi, hoành thánh chiên, chè hạt sen', 'Nhà hàng Cơm gà Bà Buổi', NULL, '1', 'published', '2026-08-08 13:51:26', '2026-08-08 13:51:26'),
('94', '11', 'Đi thuyền thả hoa đăng trên sông Hoài', 'Đoàn lên thuyền gỗ dạo sông Hoài, thả những ngọn đèn hoa đăng lung linh ước nguyện bình an.', '2026-08-25 20:00:00', 'Sông Hoài, Phố cổ Hội An', 'Đêm mát gió sông', 'Tuyệt đẹp', 'activity', NULL, 'Thuyền sông Hoài', NULL, '1', 'published', '2026-08-08 13:51:26', '2026-08-08 13:51:26'),
('95', '11', 'Ăn sáng buffet tại Mường Thanh Luxury', 'Đoàn dùng buffet sáng quốc tế tại nhà hàng tầng cao view biển.', '2026-08-26 07:00:00', 'Khách sạn Mường Thanh Luxury Đà Nẵng', 'Nắng rực rỡ', 'Sảng khoái', 'breakfast', 'Buffet 5 sao: bún bò Huế, bánh mì, hủ tiếu, trái cây', 'Khách sạn Mường Thanh Luxury', NULL, '1', 'published', '2026-08-08 13:51:26', '2026-08-08 13:51:26'),
('96', '11', 'Khởi hành đi Quần thể du lịch Bà Nà Hills', 'Xe đưa đoàn tới chân núi Bà Nà bắt đầu hành trình lên \"Đường lên tiên cảnh\".', '2026-08-26 08:00:00', 'Sun World Ba Na Hills, Đà Nẵng', 'Trời xanh trong', 'Hào hứng', 'transfer', NULL, NULL, NULL, '1', 'published', '2026-08-08 13:51:26', '2026-08-08 13:51:26'),
('97', '11', 'Check-in Cầu Vàng Bàn Tay & Làng Pháp', 'Đoàn đi cáp treo kỷ lục, check-in Cầu Vàng siêu phẩm thế giới và dạo Làng Pháp kiến trúc Châu Âu cổ kính.', '2026-08-26 09:00:00', 'Cầu Vàng & Làng Pháp Bà Nà Hills', 'Sương mây lãng mạn', 'Choáng ngợp', 'sightseeing', NULL, NULL, NULL, '1', 'published', '2026-08-08 13:51:26', '2026-08-08 13:51:26'),
('98', '11', 'Ăn trưa Buffet Châu Âu 100 món', 'Thưởng thức đại tiệc buffet Châu Âu - Á tại nhà hàng Arapang trên đỉnh Bà Nà.', '2026-08-26 12:00:00', 'Nhà hàng Arapang, Bà Nà Hills', 'Mát mẻ', 'Tuyệt vời', 'lunch', 'Buffet 100 món: đùi cừu nướng, xúc xích Đức, pizza, hải sản', 'Nhà hàng Arapang Bà Nà', NULL, '1', 'published', '2026-08-08 13:51:26', '2026-08-08 13:51:26'),
('99', '11', 'Vui chơi Công viên Fantasy Park', 'Đoàn tự do trải nghiệm hàng trăm trò chơi hiện đại tại Công viên giải trí trong nhà lớn nhất Việt Nam.', '2026-08-26 14:00:00', 'Fantasy Park, Bà Nà Hills', 'Nhiệt độ 20°C mát lạnh', 'Phấn khích', 'activity', NULL, NULL, NULL, '1', 'published', '2026-08-08 13:51:26', '2026-08-08 13:51:26'),
('100', '11', 'Tắm biển Mỹ Khê Đà Nẵng', 'Đoàn đi cáp treo xuống núi, xe đưa về bãi biển Mỹ Khê đắm mình trong làn nước xanh biếc.', '2026-08-26 16:30:00', 'Bãi biển Mỹ Khê, Đà Nẵng', 'Chiều gió biển dịu', 'Sảng khoái', 'activity', NULL, NULL, NULL, '1', 'published', '2026-08-08 13:51:26', '2026-08-08 13:51:26'),
('101', '11', 'Ăn tối hải sản tươi sống Mỹ Hạnh', 'Dùng bữa tối hải sản nướng ngon ngọt tại nhà hàng Mỹ Hạnh ngay sát bờ biển Mỹ Khê.', '2026-08-26 18:30:00', 'Nhà hàng Hải sản Mỹ Hạnh, Đà Nẵng', 'Đêm mát', 'Hài lòng', 'dinner', 'Tôm hùm nướng phô mai, mực lá nướng sa tế, chip chip hấp sả, lẩu cá bớp', 'Nhà hàng Mỹ Hạnh', NULL, '1', 'published', '2026-08-08 13:51:26', '2026-08-08 13:51:26'),
('102', '11', 'Xem Cầu Rồng phun lửa & Dạo Chợ đêm Sơn Trà', 'Đoàn chiêm ngưỡng màn trình diễn Cầu Rồng phun lửa phun nước rực rỡ và dạo Chợ đêm Sơn Trà.', '2026-08-26 20:30:00', 'Cầu Rồng & Chợ đêm Sơn Trà', 'Đêm nhộn nhịp', 'Vui vẻ', 'shopping', NULL, 'Khu Cầu Rồng Đà Nẵng', NULL, '1', 'published', '2026-08-08 13:51:26', '2026-08-08 13:51:26'),
('103', '11', 'Ăn sáng & Trả phòng khách sạn', 'Ăn sáng buffet và làm thủ tục trả phòng khách sạn Mường Thanh Luxury.', '2026-08-27 07:00:00', 'Khách sạn Mường Thanh Luxury', 'Nắng nhẹ', 'Thoải mái', 'breakfast', 'Buffet sáng khách sạn', 'Khách sạn Mường Thanh Luxury', NULL, '1', 'published', '2026-08-08 13:51:26', '2026-08-08 13:51:26'),
('104', '11', 'Tham quan Ngũ Hành Sơn & Làng đá Non Nước', 'Đoàn tham quan 5 ngọn núi Ngũ Hành Sơn, Động Huyền Không và ngắm sản phẩm đá mỹ nghệ Non Nước.', '2026-08-27 08:30:00', 'Danh thắng Ngũ Hành Sơn, Đà Nẵng', 'Nắng đẹp', 'Ấn tượng', 'sightseeing', NULL, NULL, NULL, '1', 'published', '2026-08-08 13:51:26', '2026-08-08 13:51:26'),
('105', '11', 'Mua sắm đặc sản Đà Nẵng tại Chợ Hàn', 'Đoàn ghé Chợ Hàn mua chả bò Đà Nẵng, mực rim me, cá khô và bánh khô mè Bà Liễu làm quà.', '2026-08-27 10:30:00', 'Chợ Hàn, TP. Đà Nẵng', 'Trời mát', 'Vui vẻ', 'shopping', NULL, 'Chợ Hàn Đà Nẵng', NULL, '1', 'published', '2026-08-08 13:51:26', '2026-08-08 13:51:26'),
('106', '11', 'Ăn trưa cơm niêu miền Trung', 'Dùng bữa trưa cơm niêu đậm chất ẩm thực miền Trung tại nhà hàng Hồng Phúc.', '2026-08-27 12:00:00', 'Nhà hàng Cơm Niêu Hồng Phúc', 'Nắng dịu', 'Ngon miệng', 'lunch', 'Cơm niêu đập, cá bống kho tộ, canh chua cá bớp, thịt luộc cà pháo', 'Nhà hàng Cơm Niêu Hồng Phúc', NULL, '1', 'published', '2026-08-08 13:51:26', '2026-08-08 13:51:26'),
('107', '11', 'Tiễn đoàn tại Sân bay Đà Nẵng', 'Xe đưa quý khách ra Sân bay Đà Nẵng, HDV hỗ trợ làm thủ tục check-in vé máy bay và tạm biệt đoàn.', '2026-08-27 15:30:00', 'Sân bay Quốc tế Đà Nẵng', 'Chiều dịu mát', 'Hoàn thành chuyến đi', 'return', NULL, NULL, NULL, '1', 'published', '2026-08-08 13:51:26', '2026-08-08 13:51:26'),
('108', '12', 'Đón đoàn tại Sân bay Liên Khương', 'HDV đón quý khách tại Sân bay Liên Khương / Bến xe Đà Lạt trong tiết trời se lạnh mộng mơ.', '2026-08-28 07:00:00', 'Sân bay Liên Khương, Lâm Đồng', 'Trời se lạnh 18°C', 'Hào hứng', 'transfer', NULL, NULL, NULL, '1', 'published', '2026-08-08 13:51:26', '2026-08-08 13:51:26'),
('109', '12', 'Ăn sáng Bánh mì xíu mại Hoàng Diệu', 'Thưởng thức món Bánh mì xíu mại chén Hoàng Diệu nóng hổi chấm nước dùng thơm ngon.', '2026-08-28 08:00:00', 'Quán Bánh mì xíu mại Hoàng Diệu, Đà Lạt', 'Sương mù nhẹ', 'Ngon miệng', 'breakfast', 'Bánh mì xíu mại chén, chả giòn, sữa đậu nành nóng', 'Quán Hoàng Diệu', NULL, '1', 'published', '2026-08-08 13:51:26', '2026-08-08 13:51:26'),
('110', '12', 'Check-in Quảng trường Lâm Viên & Ga Đà Lạt', 'Đoàn chụp ảnh lưu niệm tại Nụ hoa Atiso khổng lồ Quảng trường Lâm Viên và Ga xe lửa Đà Lạt cổ kính.', '2026-08-28 09:30:00', 'Quảng trường Lâm Viên & Ga Đà Lạt', 'Nắng hanh se lạnh', 'Vui vẻ', 'sightseeing', NULL, NULL, NULL, '1', 'published', '2026-08-08 13:51:26', '2026-08-08 13:51:26'),
('111', '12', 'Ăn trưa Lẩu Gà Lá É Tao Ngộ', 'Dùng bữa trưa Lẩu gà lá é trứ danh Đà Lạt với nước dùng ngọt thanh vị ớt hiểm thơm nức.', '2026-08-28 12:00:00', 'Nhà hàng Lẩu gà lá é Tao Ngộ, Đà Lạt', 'Trời dịu mát', 'Ngon miệng', 'lunch', 'Lẩu gà lá é Tao Ngộ, ngọc kê nướng, nấm nướng bơ', 'Nhà hàng Tao Ngộ', NULL, '1', 'published', '2026-08-08 13:51:26', '2026-08-08 13:51:26'),
('112', '12', 'Check-in Khách sạn Hôtel Colline 4★', 'Đoàn về nhận phòng Khách sạn Colline ngay trung tâm Đà Lạt, nghỉ ngơi thư giãn.', '2026-08-28 13:30:00', 'Khách sạn Hôtel Colline Đà Lạt', 'Chiều dịu', 'Thoải mái', 'accommodation', NULL, 'Khách sạn Hôtel Colline 4★', NULL, '1', 'published', '2026-08-08 13:51:26', '2026-08-08 13:51:26'),
('113', '12', 'Tham quan Thiền Viện Trúc Lâm & Hồ Tuyền Lâm', 'Đoàn đi cáp treo Đồi Rôbin ngắm rừng thông, viếng Thiền Viện Trúc Lâm tản bộ bờ Hồ Tuyền Lâm thơ mộng.', '2026-08-28 15:00:00', 'Thiền Viện Trúc Lâm, Hồ Tuyền Lâm', 'Gió thông vi vu se lạnh', 'Yên bình', 'sightseeing', NULL, NULL, NULL, '1', 'published', '2026-08-08 13:51:26', '2026-08-08 13:51:26'),
('114', '12', 'Ăn tối tiệc Lẩu Bò Ba Toa', 'Bữa tối Lẩu bò Ba Toa quán gỗ truyền thống với những miếng thịt bò dày ngậy thơm phức.', '2026-08-28 18:30:00', 'Nhà hàng Lẩu bò Ba Toa Quán Gỗ', 'Đêm rét dịu 16°C', 'Ấm cúng', 'dinner', 'Lẩu bò Ba Toa đặc biệt, tủy bò nướng, mì trứng', 'Quán Lẩu bò Ba Toa', NULL, '1', 'published', '2026-08-08 13:51:26', '2026-08-08 13:51:26'),
('115', '12', 'Dạo Chợ đêm Đà Lạt & Thưởng thức Sữa đậu nành', 'Đoàn tản bộ Chợ đêm Đà Lạt, ăn bánh tráng nướng \"Pizza Đà Lạt\", xiên nướng và uống sữa đậu nành nóng.', '2026-08-28 20:00:00', 'Chợ đêm Đà Lạt (Chợ Âm Phủ)', 'Sương đêm lãng mạn', 'Vui vẻ', 'shopping', NULL, 'Khu Chợ đêm Đà Lạt', NULL, '1', 'published', '2026-08-08 13:51:26', '2026-08-08 13:51:26'),
('116', '12', 'Ăn sáng buffet tại Hôtel Colline', 'Dùng bữa sáng buffet phong phú tại nhà hàng khách sạn Colline.', '2026-08-29 07:00:00', 'Hôtel Colline Đà Lạt', 'Nắng sớm dịu trong', 'Sảng khoái', 'breakfast', 'Buffet sáng: bún bò Đà Lạt, bánh ngọt, dâu tây, nước ép', 'Hôtel Colline Đà Lạt', NULL, '1', 'published', '2026-08-08 13:51:26', '2026-08-08 13:51:26'),
('117', '12', 'Săn mây & Check-in Đồi chè Cầu Đất', 'Đoàn tới Đồi chè Cầu Đất săn thảm mây bồng bềnh và check-in bên tháp quạt gió khổng lồ.', '2026-08-29 08:00:00', 'Đồi chè Cầu Đất, Đà Lạt', 'Biển mây vần vũ', 'Tuyệt sắc', 'sightseeing', NULL, NULL, NULL, '1', 'published', '2026-08-08 13:51:26', '2026-08-08 13:51:26'),
('118', '12', 'Tham quan Chùa Linh Phước (Chùa Ve Chai)', 'Đoàn chiêm ngưỡng ngôi chùa nắm giữ nhiều kỷ lục Việt Nam với kiến trúc khảm mảnh sành sứ độc đáo.', '2026-08-29 10:30:00', 'Chùa Linh Phước, Trại Mát, Đà Lạt', 'Nắng nhẹ', 'Ấn tượng', 'sightseeing', NULL, NULL, NULL, '1', 'published', '2026-08-08 13:51:26', '2026-08-08 13:51:26'),
('119', '12', 'Ăn trưa Buffet Rau Leguda Đồi Rôbin', 'Thưởng thức bữa trưa buffet rau không giới hạn Leguda ngắm toàn cảnh thành phố từ trên đỉnh đồi.', '2026-08-29 12:00:00', 'Nhà hàng Leguda Đồi Rôbin', 'Trời trong mát', 'Ngon miệng', 'lunch', 'Buffet 20 loại rau củ tươi Đà Lạt, lẩu hai ngăn hải sản & bổ dưỡng', 'Nhà hàng Leguda', NULL, '1', 'published', '2026-08-08 13:51:26', '2026-08-08 13:51:26'),
('120', '12', 'Chinh phục Đỉnh núi Langbiang bằng Xe Jeep', 'Đoàn trải nghiệm xe Jeep địa hình lao qua đường rừng thông chinh phục đỉnh Langbiang ngắm Suối Vàng Suối Bạc.', '2026-08-29 14:00:00', 'Đỉnh núi Langbiang, Lạc Dương', 'Gió ngàn vi vu', 'Hào hứng', 'activity', NULL, NULL, NULL, '1', 'published', '2026-08-08 13:51:26', '2026-08-08 13:51:26'),
('121', '12', 'Giao lưu Cồng Chiêng Tây Nguyên', 'Đoàn tham gia đêm lửa trại Giao lưu văn hóa Cồng Chiêng với đồng bào dân tộc K\'Ho (ăn thịt nướng, uống rượu cần).', '2026-08-29 18:30:00', 'Buôn làng K\'Ho dưới chân núi Langbiang', 'Đêm bập bùng lửa trại', 'Say mê', 'activity', 'Thịt heo rừng nướng xiên, gà nướng ống tre, rượu cần Tây Nguyên', 'Sân lửa trại Langbiang', NULL, '1', 'published', '2026-08-08 13:51:26', '2026-08-08 13:51:26'),
('122', '12', 'Ăn sáng & Trả phòng khách sạn', 'Ăn sáng buffet và làm thủ tục checkout khách sạn Hôtel Colline.', '2026-08-30 07:00:00', 'Khách sạn Hôtel Colline', 'Nắng sớm hanh se', 'Thoải mái', 'breakfast', 'Buffet sáng khách sạn', 'Khách sạn Hôtel Colline', NULL, '1', 'published', '2026-08-08 13:51:26', '2026-08-08 13:51:26'),
('123', '12', 'Tham quan Thung lũng Tình Yêu & Vườn hoa', 'Đoàn tham quan Khu du lịch Thung lũng Tình Yêu ngắm các loài hoa rực rỡ và đồi mộng mơ.', '2026-08-30 08:30:00', 'Thung lũng Tình Yêu, Đà Lạt', 'Nắng trong rực rỡ', 'Thích thú', 'sightseeing', NULL, NULL, NULL, '1', 'published', '2026-08-08 13:51:26', '2026-08-08 13:51:26'),
('124', '12', 'Mua sắm đặc sản L\'angfarm Đà Lạt', 'Đoàn dừng chân chuỗi L\'angfarm mua mứt dâu tây, hồng treo gió Nhật Bản, trà atiso làm quà.', '2026-08-30 10:30:00', 'Cửa hàng L\'angfarm Đà Lạt', 'Trời mát', 'Vui vẻ', 'shopping', NULL, 'Cửa hàng L\'angfarm', NULL, '1', 'published', '2026-08-08 13:51:26', '2026-08-08 13:51:26'),
('125', '12', 'Ăn trưa cơm niêu Tây Nguyên', 'Thưởng thức bữa trưa cơm niêu mộc mạc thơm dẻo trước khi chia tay Đà Lạt.', '2026-08-30 12:00:00', 'Nhà hàng Cơm Niêu Fresh Đà Lạt', 'Nắng dịu', 'Ngon miệng', 'lunch', 'Cơm niêu giòn rum, cá kho tộ, canh rau rừng, heo quay sả ớt', 'Nhà hàng Cơm Niêu Fresh', NULL, '1', 'published', '2026-08-08 13:51:26', '2026-08-08 13:51:26'),
('126', '12', 'Tiễn đoàn ra Sân bay Liên Khương', 'Xe đưa đoàn ra Sân bay Liên Khương / Bến xe Đà Lạt, HDV chào tạm biệt và hẹn gặp lại.', '2026-08-30 14:00:00', 'Sân bay Liên Khương, Lâm Đồng', 'Chiều mát', 'Hoàn thành tốt đẹp', 'return', NULL, NULL, NULL, '1', 'published', '2026-08-08 13:51:26', '2026-08-08 13:51:26'),
('127', '13', 'Tập trung đón đoàn đi Ninh Bình', 'HDV đón quý khách tại Nhà hát Lớn Hà Nội, sắp xếp xe Limousine cao cấp chuẩn bị xuất phát.', '2026-09-01 07:00:00', 'Nhà hát Lớn Hà Nội', 'Trời thu trong xanh', 'Hào hứng', 'transfer', NULL, NULL, NULL, '1', 'published', '2026-08-08 13:51:26', '2026-08-08 13:51:26'),
('128', '13', 'Khởi hành đi Ninh Bình qua cao tốc', 'Xe Limousine lướt nhẹ trên cao tốc Hà Nội - Ninh Bình. HDV giới thiệu vùng đất Cố đô Hoa Lư.', '2026-09-01 07:30:00', 'Cao tốc Hà Nội - Ninh Bình', 'Nắng thu đẹp', 'Thoải mái', 'transfer', NULL, NULL, NULL, '1', 'published', '2026-08-08 13:51:26', '2026-08-08 13:51:26'),
('129', '13', 'Ăn sáng Bánh cuốn chả Phủ Lý', 'Dừng chân tại Hà Nam thưởng thức đặc sản Bánh cuốn chả nướng than hoa Phủ Lý nóng giòn.', '2026-09-01 08:30:00', 'Quán Bánh cuốn Phủ Lý, Hà Nam', 'Nắng mát', 'Ngon miệng', 'breakfast', 'Bánh cuốn chả nướng than Phủ Lý, rau sống, chả quế', 'Quán Phủ Lý', NULL, '1', 'published', '2026-08-08 13:51:26', '2026-08-08 13:51:26'),
('130', '13', 'Viếng Quần thể Chùa Bái Đính', 'Đoàn đi xe điện tham quan Chùa Bái Đính, chiêm bái Tượng Phật bằng đồng 100 tấn và Hành lang 500 vị La Hán.', '2026-09-01 10:00:00', 'Chùa Bái Đính, Gia Viễn, Ninh Bình', 'Nắng dịu', 'Thanh tịnh', 'sightseeing', NULL, NULL, NULL, '1', 'published', '2026-08-08 13:51:26', '2026-08-08 13:51:26'),
('131', '13', 'Ăn trưa Dê núi Cơm cháy Ninh Bình', 'Thưởng thức bữa trưa đặc sản Dê núi tái chanh, Dê xào lăn và Cơm cháy sốt dê nóng hổi.', '2026-09-01 12:30:00', 'Nhà hàng Đức Mộc, Ninh Bình', 'Trời mát', 'Ngon miệng', 'lunch', 'Dê núi tái chanh, dê nướng tảng, cơm cháy sốt dê, canh đắng', 'Nhà hàng Đức Mộc', NULL, '1', 'published', '2026-08-08 13:51:26', '2026-08-08 13:51:26'),
('132', '13', 'Check-in Emeralda Resort Ninh Bình 5★', 'Đoàn về Emeralda Resort nhận phòng phong cách làng quê Bắc Bộ thanh bình, nghỉ ngơi.', '2026-09-01 14:00:00', 'Emeralda Resort Ninh Bình', 'Mát mẻ', 'Nghỉ dưỡng tuyệt vời', 'accommodation', NULL, 'Emeralda Resort Ninh Bình 5★', NULL, '1', 'published', '2026-08-08 13:51:26', '2026-08-08 13:51:26'),
('133', '13', 'Chinh phục đỉnh Hang Múa ngắm Tam Cốc', 'Đoàn leo 500 bậc đá đỉnh Múa Sơn check-in ngọn tháp rồng ngắm sông Ngoạ Đồng và thung lũng lúa Tam Cốc.', '2026-09-01 15:30:00', 'Khu du lịch Hang Múa, Ninh Bình', 'Chiều thu trong', 'Choáng ngợp', 'sightseeing', NULL, NULL, NULL, '1', 'published', '2026-08-08 13:51:26', '2026-08-08 13:51:26'),
('134', '13', 'Ăn tối lẩu Dê núi Phố Cổ', 'Bữa tối Lẩu dê núi ninh sâm thơm phức ngọt đậm đà vị thuốc bắc.', '2026-09-01 18:30:00', 'Nhà hàng Phố Cổ Hoa Lư', 'Đêm mát thu', 'Ấm cúng', 'dinner', 'Lẩu dê núi ninh sâm, dê hấp sả, ngọn su su xào tỏi', 'Nhà hàng Phố Cổ', NULL, '1', 'published', '2026-08-08 13:51:26', '2026-08-08 13:51:26'),
('135', '13', 'Dạo Phố cổ Hoa Lư ngắm Hồ Kỳ Lân đêm', 'Đoàn dạo Phố cổ Hoa Lư chiêm ngưỡng hàng ngàn ngọn đèn lồng lung linh phản chiếu xuống mặt hồ Kỳ Lân.', '2026-09-01 20:00:00', 'Phố cổ Hoa Lư & Hồ Kỳ Lân, Ninh Bình', 'Đêm thu mát lạnh', 'Yên bình mộng mơ', 'shopping', NULL, 'Khu Phố cổ Hoa Lư', NULL, '1', 'published', '2026-08-08 13:51:26', '2026-08-08 13:51:26'),
('136', '13', 'Ăn sáng buffet tại Emeralda Resort', 'Dùng điểm tâm sáng buffet phong phú giữa không gian xanh ngát resort.', '2026-09-02 07:00:00', 'Emeralda Resort Ninh Bình', 'Nắng thu sớm trong lành', 'Sảng khoái', 'breakfast', 'Buffet 5 sao: bún mộc, phở bò, bánh ngọt, nước ép', 'Emeralda Resort Ninh Bình', NULL, '1', 'published', '2026-08-08 13:51:26', '2026-08-08 13:51:26'),
('137', '13', 'Đi thuyền nan khám phá Di sản Tràng An', 'Đoàn ngồi thuyền nan lướt nhẹ qua Hang Lắm, Hang Vang, Đền Suối Tiên chiêm ngưỡng cảnh đẹp \"Hạ Long trên đất liền\".', '2026-09-02 08:30:00', 'Quần thể Danh thắng Tràng An', 'Nắng mát dịu', 'Tuyệt diệu', 'sightseeing', NULL, NULL, NULL, '1', 'published', '2026-08-08 13:51:26', '2026-08-08 13:51:26'),
('138', '13', 'Ăn trưa Cơm cháy ruốc Ninh Bình', 'Dùng bữa trưa tổng kết chuyến đi với mâm cơm truyền thống đất Cố Đồ.', '2026-09-02 11:30:00', 'Nhà hàng Tràng An 1', 'Trời mát', 'Ngon miệng', 'lunch', 'Cơm cháy chà bông, dê xào sả ớt, cá rô Tổng Trường kho tộ', 'Nhà hàng Tràng An 1', NULL, '1', 'published', '2026-08-08 13:51:26', '2026-08-08 13:51:26'),
('139', '13', 'Thăm Cố đô Hoa Lư Đền Vua Đinh Vua Lê', 'Đoàn viếng thăm Đền thờ Vua Đinh Tiên Hoàng và Vua Lê Đại Hành lắng nghe thuyết minh lịch sử triều đại Hoa Lư.', '2026-09-02 13:30:00', 'Cố đô Hoa Lư, Trường Yên, Ninh Bình', 'Nắng chiều thu', 'Trang nghiêm', 'sightseeing', NULL, NULL, NULL, '1', 'published', '2026-08-08 13:51:26', '2026-08-08 13:51:26'),
('140', '13', 'Mua sắm đặc sản Ninh Bình', 'Đoàn dừng mua Cơm cháy ruốc chà bông, Rượu Kim Sơn, Mắm tép Gia Viễn làm quà biếu.', '2026-09-02 15:30:00', 'Cửa hàng Đặc sản Ninh Bình', 'Trời dịu mát', 'Vui vẻ', 'shopping', NULL, 'Cửa hàng Đặc sản Ninh Bình', NULL, '1', 'published', '2026-08-08 13:51:26', '2026-08-08 13:51:26'),
('141', '13', 'Khởi hành về lại Hà Nội', 'Đoàn lên xe Limousine khởi hành trở về Hà Nội.', '2026-09-02 16:30:00', 'Xe Limousine', 'Chiều thu mát', 'Thoải mái', 'transfer', NULL, NULL, NULL, '1', 'published', '2026-08-08 13:51:26', '2026-08-08 13:51:26'),
('142', '13', 'Về tới Hà Nội & Kết thúc tour', 'Xe về tới Nhà hát Lớn Hà Nội, HDV giúp khách lấy hành lý và chia tay đoàn.', '2026-09-02 18:30:00', 'Nhà hát Lớn Hà Nội', 'Tối mát', 'Hoàn thành chuyến đi', 'return', NULL, NULL, NULL, '1', 'published', '2026-08-08 13:51:26', '2026-08-08 13:51:26'),
('143', '14', 'Đón đoàn tại Sân bay Phú Bài & Ga Huế', 'HDV và tài xế đón 20 khách tại sân bay & ga, kiểm tra danh sách và hỗ trợ sắp xếp hành lý.', '2026-08-01 07:00:00', 'Sân bay Phú Bài / Ga Huế', 'Nắng nhẹ mát mẻ', 'Phấn khởi', 'pickup', NULL, NULL, NULL, '2', 'published', '2026-08-08 07:06:00', '2026-08-08 07:06:00'),
('144', '14', 'Ăn sáng Bún bò Huế Cung Đình', 'Thưởng thức bún bò Huế thơm nức mùi sả mắm ruốc tại quán lâu đời.', '2026-08-01 08:30:00', 'Quán Bún Bò Huế Mụ Rớt', 'Nắng ấm', 'Ngon miệng', 'breakfast', 'Bún bò giò gân, chả nầm, trà đá', 'Quán Mụ Rớt', NULL, '2', 'published', '2026-08-08 07:06:00', '2026-08-08 07:06:00'),
('145', '14', 'Tham quan Đại Nội Huế - Hoàng Thành Cố Đô', 'Đoàn qua Cửa Ngọ Môn, Điện Thái Hòa, Thế Miếu, Tử Cấm Thành. Nghe thuyết minh lịch sử triều Nguyễn.', '2026-08-01 09:30:00', 'Đại Nội Huế, TP. Huế', 'Nắng trong lành', 'Ấn tượng hào hùng', 'sightseeing', NULL, NULL, NULL, '2', 'published', '2026-08-08 07:06:00', '2026-08-08 07:06:00'),
('146', '14', 'Ăn trưa ẩm thực Cung Đình Huế', 'Thưởng thức các món ăn trang trí kiểu Cung đình tinh tế tại nhà hàng ven Sông Hương.', '2026-08-01 12:00:00', 'Nhà hàng Hương Giang, TP. Huế', 'Nắng dịu', 'Hài lòng', 'lunch', 'Tôm nướng Cung đình, chả phụng, cơm niêu, chè hạt sen', 'Nhà hàng Hương Giang', NULL, '2', 'published', '2026-08-08 07:06:00', '2026-08-08 07:06:00'),
('147', '14', 'Check-in Khách sạn Silk Path Grand Huế 5★', 'Đoàn về nhận phòng nghỉ ngơi tự do tại khách sạn sang trọng đẳng cấp 5 sao.', '2026-08-01 14:00:00', 'Khách sạn Silk Path Grand Huế 5★', 'Trời mát', 'Thoải mái', 'accommodation', NULL, 'Khách sạn Silk Path Grand Huế 5★', NULL, '2', 'published', '2026-08-08 07:06:00', '2026-08-08 07:06:00'),
('148', '14', 'Tham quan Chùa Thiên Mụ & Tháp Phước Duyên', 'Thăm ngôi chùa cổ kính nhất xứ Huế bên bờ Sông Hương thơ mộng.', '2026-08-01 15:30:00', 'Chùa Thiên Mụ, TP. Huế', 'Gió sông mát rượi', 'Tĩnh tâm', 'sightseeing', NULL, NULL, NULL, '2', 'published', '2026-08-08 07:06:00', '2026-08-08 07:06:00'),
('149', '14', 'Ăn tối đặc sản Bánh Huế truyền thống', 'Thưởng thức mâm bánh tổng hợp Bánh bèo, bánh nậm, bánh lọc, ram ít chuẩn vị Huế.', '2026-08-01 18:30:00', 'Quán Bánh Quán Hạnh, TP. Huế', 'Đêm mát mẻ', 'Thích thú', 'dinner', 'Mâm bánh bèo nậm lọc, nem lụi nướng, bún thịt nướng', 'Quán Hạnh', NULL, '2', 'published', '2026-08-08 07:06:00', '2026-08-08 07:06:00'),
('150', '14', 'Đi Thuyền Rồng Sông Hương & Thưởng thức Nhã nhạc Cung đình', 'Đoàn lên thuyền rồng thả hoa đăng lung linh trên Sông Hương và nghe ca Huế.', '2026-08-01 20:00:00', 'Bến thuyền Toà Khâm - Sông Hương', 'Gió sông dịu rượi', 'Xúc động & Thư thái', 'activity', NULL, 'Thuyền Rồng Sông Hương', NULL, '2', 'published', '2026-08-08 07:06:00', '2026-08-08 07:06:00'),
('151', '14', 'Ăn sáng buffet tại Silk Path Resort', 'Đoàn dùng điểm tâm sáng phong phú tại nhà hàng khách sạn.', '2026-08-02 07:00:00', 'Khách sạn Silk Path Grand Huế', 'Nắng sớm trong lành', 'Sảng khoái', 'breakfast', 'Buffet sáng Âu - Á - Huế', 'Silk Path Resort', NULL, '2', 'published', '2026-08-08 07:06:00', '2026-08-08 07:06:00'),
('152', '14', 'Tham quan Lăng Khải Định - Đỉnh cao mỹ thuật khảm sành sứ', 'Đoàn khám phá công trình lăng tẩm kết hợp nét kiến trúc Á - Âu lộng lẫy và tinh xảo.', '2026-08-02 08:30:00', 'Lăng Khải Định, Ứng Lăng', 'Nắng đẹp', 'Trầm ồ ngạc nhiên', 'sightseeing', NULL, NULL, NULL, '2', 'published', '2026-08-08 07:06:00', '2026-08-08 07:06:00'),
('153', '14', 'Tham quan Lăng Tự Đức - Vẻ đẹp thơ mộng trầm mặc', 'Thăm Lăng Tự Đức giữa đồi thông xanh mát và hồ Xung Khiêm tĩnh lặng.', '2026-08-02 10:30:00', 'Lăng Tự Đức, TP. Huế', 'Bóng râm mát mẻ', 'Thư thái', 'sightseeing', NULL, NULL, NULL, '2', 'published', '2026-08-08 07:06:00', '2026-08-08 07:06:00'),
('154', '14', 'Ăn trưa cơm niêu Huế đậm đà', 'Dùng bữa trưa gia đình với các món ăn dân dã xứ Huế.', '2026-08-02 12:00:00', 'Nhà hàng Khải Hoàn, TP. Huế', 'Mát mẻ', 'Ngon miệng', 'lunch', 'Cá bống kho tiêu, canh chua cá lóc, thịt luộc tôm chua, cơm niêu', 'Nhà hàng Khải Hoàn', NULL, '2', 'published', '2026-08-08 07:06:00', '2026-08-08 07:06:00'),
('155', '14', 'Check-in Làng hương Thủy Xuân rực rỡ sắc màu', 'Đoàn trải nghiệm làm hương trầm thủ công và mặc cổ phục chụp ảnh rực rỡ.', '2026-08-02 14:30:00', 'Làng hương Thủy Xuân, TP. Huế', 'Nắng rực rỡ', 'Hào hứng chụp ảnh', 'activity', NULL, 'Làng hương Thủy Xuân', NULL, '2', 'published', '2026-08-08 07:06:00', '2026-08-08 07:06:00'),
('156', '14', 'Ngắm hoàng hôn trên Đồi Vọng Cảnh', 'Thư giãn ngắm dòng Sông Hương uốn lượn bên rặng thông từ đỉnh đồi.', '2026-08-02 16:30:00', 'Đồi Vọng Cảnh, TP. Huế', 'Hoàng hôn lãng mạn', 'Tuyệt vời', 'sightseeing', NULL, NULL, NULL, '2', 'published', '2026-08-08 07:06:00', '2026-08-08 07:06:00'),
('157', '14', 'Ăn tối tiệc hải sản Đầm Phá Tam Giang', 'Thưởng thức tôm đất, tôm sú, cá mú tươi ngon đánh bắt từ đầm phá.', '2026-08-02 18:30:00', 'Nhà hàng Đầm Phá Tam Giang', 'Gió lagoon mát lạnh', 'Phấn khởi', 'dinner', 'Tôm đất hấp bia, cá mú chưng tương, lẩu hải sản Tam Giang', 'Nhà hàng Tam Giang', NULL, '2', 'published', '2026-08-08 07:06:00', '2026-08-08 07:06:00'),
('158', '14', 'Dạo Phố đi bộ Nguyễn Đình Chiểu & Cầu Tràng Tiền', 'Đoàn dạo phố đêm ngắm Cầu Tràng Tiền đổi màu lung linh và thưởng thức chè hẻm.', '2026-08-02 20:30:00', 'Cầu Tràng Tiền & Phố đi bộ', 'Đêm mát', 'Vui vẻ', 'shopping', NULL, NULL, NULL, '2', 'published', '2026-08-08 07:06:00', '2026-08-08 07:06:00'),
('159', '14', 'Ăn sáng & Trả phòng khách sạn', 'Đoàn điểm tâm sáng và làm thủ tục check-out khách sạn Silk Path.', '2026-08-03 07:00:00', 'Khách sạn Silk Path Grand Huế', 'Trời dịu mát', 'Thoải mái', 'breakfast', 'Buffet sáng khách sạn', 'Silk Path Resort', NULL, '2', 'published', '2026-08-08 07:06:00', '2026-08-08 07:06:00'),
('160', '14', 'Mua sắm đặc sản tại Chợ Đông Ba', 'Đoàn tự do mua sắm Mè xửng, Tôm chua, Trà Cung Đình và nón lá Huế làm quà.', '2026-08-03 08:30:00', 'Chợ Đông Ba, TP. Huế', 'Nắng dịu', 'Hào hứng', 'shopping', NULL, 'Chợ Đông Ba', NULL, '2', 'published', '2026-08-08 07:06:00', '2026-08-08 07:06:00'),
('161', '14', 'Ăn trưa chè Huế 20 món & Tiệc chia tay', 'Thưởng thức bữa trưa ấm cúng và tráng miệng với mâm chè Huế 20 vị phong phú.', '2026-08-03 11:30:00', 'Nhà hàng Y Thảo Garden, TP. Huế', 'Mát mẻ', 'Ấm cúng', 'lunch', 'Mâm 20 vị chè Huế (chè bột lọc bọc heo quay, chè hạt sen, chè nhãn)', 'Nhà hàng Y Thảo', NULL, '2', 'published', '2026-08-08 07:06:00', '2026-08-08 07:06:00'),
('162', '14', 'Xe đưa đoàn ra Sân bay Phú Bài & Kết thúc tour', 'Xe đưa đoàn ra sân bay Phú Bài. HDV phát phiếu cảm nhận, cảm ơn và chia tay đoàn.', '2026-08-03 13:30:00', 'Sân bay Phú Bài / Ga Huế', 'Trời đẹp', 'Lưu luyến chia tay', 'return', NULL, NULL, NULL, '2', 'published', '2026-08-08 07:06:00', '2026-08-08 07:06:00');

-- --------------------------------------------------------
-- Table structure for table `tour_diaries`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `tour_diaries`;
CREATE TABLE `tour_diaries` (
  `id` int NOT NULL AUTO_INCREMENT,
  `departure_id` int NOT NULL,
  `tour_log_id` int DEFAULT NULL,
  `created_by_hdv_id` int DEFAULT NULL,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `diary_date` date NOT NULL,
  `weather` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mood` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `photos` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_departure_id` (`departure_id`),
  KEY `idx_tour_log_id` (`tour_log_id`),
  KEY `idx_created_by_hdv_id` (`created_by_hdv_id`),
  CONSTRAINT `fk_tour_diaries_tour_log` FOREIGN KEY (`tour_log_id`) REFERENCES `tour_logs` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `tour_diaries_ibfk_1` FOREIGN KEY (`departure_id`) REFERENCES `departures` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table `tour_diaries`
INSERT INTO `tour_diaries` (`id`, `departure_id`, `tour_log_id`, `created_by_hdv_id`, `title`, `content`, `diary_date`, `weather`, `mood`, `photos`, `created_at`, `updated_at`) VALUES
('1', '14', '145', '2', 'Ấn tượng ngày đầu tiên khám phá Hoàng Thành Cố Đô Huế', 'Hôm nay đoàn khởi hành đúng giờ. Thời tiết Huế dịu mát, nắng nhẹ rất thích hợp cho du khách đi bộ tham quan Đại Nội. Khách trong đoàn rất hào hứng khi nghe giới thiệu về lịch sử triều Nguyễn. Mọi người chụp rất nhiều ảnh kỷ niệm tại Ngọ Môn và Điện Thái Hòa. Ăn trưa ẩm thực Cung đình ai cũng khen ngon!', '2026-08-01', 'Nắng nhẹ, gió mát', 'Hào hứng & Vui vẻ', 'storage/uploads/tours/hue.jpg', '2026-08-07 10:06:00', '2026-08-07 10:16:09'),
('2', '14', '150', '2', 'Đêm Sông Hương huyền ảo & Lắng đọng cùng Nhã nhạc Cung đình', 'Buổi tối cả đoàn lên thuyền rồng thả hoa đăng trên Sông Hương. Âm hưởng Nhã nhạc cung đình Huế vang lên giữa không gian sông nước lung linh khiến du khách vô cùng xúc động. Tất cả 20 thành viên trong đoàn đều tham gia thả hoa đăng cầu may mắn và chụp ảnh lưu niệm cùng các nghệ sĩ.', '2026-08-01', 'Đêm mát mẻ, gió sông dịu rượi', 'Thư thái & Xúc động', 'storage/uploads/tours/hue.jpg', '2026-08-07 10:06:00', '2026-08-07 10:16:09'),
('3', '14', '155', '2', 'Sắc màu rực rỡ tại Làng hương Thủy Xuân & Đồi Vọng Cảnh', 'Mọi người vô cùng thích thú khi đến Làng hương Thủy Xuân. Các cô bác và bạn trẻ trong đoàn tha hồ mặc trang phục cổ phục nón lá chụp ảnh cùng các bó hương xòe hoa đủ màu sắc. Đến chiều lên Đồi Vọng Cảnh ngắm khúc quanh Sông Hương đẹp như bức tranh thủy mặc.', '2026-08-02', 'Nắng vàng trong trẻo', 'Phấn khởi & Hài lòng', 'storage/uploads/tours/hue.jpg', '2026-08-07 10:06:00', '2026-08-07 10:16:09'),
('4', '14', '162', '2', 'Tổng kết chuyến đi Huế 3N2Đ - Chia tay đoàn với muôn vàn kỷ niệm đẹp', 'Chuyến đi đã kết thúc an toàn và thành công rực rỡ. 100% du khách gửi lời cảm ơn đến HDV và tài xế. Mọi người mua rất nhiều đặc sản Mè xửng và Trà Cung Đình làm quà. Hẹn gặp lại quý khách trong các hành trình tiếp theo của công ty!', '2026-08-03', 'Trời mát mẻ', 'Trọn vẹn & Lưu luyến', 'storage/uploads/tours/hue.jpg', '2026-08-07 10:06:00', '2026-08-07 10:16:09');

-- --------------------------------------------------------
-- Table structure for table `booking_guests`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `booking_guests`;
CREATE TABLE `booking_guests` (
  `id` int NOT NULL AUTO_INCREMENT,
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
  `updated_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_booking_guests_booking_id` (`booking_id`),
  KEY `idx_booking_guests_check_in_status` (`check_in_status`),
  CONSTRAINT `fk_booking_guests_booking` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table `booking_guests`
INSERT INTO `booking_guests` (`id`, `booking_id`, `full_name`, `gender`, `dob`, `phone`, `email`, `identity_no`, `address`, `payment_status`, `check_in_status`, `checked_in_at`, `note`, `created_at`, `updated_at`) VALUES
('1', '10', 'Lê Hoàng Nam', 'male', '1988-03-15', '0912345678', 'nam.lehoang@gmail.com', '012345678901', 'Số 22 Kim Mã, Ba Đình, Hà Nội', 'paid', '1', '2026-08-01 07:15:00', 'Ăn chay', '2026-08-07 10:44:46', NULL),
('2', '10', 'Lê Thị Hạnh', 'female', '1990-07-20', '0912345679', 'hanh.le@gmail.com', '012345678902', 'Số 22 Kim Mã, Ba Đình, Hà Nội', 'paid', '1', '2026-08-01 07:15:00', '', '2026-08-07 10:44:46', NULL),
('3', '11', 'Trần Thu Hà', 'female', '1985-11-08', '0987654321', 'thuha.tran@yahoo.com', '079085123456', '15 Nguyễn Văn Trỗi, Q.Phú Nhuận, TP.HCM', 'paid', '1', '2026-08-01 07:20:00', 'Bị dị ứng hải sản vỏ cứng (tôm, cua)', '2026-08-07 10:44:46', '2026-08-07 10:50:18'),
('4', '11', 'Nguyễn Văn Phúc', 'male', '1983-05-12', '0987654322', 'phuc.nguyen@yahoo.com', '079083234567', '15 Nguyễn Văn Trỗi, Q.Phú Nhuận, TP.HCM', 'paid', '1', '2026-08-01 07:20:00', 'Huyết áp cao, hạn chế ăn mặn', '2026-08-07 10:44:46', '2026-08-07 10:50:18'),
('5', '11', 'Nguyễn Trần Bảo Ngọc', 'female', '2016-09-01', NULL, NULL, NULL, '15 Nguyễn Văn Trỗi, Q.Phú Nhuận, TP.HCM', 'paid', '1', '2026-08-01 07:20:00', '', '2026-08-07 10:44:46', '2026-08-07 10:50:18'),
('6', '11', 'Nguyễn Trần Minh Khôi', 'male', '2019-02-14', NULL, NULL, NULL, '15 Nguyễn Văn Trỗi, Q.Phú Nhuận, TP.HCM', 'paid', '1', '2026-08-01 07:20:00', '', '2026-08-07 10:44:46', '2026-08-07 10:50:18'),
('7', '12', 'Nguyễn Minh Tuấn', 'male', '1992-06-25', '0901234567', 'minhtuan.nguyen@gmail.com', '024092567890', 'Số 15 Nguyễn Huệ, Q.1, TP.HCM', 'paid', '1', '2026-08-01 06:55:00', 'Dị ứng hải sản', '2026-08-07 10:44:46', NULL),
('8', '12', 'Trần Thị Kim Ngân', 'female', '1994-12-03', '0901234568', 'ngan.tran@gmail.com', '024094345678', 'Số 15 Nguyễn Huệ, Q.1, TP.HCM', 'paid', '1', '2026-08-01 06:55:00', '', '2026-08-07 10:44:46', '2026-08-07 10:50:18'),
('9', '13', 'Phạm Thị Lan', 'female', '1992-04-18', '0918765432', 'lan.pham92@gmail.com', '001092456789', '42 Hai Bà Trưng, Hoàn Kiếm, Hà Nội', 'paid', '1', '2026-08-01 07:00:00', 'Cần chuẩn bị suất ăn chay thanh tịnh', '2026-08-07 10:44:46', '2026-08-07 10:50:18'),
('10', '14', 'Vũ Đức Anh', 'male', '1990-08-10', '0935678901', 'ducanh.vu@hotmail.com', '048090678901', 'Số 8 Trần Phú, Hải Châu, Đà Nẵng', 'paid', '1', '2026-08-01 07:05:00', '', '2026-08-07 10:44:46', '2026-08-07 10:50:18'),
('11', '14', 'Nguyễn Thị Mỹ Duyên', 'female', '1993-01-22', '0935678902', 'duyen.nguyen@hotmail.com', '048093345678', 'Số 8 Trần Phú, Hải Châu, Đà Nẵng', 'paid', '1', '2026-08-01 07:05:00', 'Dị ứng đậu phụng (lạc)', '2026-08-07 10:44:46', '2026-08-07 10:50:18'),
('12', '14', 'Vũ Nguyễn Bảo An', 'male', '2021-05-30', NULL, NULL, NULL, 'Số 8 Trần Phú, Hải Châu, Đà Nẵng', 'paid', '1', '2026-08-01 07:05:00', 'Trẻ nhỏ 5 tuổi, cần ghế nâng', '2026-08-07 10:44:46', NULL),
('13', '15', 'Hoàng Thị Mai', 'female', '1987-10-05', '0976543210', 'mai.hoang@outlook.com', '024087890123', '120 Lê Lợi, Q.3, TP.HCM', 'paid', '1', '2026-08-01 07:10:00', '', '2026-08-07 10:44:46', NULL),
('14', '15', 'Hoàng Văn Tùng', 'male', '1985-02-28', '0976543211', 'tung.hoang@outlook.com', '024085123456', '120 Lê Lợi, Q.3, TP.HCM', 'paid', '1', '2026-08-01 07:10:00', 'Tiểu đường, ăn kiêng đường', '2026-08-07 10:44:46', '2026-08-07 10:50:18'),
('15', '16', 'Đặng Quốc Bảo', 'male', '1995-12-01', '0862345678', 'quocbao.dang@gmail.com', '001095234567', '55 Nguyễn Trãi, Thanh Xuân, Hà Nội', 'paid', '1', '2026-08-01 07:12:00', 'Ăn chay trường', '2026-08-07 10:44:46', NULL),
('16', '17', 'Trịnh Thùy Linh', 'female', '1991-09-14', '0945678123', 'thuylinh.trinh@yahoo.com', '024091567890', '22 Pasteur, Q.1, TP.HCM', 'paid', '1', '2026-08-01 07:18:00', '', '2026-08-07 10:44:46', '2026-08-07 10:50:18'),
('17', '17', 'Lê Quang Vinh', 'male', '1989-04-20', '0945678124', 'quangvinh.le@yahoo.com', '024089678901', '22 Pasteur, Q.1, TP.HCM', 'paid', '1', '2026-08-01 07:18:00', 'Dị ứng lông động vật', '2026-08-07 10:44:46', '2026-08-07 10:50:18'),
('18', '18', 'Bùi Văn Hùng', 'male', '1986-07-07', '0923456789', 'vanhung.bui@gmail.com', '031086789012', '78 Bạch Đằng, Ngô Quyền, Hải Phòng', 'paid', '0', NULL, 'Hay bị say xe, cần ngồi đầu xe', '2026-08-07 10:44:46', NULL),
('19', '19', 'Lý Thanh Hương', 'female', '1993-03-08', '0956789012', 'thanhhuong.ly@gmail.com', '046093890123', '34 Ngô Quyền, TP. Huế', 'paid', '1', '2026-08-01 07:25:00', '', '2026-08-07 10:44:46', '2026-08-07 10:50:18'),
('20', '19', 'Lý Thanh Phong', 'male', '1990-11-15', '0956789013', 'thanhphong.ly@gmail.com', '046090901234', '34 Ngô Quyền, TP. Huế', 'paid', '1', '2026-08-01 07:25:00', 'Dị ứng hải sản nhẹ', '2026-08-07 10:44:46', '2026-08-07 10:50:18'),
('21', '103', 'Thanh Huệ', 'male', '1990-01-01', '0865144307', 'thanhhue001@gmail.com', NULL, '8B nghách 46 ngõ 1 Bùi Xương Trạch', 'unpaid', '0', NULL, '', '2026-08-07 10:45:14', NULL),
('22', '103', 'Thanh Huệ (Khách đi cùng #2)', 'female', '1990-01-01', '0900000002', NULL, NULL, '8B nghách 46 ngõ 1 Bùi Xương Trạch', 'unpaid', '0', NULL, '', '2026-08-07 10:45:14', NULL),
('23', '103', 'Thanh Huệ (Khách đi cùng #3)', 'male', '1990-01-01', '0900000003', NULL, NULL, '8B nghách 46 ngõ 1 Bùi Xương Trạch', 'unpaid', '0', NULL, '', '2026-08-07 10:45:14', NULL),
('24', '103', 'Thanh Huệ (Khách đi cùng #4)', 'female', '1990-01-01', '0900000004', NULL, NULL, '8B nghách 46 ngõ 1 Bùi Xương Trạch', 'unpaid', '0', NULL, '', '2026-08-07 10:45:14', NULL),
('25', '103', 'Thanh Huệ (Khách đi cùng #5)', 'male', '1990-01-01', '0900000005', NULL, NULL, '8B nghách 46 ngõ 1 Bùi Xương Trạch', 'unpaid', '0', NULL, '', '2026-08-07 10:45:14', NULL),
('26', '104', 'Chuẩn', 'male', '1990-01-01', '0349422856', 'zvuchuan98@gmail.com', NULL, '8B nghách 46 ngõ 1 Bùi Xương Trạch', 'unpaid', '0', NULL, '', '2026-08-07 10:45:14', NULL),
('27', '104', 'Chuẩn (Khách đi cùng #2)', 'female', '1990-01-01', '0900000002', NULL, NULL, '8B nghách 46 ngõ 1 Bùi Xương Trạch', 'unpaid', '0', NULL, '', '2026-08-07 10:45:14', NULL),
('28', '104', 'Chuẩn (Khách đi cùng #3)', 'male', '1990-01-01', '0900000003', NULL, NULL, '8B nghách 46 ngõ 1 Bùi Xương Trạch', 'unpaid', '0', NULL, '', '2026-08-07 10:45:14', NULL),
('29', '104', 'Chuẩn (Khách đi cùng #4)', 'female', '1990-01-01', '0900000004', NULL, NULL, '8B nghách 46 ngõ 1 Bùi Xương Trạch', 'unpaid', '0', NULL, '', '2026-08-07 10:45:14', NULL),
('30', '104', 'Chuẩn (Khách đi cùng #5)', 'male', '1990-01-01', '0900000005', NULL, NULL, '8B nghách 46 ngõ 1 Bùi Xương Trạch', 'unpaid', '0', NULL, '', '2026-08-07 10:45:14', NULL);

SET FOREIGN_KEY_CHECKS=1;
SET UNIQUE_CHECKS=1;
