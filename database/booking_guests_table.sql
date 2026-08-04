SET NAMES utf8mb4;
SET time_zone = '+07:00';

CREATE TABLE IF NOT EXISTS booking_guests (
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

SET @tour_id := (SELECT id FROM tours ORDER BY id LIMIT 1);
SET @departure_id := (SELECT id FROM departures ORDER BY id LIMIT 1);

INSERT INTO bookings
(tour_id, departure_id, customer_name, customer_email, customer_phone, num_people, total_price, booking_date, status, note)
SELECT
    @tour_id, @departure_id, 'Booking mẫu - Tài Nguyên', 'booking.sample@example.com', '0900000000', 10, 10000000, CURDATE(), 1, 'Dữ liệu mẫu'
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

