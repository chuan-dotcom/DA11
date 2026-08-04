SET NAMES utf8mb4;
SET time_zone = '+07:00';

CREATE TABLE IF NOT EXISTS bookings (
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

SET @tour_id := (SELECT id FROM tours ORDER BY id LIMIT 1);
SET @departure_id := (SELECT id FROM departures ORDER BY id LIMIT 1);

INSERT INTO bookings
(tour_id, departure_id, customer_name, customer_email, customer_phone, num_people, total_price, booking_date, status, note)
SELECT
    @tour_id, @departure_id, 'Chuẩn', 'demo-booking-17@example.com', '0349422856', 17, 17000000, CURDATE(), 1, 'Dữ liệu mẫu (17 khách)'
WHERE @tour_id IS NOT NULL;
