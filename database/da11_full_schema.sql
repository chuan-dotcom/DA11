SET NAMES utf8mb4;
SET time_zone = '+07:00';
SET FOREIGN_KEY_CHECKS = 0;

CREATE DATABASE IF NOT EXISTS da11 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE da11;

DROP TABLE IF EXISTS tour_diaries;
DROP TABLE IF EXISTS services;
DROP TABLE IF EXISTS staff_assignments;
DROP TABLE IF EXISTS booking_guests;
DROP TABLE IF EXISTS bookings;
DROP TABLE IF EXISTS departures;
DROP TABLE IF EXISTS hdv;
DROP TABLE IF EXISTS tours;
DROP TABLE IF EXISTS tour_categories;
DROP TABLE IF EXISTS users;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20) NULL,
    role ENUM('admin', 'user') NOT NULL DEFAULT 'user',
    avatar VARCHAR(255) NULL,
    status TINYINT(1) NOT NULL DEFAULT 1,
    created_at DATETIME NULL,
    updated_at DATETIME NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE tour_categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE tours (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    category_id INT NOT NULL,
    price DECIMAL(15,2) NOT NULL DEFAULT 0,
    duration VARCHAR(50) NULL,
    description TEXT NULL,
    image VARCHAR(255) NULL,
    status TINYINT(1) NOT NULL DEFAULT 1,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_tours_category_id (category_id),
    CONSTRAINT fk_tours_category
        FOREIGN KEY (category_id)
        REFERENCES tour_categories(id)
        ON UPDATE CASCADE
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE hdv (
    HDV_id INT AUTO_INCREMENT PRIMARY KEY,
    Hoten VARCHAR(50) NOT NULL,
    Ngaysinh DATE NULL,
    Gioitinh VARCHAR(10) NULL,
    Lienhe VARCHAR(150) NULL,
    Ngonngu VARCHAR(50) NULL,
    Diachi VARCHAR(255) NULL,
    chungchiHDV VARCHAR(50) NULL,
    Kinhnghiem INT NULL,
    Ngaybatdaulam DATE NULL,
    Trangthaisuckhoe TEXT NULL,
    Ghichunoibo TEXT NULL,
    Diemdanhgia DECIMAL(3,1) NULL DEFAULT 0.0,
    Nhanxetdanhgia TEXT NULL,
    HDV_group_id INT NULL,
    Status ENUM('active', 'inactive', 'on_leave') NOT NULL DEFAULT 'active',
    created_at DATETIME NULL,
    updated_at DATETIME NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE departures (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tour_id INT NOT NULL,
    group_name VARCHAR(255) NULL,
    departure_date DATE NOT NULL,
    return_date DATE NULL,
    max_participants INT NOT NULL DEFAULT 0,
    meeting_point VARCHAR(255) NULL,
    meeting_time TIME NULL,
    vehicle VARCHAR(100) NULL,
    notes TEXT NULL,
    status ENUM('scheduled','in_progress','completed','cancelled') NOT NULL DEFAULT 'scheduled',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_departures_tour_id (tour_id),
    INDEX idx_departures_departure_date (departure_date),
    CONSTRAINT fk_departures_tour
        FOREIGN KEY (tour_id)
        REFERENCES tours(id)
        ON UPDATE CASCADE
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tour_id INT NOT NULL,
    departure_id INT NULL,
    customer_name VARCHAR(255) NOT NULL,
    customer_email VARCHAR(255) NOT NULL,
    customer_phone VARCHAR(50) NOT NULL,
    num_people INT NOT NULL DEFAULT 1,
    total_price DECIMAL(15,2) NOT NULL DEFAULT 0,
    booking_date DATE NOT NULL,
    status TINYINT NOT NULL DEFAULT 0,
    note TEXT NULL,
    check_in_status TINYINT(1) NOT NULL DEFAULT 0,
    checked_in_at DATETIME NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_bookings_tour_id (tour_id),
    INDEX idx_bookings_departure_id (departure_id),
    INDEX idx_bookings_status (status),
    INDEX idx_bookings_booking_date (booking_date),
    CONSTRAINT fk_bookings_tour
        FOREIGN KEY (tour_id)
        REFERENCES tours(id)
        ON UPDATE CASCADE
        ON DELETE CASCADE,
    CONSTRAINT fk_bookings_departure
        FOREIGN KEY (departure_id)
        REFERENCES departures(id)
        ON UPDATE CASCADE
        ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE booking_guests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    booking_id INT NOT NULL,
    full_name VARCHAR(255) NOT NULL,
    gender ENUM('male','female','other') NULL DEFAULT NULL,
    dob DATE NULL DEFAULT NULL,
    phone VARCHAR(50) NULL DEFAULT NULL,
    email VARCHAR(255) NULL DEFAULT NULL,
    identity_no VARCHAR(50) NULL DEFAULT NULL,
    address VARCHAR(255) NULL DEFAULT NULL,
    payment_status ENUM('unpaid','deposit','paid') NOT NULL DEFAULT 'unpaid',
    check_in_status TINYINT(1) NOT NULL DEFAULT 0,
    checked_in_at DATETIME NULL DEFAULT NULL,
    note TEXT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_booking_guests_booking_id (booking_id),
    INDEX idx_booking_guests_check_in_status (check_in_status),
    CONSTRAINT fk_booking_guests_booking
        FOREIGN KEY (booking_id)
        REFERENCES bookings(id)
        ON UPDATE CASCADE
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE staff_assignments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    departure_id INT NOT NULL,
    staff_id INT NOT NULL,
    role ENUM('lead_guide','assistant_guide','driver','photographer','other') NOT NULL DEFAULT 'other',
    responsibilities TEXT NULL,
    notes TEXT NULL,
    status ENUM('assigned','confirmed','completed','rejected') NOT NULL DEFAULT 'assigned',
    assigned_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uniq_departure_staff (departure_id, staff_id),
    INDEX idx_staff_assignments_departure_id (departure_id),
    INDEX idx_staff_assignments_staff_id (staff_id),
    CONSTRAINT fk_staff_departure
        FOREIGN KEY (departure_id)
        REFERENCES departures(id)
        ON UPDATE CASCADE
        ON DELETE CASCADE,
    CONSTRAINT fk_staff_hdv
        FOREIGN KEY (staff_id)
        REFERENCES hdv(HDV_id)
        ON UPDATE CASCADE
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE services (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tour_id INT NOT NULL,
    service_types VARCHAR(255) NOT NULL,
    supplier VARCHAR(255) NOT NULL,
    quantity INT NOT NULL DEFAULT 1,
    status TINYINT NOT NULL DEFAULT 0,
    start_time DATETIME NULL,
    end_time DATETIME NULL,
    note TEXT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_services_tour_id (tour_id),
    CONSTRAINT fk_services_tour
        FOREIGN KEY (tour_id)
        REFERENCES tours(id)
        ON UPDATE CASCADE
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE tour_diaries (
    id INT AUTO_INCREMENT PRIMARY KEY,
    departure_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    diary_date DATE NOT NULL,
    weather VARCHAR(100) NULL,
    mood VARCHAR(100) NULL,
    photos TEXT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_tour_diaries_departure_id (departure_id),
    INDEX idx_tour_diaries_diary_date (diary_date),
    CONSTRAINT fk_tour_diaries_departure
        FOREIGN KEY (departure_id)
        REFERENCES departures(id)
        ON UPDATE CASCADE
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO users (name, email, password, phone, role, status, created_at) VALUES
('Administrator', 'admin@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '0901234567', 'admin', 1, NOW());

INSERT INTO tour_categories (name, description) VALUES
('Du lịch trong nước', 'Tour du lịch trong nước'),
('Du lịch quốc tế', 'Tour du lịch quốc tế');

INSERT INTO tours (name, category_id, price, duration, description, image, status) VALUES
('Tour mẫu 1', 1, 1000000, '3N2Đ', 'Tour mẫu', NULL, 1),
('Tour mẫu 2', 2, 2000000, '4N3Đ', 'Tour mẫu', NULL, 1);

SET @tour_id := (SELECT id FROM tours ORDER BY id LIMIT 1);

INSERT INTO departures
(tour_id, group_name, departure_date, return_date, max_participants, meeting_point, meeting_time, vehicle, notes, status)
SELECT
    @tour_id, 'Booking #3 - Tài Nguyên', DATE_ADD(CURDATE(), INTERVAL 7 DAY), DATE_ADD(CURDATE(), INTERVAL 9 DAY), 30, 'Văn phòng công ty', '08:00:00', 'Xe du lịch', 'Chuyến mẫu', 'scheduled'
WHERE @tour_id IS NOT NULL;

SET @departure_id := (SELECT id FROM departures ORDER BY id DESC LIMIT 1);

INSERT INTO bookings
(tour_id, departure_id, customer_name, customer_email, customer_phone, num_people, total_price, booking_date, status, note)
SELECT
    @tour_id, @departure_id, 'Tài Nguyên', 'booking.sample@example.com', '0900000000', 10, 10000000, CURDATE(), 1, 'Booking mẫu'
WHERE @tour_id IS NOT NULL;

SET @booking_id := (SELECT id FROM bookings ORDER BY id DESC LIMIT 1);

INSERT INTO booking_guests
(booking_id, full_name, gender, dob, phone, email, identity_no, address, payment_status, check_in_status, checked_in_at, note)
SELECT
    @booking_id, 'Huỳnh Bảo Ny', 'female', '2010-08-05', '0986714037', 'nguyenanhtai24082006@gmail.com', '12345', 'Huế', 'unpaid', 0, NULL, NULL
WHERE @booking_id IS NOT NULL;

INSERT INTO booking_guests
(booking_id, full_name, gender, dob, phone, email, identity_no, address, payment_status, check_in_status, checked_in_at, note)
SELECT
    @booking_id, 'Nguyễn Anh Tài', 'male', '2025-11-30', '0986714036', 'nguyenanhtai24082006@gmail.com', '12345', 'Hà Nội', 'deposit', 1, NOW(), 'Yêu cầu: có cỏ'
WHERE @booking_id IS NOT NULL;

INSERT INTO hdv (Hoten, Ngaysinh, Gioitinh, Lienhe, Ngonngu, Diachi, chungchiHDV, Kinhnghiem, Ngaybatdaulam, Trangthaisuckhoe, Ghichunoibo, Diemdanhgia, Nhanxetdanhgia, HDV_group_id, Status, created_at) VALUES
('Nguyễn Văn An', '1990-05-15', 'Nam', '0901234567 - an.nguyen@email.com', 'Tiếng Anh, Tiếng Việt', '123 Nguyễn Huệ, Q.1, TP.HCM', 'HDV Quốc tế #9921', 5, '2020-01-15', 'Tốt', 'Nhiệt tình', 4.8, 'Phản hồi tốt', 1, 'active', NOW()),
('Trần Thị Bích', '1995-08-20', 'Nữ', '0902345678 - bich.tran@email.com', 'Tiếng Trung, Tiếng Việt', '456 Lê Lợi, Q.3, TP.HCM', 'HDV Nội địa #4412', 3, '2022-03-01', 'Bình thường', 'Phù hợp tour nghỉ dưỡng', 4.5, 'Nhiệt tình', 2, 'active', NOW());

SET FOREIGN_KEY_CHECKS = 1;
